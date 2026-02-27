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
            ->with('admin')
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
            'category' => 'required|string|in:payment,membership,distribution,technical,other',
            'description' => 'required|string',
        ]);

        $user = auth()->user();

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $request->subject,
            'category' => $request->category,
            'description' => $request->description,
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

        $ticket->load(['replies.admin', 'admin']);

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

        $ticket->replies()->create([
            'user_id' => $user->id,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Reply added successfully');
    }
}
