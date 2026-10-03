<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodAssistancePlan;
use Illuminate\Http\Request;

class FoodAssistanceController extends Controller
{
    public function index(Request $request)
    {
        $query = FoodAssistancePlan::with('user', 'tier');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $plans = $query->latest()->paginate(20);

        $stats = [
            'pending' => FoodAssistancePlan::where('status', 'pending')->count(),
            'processing_fee_paid' => FoodAssistancePlan::where('status', 'processing_fee_paid')->count(),
            'first_installment_paid' => FoodAssistancePlan::where('status', 'first_installment_paid')->count(),
            'active' => FoodAssistancePlan::where('status', 'active')->count(),
            'completed' => FoodAssistancePlan::where('status', 'completed')->count(),
            'total_disbursed' => FoodAssistancePlan::whereIn('status', ['active', 'completed'])->sum('total_amount'),
            'total_processing_fees' => FoodAssistancePlan::sum('processing_fee'),
        ];

        return view('admin.food-assistance.index', compact('plans', 'stats'));
    }

    public function show(FoodAssistancePlan $assistancePlan)
    {
        $assistancePlan->load('user', 'tier', 'installments');
        $installments = $assistancePlan->installments()->orderBy('installment_number')->get();
        $pendingInstallments = $assistancePlan->pendingInstallments;

        return view('admin.food-assistance.show', compact('assistancePlan', 'installments', 'pendingInstallments'));
    }

    public function approve(FoodAssistancePlan $assistancePlan)
    {
        if ($assistancePlan->status !== 'pending') {
            return back()->with('error', 'Only pending plans can be approved.');
        }

        $assistancePlan->update([
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Plan approved successfully.');
    }

    public function markCollected(FoodAssistancePlan $assistancePlan)
    {
        if ($assistancePlan->status !== 'first_installment_paid') {
            return back()->with('error', 'First installment must be paid before marking as collected.');
        }

        $assistancePlan->markCollected();

        return back()->with('success', 'Food marked as collected. Weekly repayment schedule is now active.');
    }

    public function markCompleted(FoodAssistancePlan $assistancePlan)
    {
        if ($assistancePlan->status === 'completed') {
            return back()->with('error', 'Plan is already completed.');
        }

        $assistancePlan->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Plan marked as completed.');
    }

    public function cancel(Request $request, FoodAssistancePlan $assistancePlan)
    {
        if (in_array($assistancePlan->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Plan cannot be cancelled.');
        }

        $assistancePlan->update([
            'status' => 'cancelled',
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return back()->with('success', 'Plan cancelled successfully.');
    }
}
