<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LandingController extends Controller
{
    public function sponsor(Request $request)
    {
        $validated = $request->validate([
            'organization' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'nullable|string|max:5000',
        ]);

        $messageText = $validated['message'] ?? 'N/A';

        Mail::raw(
            "Sponsorship Inquiry\n\nOrganization: {$validated['organization']}\nName: {$validated['name']}\nEmail: {$validated['email']}\nMessage: {$messageText}",
            function ($message) {
                $message->to(config('mail.from.address'))
                    ->subject('New Sponsorship Inquiry');
            }
        );

        return back()->with('success', 'Thank you for your sponsorship interest! We will contact you shortly.');
    }

    public function supplier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string|max:5000',
        ]);

        Mail::raw(
            "Supplier Inquiry\n\nName: {$validated['name']}\nOrganization: {$validated['organization']}\nEmail: {$validated['email']}\nMessage: {$validated['message']}",
            function ($message) {
                $message->to(config('mail.from.address'))
                    ->subject('New Supplier Registration');
            }
        );

        return back()->with('success', 'Thank you for registering as a supplier! We will review your inquiry.');
    }

    public function rider(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'vehicle' => 'required|string|max:255',
            'address' => 'required|string|max:1000',
        ]);

        Mail::raw(
            "Rider Registration\n\nName: {$validated['name']}\nPhone: {$validated['phone']}\nEmail: {$validated['email']}\nVehicle: {$validated['vehicle']}\nAddress: {$validated['address']}",
            function ($message) {
                $message->to(config('mail.from.address'))
                    ->subject('New Dispatch Rider Registration');
            }
        );

        return back()->with('success', 'Registration submitted! We will contact you shortly.');
    }
}
