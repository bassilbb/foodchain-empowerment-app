<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\Order;
use App\Models\NigerianState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        $stats = [
            'orders' => Order::where('user_id', $user->id)->count(),
            'lifetime_spend' => (float) Order::where('user_id', $user->id)
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->sum('total'),
            'addresses' => $user->addresses()->count(),
            'wishlist' => $user->savedProducts()->count(),
        ];

        return view('account.profile.show', [
            'user' => $user,
            'stats' => $stats,
            'states' => NigerianState::active()->ordered()->pluck('name')->all(),
            'wishlistCount' => $stats['wishlist'],
            'unreadNotifications' => $user->notifications()->where('is_read', false)->count(),
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'first_name' => 'required|string|max:120',
            'last_name' => 'required|string|max:120',
            // Unique checks ignore the current user; a customer can never take
            // another account's email or phone number.
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'state' => ['nullable', 'string', Rule::in(NigerianState::active()->pluck('name')->all())],
            'city' => 'nullable|string|max:80',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Handle the avatar before the array update so the path is included.
        if ($request->hasFile('avatar')) {
            $previous = $user->avatar;
            $path = $request->file('avatar')->store('avatars', 'public');

            if ($path) {
                $data['avatar'] = $path;

                if ($previous && $previous !== $path && Storage::disk('public')->exists($previous)) {
                    Storage::disk('public')->delete($previous);
                }
            }
        } else {
            unset($data['avatar']);
        }

        $emailChanged = isset($data['email']) && $data['email'] !== $user->email;

        $user->update($data);

        $message = 'Profile updated successfully.';

        // Email is the login identifier; warn the customer it has changed.
        if ($emailChanged) {
            $message = 'Profile updated. Your sign-in email is now ' . $user->email . '.';
        }

        return back()->with('success', $message);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->input('password'))]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function destroyAvatar()
    {
        $user = auth()->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return back()->with('success', 'Profile photo removed.');
    }
}
