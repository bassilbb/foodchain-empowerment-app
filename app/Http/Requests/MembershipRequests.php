<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUnionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'min:3'],
            'leader_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Union name is required',
            'name.min' => 'Union name must be at least 3 characters',
            'leader_id.required' => 'Leader is required',
            'leader_id.exists' => 'Leader user does not exist',
        ];
    }
}

class JoinUnionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'union_id' => ['required', 'integer', 'exists:unions,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'equity_contributed' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'union_id.required' => 'Union ID is required',
            'union_id.exists' => 'Union does not exist',
            'user_id.required' => 'User ID is required',
            'user_id.exists' => 'User does not exist',
        ];
    }
}

class ApplyForFoodLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'union_id' => ['required', 'integer', 'exists:unions,id'],
            'amount' => ['required', 'numeric', 'min:10000', 'max:10000000'],
        ];
    }

    public function messages(): array
    {
        return [
            'union_id.required' => 'Union ID is required',
            'amount.required' => 'Loan amount is required',
            'amount.min' => 'Minimum loan amount is ₦10,000',
            'amount.max' => 'Maximum loan amount is ₦10,000,000',
        ];
    }
}

class ApproveFoodLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'loan_id' => ['required', 'integer', 'exists:food_loans,id'],
            'approved_amount' => ['required', 'numeric', 'min:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'loan_id.required' => 'Loan ID is required',
            'approved_amount.required' => 'Approved amount is required',
            'approved_amount.min' => 'Minimum approved amount is ₦10,000',
        ];
    }
}

class RecordLoanRepaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'loan_id' => ['required', 'integer', 'exists:food_loans,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'loan_id.required' => 'Loan ID is required',
            'user_id.required' => 'User ID is required',
            'amount.required' => 'Repayment amount is required',
            'amount.min' => 'Minimum repayment amount is ₦100',
        ];
    }
}

class ClaimQuarterlyBenefitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'eligibility_id' => ['required', 'integer', 'exists:quarterly_benefit_eligibilities,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'eligibility_id.required' => 'Benefit eligibility ID is required',
        ];
    }
}

class UpdateDeliveryFeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tier_id' => ['required', 'integer', 'exists:membership_tiers,id'],
            'base_fee' => ['required', 'numeric', 'min:0'],
            'subsidy_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_free' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'tier_id.required' => 'Tier ID is required',
            'base_fee.required' => 'Base fee is required',
            'subsidy_percentage.required' => 'Subsidy percentage is required',
            'subsidy_percentage.max' => 'Subsidy cannot exceed 100%',
        ];
    }
}

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'type' => ['required', Rule::in(['membership', 'starter_pack', 'instant_food_pack', 'delivery', 'union_equity', 'loan_repayment'])],
            'amount' => ['required', 'numeric', 'min:100'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required',
            'type.required' => 'Payment type is required',
            'type.in' => 'Invalid payment type',
            'amount.required' => 'Amount is required',
            'amount.min' => 'Minimum amount is ₦100',
        ];
    }
}
