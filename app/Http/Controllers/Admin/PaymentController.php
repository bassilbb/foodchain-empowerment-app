<?php

// app/Http/Controllers/Admin/PaymentController.php

namespace App\Http\Controllers\Admin;

use App\Exports\PaymentsExport;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('user');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search by reference or user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('email', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->latest()->paginate(20);

        // Summary statistics
        $totalAmount = Payment::successful()->sum('amount');
        $successfulCount = Payment::successful()->count();
        $pendingCount = Payment::pending()->count();
        $failedCount = Payment::failed()->count();

        return view('admin.payments.index', compact(
            'payments',
            'totalAmount',
            'successfulCount',
            'pendingCount',
            'failedCount'
        ));
    }

    public function show(Payment $payment)
    {
        $payment->load('user', 'starterPack', 'renewal');

        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Payment $payment)
    {
        if ($payment->status === 'successful') {
            return back()->with('info', 'Payment is already verified.');
        }

        // Manual verification - directly approve the payment
        $payment->update([
            'status' => 'successful',
            'paid_at' => now(),
        ]);

        // Activate user if this is a membership payment
        if ($payment->user) {
            $payment->user->update([
                'is_active' => true,
                'membership_started_at' => now(),
                'membership_expires_at' => now()->addYear(),
            ]);
        }

        return back()->with('success', 'Payment verified successfully and user has been activated.');
    }

    public function export(Request $request)
    {
        $query = Payment::with('user');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        return Excel::download(new PaymentsExport($query), 'payments-'.date('Y-m-d').'.xlsx');
    }
}
