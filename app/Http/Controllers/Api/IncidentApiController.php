<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Department;

class IncidentApiController extends Controller
{
    public function getDepartments($branchId)
    {
        $branch = Branch::findOrFail($branchId);
        $departments = $branch->departments()->active()->get(['id', 'name', 'code']);

        return response()->json([
            'success' => true,
            'data' => $departments,
        ]);
    }

    public function getStaff($departmentId)
    {
        $department = Department::findOrFail($departmentId);
        $staff = $department->staff()->active()->with('adminUser')->get();

        $formattedStaff = $staff->map(function ($s) {
            return [
                'id' => $s->id,
                'staff_number' => $s->staff_number,
                'position' => $s->position,
                'phone' => $s->phone,
                'name' => $s->adminUser ? $s->adminUser->name : 'Unassigned',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedStaff,
        ]);
    }
}
