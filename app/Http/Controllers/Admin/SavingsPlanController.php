<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavingsPlan;
use App\Models\SavingsPlanOption;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SavingsPlanController extends Controller
{
    public function index()
    {
        $plans = SavingsPlan::with('options')->latest()->get();

        return view('admin.savings-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.savings-plans.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'options' => 'required|array|min:1',
            'options.*.duration_weeks' => 'required|integer|min:1',
            'options.*.weekly_contribution' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $plan = SavingsPlan::create([
            'name' => $request->name,
            'target_amount' => $request->target_amount,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        foreach ($request->options as $option) {
            $plan->options()->create([
                'duration_weeks' => $option['duration_weeks'],
                'weekly_contribution' => $option['weekly_contribution'],
            ]);
        }

        return redirect()->route('admin.savings-plans.index')
            ->with('success', 'Savings plan created successfully');
    }

    public function edit(SavingsPlan $savingsPlan)
    {
        $savingsPlan->load('options');

        return view('admin.savings-plans.edit', compact('savingsPlan'));
    }

    public function update(Request $request, SavingsPlan $savingsPlan)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'options' => 'required|array|min:1',
            'options.*.id' => 'nullable|exists:savings_plan_options,id',
            'options.*.duration_weeks' => 'required|integer|min:1',
            'options.*.weekly_contribution' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $savingsPlan->update([
            'name' => $request->name,
            'target_amount' => $request->target_amount,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $submittedIds = [];
        foreach ($request->options as $option) {
            if (! empty($option['id'])) {
                $opt = $savingsPlan->options()->find($option['id']);
                if ($opt) {
                    $opt->update([
                        'duration_weeks' => $option['duration_weeks'],
                        'weekly_contribution' => $option['weekly_contribution'],
                    ]);
                    $submittedIds[] = $opt->id;
                }
            } else {
                $opt = $savingsPlan->options()->create([
                    'duration_weeks' => $option['duration_weeks'],
                    'weekly_contribution' => $option['weekly_contribution'],
                ]);
                $submittedIds[] = $opt->id;
            }
        }

        $savingsPlan->options()->whereNotIn('id', $submittedIds)->delete();

        return redirect()->route('admin.savings-plans.index')
            ->with('success', 'Savings plan updated successfully');
    }

    public function destroy(SavingsPlan $savingsPlan)
    {
        $accountCount = UserAccount::whereIn('savings_plan_option_id', $savingsPlan->options()->pluck('id'))->count();
        if ($accountCount > 0) {
            return redirect()->back()->with('error', 'Cannot delete plan with active user accounts');
        }

        $savingsPlan->delete();

        return redirect()->route('admin.savings-plans.index')
            ->with('success', 'Savings plan deleted successfully');
    }

    public function toggleOption(SavingsPlanOption $option)
    {
        $option->update(['is_active' => ! $option->is_active]);

        $status = $option->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Option {$status} successfully");
    }
}
