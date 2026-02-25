<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = User::where('plan', 'pro')
            ->with(['resumes'])
            ->latest()
            ->paginate(20);
        return Inertia::render('Admin/Subscriptions', ['subscriptions' => $subscriptions]);
    }

    public function extend(User $user)
    {
        $user->update([
            'subscription_ends_at' => ($user->subscription_ends_at && $user->subscription_ends_at->isFuture())
                ? $user->subscription_ends_at->addDays(30)
                : now()->addDays(30),
        ]);
        return back()->with('success', 'Subscription extended by 30 days.');
    }

    public function cancel(User $user)
    {
        $user->update([
            'plan' => 'free',
            'subscription_ends_at' => null,
        ]);
        return back()->with('success', 'Subscription cancelled.');
    }

    // Coupons
    public function coupons()
    {
        $coupons = Coupon::latest()->paginate(20);
        return Inertia::render('Admin/Coupons', ['coupons' => $coupons]);
    }

    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'code'             => 'required|string|unique:coupons,code',
            'discount_percent' => 'required|integer|min:1|max:100',
            'max_uses'         => 'required|integer|min:1',
            'expires_at'       => 'nullable|date',
        ]);

        Coupon::create($validated);

        return back()->with('success', 'Coupon created successfully.');
    }

    public function destroyCoupon(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }
}
