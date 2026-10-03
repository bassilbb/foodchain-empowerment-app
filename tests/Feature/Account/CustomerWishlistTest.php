<?php

namespace Tests\Feature\Account;

use App\Models\Marketplace\Category;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\SavedProduct;
use App\Models\Marketplace\Vendor;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerWishlistTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);
    }

    private function product(array $overrides = []): Product
    {
        $vendor = Vendor::create([
            'user_id' => $this->user->id,
            'name' => 'Vendor ' . uniqid(),
            'slug' => 'vendor-' . uniqid(),
            'business_name' => 'Test Vendor Ltd',
            'status' => 'approved',
        ]);

        $category = Category::create([
            'name' => 'Category ' . uniqid(),
            'slug' => 'category-' . uniqid(),
            'is_active' => true,
        ]);

        return Product::create(array_merge([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Rice 5kg',
            'slug' => 'rice-5kg-' . uniqid(),
            'sku' => Product::generateSku(),
            'price' => 12000,
            'stock_quantity' => 25,
            'is_active' => true,
        ], $overrides));
    }

    public function test_guest_cannot_view_the_wishlist(): void
    {
        $this->get(route('account.wishlist.index'))->assertRedirect(route('login'));
    }

    public function test_customer_can_view_an_empty_wishlist(): void
    {
        $this->actingAs($this->user)
            ->get(route('account.wishlist.index'))
            ->assertOk()
            ->assertSee('No wishlist items yet')
            ->assertSee('Save products you love and they will appear here.');
    }

    public function test_customer_can_save_a_product_to_the_wishlist(): void
    {
        $product = $this->product();

        $this->actingAs($this->user)
            ->post(route('account.wishlist.store'), ['product_id' => $product->id])
            ->assertRedirect();

        $this->assertDatabaseHas('marketplace_saved_products', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_saving_the_same_product_twice_does_not_duplicate_it(): void
    {
        $product = $this->product();

        $this->actingAs($this->user)->post(route('account.wishlist.store'), ['product_id' => $product->id]);
        $this->actingAs($this->user)->post(route('account.wishlist.store'), ['product_id' => $product->id]);

        $this->assertSame(1, SavedProduct::where('user_id', $this->user->id)->count());
    }

    public function test_wishlist_shows_saved_products(): void
    {
        $product = $this->product();
        SavedProduct::create(['user_id' => $this->user->id, 'product_id' => $product->id]);

        $this->actingAs($this->user)
            ->get(route('account.wishlist.index'))
            ->assertOk()
            ->assertSee('Rice 5kg');
    }

    public function test_customer_wishlist_only_shows_their_own_items(): void
    {
        $other = User::factory()->create(['is_active' => true]);

        $mine = $this->product(['name' => 'My Product']);
        $theirs = $this->product(['name' => 'Their Product']);

        SavedProduct::create(['user_id' => $this->user->id, 'product_id' => $mine->id]);
        SavedProduct::create(['user_id' => $other->id, 'product_id' => $theirs->id]);

        $this->actingAs($this->user)
            ->get(route('account.wishlist.index'))
            ->assertOk()
            ->assertSee('My Product')
            ->assertDontSee('Their Product');
    }

    public function test_customer_can_remove_a_wishlist_item(): void
    {
        $product = $this->product();
        $saved = SavedProduct::create(['user_id' => $this->user->id, 'product_id' => $product->id]);

        $this->actingAs($this->user)
            ->delete(route('account.wishlist.destroy', $saved))
            ->assertRedirect();

        $this->assertDatabaseMissing('marketplace_saved_products', ['id' => $saved->id]);
    }

    public function test_customer_cannot_delete_another_customers_wishlist_item(): void
    {
        $other = User::factory()->create(['is_active' => true]);
        $product = $this->product();
        $theirs = SavedProduct::create(['user_id' => $other->id, 'product_id' => $product->id]);

        $this->actingAs($this->user)
            ->delete(route('account.wishlist.destroy', $theirs))
            ->assertNotFound();

        $this->assertDatabaseHas('marketplace_saved_products', ['id' => $theirs->id]);
    }

    public function test_moving_an_in_stock_wishlist_item_to_cart_succeeds(): void
    {
        $product = $this->product(['stock_quantity' => 10]);
        $saved = SavedProduct::create(['user_id' => $this->user->id, 'product_id' => $product->id]);

        $this->actingAs($this->user)
            ->post(route('account.wishlist.move-to-cart', $saved))
            ->assertRedirect(route('marketplace.cart.index'));

        // Item leaves the wishlist once it is in the basket.
        $this->assertDatabaseMissing('marketplace_saved_products', ['id' => $saved->id]);
    }

    public function test_out_of_stock_items_cannot_be_moved_to_cart(): void
    {
        $product = $this->product(['stock_quantity' => 0]);
        $saved = SavedProduct::create(['user_id' => $this->user->id, 'product_id' => $product->id]);

        $this->actingAs($this->user)
            ->post(route('account.wishlist.move-to-cart', $saved))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('marketplace_saved_products', ['id' => $saved->id]);
    }

    public function test_wishlist_count_is_shown_in_the_sidebar(): void
    {
        $product = $this->product();
        SavedProduct::create(['user_id' => $this->user->id, 'product_id' => $product->id]);

        $this->actingAs($this->user)
            ->get(route('account.wishlist.index'))
            ->assertOk()
            ->assertViewHas('wishlistCount', 1);
    }
}
