<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('user')->latest()->paginate(20);
        return Inertia::render('Admin/Payments', ['payments' => $payments]);
    }

    public function refund(Payment $payment)
    {
        $payment->update(['status' => 'refunded']);
        return back()->with('success', 'Payment marked as refunded.');
    }

    public function export()
    {
        $payments = Payment::with('user')->get();

        $csv = "ID,Transaction ID,User,Email,Plan,Amount,Currency,Status,Payment Method,Date\n";
        foreach ($payments as $p) {
            $csv .= implode(',', [
                $p->id,
                $p->transaction_id ?? '',
                $p->user?->name ?? '',
                $p->user?->email ?? '',
                $p->plan,
                $p->amount,
                $p->currency,
                $p->status,
                $p->payment_method ?? '',
                $p->created_at?->toDateString() ?? '',
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
