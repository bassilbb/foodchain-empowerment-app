<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Incident;
use App\Models\IncidentEscalation;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Incident::with('branch', 'department', 'assignedTo', 'user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        $incidents = $query->latest()->paginate(15);
        $branches = Branch::active()->get();

        return view('admin.incidents.index', compact('incidents', 'branches'));
    }

    public function create()
    {
        $branches = Branch::active()->get();
        $categories = ['technical', 'operational', 'customer_service', 'logistics', 'safety', 'other'];
        $priorities = ['low', 'medium', 'high', 'critical'];

        return view('admin.incidents.create', compact('branches', 'categories', 'priorities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'assigned_to' => 'nullable|exists:staff,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'category' => 'required|in:technical,operational,customer_service,logistics,safety,other',
            'location' => 'nullable|string|max:255',
            'reported_by' => 'nullable|exists:users,id',
        ]);

        $incident = Incident::create($validated);

        return redirect()->route('admin.incidents.show', $incident)
            ->with('success', 'Incident created successfully.');
    }

    public function show(Incident $incident)
    {
        $incident->load('branch', 'department', 'assignedTo.adminUser', 'user', 'reporter', 'escalations.escalatedTo', 'comments.user', 'comments.adminUser');

        $staff = Staff::where('department_id', $incident->department_id)->active()->with('adminUser')->get();

        return view('admin.incidents.show', compact('incident', 'staff'));
    }

    public function edit(Incident $incident)
    {
        $branches = Branch::active()->get();
        $departments = Department::where('branch_id', $incident->branch_id)->active()->get();
        $staff = Staff::where('department_id', $incident->department_id)->active()->get();
        $categories = ['technical', 'operational', 'customer_service', 'logistics', 'safety', 'other'];
        $priorities = ['low', 'medium', 'high', 'critical'];
        $users = User::get();

        return view('admin.incidents.edit', compact('incident', 'branches', 'departments', 'staff', 'categories', 'priorities', 'users'));
    }

    public function update(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'assigned_to' => 'nullable|exists:staff,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:open,in_progress,escalated,pending,resolved,closed',
            'category' => 'required|in:technical,operational,customer_service,logistics,safety,other',
            'location' => 'nullable|string|max:255',
            'reported_by' => 'nullable|exists:users,id',
        ]);

        $incident->update($validated);

        return redirect()->route('admin.incidents.show', $incident)
            ->with('success', 'Incident updated successfully.');
    }

    public function destroy(Incident $incident)
    {
        $incident->delete();

        return redirect()->route('admin.incidents.index')
            ->with('success', 'Incident deleted successfully.');
    }

    public function assign(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
        ]);

        $staff = Staff::findOrFail($validated['staff_id']);

        $incident->update([
            'assigned_to' => $staff->admin_user_id,
            'status' => 'in_progress',
        ]);

        return back()->with('success', 'Incident assigned successfully.');
    }

    public function escalate(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'reason' => 'required|string',
        ]);

        $staff = Staff::findOrFail($validated['staff_id']);
        $adminUser = Auth::guard('admin')->user();

        $incident->escalate($staff, $validated['reason'], $adminUser);

        return back()->with('success', 'Incident escalated successfully.');
    }

    public function resolve(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $incident->resolve($validated['resolution_notes']);

        return back()->with('success', 'Incident marked as resolved.');
    }

    public function close(Incident $incident)
    {
        $incident->close();

        return back()->with('success', 'Incident closed.');
    }

    public function reopen(Incident $incident)
    {
        $incident->reopen();

        return back()->with('success', 'Incident reopened.');
    }

    public function addComment(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'is_internal' => 'boolean',
        ]);

        $adminUser = Auth::guard('admin')->user();

        $incident->comments()->create([
            'comment' => $validated['comment'],
            'admin_user_id' => $adminUser->id,
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        return back()->with('success', 'Comment added successfully.');
    }

    public function acknowledgeEscalation(IncidentEscalation $escalation)
    {
        $escalation->acknowledge();

        return back()->with('success', 'Escalation acknowledged.');
    }
}
