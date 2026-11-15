<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ApplyPriceImportBatch;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\PriceImportBatch;
use App\Models\Marketplace\PriceImportItem;
use App\Models\Marketplace\ProductPriceHistory;
use App\Models\Marketplace\Vendor;
use App\Services\Marketplace\PriceImport\ProductPriceImportService;
use App\Services\Marketplace\PriceImport\PriceImportRollbackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ProductPriceImportController extends Controller
{
    public function index(Request $request)
    {
        $batches = PriceImportBatch::with('vendor', 'admin')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'batches' => PriceImportBatch::count(),
            'processed' => (int) PriceImportBatch::sum('total_records'),
            'updated' => (int) PriceImportBatch::sum('applied_count'),
            'created' => (int) PriceImportBatch::sum('created_count'),
            'failed' => (int) PriceImportBatch::sum('invalid_count') + (int) PriceImportBatch::sum('failed_count'),
            'last' => PriceImportBatch::with('vendor')
                ->whereIn('status', [PriceImportBatch::STATUS_COMPLETED, PriceImportBatch::STATUS_PARTIALLY_APPLIED])
                ->latest('applied_at')
                ->first(),
        ];

        return view('admin.marketplace.price-imports.index', [
            'batches' => $batches,
            'stats' => $stats,
            'vendors' => Vendor::orderBy('name')->get(['id', 'name', 'business_name', 'status']),
            'categories' => Category::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request, ProductPriceImportService $service)
    {
        $data = $request->validate([
            'vendor_id' => ['required', 'integer', 'exists:marketplace_vendors,id'],
            'file' => ['required', 'file', 'max:10240'],
            'create_new_products' => ['nullable', 'boolean'],
            'default_category_id' => ['nullable', 'integer', 'exists:marketplace_categories,id'],
            'mapping' => ['nullable', 'array'],
            'mapping.*' => ['nullable', 'string', 'max:120'],
        ]);

        $file = $request->file('file');
        $extension = strtolower((string) $file->getClientOriginalExtension());

        // The extension whitelist is authoritative; browser MIME types for CSV
        // files are unreliable (Windows often reports application/vnd.ms-excel).
        // Content is validated again by the CSV/XML parsers.
        if (! in_array($extension, ['csv', 'txt', 'xml'], true)) {
            return back()->withInput()->withErrors([
                'file' => 'Only CSV and XML price lists are supported (.csv, .txt, .xml).',
            ]);
        }

        $handle = fopen($file->getRealPath(), 'rb');
        $head = $handle !== false ? (string) fread($handle, 64) : '';
        if ($handle !== false) {
            fclose($handle);
        }

        if (stripos($head, '<?php') === 0 || stripos($head, '<script') === 0) {
            return back()->withInput()->withErrors([
                'file' => 'The uploaded file does not look like a CSV or XML price list.',
            ]);
        }

        $vendor = Vendor::findOrFail($data['vendor_id']);
        $admin = Auth::guard('admin')->user();

        try {
            $batch = $service->createPreview($file, $vendor, $admin, [
                'create_new_products' => $request->boolean('create_new_products'),
                'default_category_id' => $data['default_category_id'] ?? null,
                'mapping' => array_filter($data['mapping'] ?? [], fn ($value) => $value !== null && $value !== ''),
            ]);
        } catch (Throwable $exception) {
            return back()->withInput()->with('error', 'The price list could not be read: '.$exception->getMessage());
        }

        return redirect()
            ->route('admin.marketplace.price-imports.show', $batch)
            ->with('success', 'Price list uploaded. Review the matches and confirm before applying.');
    }

    public function show(Request $request, PriceImportBatch $batch)
    {
        $batch->load('vendor', 'admin', 'rollbackAdmin');

        $changeType = $request->input('change_type');

        $items = $batch->items()
            ->with('product:id,name,sku,price,is_active,approval_status')
            ->when($changeType && array_key_exists($changeType, PriceImportItem::CHANGE_LABELS), fn ($query) => $query->where('change_type', $changeType))
            ->orderBy('row_number')
            ->paginate(50)
            ->withQueryString();

        $counts = $batch->items()
            ->select('change_type', DB::raw('count(*) as total'))
            ->groupBy('change_type')
            ->pluck('total', 'change_type');

        return view('admin.marketplace.price-imports.show', [
            'batch' => $batch,
            'items' => $items,
            'counts' => $counts,
            'changeType' => $changeType,
            'missing' => $batch->missingProducts()->orderBy('product_name')->get(),
            'histories' => $batch->priceHistories()
                ->with('admin')
                ->latest('id')
                ->paginate(25, ['*'], 'history_page'),
            'categories' => Category::active()->orderBy('name')->get(['id', 'name']),
            'queueThreshold' => 500,
        ]);
    }

    public function apply(Request $request, PriceImportBatch $batch, ProductPriceImportService $service)
    {
        $data = $request->validate([
            'create_new_products' => ['nullable', 'boolean'],
            'default_category_id' => ['nullable', 'integer', 'exists:marketplace_categories,id'],
            'deactivate_missing' => ['nullable', 'boolean'],
            'confirm_complete_catalogue' => ['nullable', 'boolean'],
            'confirm_review' => ['accepted'],
        ]);

        if ($request->boolean('deactivate_missing') && ! $request->boolean('confirm_complete_catalogue')) {
            return back()->withErrors([
                'confirm_complete_catalogue' => 'Confirm that this upload is the supplier\'s complete catalogue before deactivating missing products.',
            ]);
        }

        if ($batch->isProcessing()) {
            return back()->with('error', 'This upload is already being processed.');
        }

        $admin = Auth::guard('admin')->user();

        $options = [
            'create_new_products' => $request->boolean('create_new_products'),
            'default_category_id' => $data['default_category_id'] ?? null,
            'deactivate_missing' => $request->boolean('deactivate_missing'),
            'confirm_complete_catalogue' => $request->boolean('confirm_complete_catalogue'),
        ];

        $threshold = 500;
        $total = $batch->items()->count();

        if ($total > $threshold && config('queue.default') !== 'sync') {
            $batch->update([
                'status' => PriceImportBatch::STATUS_QUEUED,
                'options' => array_merge($batch->options ?? [], $options),
            ]);

            ApplyPriceImportBatch::dispatch($batch->id, $admin->id, $options);

            return redirect()
                ->route('admin.marketplace.price-imports.show', $batch)
                ->with('success', 'This is a large upload ('.number_format($total).' records). It has been queued and will update in the background.');
        }

        try {
            $service->apply($batch, $admin, $options);
        } catch (Throwable $exception) {
            return back()->with('error', 'The price update could not be applied: '.$exception->getMessage());
        }

        return redirect()
            ->route('admin.marketplace.price-imports.show', $batch)
            ->with('success', 'Price updates applied. Every change was recorded in the price history.');
    }

    public function rollback(Request $request, PriceImportBatch $batch, PriceImportRollbackService $service)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
            'confirm_rollback' => ['accepted'],
        ]);

        try {
            $result = $service->rollback($batch, Auth::guard('admin')->user(), $data['reason']);
        } catch (Throwable $exception) {            return back()->with('error', 'Rollback failed: '.$exception->getMessage());
        }

        $message = number_format($result['restored']).' product price(s) restored.';

        if ($result['skipped'] > 0) {
            $message .= ' '.number_format($result['skipped']).' skipped because a newer price superseded the batch.';
        }

        if ($result['created_products'] > 0) {
            $message .= ' '.number_format($result['created_products']).' product(s) created by this batch were left in place.';
        }

        return redirect()
            ->route('admin.marketplace.price-imports.show', $batch)
            ->with('success', $message);
    }

    public function status(PriceImportBatch $batch)
    {
        return response()->json([
            'id' => $batch->id,
            'status' => $batch->status,
            'status_label' => $batch->statusLabel(),
            'total' => $batch->total_records,
            'processed' => $batch->processed_count,
            'applied' => $batch->applied_count,
            'created' => $batch->created_count,
            'failed' => $batch->failed_count,
            'deactivated' => $batch->deactivated_count,
            'finished' => ! $batch->isProcessing(),
            'error' => $batch->last_error,
        ]);
    }

    public function template(string $format): StreamedResponse
    {
        $format = strtolower($format);

        if (! in_array($format, ['csv', 'xml'], true)) {
            abort(404);
        }

        if ($format === 'csv') {
            $rows = [
                ['sku', 'supplier_product_code', 'barcode', 'product_name', 'category', 'unit', 'price'],
                ['OPEY-0001', 'SUP-1001', '6151100012345', 'Golden Penny Spaghetti', 'Food Items', '500g', '12500'],
                ['OPEY-0002', 'SUP-1002', '6151100012346', 'Golden Penny Macaroni', 'Food Items', '500g', '13500'],
                ['NEW-0001', 'SUP-2001', '6151100099999', 'New Product Example', 'Groceries', '1L', '9500'],
            ];

            return response()->streamDownload(function () use ($rows) {
                $handle = fopen('php://output', 'wb');
                fwrite($handle, "\xEF\xBB\xBF");

                foreach ($rows as $row) {
                    fputcsv($handle, $row);
                }

                fclose($handle);
            }, 'foodchain-price-list-template.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<products>
    <product>
        <sku>OPEY-0001</sku>
        <supplier_product_code>SUP-1001</supplier_product_code>
        <barcode>6151100012345</barcode>
        <product_name>Golden Penny Spaghetti</product_name>
        <category>Food Items</category>
        <unit>500g</unit>
        <price>12500</price>
    </product>
    <product>
        <sku>NEW-0001</sku>
        <supplier_product_code>SUP-2001</supplier_product_code>
        <barcode>6151100099999</barcode>
        <product_name>New Product Example</product_name>
        <category>Groceries</category>
        <unit>1L</unit>
        <price>9500</price>
    </product>
</products>
XML;

        return response()->streamDownload(function () use ($xml) {
            echo $xml;
        }, 'foodchain-price-list-template.xml', ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function export(PriceImportBatch $batch, string $type): StreamedResponse
    {
        if (! in_array($type, ['items', 'errors', 'history'], true)) {
            abort(404);
        }

        $filename = 'price-import-'.$batch->id.'-'.$type.'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($batch, $type) {
            $handle = fopen('php://output', 'wb');
            fwrite($handle, "\xEF\xBB\xBF");

            if ($type === 'items') {
                fputcsv($handle, [
                    'Row', 'SKU', 'Supplier code', 'Barcode', 'Product', 'Category', 'Unit',
                    'Uploaded price', 'Current price', 'New price', 'Difference', '% change',
                    'Status', 'Change', 'Match method', 'Applied', 'Errors',
                ]);

                $batch->items()->orderBy('row_number')->chunk(500, function ($items) use ($handle) {
                    foreach ($items as $item) {
                        fputcsv($handle, [
                            $item->row_number,
                            $item->sku,
                            $item->supplier_product_code,
                            $item->barcode,
                            $item->product_name,
                            $item->category_name,
                            $item->unit,
                            $item->price,
                            $item->current_price,
                            $item->new_price,
                            $item->difference,
                            $item->percent_change,
                            $item->status,
                            $item->change_type,
                            $item->match_method,
                            $item->applied ? 'yes' : 'no',
                            implode(' ', $item->errors ?? []),
                        ]);
                    }
                });
            } elseif ($type === 'errors') {
                fputcsv($handle, ['Row', 'SKU', 'Supplier code', 'Barcode', 'Product', 'Uploaded price', 'Status', 'Errors']);

                $batch->items()
                    ->where(function ($query) {
                        $query->whereIn('status', [PriceImportItem::STATUS_INVALID, PriceImportItem::STATUS_AMBIGUOUS])
                            ->orWhere('requires_manual_completion', true);
                    })
                    ->orderBy('row_number')
                    ->chunk(500, function ($items) use ($handle) {
                        foreach ($items as $item) {
                            fputcsv($handle, [
                                $item->row_number,
                                $item->sku,
                                $item->supplier_product_code,
                                $item->barcode,
                                $item->product_name,
                                $item->price,
                                $item->status,
                                implode(' ', $item->errors ?? []),
                            ]);
                        }
                    });
            } else {
                fputcsv($handle, [
                    'Product', 'SKU', 'Old price', 'New price', 'Difference', '% change',
                    'Source', 'Status', 'Admin', 'Date', 'Note',
                ]);

                $batch->priceHistories()->with('admin')->orderBy('id')->chunk(500, function ($histories) use ($handle) {
                    foreach ($histories as $history) {
                        fputcsv($handle, [
                            $history->product_name,
                            $history->sku,
                            $history->old_price,
                            $history->new_price,
                            $history->difference,
                            $history->percent_change,
                            $history->source,
                            $history->status,
                            $history->admin?->name,
                            $history->created_at?->toDateTimeString(),
                            $history->note,
                        ]);
                    }
                });
            }

            fclose($handle);
        }, Str::slug($filename, '.'), ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
