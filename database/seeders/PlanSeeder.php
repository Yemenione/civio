<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Coupon;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Plan data (Baqat)

        // 2. Seed Competitive Plans (Baqat)
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Perfect for trying Civio basics.',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'features' => [
                    'resumes_limit' => 1,
                    'ai_tokens' => 1000,
                    'ats_check' => false,
                    'premium_templates' => false,
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Best for active job seekers.',
                'price_monthly' => 2.00,
                'price_yearly' => 20.00,
                'features' => [
                    'resumes_limit' => 5,
                    'ai_tokens' => 10000,
                    'ats_check' => true,
                    'premium_templates' => true,
                    'cover_letters' => true,
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'The ultimate career toolkit.',
                'price_monthly' => 5.00,
                'price_yearly' => 49.00,
                'features' => [
                    'resumes_limit' => 50,
                    'ai_tokens' => 100000,
                    'ats_check' => true,
                    'premium_templates' => true,
                    'cover_letters' => true,
                    'ai_chat' => true,
                ],
                'is_active' => true,
                'is_popular' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'For teams and professional recruitment.',
                'price_monthly' => 19.00,
                'price_yearly' => 190.00,
                'features' => [
                    'resumes_limit' => 500,
                    'ai_tokens' => 1000000,
                    'ats_check' => true,
                    'premium_templates' => true,
                    'cover_letters' => true,
                    'team_management' => true,
                    'api_access' => true,
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'Lifetime',
                'slug' => 'lifetime',
                'description' => 'One-time payment for perpetual access.',
                'price_monthly' => 29.00, // Using monthly field for one-time price in some systems, or handling specially
                'price_yearly' => 29.00,
                'features' => [
                    'resumes_limit' => 9999,
                    'ai_tokens' => 999999,
                    'ats_check' => true,
                    'premium_templates' => true,
                    'cover_letters' => true,
                    'ai_chat' => true,
                    'perpetual' => true,
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }

        // 3. Seed Trial Coupon
        Coupon::updateOrCreate(
            ['code' => 'FREE14'],
            [
                'discount_percent' => 100,
                'max_uses' => 999999,
                'expires_at' => now()->addYear(),
            ]
        );
    }
}
