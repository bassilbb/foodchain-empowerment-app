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

    /**
     * Public "Send us a Message" form on the landing page.
     */
    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        Mail::raw(
            "Contact Message\n\nName: {$validated['name']}\nEmail: {$validated['email']}\nSubject: {$validated['subject']}\n\n{$validated['message']}",
            function ($message) use ($validated) {
                $message->to(config('mail.from.address'))
                    ->replyTo($validated['email'], $validated['name'])
                    ->subject('New website message: '.$validated['subject']);
            }
        );

        return back()
            ->with('success', 'Thank you for your message! We have received it and will get back to you soon.')
            ->with('status_section', 'contact');
    }

    /**
     * Landing page newsletter subscription form.
     */
    public function newsletter(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        Mail::raw(
            "Newsletter subscription\n\nEmail: {$validated['email']}",
            function ($message) use ($validated) {
                $message->to(config('mail.from.address'))
                    ->replyTo($validated['email'])
                    ->subject('New newsletter subscriber');
            }
        );

        return back()
            ->with('success', 'You are subscribed! Watch your inbox for marketplace updates.')
            ->with('status_section', 'newsletter');
    }
}
