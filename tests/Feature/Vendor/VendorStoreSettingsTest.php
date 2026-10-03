<?php

namespace Tests\Feature\Vendor;

use App\Models\AdminAuditLog;
use App\Models\Marketplace\Vendor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Vendor store profile/settings: approved vendors maintain their public store
 * details from the vendor centre.
 */
class VendorStoreSettingsTest extends TestCase
{
    use RefreshDatabase;

    private Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);

        $this->vendor = Vendor::create([
            'user_id' => $user->id,
            'name' => 'Healthy Store',
            'slug' => 'healthy-store-'.uniqid(),
            'business_name' => 'Healthy Foods Ltd',
            'email' => 'hello@healthy.test',
            'phone' => '08000000000',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);
    }

    private function actingAsVendor()
    {
        return $this->actingAs($this->vendor->user);
    }

    public function test_approved_vendor_can_view_their_settings_page(): void
    {
        $this->actingAsVendor()
            ->get(route('vendor.settings.index'))
            ->assertOk()
            ->assertSee('Healthy Foods Ltd');
    }

    public function test_vendor_can_update_store_profile(): void
    {
        $this->actingAsVendor()
            ->put(route('vendor.settings.update'), [
                'name' => 'Renamed Store',
                'business_name' => 'Renamed Foods Ltd',
                'email' => 'new@healthy.test',
                'phone' => '08011122233',
                'description' => 'Updated blurb.',
                'address' => '44 New Road, Lagos',
                'city' => 'Ikeja',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->vendor->refresh();

        $this->assertSame('Renamed Store', $this->vendor->name);
        $this->assertSame('Renamed Foods Ltd', $this->vendor->business_name);
        $this->assertSame('new@healthy.test', $this->vendor->email);
        $this->assertSame('44 New Road, Lagos', $this->vendor->address);
        $this->assertSame('Ikeja', $this->vendor->city);
    }

    public function test_profile_update_is_audit_logged(): void
    {
        $this->actingAsVendor()->put(route('vendor.settings.update'), [
            'name' => 'Renamed Store',
            'business_name' => 'Renamed Foods Ltd',
            'email' => 'new@healthy.test',
            'phone' => '08011122233',
        ]);

        $this->assertDatabaseHas('admin_audit_logs', [
            'admin_user_id' => null,
            'subject_id' => $this->vendor->id,
            'action' => 'vendor.profile_updated',
        ]);
    }

    public function test_vendor_can_upload_a_store_logo(): void
    {
        Storage::fake('public');

        $this->actingAsVendor()
            ->put(route('vendor.settings.update'), [
                'name' => 'Healthy Store',
                'business_name' => 'Healthy Foods Ltd',
                'email' => 'hello@healthy.test',
                'phone' => '08000000000',
                'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->vendor->refresh();

        $this->assertNotNull($this->vendor->logo);
        $this->assertStringStartsWith('/vendor-logos/', $this->vendor->logo);
        Storage::disk('public')->assertExists(ltrim($this->vendor->logo, '/'));
    }
}