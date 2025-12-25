<?php

namespace App\Console\Commands;

use App\Models\SubscriptionPlan;
use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;

class CreateStripeProducts extends Command
{
    protected $signature = 'stripe:create-products';
    protected $description = 'Create Stripe products and prices for subscription plans';

    public function handle()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $plans = [
            [
                'slug' => 'basic',
                'name' => 'Basic Plan',
                'description' => 'Perfect for beginners starting their IELTS journey',
                'price' => 1999, // in cents
                'interval' => 'month',
                'trial_days' => 7,
            ],
            [
                'slug' => 'premium',
                'name' => 'Premium Plan',
                'description' => 'Most popular plan with comprehensive content',
                'price' => 3999, // in cents
                'interval' => 'month',
                'trial_days' => 14,
            ],
            [
                'slug' => 'pro',
                'name' => 'Pro Plan',
                'description' => 'Complete access with tutor support',
                'price' => 7999, // in cents
                'interval' => 'month',
                'trial_days' => 14,
            ],
        ];

        foreach ($plans as $planData) {
            $this->info("Creating Stripe product for {$planData['name']}...");

            try {
                // Create Product
                $product = Product::create([
                    'name' => $planData['name'],
                    'description' => $planData['description'],
                    'metadata' => [
                        'slug' => $planData['slug'],
                    ],
                ]);

                $this->info("✓ Product created: {$product->id}");

                // Create Price
                $price = Price::create([
                    'product' => $product->id,
                    'unit_amount' => $planData['price'],
                    'currency' => 'usd',
                    'recurring' => [
                        'interval' => $planData['interval'],
                        'trial_period_days' => $planData['trial_days'],
                    ],
                ]);

                $this->info("✓ Price created: {$price->id}");

                // Update database
                SubscriptionPlan::where('slug', $planData['slug'])->update([
                    'stripe_product_id' => $product->id,
                    'stripe_price_id' => $price->id,
                ]);

                $this->info("✓ Database updated for {$planData['slug']}\n");

            } catch (\Exception $e) {
                $this->error("Error creating {$planData['name']}: " . $e->getMessage());
            }
        }

        $this->info('All done! Subscription plans are ready for checkout.');
    }
}
