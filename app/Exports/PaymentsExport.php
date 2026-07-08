<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Payment::with('user')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'User Name',
            'User Email',
            'Type',
            'Amount',
            'Reference',
            'Status',
            'Created At',
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->id,
            $payment->user->full_name ?? 'N/A',
            $payment->user->email ?? 'N/A',
            ucfirst($payment->type),
            $payment->amount,
            $payment->reference,
            ucfirst($payment->status),
            $payment->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
