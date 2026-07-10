<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return ($this->query ? $this->query : User::with('currentTier'))->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Membership Tier',
            'Referral Code',
            'Direct Referrals',
            'Total Community',
            'Status',
            'Membership Expires',
            'Created At',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->first_name,
            $user->last_name,
            $user->email,
            $user->phone ?? 'N/A',
            $user->currentTier->name ?? 'None',
            $user->referral_code,
            $user->direct_referrals_count,
            $user->total_community_count ?? 0,
            $user->is_active ? 'Active' : 'Inactive',
            $user->membership_expires_at?->format('Y-m-d') ?? 'Never',
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
