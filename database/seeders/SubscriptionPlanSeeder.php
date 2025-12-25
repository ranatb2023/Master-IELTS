<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic Plan',
                'slug' => 'basic',
                'description' => 'Perfect for beginners starting their IELTS journey',
                'price' => 19.99,
                'regular_price' => 19.99,
                'currency' => 'USD',
                'interval' => 'month',
                'trial_days' => 7,
                'stripe_price_id' => env('STRIPE_BASIC_PRICE_ID', 'price_basic'),
                'stripe_product_id' => env('STRIPE_BASIC_PRODUCT_ID', 'prod_basic'),
                'features' => [
                    'Access to all basic courses',
                    'Practice tests',
                    'Email support',
                    '7-day free trial',
                ],
                'included_package_ids' => [],
                'included_course_ids' => [],
                'is_active' => true,
            ],
            [
                'name' => 'Premium Plan',
                'slug' => 'premium',
                'description' => 'Most popular plan with comprehensive content',
                'price' => 39.99,
                'first_month_price' => 29.99,
                'regular_price' => 39.99,
                'promotional_months' => 1,
                'currency' => 'USD',
                'interval' => 'month',
                'trial_days' => 14,
                'stripe_price_id' => env('STRIPE_PREMIUM_PRICE_ID', 'price_premium'),
                'stripe_product_id' => env('STRIPE_PREMIUM_PRODUCT_ID', 'prod_premium'),
                'features' => [
                    'All Basic features',
                    'Access to premium courses',
                    'Advanced practice tests',
                    'Priority email support',
                    '14-day free trial',
                    'First month discount',
                ],
                'included_package_ids' => [],
                'included_course_ids' => [],
                'is_active' => true,
            ],
            [
                'name' => 'Pro Plan',
                'slug' => 'pro',
                'description' => 'Complete access with tutor support',
                'price' => 79.99,
                'first_month_price' => 59.99,
                'regular_price' => 79.99,
                'promotional_months' => 1,
                'currency' => 'USD',
                'interval' => 'month',
                'trial_days' => 14,
                'stripe_price_id' => env('STRIPE_PRO_PRICE_ID', 'price_pro'),
                'stripe_product_id' => env('STRIPE_PRO_PRODUCT_ID', 'prod_pro'),
                'features' => [
                    'All Premium features',
                    'Unlimited access to all courses',
                    'Live tutor sessions',
                    '1-on-1 feedback',
                    'Speaking practice sessions',
                    'Priority support',
                    '14-day free trial',
                ],
                'included_package_ids' => [],
                'included_course_ids' => [],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('Subscription plans created successfully!');
    }
}
