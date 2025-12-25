<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionEnded;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Subscription;

class SuspendExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:suspend-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Suspend enrollments for expired subscriptions (backup for webhook failures)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired subscriptions...');

        // Find subscriptions that have ended but still have active enrollments
        $expiredSubscriptions = Subscription::where('ends_at', '<', now())
            ->where('stripe_status', '!=', 'canceled')
            ->whereHas('user.enrollments', function ($query) {
                $query->where('enrollment_source', 'subscription')
                    ->where('status', 'active');
            })
            ->with('user')
            ->get();

        if ($expiredSubscriptions->isEmpty()) {
            $this->info('No expired subscriptions found.');
            return 0;
        }

        $this->info("Found {$expiredSubscriptions->count()} expired subscription(s).");

        $processedCount = 0;

        foreach ($expiredSubscriptions as $subscription) {
            $user = $subscription->user;

            $this->info("Processing subscription for user: {$user->email}");

            try {
                // Suspend enrollments
                $suspendedCount = $user->enrollments()
                    ->where('subscription_id', $subscription->id)
                    ->where('enrollment_source', 'subscription')
                    ->where('status', 'active')
                    ->update([
                        'status' => 'suspended',
                        'notes' => 'Subscription ended on ' . $subscription->ends_at->format('Y-m-d') . '. Suspended via scheduled command.'
                    ]);

                // Deactivate package access
                $user->userPackageAccess()
                    ->where('subscription_id', $subscription->id)
                    ->update(['is_active' => false]);

                // Update subscription status
                $subscription->update(['stripe_status' => 'canceled']);

                Log::info('Suspended enrollments for expired subscription', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'suspended_count' => $suspendedCount
                ]);

                // Send email notification
                try {
                    Mail::to($user)->send(new SubscriptionEnded($user, $subscription));
                    $this->info("Email sent to {$user->email}");
                } catch (\Exception $e) {
                    $this->error("Failed to send email to {$user->email}: " . $e->getMessage());
                }

                $processedCount++;
                $this->info("✓ Suspended {$suspendedCount} enrollment(s) for {$user->email}");

            } catch (\Exception $e) {
                $this->error("Failed to process subscription for {$user->email}: " . $e->getMessage());
                Log::error('Failed to suspend expired subscription', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("\n✓ Processed {$processedCount} expired subscription(s).");
        return 0;
    }
}
