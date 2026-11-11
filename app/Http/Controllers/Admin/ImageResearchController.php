<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImageResearch;
use App\Services\AuditService;
use App\Services\Images\ImageResearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/**
 * Admin review desk for the packaging-image research workflow.
 *
 * Nothing here downloads or attaches an image until an administrator has
 * verified the candidate (or uploaded a replacement). This keeps fabricated,
 * generic or wrong-variant images off the storefront.
 */
class ImageResearchController extends Controller
{
    public function __construct(private ImageResearchService $service)
    {
    }

    public function index(Request $request)
    {
        $this->authorise('view_inventory');

        $query = ProductImageResearch::with(['product', 'verifier'])
            ->where('vendor_id', $this->service->vendorId());

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($inner) use ($search) {
                $inner->where('sku', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%")
                    ->orWhere('candidate_source_site', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('without_image')) {
            $query->whereNotIn('status', [
                ProductImageResearch::STATUS_VERIFIED,
                ProductImageResearch::STATUS_DOWNLOADED,
            ]);
        }

        $records = $query->orderByRaw("CASE status
                WHEN 'pending_verification' THEN 0
                WHEN 'candidate_found' THEN 1
                WHEN 'verified' THEN 2
                WHEN 'pending_research' THEN 3
                WHEN 'not_found' THEN 4
                WHEN 'rejected' THEN 5
                ELSE 6 END")
            ->orderBy('sku')
            ->paginate(50)
            ->withQueryString();

        $report = $this->service->report();

        return view('admin.image-research.index', compact('records', 'report'));
    }

    public function show(ProductImageResearch $research)
    {
        $this->authorise('view_inventory');

        $research->load(['product.images', 'verifier']);

        return view('admin.image-research.show', compact('research'));
    }

    public function audit()
    {
        $this->authorise('manage_inventory');

        $result = $this->service->audit();

        AuditService::log(
            'image_research.audit',
            null,
            $result,
            'Refreshed the packaging-image research queue.'
        );

        return back()->with(
            'success',
            "Queue refreshed: {$result['created']} created, {$result['refreshed']} updated, {$result['outstanding']} outstanding."
        );
    }

    public function research(ProductImageResearch $research)
    {
        $this->authorise('manage_inventory');

        if (! $this->service->providerConfigured()) {
            return back()->with(
                'error',
                'No image-search provider is configured. Set IMAGE_SEARCH_PROVIDER and an API key, or upload a verified image manually.'
            );
        }

        $this->service->research($research);

        AuditService::log(
            'image_research.research',
            $research,
            ['status' => $research->status],
            'Ran image research for '.$research->sku.'.'
        );

        $message = $research->status === ProductImageResearch::STATUS_PENDING_VERIFICATION
            ? 'A candidate was found. Verify it before it can be downloaded.'
            : 'No candidate image was found for this product.';

        return back()->with($research->status === ProductImageResearch::STATUS_PENDING_VERIFICATION ? 'success' : 'error', $message);
    }

    public function verify(Request $request, ProductImageResearch $research)
    {
        $this->authorise('manage_inventory');

        $validated = $request->validate([
            'notes' => 'nullable|string|max:2000',
        ]);

        try {
            $this->service->verify($research, (int) Auth::guard('admin')->id(), $validated['notes'] ?? null);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        AuditService::log(
            'image_research.verify',
            $research,
            ['status' => $research->status],
            'Verified the packaging image candidate for '.$research->sku.'.'
        );

        return back()->with('success', 'Candidate verified. It can now be downloaded and attached.');
    }

    public function reject(Request $request, ProductImageResearch $research)
    {
        $this->authorise('manage_inventory');

        $validated = $request->validate([
            'reason' => 'required|string|max:2000',
        ]);

        try {
            $this->service->reject($research, (int) Auth::guard('admin')->id(), $validated['reason']);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        AuditService::log(
            'image_research.reject',
            $research,
            ['status' => $research->status, 'reason' => $validated['reason']],
            'Rejected the packaging image candidate for '.$research->sku.'.'
        );

        return back()->with('success', 'Candidate rejected. The product keeps its existing image.');
    }

    public function download(Request $request, ProductImageResearch $research)
    {
        $this->authorise('manage_inventory');

        $result = $this->service->download($research, $request->boolean('replace'));

        AuditService::log(
            'image_research.download',
            $research,
            ['success' => $result['success'], 'path' => $result['path'] ?? null],
            $result['message']
        );

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function upload(Request $request, ProductImageResearch $research)
    {
        $this->authorise('manage_inventory');

        $validated = $request->validate([
            'image' => [
                'required',
                'file',
                'max:8192',
                'mimes:jpg,jpeg,png,webp,gif',
            ],
        ]);

        $result = $this->service->attachUpload(
            $research,
            $validated['image'],
            (int) Auth::guard('admin')->id()
        );

        AuditService::log(
            'image_research.upload',
            $research,
            ['success' => $result['success'], 'path' => $result['path'] ?? null],
            $result['message']
        );

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    private function authorise(string $permission): void
    {
        $admin = Auth::guard('admin')->user();

        abort_unless($admin && $admin->hasPermission($permission), 403, 'You do not have permission to manage product images.');
    }
}
