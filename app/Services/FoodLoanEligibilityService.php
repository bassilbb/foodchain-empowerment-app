<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Union;
use App\Models\User;

class FoodLoanEligibilityService
{
    protected array $rules = [];

    protected User $user;

    public function __construct()
    {
        $this->loadRules();
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function loadRules(): void
    {
        $this->rules = [
            'min_referral_count' => [
                'name' => 'Minimum Referral Count',
                'description' => 'User must have at least this many direct referrals',
                'value' => (int) Setting::get('loan_eligibility_min_referral_count', 10),
                'enabled' => (bool) Setting::get('loan_eligibility_require_min_referral_count', false),
                'type' => 'integer',
                'min' => 0,
            ],
            'union_membership' => [
                'name' => 'Union Membership',
                'description' => 'User must be a member of an active union',
                'enabled' => (bool) Setting::get('loan_eligibility_require_union_membership', true),
                'type' => 'boolean',
            ],
            'union_min_members' => [
                'name' => 'Union Minimum Members',
                'description' => 'Union must have at least this many members',
                'value' => (int) Setting::get('loan_eligibility_union_min_members', 10),
                'enabled' => (bool) Setting::get('loan_eligibility_require_union_min_members', true),
                'type' => 'integer',
                'min' => 2,
            ],
            'equity_percentage' => [
                'name' => 'Equity Percentage',
                'description' => 'Union must have contributed at least this percentage of loan amount',
                'value' => (int) Setting::get('loan_eligibility_equity_percentage', 30),
                'enabled' => (bool) Setting::get('loan_eligibility_require_equity_percentage', true),
                'type' => 'integer',
                'min' => 0,
                'max' => 100,
            ],
            'active_membership' => [
                'name' => 'Active Membership',
                'description' => 'User must have an active membership subscription',
                'enabled' => (bool) Setting::get('loan_eligibility_require_active_membership', true),
                'type' => 'boolean',
            ],
            'kyc_verification' => [
                'name' => 'KYC Verification',
                'description' => 'User must have completed KYC verification',
                'enabled' => (bool) Setting::get('loan_eligibility_require_kyc_verification', false),
                'type' => 'boolean',
            ],
            'no_active_loan' => [
                'name' => 'No Active Loan',
                'description' => 'User/Union must not have an existing active loan',
                'enabled' => true,
                'type' => 'boolean',
            ],
            'min_membership_duration' => [
                'name' => 'Minimum Membership Duration',
                'description' => 'User must be a member for at least this many days',
                'value' => (int) Setting::get('loan_eligibility_min_membership_duration', 0),
                'enabled' => (bool) Setting::get('loan_eligibility_require_min_membership_duration', false),
                'type' => 'integer',
                'min' => 0,
            ],
        ];
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getRule(string $key): ?array
    {
        return $this->rules[$key] ?? null;
    }

    public function updateRule(string $key, array $data): bool
    {
        if (! isset($this->rules[$key])) {
            return false;
        }

        $rule = $this->rules[$key];

        if (isset($data['enabled'])) {
            Setting::set("loan_eligibility_require_{$key}", (bool) $data['enabled']);
        }

        if (isset($data['value']) && $rule['type'] !== 'boolean') {
            Setting::set("loan_eligibility_{$key}", $data['value']);
        }

        $this->loadRules();

        return true;
    }

    protected function getSettingKey(string $ruleKey): string
    {
        $mapping = [
            'min_referral_count' => 'min_referrals',
            'union_membership' => 'require_union',
            'union_min_members' => 'union_min_members',
            'equity_percentage' => 'equity_percentage',
            'active_membership' => 'require_active_membership',
            'kyc_verification' => 'require_kyc',
            'min_membership_duration' => 'min_membership_days',
            'no_active_loan' => 'no_active_loan',
        ];

        return $mapping[$ruleKey] ?? $ruleKey;
    }

    public function getSettingKeyForRule(string $ruleKey): string
    {
        return $this->getSettingKey($ruleKey);
    }

    public function checkEligibility(?User $user = null): array
    {
        if ($user) {
            $this->user = $user;
        }

        $result = [
            'eligible' => true,
            'passed' => [],
            'failed' => [],
            'warnings' => [],
        ];

        foreach ($this->rules as $key => $rule) {
            if (! ($rule['enabled'] ?? false)) {
                $result['passed'][] = [
                    'rule' => $key,
                    'name' => $rule['name'],
                    'message' => "{$rule['name']}: Skipped (disabled)",
                ];

                continue;
            }

            $checkResult = $this->runRuleCheck($key);

            if ($checkResult['passed']) {
                $result['passed'][] = [
                    'rule' => $key,
                    'name' => $rule['name'],
                    'message' => $checkResult['message'],
                ];
            } else {
                $result['eligible'] = false;
                $result['failed'][] = [
                    'rule' => $key,
                    'name' => $rule['name'],
                    'message' => $checkResult['message'],
                    'requirement' => $checkResult['requirement'] ?? null,
                    'current' => $checkResult['current'] ?? null,
                ];
            }
        }

        return $result;
    }

    protected function runRuleCheck(string $ruleKey): array
    {
        return match ($ruleKey) {
            'min_referral_count' => $this->checkMinReferralCount(),
            'union_membership' => $this->checkUnionMembership(),
            'union_min_members' => $this->checkUnionMinMembers(),
            'equity_percentage' => $this->checkEquityPercentage(),
            'active_membership' => $this->checkActiveMembership(),
            'kyc_verification' => $this->checkKycVerification(),
            'no_active_loan' => $this->checkNoActiveLoan(),
            'min_membership_duration' => $this->checkMinMembershipDuration(),
            default => ['passed' => true, 'message' => 'Unknown rule'],
        };
    }

    protected function checkMinReferralCount(): array
    {
        $required = $this->rules['min_referral_count']['value'] ?? 10;
        $referralCount = $this->user->referralCount();

        return [
            'passed' => $referralCount >= $required,
            'message' => "Referral count: {$referralCount} (required: {$required})",
            'requirement' => $required,
            'current' => $referralCount,
        ];
    }

    protected function checkUnionMembership(): array
    {
        $union = $this->getUserUnion();

        return [
            'passed' => $union !== null && $union->status === 'active',
            'message' => $union
                ? "Union membership: {$union->name}"
                : 'No active union membership',
            'current' => $union?->name,
        ];
    }

    protected function checkUnionMinMembers(): array
    {
        $union = $this->getUserUnion();
        $required = $this->rules['union_min_members']['value'] ?? 10;

        if (! $union) {
            return [
                'passed' => false,
                'message' => 'No union to check',
                'requirement' => $required,
                'current' => 0,
            ];
        }

        $memberCount = $union->member_count ?? 0;

        return [
            'passed' => $memberCount >= $required,
            'message' => "Union members: {$memberCount} (required: {$required})",
            'requirement' => $required,
            'current' => $memberCount,
        ];
    }

    protected function checkEquityPercentage(): array
    {
        $union = $this->getUserUnion();
        $requiredPercentage = $this->rules['equity_percentage']['value'] ?? 30;

        if (! $union) {
            return [
                'passed' => false,
                'message' => 'No union to check equity',
                'requirement' => $requiredPercentage.'%',
                'current' => '0%',
            ];
        }

        $percentage = $union->getEquityContributedPercentage();

        return [
            'passed' => $percentage >= $requiredPercentage,
            'message' => 'Equity contribution: '.round($percentage, 2)."% (required: {$requiredPercentage}%)",
            'requirement' => $requiredPercentage.'%',
            'current' => round($percentage, 2).'%',
        ];
    }

    protected function checkActiveMembership(): array
    {
        $hasActive = $this->user->hasActiveMembership();

        return [
            'passed' => $hasActive,
            'message' => $hasActive
                ? 'Active membership confirmed'
                : 'No active membership',
        ];
    }

    protected function checkKycVerification(): array
    {
        $isVerified = $this->user->isKycVerified();

        return [
            'passed' => $isVerified,
            'message' => $isVerified
                ? 'KYC verification completed'
                : 'KYC verification required',
        ];
    }

    protected function checkNoActiveLoan(): array
    {
        $union = $this->getUserUnion();

        if (! $union) {
            return [
                'passed' => true,
                'message' => 'No union, no active loan possible',
            ];
        }

        $hasActiveLoan = $union->foodLoan !== null
            && in_array($union->foodLoan->status, ['pending', 'approved', 'disbursed', 'repaying']);

        return [
            'passed' => ! $hasActiveLoan,
            'message' => $hasActiveLoan
                ? 'Active loan exists: '.$union->foodLoan->reference
                : 'No active loan',
        ];
    }

    protected function checkMinMembershipDuration(): array
    {
        $requiredDays = $this->rules['min_membership_duration']['value'] ?? 0;
        $membershipDays = $this->user->getMembershipDurationDays();

        return [
            'passed' => $membershipDays >= $requiredDays,
            'message' => "Membership duration: {$membershipDays} days (required: {$requiredDays})",
            'requirement' => $requiredDays,
            'current' => $membershipDays,
        ];
    }

    protected function getUserUnion(): ?Union
    {
        $union = $this->user->unionMembership?->union;

        if ($union) {
            $union->load('foodLoan');
        }

        return $union;
    }

    public function getEligibilityStatus(?User $user = null): string
    {
        $result = $this->checkEligibility($user);

        if ($result['eligible']) {
            return 'eligible';
        }

        if (count($result['failed']) === 1 && $result['failed'][0]['rule'] === 'no_active_loan') {
            return 'has_active_loan';
        }

        return 'not_eligible';
    }

    public function getFailedRequirements(?User $user = null): array
    {
        $result = $this->checkEligibility($user);

        return $result['failed'];
    }

    public function canApplyForLoan(?User $user = null): bool
    {
        return $this->checkEligibility($user)['eligible'];
    }

    public static function getRequirementSummary(): array
    {
        $service = new self;
        $rules = $service->getRules();

        $summary = [];

        foreach ($rules as $key => $rule) {
            if (! ($rule['enabled'] ?? false)) {
                continue;
            }

            $value = $rule['value'] ?? null;

            $summary[$key] = [
                'name' => $rule['name'],
                'description' => $rule['description'],
                'value' => $value,
            ];
        }

        return $summary;
    }
}
