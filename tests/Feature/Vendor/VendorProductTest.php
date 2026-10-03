<?php

namespace Tests\Feature\Vendor;

use App\Models\Marketplace\Category;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\ProductStatePrice;
use App\Models\Marketplace\Vendor;
use App\Models\NigerianState;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorProductTest extends TestCase
{
    use RefreshDatabase;

    private User $vendorUser;

    private Vendor $vendor;

    private User $otherVendorUser;

    private Vendor $otherVendor;

    protected function setUp(): void
    {
        parent::setUp();

        [$this->vendorUser, $this->vendor] = $this->makeVendor();
        [$this->otherVendorUser, $this->otherVendor] = $this->makeVendor();
    }

    /** @return array{0: User, 1: Vendor} */
    private function makeVendor(array $vendorOverrides = []): array
    {
        $user = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);

        $vendor = Vendor::create(array_merge([
            'user_id' => $user->id,
            'name' => 'Store ' . uniqid(),
            'slug' => 'store-' . uniqid(),
            'business_name' => 'Business ' . uniqid(),
            'status' => 'approved',
            'commission_rate' => 10,
        ], $vendorOverrides));

        return [$user, $vendor];
    }

    private function category(): Category
    {
        return Category::create([
            'name' => 'Cat ' . uniqid(),
            'slug' => 'cat-' . uniqid(),
            'is_active' => true,
        ]);
    }

    private function productFor(Vendor $vendor, array $overrides = []): Product
    {
        return Product::create(array_merge([
            'vendor_id' => $vendor->id,
            'category_id' => $this->category()->id,
            'name' => 'Rice 5kg',
            'slug' => 'rice-' . uniqid(),
            'sku' => Product::generateSku(),
            'price' => 12000,
            'stock_quantity' => 20,
            'is_active' => true,
        ], $overrides));
    }

    private function productPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'New Product',
            'category_id' => $this->category()->id,
            'unit' => 'bag',
            'price' => 5000,
            'stock_quantity' => 10,
            'low_stock_threshold' => 3,
            'is_active' => 1,
        ], $overrides);
    }

    // ------------------------------------------------------------- access

    public function test_guest_cannot_access_the_vendor_centre(): void
    {
        $this->get(route('vendor.dashboard'))->assertRedirect(route('login'));
    }

    public function test_plain_customer_is_forbidden_from_the_vendor_centre(): void
    {
        $customer = User::factory()->create(['is_active' => true]);

        // A customer has no vendor record, so the whole area must be closed to them.
        foreach (['vendor.dashboard', 'vendor.products.index', 'vendor.orders.index', 'vendor.payouts.index'] as $route) {
            $this->actingAs($customer)->get(route($route))->assertForbidden();
        }
    }

    public function test_vendor_can_access_their_dashboard(): void
    {
        $this->actingAs($this->vendorUser)
            ->get(route('vendor.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_vendor_can_access_the_product_list(): void
    {
        $this->productFor($this->vendor);

        $this->actingAs($this->vendorUser)
            ->get(route('vendor.products.index'))
            ->assertOk()
            ->assertSee('Rice 5kg');
    }

    // --------------------------------------------------- ownership on read

    public function test_vendor_product_list_excludes_other_vendors_products(): void
    {
        $mine = $this->productFor($this->vendor, ['name' => 'My Rice']);
        $theirs = $this->productFor($this->otherVendor, ['name' => 'Their Beans']);

        $this->actingAs($this->vendorUser)
            ->get(route('vendor.products.index'))
            ->assertOk()
            ->assertSee('My Rice')
            ->assertDontSee('Their Beans');
    }

    public function test_vendor_cannot_open_another_vendors_product_for_edit(): void
    {
        $theirs = $this->productFor($this->otherVendor);

        $this->actingAs($this->vendorUser)
            ->get(route('vendor.products.edit', $theirs))
            ->assertNotFound();
    }

    // ------------------------------------------------ ownership on write

    public function test_vendor_cannot_update_another_vendors_product(): void
    {
        $theirs = $this->productFor($this->otherVendor, ['name' => 'Untouched', 'price' => 1000]);

        $this->actingAs($this->vendorUser)
            ->put(route('vendor.products.update', $theirs), $this->productPayload(['name' => 'Hijacked']))
            ->assertNotFound();

        $fresh = $theirs->fresh();
        $this->assertSame('Untouched', $fresh->name);
        $this->assertSame('1000.00', (string) $fresh->price);
    }

    public function test_vendor_cannot_deactivate_another_vendors_product(): void
    {
        $theirs = $this->productFor($this->otherVendor);

        $this->actingAs($this->vendorUser)
            ->delete(route('vendor.products.delete', $theirs))
            ->assertNotFound();

        $this->assertTrue((bool) $theirs->fresh()->is_active);
    }

    public function test_vendor_cannot_toggle_another_vendors_product(): void
    {
        $theirs = $this->productFor($this->otherVendor, ['is_active' => true]);

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.products.toggle', $theirs))
            ->assertNotFound();

        $this->assertTrue((bool) $theirs->fresh()->is_active);
    }

    public function test_vendor_cannot_adjust_another_vendors_stock(): void
    {
        $theirs = $this->productFor($this->otherVendor, ['stock_quantity' => 50]);

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.products.stock', $theirs), ['quantity' => 0])
            ->assertNotFound();

        $this->assertSame(50, $theirs->fresh()->stock_quantity);
    }

    // -------------------------------------------------------------- create

    public function test_vendor_can_create_a_product(): void
    {
        $this->actingAs($this->vendorUser)
            ->post(route('vendor.products.store'), $this->productPayload(['name' => 'Fresh Tomatoes']))
            ->assertRedirect(route('vendor.products.index'));

        $this->assertDatabaseHas('marketplace_products', [
            'vendor_id' => $this->vendor->id,
            'name' => 'Fresh Tomatoes',
        ]);
    }

    public function test_product_is_assigned_to_the_authenticated_vendor_not_a_posted_id(): void
    {
        // A crafted vendor_id must be ignored — ownership comes from the session.
        $this->actingAs($this->vendorUser)
            ->post(route('vendor.products.store'), $this->productPayload([
                'name' => 'Spoof Attempt',
                'vendor_id' => $this->otherVendor->id,
            ]));

        $product = Product::where('name', 'Spoof Attempt')->first();

        $this->assertNotNull($product);
        $this->assertSame($this->vendor->id, $product->vendor_id);
    }

    public function test_discount_price_must_be_below_the_regular_price(): void
    {
        $this->actingAs($this->vendorUser)
            ->post(route('vendor.products.store'), $this->productPayload([
                'price' => 1000,
                'discount_price' => 1500,
            ]))
            ->assertSessionHasErrors('discount_price');
    }

    public function test_negative_price_is_rejected(): void
    {
        $this->actingAs($this->vendorUser)
            ->post(route('vendor.products.store'), $this->productPayload(['price' => -50]))
            ->assertSessionHasErrors('price');
    }

    // -------------------------------------------------------------- update

    public function test_vendor_can_update_their_own_product(): void
    {
        $product = $this->productFor($this->vendor);

        $this->actingAs($this->vendorUser)
            ->put(route('vendor.products.update', $product), $this->productPayload(['name' => 'Renamed', 'price' => 9900]))
            ->assertRedirect(route('vendor.products.index'));

        $this->assertSame('Renamed', $product->fresh()->name);
    }

    public function test_updating_does_not_reassign_vendor_ownership(): void
    {
        $product = $this->productFor($this->vendor);

        $this->actingAs($this->vendorUser)
            ->put(route('vendor.products.update', $product), $this->productPayload([
                'vendor_id' => $this->otherVendor->id,
            ]));

        $this->assertSame($this->vendor->id, $product->fresh()->vendor_id);
    }

    // --------------------------------------------------------------- stock

    public function test_vendor_can_adjust_stock_on_their_own_product(): void
    {
        $product = $this->productFor($this->vendor, ['stock_quantity' => 10]);

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.products.stock', $product), ['quantity' => 25, 'reason' => 'Restock'])
            ->assertRedirect();

        $this->assertSame(25, $product->fresh()->stock_quantity);

        // The movement log must record the change.
        $this->assertDatabaseHas('marketplace_inventory_movements', [
            'product_id' => $product->id,
            'vendor_id' => $this->vendor->id,
            'type' => 'stock_in',
        ]);
    }

    private function state(string $name = 'Lagos'): NigerianState
    {
        return NigerianState::create([
            'name' => $name . ' ' . uniqid(),
            'is_active' => true,
            'sort_order' => 0,
        ]);
    }

    // ------------------------------------------------------- state pricing

    public function test_vendor_can_save_state_prices_when_creating_a_product(): void
    {
        $state = $this->state();

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.products.store'), $this->productPayload([
                'name' => 'Rice With State Price',
                'prices' => [$state->id => 11500],
            ]))
            ->assertRedirect(route('vendor.products.index'));

        $product = Product::where('name', 'Rice With State Price')->firstOrFail();

        $this->assertDatabaseHas('product_state_prices', [
            'product_id' => $product->id,
            'state_id' => $state->id,
            'price' => 11500.00,
            'is_active' => true,
        ]);
    }

    public function test_vendor_can_update_state_prices_on_their_own_product(): void
    {
        $product = $this->productFor($this->vendor);
        $state = $this->state();

        $this->actingAs($this->vendorUser)
            ->put(route('vendor.products.update', $product), $this->productPayload([
                'prices' => [$state->id => 9900],
            ]))
            ->assertRedirect(route('vendor.products.index'));

        $this->assertSame(
            9900.0,
            (float) ProductStatePrice::where('product_id', $product->id)->where('state_id', $state->id)->value('price')
        );
    }

    public function test_a_blank_state_price_deactivates_an_existing_override(): void
    {
        $product = $this->productFor($this->vendor);
        $state = $this->state();

        ProductStatePrice::create([
            'product_id' => $product->id,
            'state_id' => $state->id,
            'price' => 8000,
            'is_active' => true,
        ]);

        // A blank input means "no override for this state" — the existing row must
        // be switched off rather than left charged at the stale price.
        $this->actingAs($this->vendorUser)
            ->put(route('vendor.products.update', $product), $this->productPayload([
                'prices' => [$state->id => ''],
            ]));

        $this->assertFalse((bool) ProductStatePrice::where('product_id', $product->id)->where('state_id', $state->id)->value('is_active'));
    }

    public function test_vendor_cannot_edit_another_vendors_state_prices(): void
    {
        $theirs = $this->productFor($this->otherVendor);
        $state = $this->state();

        $this->actingAs($this->vendorUser)
            ->put(route('vendor.products.update', $theirs), $this->productPayload([
                'prices' => [$state->id => 1],
            ]))
            ->assertNotFound();

        $this->assertDatabaseMissing('product_state_prices', [
            'product_id' => $theirs->id,
            'state_id' => $state->id,
        ]);
    }

    public function test_state_price_for_an_unknown_state_is_ignored(): void
    {
        $product = $this->productFor($this->vendor);

        $this->actingAs($this->vendorUser)
            ->put(route('vendor.products.update', $product), $this->productPayload([
                // 999999 is not an active state id, so nothing may be written.
                'prices' => [999999 => 100],
            ]))
            ->assertRedirect(route('vendor.products.index'));

        $this->assertSame(0, ProductStatePrice::where('product_id', $product->id)->count());
    }

    // ------------------------------------------------------------- filters

    public function test_low_stock_filter_returns_only_low_stock_products(): void
    {
        $low = $this->productFor($this->vendor, ['name' => 'Almost Gone', 'stock_quantity' => 2, 'low_stock_threshold' => 5]);
        $healthy = $this->productFor($this->vendor, ['name' => 'Plenty Left', 'stock_quantity' => 100, 'low_stock_threshold' => 5]);

        $this->actingAs($this->vendorUser)
            ->get(route('vendor.products.index', ['stock' => 'low']))
            ->assertOk()
            ->assertSee('Almost Gone')
            ->assertDontSee('Plenty Left');
    }

    public function test_product_search_is_scoped_to_the_vendor(): void
    {
        $this->productFor($this->vendor, ['name' => 'Findable Item']);
        $this->productFor($this->otherVendor, ['name' => 'Findable Item']);

        $response = $this->actingAs($this->vendorUser)
            ->get(route('vendor.products.index', ['search' => 'Findable']));

        $response->assertOk();
        $response->assertViewHas('products', function ($products) {
            return $products->total() === 1;
        });
    }
}

