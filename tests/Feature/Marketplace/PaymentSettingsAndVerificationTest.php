<?php

namespace Tests\Feature\Marketplace;

use App\Models\AdminUser;
use App\Models\BankAccount;
use App\Models\ManualPayment;
use App\Models\Marketplace\CartItem;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use App\Models\Payment;
use App\Models\User;
use App\Services\Marketplace\MarketplacePricingService;
use App\Services\Payments\PaymentMethodSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use Tests\TestCase;

/**
 * Covers the admin-controlled payment availability rules and the full
 * bank-transfer notification -> admin verification workflow.
 */
class PaymentSettingsAndVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private AdminUser $admin;

    private PaymentMethodSettings $methods;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resetPricingCache();

        $this->customer = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
            'wallet_balance' => 0,
        ]);

        $this->admin = AdminUser::create([
            'name' => 'Payments Admin',
            'email' => 'payments-admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        BankAccount::create([
            'bank_name' => 'First Bank',
            'account_name' => 'FoodChain Global Services Ltd',
            'account_number' => '1234567890',
            'account_type' => 'current',
            'is_active' => true,
            'is_default' => true,
        ]);

        $this->methods = app(PaymentMethodSettings::class);
    }

    private function resetPricingCache(): void
    {
        $class = new ReflectionClass(MarketplacePricingService::class);
        $class->setStaticPropertyValue('mapCache', []);
        $class->setStaticPropertyValue('nameCache', []);
        $class->setStaticPropertyValue('currentCache', null);
    }

    /**
     * Turn every method off except bank transfer. The test base class enables
     * everything by default so unrelated flows keep working.
     */
    private function onlyBankTransfer(): void
    {
        foreach (array_keys($this->methods->labels()) as $method) {
            $this->methods->setEnabled($method, $method === PaymentMethodSettings::BANK_TRANSFER);
        }
    }

    private function actingAsAdmin()
    {
        return $this->actingAs($this->admin, 'admin');
    }

    private function makeOrder(array $overrides = []): Order
    {
        return Order::create(array_merge([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-'.strtoupper(uniqid()),
            'status' => 'pending',
            'subtotal' => 10000,
            'total' => 10000,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'unpaid',
            'delivery_type' => 'home_delivery',
            'delivery_state' => 'Lagos',
        ], $overrides));
    }

    private function makeManualPayment(Order $order, array $overrides = []): ManualPayment
    {
        return ManualPayment::create(array_merge([
            'user_id' => $this->customer->id,
            'marketplace_order_id' => $order->id,
            'type' => 'marketplace_order',
            'amount' => $order->total,
            'currency' => 'NGN',
            'reference' => ManualPayment::generateReference(),
            'bank_account_id' => BankAccount::first()->id,
            'sender_name' => $this->customer->full_name ?: 'Test Customer',
            'proof_image_path' => 'manual-payments/proofs/not-provided',
            'transfer_date' => now(),
            'status' => ManualPayment::STATUS_PENDING,
            'payment_status' => 'pending',
            'verification_status' => 'pending',
        ], $overrides));
    }

    private function seedCart(): void
    {
        $vendorUser = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);

        $vendor = Vendor::create([
            'user_id' => $vendorUser->id,
            'name' => 'Payment Test Store',
            'slug' => 'payment-test-store-'.uniqid(),
            'business_name' => 'Payment Test Business',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);

        $category = Category::create([
            'name' => 'Test Category '.uniqid(),
            'slug' => 'test-category-'.uniqid(),
            'is_active' => true,
        ]);

        $product = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Test Rice 5kg',
            'slug' => 'test-rice-'.uniqid(),
            'sku' => Product::generateSku(),
            'price' => 10000,
            'stock_quantity' => 20,
            'low_stock_threshold' => 3,
            'is_active' => true,
            'approval_status' => 'approved',
        ]);

        CartItem::create([
            'user_id' => $this->customer->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }

    // ------------------------------------------------- admin settings

    public function test_only_enabled_methods_are_offered_at_checkout(): void
    {
        $this->onlyBankTransfer();
        $this->seedCart();

        $response = $this->actingAs($this->customer)
            ->withSession(['marketplace_state' => 'Lagos'])
            ->get(route('marketplace.checkout.index'))
            ->assertOk();

        $response->assertViewHas('paymentMethods', function (array $methods) {
            return array_keys($methods) === [PaymentMethodSettings::BANK_TRANSFER];
        });

        $response->assertSee('Bank Transfer');
    }

    public function test_disabled_method_is_rejected_on_direct_checkout_post(): void
    {
        $this->onlyBankTransfer();
        $this->seedCart();

        $this->actingAs($this->customer)
            ->withSession(['marketplace_state' => 'Lagos'])
            ->post(route('marketplace.checkout.store'), [
                'payment_method' => 'wallet',
                'delivery_type' => 'home_delivery',
                'delivery_state' => 'Lagos',
            ])
            ->assertSessionHasErrors('payment_method');

        $this->assertSame(0, Order::count());
    }

    public function test_admin_can_toggle_payment_methods(): void
    {
        $this->actingAsAdmin()
            ->post(route('admin.payment-settings.methods'), [
                'methods' => [
                    PaymentMethodSettings::BANK_TRANSFER => '1',
                ],
            ])
            ->assertRedirect();

        $this->methods->setEnabled(PaymentMethodSettings::BANK_TRANSFER, true);

        $this->assertTrue($this->methods->isEnabled('bank_transfer'));
        $this->assertFalse($this->methods->isEnabled('wallet'));
        $this->assertFalse($this->methods->isEnabled('paystack'));
    }

    public function test_admin_can_update_the_bank_transfer_account(): void
    {
        $this->actingAsAdmin()
            ->post(route('admin.payment-settings.bank-transfer'), [
                'bank_name' => 'Zenith Bank',
                'account_name' => 'Food Chain Enterprises Limited',
                'account_number' => '9876543210',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $details = $this->methods->bankTransferDetails();

        $this->assertSame('Zenith Bank', $details['bank_name']);
        $this->assertSame('Food Chain Enterprises Limited', $details['account_name']);
        $this->assertSame('9876543210', $details['account_number']);
    }

    // --------------------------------------- customer notification flow

    public function test_customer_notification_is_blocked_when_bank_transfer_is_disabled(): void
    {
        $order = $this->makeOrder();

        $this->methods->setEnabled(PaymentMethodSettings::BANK_TRANSFER, false);

        $this->actingAs($this->customer)
            ->post(route('marketplace.orders.bank-transfer', $order))
            ->assertRedirect(route('marketplace.orders.show', $order))
            ->assertSessionHas('error');

        $this->assertSame(0, ManualPayment::count());
    }

    public function test_customer_notification_creates_a_single_pending_record(): void
    {
        $this->onlyBankTransfer();
        $order = $this->makeOrder();

        $this->actingAs($this->customer)
            ->post(route('marketplace.orders.bank-transfer', $order), [
                'sender_name' => 'Test Customer',
            ])
            ->assertRedirect(route('marketplace.orders.show', $order))
            ->assertSessionHas('payment_notification_reference');

        $this->assertSame(1, ManualPayment::where('marketplace_order_id', $order->id)->count());

        $payment = ManualPayment::first();
        $this->assertSame(ManualPayment::STATUS_PENDING, $payment->status);
        $this->assertSame('pending', $payment->verification_status);
        $this->assertSame('pending', $payment->payment_status);
        $this->assertSame('NGN', $payment->currency);

        // Submitting again must not create a duplicate notification.
        $this->actingAs($this->customer)
            ->post(route('marketplace.orders.bank-transfer', $order))
            ->assertRedirect(route('marketplace.orders.show', $order));

        $this->assertSame(1, ManualPayment::where('marketplace_order_id', $order->id)->count());
    }

    public function test_each_notification_gets_a_unique_reference(): void
    {
        $this->onlyBankTransfer();

        $first = $this->makeOrder();
        $second = $this->makeOrder();

        $this->actingAs($this->customer)->post(route('marketplace.orders.bank-transfer', $first));
        $this->actingAs($this->customer)->post(route('marketplace.orders.bank-transfer', $second));

        $references = ManualPayment::pluck('reference');

        $this->assertCount(2, $references);
        $this->assertCount(2, $references->unique());
    }

    public function test_rejected_order_can_submit_a_new_notification(): void
    {
        $this->onlyBankTransfer();
        $order = $this->makeOrder();

        $rejected = $this->makeManualPayment($order, [
            'status' => ManualPayment::STATUS_REJECTED,
            'payment_status' => 'unpaid',
            'verification_status' => 'rejected',
            'rejection_reason' => 'Funds not found.',
            'verified_by' => $this->admin->id,
            'verified_at' => now(),
        ]);

        $this->actingAs($this->customer)
            ->post(route('marketplace.orders.bank-transfer', $order))
            ->assertSessionHas('payment_notification_reference');

        $this->assertSame(2, ManualPayment::where('marketplace_order_id', $order->id)->count());
        $this->assertSame(1, ManualPayment::where('marketplace_order_id', $order->id)
            ->where('status', ManualPayment::STATUS_PENDING)->count());
        $this->assertSame(ManualPayment::STATUS_REJECTED, $rejected->fresh()->status);
    }

    public function test_customer_cannot_reach_admin_verification(): void
    {
        $this->actingAs($this->customer)
            ->get(route('admin.manual-payments.index'))
            ->assertRedirect(route('admin.login'));
    }

    // ----------------------------------------------- admin verification

    public function test_admin_can_approve_a_confirmed_payment(): void
    {
        $order = $this->makeOrder();
        $manualPayment = $this->makeManualPayment($order);

        $this->actingAsAdmin()
            ->post(route('admin.manual-payments.verify', $manualPayment), [
                'action' => 'verify',
                'amount_confirmed' => '1',
                'confirmed_amount' => $order->total,
                'notes' => 'Funds received in full.',
            ])
            ->assertRedirect(route('admin.manual-payments.show', $manualPayment))
            ->assertSessionHas('success');

        $manualPayment->refresh();
        $this->assertSame(ManualPayment::STATUS_VERIFIED, $manualPayment->status);
        $this->assertSame('approved', $manualPayment->verification_status);
        $this->assertSame('paid', $manualPayment->payment_status);
        $this->assertSame($this->admin->id, $manualPayment->verified_by);
        $this->assertNotNull($manualPayment->verified_at);
        $this->assertNotNull($manualPayment->payment_id);

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame(1, Payment::where('manual_payment_id', $manualPayment->id)->count());
    }

    public function test_admin_cannot_approve_without_confirming_the_funds(): void
    {
        $order = $this->makeOrder();
        $manualPayment = $this->makeManualPayment($order);

        $this->actingAsAdmin()
            ->post(route('admin.manual-payments.verify', $manualPayment), [
                'action' => 'verify',
                'confirmed_amount' => $order->total,
            ])
            ->assertSessionHas('error');

        $this->assertSame(ManualPayment::STATUS_PENDING, $manualPayment->fresh()->status);
        $this->assertSame(0, Payment::count());
        $this->assertSame('unpaid', $order->fresh()->payment_status);
    }

    public function test_admin_cannot_approve_a_mismatched_amount(): void
    {
        $order = $this->makeOrder();
        $manualPayment = $this->makeManualPayment($order);

        $this->actingAsAdmin()
            ->post(route('admin.manual-payments.verify', $manualPayment), [
                'action' => 'verify',
                'amount_confirmed' => '1',
                'confirmed_amount' => $order->total - 500,
            ])
            ->assertSessionHas('error');

        $this->assertSame(ManualPayment::STATUS_PENDING, $manualPayment->fresh()->status);
        $this->assertSame(0, Payment::count());
    }

    public function test_rejection_requires_a_reason_and_keeps_the_order_unpaid(): void
    {
        $order = $this->makeOrder();
        $manualPayment = $this->makeManualPayment($order);

        $this->actingAsAdmin()
            ->post(route('admin.manual-payments.verify', $manualPayment), [
                'action' => 'reject',
            ])
            ->assertSessionHas('error');

        $this->assertSame(ManualPayment::STATUS_PENDING, $manualPayment->fresh()->status);

        $this->actingAsAdmin()
            ->post(route('admin.manual-payments.verify', $manualPayment), [
                'action' => 'reject',
                'notes' => 'Transfer not found on the company statement.',
            ])
            ->assertSessionHas('success');

        $manualPayment->refresh();
        $this->assertSame(ManualPayment::STATUS_REJECTED, $manualPayment->status);
        $this->assertSame('rejected', $manualPayment->verification_status);
        $this->assertSame('unpaid', $manualPayment->payment_status);
        $this->assertSame('Transfer not found on the company statement.', $manualPayment->rejection_reason);
        $this->assertSame('unpaid', $order->fresh()->payment_status);
        $this->assertSame(0, Payment::count());
    }

    public function test_approval_is_idempotent_and_never_duplicates_the_payment(): void
    {
        $order = $this->makeOrder();
        $manualPayment = $this->makeManualPayment($order);

        $payload = [
            'action' => 'verify',
            'amount_confirmed' => '1',
            'confirmed_amount' => $order->total,
        ];

        $this->actingAsAdmin()
            ->post(route('admin.manual-payments.verify', $manualPayment), $payload)
            ->assertSessionHas('success');

        $this->actingAsAdmin()
            ->post(route('admin.manual-payments.verify', $manualPayment), $payload)
            ->assertSessionHas('error');

        $this->assertSame(1, Payment::where('manual_payment_id', $manualPayment->id)->count());
    }

    public function test_admin_without_verify_permission_cannot_approve(): void
    {
        $supportAdmin = AdminUser::create([
            'name' => 'Support Admin',
            'email' => 'support-admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer_support',
            'is_active' => true,
        ]);

        $order = $this->makeOrder();
        $manualPayment = $this->makeManualPayment($order);

        $this->actingAs($supportAdmin, 'admin')
            ->post(route('admin.manual-payments.verify', $manualPayment), [
                'action' => 'verify',
                'amount_confirmed' => '1',
                'confirmed_amount' => $order->total,
            ])
            ->assertForbidden();

        $this->assertSame(ManualPayment::STATUS_PENDING, $manualPayment->fresh()->status);
        $this->assertSame(0, Payment::count());
    }

    public function test_payment_verification_dashboard_shows_pending_totals(): void
    {
        $order = $this->makeOrder();
        $this->makeManualPayment($order);

        $this->actingAsAdmin()
            ->get(route('admin.manual-payments.index'))
            ->assertOk()
            ->assertSee('Pending Verification')
            ->assertSee('Amount Awaiting Verification');
    }

    public function test_admin_payment_settings_page_renders_current_configuration(): void
    {
        $this->actingAsAdmin()
            ->get(route('admin.payment-settings.index'))
            ->assertOk()
            ->assertSee('Available Payment Methods')
            ->assertSee('Bank Transfer Account')
            ->assertSee('First Bank')
            ->assertSee('1234567890');
    }

    public function test_customer_order_page_renders_bank_details_copy_and_spinner(): void
    {
        $this->onlyBankTransfer();
        $order = $this->makeOrder();

        $this->actingAs($this->customer)
            ->get(route('marketplace.orders.show', $order))
            ->assertOk()
            ->assertSee('Bank Transfer Payment')
            ->assertSee('1234567890')
            ->assertSee('Copy Account Number')
            ->assertSee('I Have Made Payment')
            ->assertSee('payment-spinner-overlay');
    }
}
