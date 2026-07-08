<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::with('department.branch')->latest()->paginate(10);

        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        $departments = Department::active()->with('branch')->get();

        return view('admin.staff.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'admin_user_id' => 'nullable|exists:admin_users,id',
            'staff_number' => 'required|string|max:50|unique:staff,staff_number',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        Staff::create($validated);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member created successfully.');
    }

    public function show(Staff $staff)
    {
        $staff->load('department.branch', 'adminUser');

        return view('admin.staff.show', compact('staff'));
    }

    public function edit(Staff $staff)
    {
        $departments = Department::active()->with('branch')->get();

        return view('admin.staff.edit', compact('staff', 'departments'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'admin_user_id' => 'nullable|exists:admin_users,id',
            'staff_number' => 'required|string|max:50|unique:staff,staff_number,'.$staff->id,
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $staff->update($validated);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }

    public function getByDepartment(Department $department)
    {
        $staff = $department->staff()->active()->with('adminUser')->get();

        return response()->json($staff);
    }
}
