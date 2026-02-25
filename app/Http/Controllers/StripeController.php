<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        $plan = $request->input('plan', 'pro');
        $billing = $request->input('billing', 'monthly');
        $couponCode = $request->input('coupon');

        $price = $billing === 'monthly' ? 900 : 7900; // Cents
        $discountValue = 0;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid()) {
                $discountValue = ($price * $coupon->discount_percent) / 100;
                $price = $price - $discountValue;
            }
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => "Civio " . ucfirst($plan) . " Plan (" . ucfirst($billing) . ")",
                        ],
                        'unit_amount' => (int)$price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('pricing'),
                'customer_email' => Auth::user()->email,
                'metadata' => [
                    'user_id' => Auth::id(),
                    'plan' => $plan,
                    'coupon_code' => $couponCode,
                ],
            ]);

            return response()->json(['id' => $session->id]);
        } catch (\Exception $e) {
            Log::error('Stripe Checkout Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function success(Request $request)
    {
        // Ideally, we wait for the webhook, but for a smoother UX we can verify the session here
        $session_id = $request->query('session_id');
        if ($session_id) {
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = Session::retrieve($session_id);
            
            if ($session->payment_status === 'paid') {
                $user = \App\Models\User::find(Auth::id());
                $user->update([
                    'plan' => 'pro',
                    'subscription_ends_at' => now()->addMonth(), // Simple logic for now
                ]);

                // Increment coupon use if any
                $couponCode = $session->metadata->coupon_code;
                if ($couponCode) {
                    $coupon = Coupon::where('code', $couponCode)->first();
                    if ($coupon) {
                        $coupon->increment('used_count');
                    }
                }

                return redirect()->route('dashboard')->with('success', 'Welcome to Pro!');
            }
        }

        return redirect()->route('pricing')->with('error', 'Payment failed or was cancelled.');
    }

    public function validateCoupon(Request $request)
    {
        $code = $request->input('code');
        $coupon = Coupon::where('code', $code)->first();

        if ($coupon && $coupon->isValid()) {
            return response()->json([
                'valid' => true,
                'discount_percent' => $coupon->discount_percent
            ]);
        }

        return response()->json(['valid' => false]);
    }
}
