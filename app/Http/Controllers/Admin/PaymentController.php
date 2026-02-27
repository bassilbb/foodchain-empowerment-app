<?php

// app/Http/Controllers/Admin/PaymentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

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
        if ($payment->isSuccessful()) {
            return back()->with('info', 'Payment is already verified.');
        }

        $paymentService = app(PaymentService::class);
        $result = $paymentService->verifyPayment($payment->reference);

        if ($result['status'] === 'success') {
            return back()->with('success', 'Payment verified successfully.');
        }

        return back()->with('error', $result['message'] ?? 'Payment verification failed.');
    }
}
