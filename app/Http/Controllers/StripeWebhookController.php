<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe webhook payload invalid: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
            'checkout.session.expired'   => Log::info('Stripe checkout session expired: ' . $event->data->object->id),
            'payment_intent.payment_failed' => Log::warning('Stripe payment failed for: ' . ($event->data->object->metadata->user_id ?? 'unknown')),
            default => Log::info('Unhandled Stripe event: ' . $event->type),
        };

        return response()->json(['status' => 'ok']);
    }

    private function handleCheckoutCompleted(object $session): void
    {
        $userId = $session->metadata->user_id ?? null;
        if (!$userId) {
            Log::warning('Stripe webhook: no user_id in metadata');
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            Log::warning('Stripe webhook: user not found: ' . $userId);
            return;
        }

        // Upgrade user plan
        $user->update([
            'plan'                 => $session->metadata->plan ?? 'pro',
            'subscription_ends_at' => now()->addMonth(),
        ]);

        // Record payment
        Payment::create([
            'user_id'    => $user->id,
            'amount'     => $session->amount_total / 100,
            'currency'   => strtoupper($session->currency),
            'status'     => 'paid',
            'stripe_id'  => $session->payment_intent,
            'description'=> 'Pro Plan - ' . ucfirst($session->metadata->plan ?? 'pro'),
        ]);

        // Coupon usage
        $couponCode = $session->metadata->coupon_code ?? null;
        if ($couponCode) {
            \App\Models\Coupon::where('code', $couponCode)->increment('used_count');
        }

        Log::info("Stripe: user {$user->id} upgraded to plan {$user->plan}");
    }
}
