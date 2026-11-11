<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\ManualPayment;
use App\Services\AuditService;
use App\Services\Payments\PaymentMethodSettings;
use Illuminate\Http\Request;

/**
 * Admin control surface for DB-driven payment methods and the single bank
 * account used for bank-transfer checkout.
 *
 * Access is limited to administrators with the "manage_settings" permission
 * (super admins always pass). Customers never reach these routes: they live
 * inside the admin guard group in routes/admin.php.
 */
class PaymentSettingsController extends Controller
{
    public function __construct(private PaymentMethodSettings $methods)
    {
    }

    public function index()
    {
        $this->authorise();

        $this->methods->syncDefaults();

        $bankAccount = BankAccount::active()->default()->first()
            ?? BankAccount::orderBy('id')->first();

        return view('admin.payment-settings.index', [
            'methods' => $this->methods->all(),
            'bankAccount' => $bankAccount,
            'accounts' => BankAccount::orderByDesc('is_default')->orderBy('bank_name')->get(),
            'stats' => [
                'pending' => ManualPayment::pending()->count(),
                'verified' => ManualPayment::verified()->count(),
                'rejected' => ManualPayment::rejected()->count(),
                'awaiting_amount' => (float) ManualPayment::pending()->sum('amount'),
            ],
            'enabledCount' => count($this->methods->enabledMethods()),
            'totalMethods' => count($this->methods->labels()),
        ]);
    }

    public function updateMethods(Request $request)
    {
        $this->authorise();

        $validated = $request->validate([
            'methods' => 'required|array',
            'methods.*' => 'boolean',
        ]);

        $before = collect($this->methods->all())->map(fn ($m) => $m['enabled'])->all();

        foreach (array_keys($this->methods->labels()) as $method) {
            $enabled = (bool) ($validated['methods'][$method] ?? false);
            $this->methods->setEnabled($method, $enabled);
        }

        $after = collect($this->methods->all())->map(fn ($m) => $m['enabled'])->all();

        AuditService::log(
            'payment_settings.methods_updated',
            null,
            AuditService::changed($before, $after),
            'Payment method availability updated.'
        );

        return back()->with('success', 'Payment methods updated. Checkout now reflects the new availability.');
    }

    public function saveBankTransfer(Request $request)
    {
        $this->authorise();

        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:100',
            'account_number' => [
                'required', 'string', 'max:20', 'regex:/^[0-9\s-]+$/',
            ],
            'instructions' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $before = $this->methods->bankTransferDetails();

        $account = $this->methods->saveBankTransferAccount([
            'account_name' => $validated['account_name'],
            'bank_name' => $validated['bank_name'],
            'account_number' => preg_replace('/\s+/', '', $validated['account_number']),
            'instructions' => $validated['instructions'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditService::log(
            'payment_settings.bank_transfer_updated',
            $account,
            [
                'before' => $before,
                'after' => $this->methods->bankTransferDetails(),
            ],
            'Bank transfer account details updated.'
        );

        return back()->with('success', 'Bank transfer account saved. Customers will see these details at checkout.');
    }

    /**
     * Read-only JSON preview for the admin screen. Never exposed publicly.
     */
    public function preview()
    {
        $this->authorise();

        return response()->json([
            'bank_transfer' => $this->methods->bankTransferDetails(),
            'enabled_methods' => $this->methods->enabledLabels(),
        ]);
    }

    private function authorise(): void
    {
        $admin = auth('admin')->user();

        abort_unless($admin && $admin->hasPermission('manage_settings'), 403);
    }
}
