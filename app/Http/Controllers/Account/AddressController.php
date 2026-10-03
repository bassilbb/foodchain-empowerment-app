<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\NigerianState;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    /**
     * Nigerian states offered in the address form.
     *
     * Sourced from the same `nigerian_states` table that drives state-based
     * pricing, so an address can never name a state the pricing engine does not
     * recognise (Section 26).
     */
    private function states(): array
    {
        return NigerianState::active()->ordered()->pluck('name')->all();
    }

    public function index()
    {
        $user = auth()->user();

        $addresses = UserAddress::forUser($user->id)
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return view('account.addresses.index', [
            'addresses' => $addresses,
            'wishlistCount' => $user->savedProducts()->count(),
            'unreadNotifications' => $user->notifications()->where('is_read', false)->count(),
        ]);
    }

    public function create()
    {
        $user = auth()->user();

        return view('account.addresses.form', [
            'address' => null,
            'states' => $this->states(),
            'defaults' => [
                'full_name' => $user->full_name ?? $user->name,
                'phone' => $user->phone,
                'state' => $user->state,
                'city' => $user->city,
            ],
            'isFirstAddress' => ! UserAddress::forUser($user->id)->exists(),
            'wishlistCount' => $user->savedProducts()->count(),
            'unreadNotifications' => $user->notifications()->where('is_read', false)->count(),
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $this->validated($request);

        // The very first address a customer saves becomes their default, so
        // checkout always has something selected.
        $isFirst = ! UserAddress::forUser($user->id)->exists();

        $address = DB::transaction(function () use ($user, $data, $isFirst) {
            $address = UserAddress::create($data + [
                'user_id' => $user->id,
                'is_default' => $isFirst || $request->boolean('is_default'),
            ]);

            if ($address->is_default) {
                $address->makeDefault();
            }

            return $address;
        });

        return redirect()
            ->route('account.addresses.index')
            ->with('success', 'Delivery address saved.');
    }

    public function edit(UserAddress $address)
    {
        $this->authorizeAddress($address);
        $user = auth()->user();

        return view('account.addresses.form', [
            'address' => $address,
            'states' => $this->states(),
            'defaults' => [],
            'isFirstAddress' => false,
            'wishlistCount' => $user->savedProducts()->count(),
            'unreadNotifications' => $user->notifications()->where('is_read', false)->count(),
        ]);
    }

    public function update(Request $request, UserAddress $address)
    {
        $this->authorizeAddress($address);

        $data = $this->validated($request);

        DB::transaction(function () use ($address, $data, $request) {
            $address->update($data + ['is_default' => $request->boolean('is_default')]);

            if ($address->is_default) {
                $address->makeDefault();
            } elseif (! UserAddress::forUser($address->user_id)->where('is_default', true)->exists()) {
                // Never leave the customer without a default.
                $address->update(['is_default' => true]);
            }
        });

        return redirect()
            ->route('account.addresses.index')
            ->with('success', 'Delivery address updated.');
    }

    public function destroy(UserAddress $address)
    {
        $this->authorizeAddress($address);

        DB::transaction(function () use ($address) {
            $wasDefault = $address->is_default;
            $userId = $address->user_id;
            $address->delete();

            // Promote another address so checkout keeps a preselected option.
            if ($wasDefault) {
                UserAddress::forUser($userId)->latest()->first()?->makeDefault();
            }
        });

        return redirect()
            ->route('account.addresses.index')
            ->with('success', 'Delivery address removed.');
    }

    public function setDefault(UserAddress $address)
    {
        $this->authorizeAddress($address);

        $address->makeDefault();

        return back()->with('success', 'Default delivery address updated.');
    }

    /**
     * Ownership guard for every address action.
     *
     * Returns 404 rather than 403 so an id probe cannot confirm that another
     * customer's address exists.
     */
    private function authorizeAddress(UserAddress $address): void
    {
        abort_unless($address->user_id === auth()->id(), 404);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => 'nullable|string|max:40',
            'full_name' => 'required|string|max:120',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'state' => ['required', 'string', Rule::in($this->states())],
            'lga' => 'nullable|string|max:80',
            'city' => 'nullable|string|max:80',
            'landmark' => 'nullable|string|max:160',
            'delivery_instructions' => 'nullable|string|max:1000',
            'is_default' => 'nullable|boolean',
        ]);
    }
}
