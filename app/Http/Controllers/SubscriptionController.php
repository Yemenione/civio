<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        return Inertia::render('Subscription/Pricing');
    }

    public function upgrade(Request $request)
    {
        $user = Auth::user();
        
        // Simulate payment logic
        $user->update([
            'plan' => 'pro',
            'subscription_ends_at' => now()->addMonth(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Plan upgraded successfully!');
    }
}
