<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\NigerianState;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Resolve the vendor profile attached to the signed-in user.
     */
    private function vendor()
    {
        $vendor = auth()->user()->marketplaceVendor;

        abort_unless($vendor, 403, 'No vendor account is linked to your profile.');

        return $vendor;
    }

    public function show()
    {
        $vendor = $this->vendor();

        return view('vendor.settings', [
            'vendor' => $vendor,
            'states' => NigerianState::active()->ordered()->get(['id', 'name']),
        ]);
    }

    public function update(Request $request)
    {
        $vendor = $this->vendor();

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'business_name' => 'required|string|max:120',
            'email' => 'required|email|max:190',
            'phone' => 'required|string|max:30',
            'description' => 'nullable|string|max:2000',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:120',
            'state_id' => 'nullable|integer|exists:nigerian_states,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $state = ! empty($data['state_id']) ? NigerianState::find($data['state_id']) : null;

        $vendor->update([
            'name' => $data['name'],
            'business_name' => $data['business_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'description' => $data['description'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $state?->name,
        ]);

        if ($request->hasFile('logo')) {
            if ($vendor->logo && str_starts_with($vendor->logo, '/vendor-logos/')) {
                Storage::disk('public')->delete(str_replace('/vendor-logos/', 'vendor-logos/', $vendor->logo));
            }

            $path = $request->file('logo')->store('vendor-logos', 'public');

            if ($path) {
                $vendor->update(['logo' => '/'.ltrim($path, '/')]);
            }
        }

        AuditService::log(
            'vendor.profile_updated',
            $vendor,
            [],
            'Vendor "'.($vendor->business_name ?? $vendor->name).'" updated their store profile.',
        );

        return back()->with('success', 'Store settings saved.');
    }
}