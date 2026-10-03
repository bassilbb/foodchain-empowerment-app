<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $tickets = $user->supportTickets()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.support.index', compact('tickets'));
    }

    public function create()
    {
        return view('user.support.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string|in:billing,referrals,account,technical,other',
            'message' => 'required|string',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = auth()->user();

        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('support-attachments', 'public')
            : null;

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $request->subject,
            'category' => $request->category,
            'message' => $request->message,
            'attachment_path' => $attachmentPath,
            'status' => 'open',
        ]);

        return redirect()->route('support.show', $ticket->id)
            ->with('success', 'Support ticket created successfully');
    }

    public function show(SupportTicket $ticket)
    {
        $user = auth()->user();

        if ($ticket->user_id !== $user->id) {
            abort(403);
        }

        $ticket->load(['replies.sender']);

        return view('user.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = auth()->user();

        if ($ticket->user_id !== $user->id) {
            abort(403);
        }

        if ($ticket->isClosed()) {
            return back()->with('error', 'This ticket is closed and cannot receive replies.');
        }

        $ticket->replies()->create([
            'sender_type' => get_class($user),
            'sender_id' => $user->id,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Reply added successfully');
    }

    public function close(SupportTicket $ticket)
    {
        $user = auth()->user();

        if ($ticket->user_id !== $user->id) {
            abort(403);
        }

        $ticket->close();

        return redirect()->route('support.index')
            ->with('success', 'Ticket closed successfully');
    }
}
