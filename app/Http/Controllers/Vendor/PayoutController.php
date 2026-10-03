<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\Vendor;
use App\Models\Marketplace\VendorPayout;
use App\Services\Marketplace\VendorEarningsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    /** Minimum a vendor may request, to avoid pointless micro-payouts. */
    private const MINIMUM_PAYOUT = 5000;

    public function __construct(
        private VendorEarningsService $earnings,
    ) {}

    private function vendor(): Vendor
    {
        $vendor = auth()->user()->marketplaceVendor;

        abort_unless($vendor, 403, 'No vendor account is linked to your profile.');

        return $vendor;
    }

    /**
     * Earnings breakdown plus the vendor's payout ledger.
     *
     * Every figure is derived from delivered order items and the vendor's own
     * payout rows, both hard-scoped by vendor_id.
     */
    public function index(Request $request)
    {
        $vendor = $this->vendor();

        $from = $request->input('from');
        $to = $request->input('to');

        $summary = $this->earnings->summary($vendor, $from, $to);

        $payouts = VendorPayout::forVendor($vendor->id)
            ->when($request->input('status'), fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $payoutCounts = VendorPayout::forVendor($vendor->id)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('vendor.payouts.index', [
            'vendor' => $vendor,
            'summary' => $summary,
            'payouts' => $payouts,
            'payoutCounts' => $payoutCounts,
            'currentStatus' => $request->input('status'),
            'from' => $from,
            'to' => $to,
            'minimumPayout' => self::MINIMUM_PAYOUT,
            'canRequest' => $summary['available'] >= self::MINIMUM_PAYOUT,
        ]);
    }

    /**
     * Create a payout request.
     *
     * The amount is re-derived server-side from delivered sales; the client
     * cannot post an arbitrary figure. A payout row is written immediately with
     * status `requested`, which is what makes the balance stop showing as
     * available (preventing a double request against the same earnings).
     */
    public function store(Request $request)
    {
        $vendor = $this->vendor();

        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payout_method' => 'required|string|max:40',
            'account_name' => 'required|string|max:120',
            'account_number' => 'required|string|max:20',
            'bank_name' => 'required|string|max:120',
        ]);

        $summary = $this->earnings->summary($vendor);

        $requested = round((float) $data['amount'], 2);

        if ($requested > $summary['available']) {
            return back()
                ->withInput()
                ->with('error', 'You cannot request more than your available balance of ' . format_currency($summary['available']) . '.');
        }

        if ($requested < self::MINIMUM_PAYOUT) {
            return back()
                ->withInput()
                ->with('error', 'The minimum payout request is ' . format_currency(self::MINIMUM_PAYOUT) . '.');
        }

        DB::transaction(function () use ($vendor, $data, $requested, $summary) {
            VendorPayout::create([
                'vendor_id' => $vendor->id,
                'reference' => VendorPayout::generateReference(),
                'amount' => $requested,
                'gross_sales' => $summary['gross_sales'],
                'commission_amount' => $summary['commission_amount'],
                'commission_rate' => $summary['commission_rate'],
                'other_deductions' => $summary['other_deductions'],
                'status' => 'requested',
                'payout_method' => $data['payout_method'],
                'account_name' => $data['account_name'],
                'account_number' => $data['account_number'],
                'bank_name' => $data['bank_name'],
                'requested_at' => now(),
            ]);
        });

        return redirect()
            ->route('vendor.payouts.index')
            ->with('success', 'Payout request submitted. Our team will review it shortly.');
    }

    /** Cancel a request that has not yet been actioned by an administrator. */
    public function cancel(VendorPayout $payout)
    {
        $vendor = $this->vendor();

        abort_unless($payout->vendor_id === $vendor->id, 404);

        if (! in_array($payout->status, ['requested'], true)) {
            return back()->with('error', 'This payout can no longer be cancelled.');
        }

        $payout->update([
            'status' => 'cancelled',
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Payout request cancelled.');
    }
}
