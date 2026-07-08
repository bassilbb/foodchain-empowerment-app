<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentReportController extends Controller
{
    public function index()
    {
        $incidents = Incident::where('reported_by', Auth::id())
            ->with(['branch', 'department', 'assignedTo', 'comments'])
            ->latest()
            ->paginate(10);

        return view('user.incidents.index', compact('incidents'));
    }

    public function create()
    {
        $branches = Branch::active()->get();
        $categories = Incident::getCategories();
        $priorities = Incident::getPriorities();

        return view('user.incidents.create', compact('branches', 'categories', 'priorities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'nullable|exists:staff,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:'.implode(',', Incident::getCategories()),
            'priority' => 'required|in:'.implode(',', Incident::getPriorities()),
            'location' => 'nullable|string|max:255',
        ]);

        $incident = Incident::create([
            'branch_id' => $validated['branch_id'],
            'department_id' => $validated['department_id'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'location' => $validated['location'] ?? null,
            'reported_by' => Auth::id(),
            'status' => 'open',
        ]);

        return redirect()
            ->route('user.incidents.show', $incident)
            ->with('success', 'Incident reported successfully. We will look into it shortly.');
    }

    public function show(Incident $incident)
    {
        if ($incident->reported_by !== Auth::id()) {
            abort(403);
        }

        $incident->load(['branch', 'department', 'assignedTo', 'comments', 'escalations']);

        return view('user.incidents.show', compact('incident'));
    }

    public function addComment(Request $request, Incident $incident)
    {
        if ($incident->reported_by !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'comment' => 'required|string',
        ]);

        $incident->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'is_internal' => false,
        ]);

        return back()->with('success', 'Comment added successfully.');
    }
}
