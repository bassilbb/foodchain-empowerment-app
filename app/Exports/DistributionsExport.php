<?php

namespace App\Exports;

use App\Models\FoodDistribution;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DistributionsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return FoodDistribution::with(['user', 'tier', 'pickupLocation'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Distribution #',
            'User Name',
            'User Email',
            'Tier',
            'Type',
            'Package Value',
            'Status',
            'Delivery Method',
            'Pickup Location',
            'Created At',
        ];
    }

    public function map($distribution): array
    {
        return [
            $distribution->id,
            $distribution->distribution_number,
            $distribution->user->full_name ?? 'N/A',
            $distribution->user->email ?? 'N/A',
            $distribution->tier->name ?? 'N/A',
            ucfirst($distribution->distribution_type),
            $distribution->package_value,
            ucfirst($distribution->status),
            ucfirst($distribution->delivery_method ?? 'delivery'),
            $distribution->pickupLocation->name ?? 'N/A',
            $distribution->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
