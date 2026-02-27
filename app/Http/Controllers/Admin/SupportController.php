<?php

// app/Http/Controllers/Admin/SupportController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with('user', 'assignedTo');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned admin
        if ($request->filled('assigned_to')) {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->latest()->paginate(20);
        $admins = AdminUser::active()->get();

        return view('admin.support.index', compact('tickets', 'admins'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'assignedTo', 'replies.sender']);

        return view('admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $admin = Auth::guard('admin')->user();

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'sender_type' => get_class($admin),
            'sender_id' => $admin->id,
            'message' => $request->message,
        ]);

        // Update ticket status if it was open
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Reply sent successfully.');
    }

    public function assign(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'admin_id' => 'required|exists:admin_users,id',
        ]);

        $admin = AdminUser::findOrFail($request->admin_id);
        $ticket->assign($admin);

        return back()->with('success', 'Ticket assigned successfully.');
    }

    public function resolve(SupportTicket $ticket)
    {
        $ticket->resolve();

        return back()->with('success', 'Ticket marked as resolved.');
    }

    public function close(SupportTicket $ticket)
    {
        $ticket->close();

        return back()->with('success', 'Ticket closed successfully.');
    }
}
