<?php

namespace App\Console\Commands;

use App\Models\UserPackageAccess;
use App\Models\UserFeatureAccess;
use App\Models\UserSubscription;
use App\Models\Enrollment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpirePackageAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packages:expire-access
                            {--dry-run : Run without making changes}
                            {--notify : Send notifications to affected users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire package and feature access that has passed its expiration date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $shouldNotify = $this->option('notify');

        $this->info('Starting expiration check...');
        $this->newLine();

        // Use database transaction for safety
        DB::beginTransaction();

        try {
            // Expire package access
            $expiredPackagesCount = $this->expirePackageAccess($isDryRun, $shouldNotify);

            // Expire feature access
            $expiredFeaturesCount = $this->expireFeatureAccess($isDryRun);

            // Expire subscriptions
            $expiredSubscriptionsCount = $this->expireSubscriptions($isDryRun, $shouldNotify);

            if (!$isDryRun) {
                DB::commit();
                $this->newLine();
                $this->info('✓ Expiration process completed successfully!');
            } else {
                DB::rollBack();
                $this->newLine();
                $this->warn('DRY RUN - No changes were made');
            }

            $this->newLine();
            $this->table(
                ['Type', 'Expired Count'],
                [
                    ['Package Access', $expiredPackagesCount],
                    ['Feature Access', $expiredFeaturesCount],
                    ['Subscriptions', $expiredSubscriptionsCount],
                    ['Total', $expiredPackagesCount + $expiredFeaturesCount + $expiredSubscriptionsCount],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error during expiration: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Expire package access
     */
    protected function expirePackageAccess(bool $isDryRun, bool $shouldNotify): int
    {
        $this->info('Checking expired package access...');

        $expiredAccess = UserPackageAccess::where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->with(['user', 'package'])
            ->get();

        $count = $expiredAccess->count();

        if ($count === 0) {
            $this->line('  No expired package access found.');
            return 0;
        }

        $this->line("  Found {$count} expired package access records.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($expiredAccess as $access) {
            if (!$isDryRun) {
                // Deactivate package access
                $access->update(['is_active' => false]);

                // Deactivate associated enrollments
                Enrollment::where('package_access_id', $access->id)
                    ->where('status', 'active')
                    ->update([
                        'status' => 'expired',
                        'expires_at' => now(),
                    ]);

                // Send notification if enabled
                if ($shouldNotify && $access->user) {
                    // TODO: Send notification to user
                    // $access->user->notify(new PackageExpiredNotification($access));
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return $count;
    }

    /**
     * Expire feature access
     */
    protected function expireFeatureAccess(bool $isDryRun): int
    {
        $this->info('Checking expired feature access...');

        $expiredFeatures = UserFeatureAccess::where('has_access', true)
            ->whereNotNull('access_expires_at')
            ->where('access_expires_at', '<=', now())
            ->with('user')
            ->get();

        $count = $expiredFeatures->count();

        if ($count === 0) {
            $this->line('  No expired feature access found.');
            return 0;
        }

        $this->line("  Found {$count} expired feature access records.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($expiredFeatures as $featureAccess) {
            if (!$isDryRun) {
                $featureAccess->update(['has_access' => false]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return $count;
    }

    /**
     * Expire subscriptions
     */
    protected function expireSubscriptions(bool $isDryRun, bool $shouldNotify): int
    {
        $this->info('Checking expired subscriptions...');

        $expiredSubscriptions = UserSubscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->with(['user', 'plan'])
            ->get();

        $count = $expiredSubscriptions->count();

        if ($count === 0) {
            $this->line('  No expired subscriptions found.');
            return 0;
        }

        $this->line("  Found {$count} expired subscriptions.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($expiredSubscriptions as $subscription) {
            if (!$isDryRun) {
                // Update subscription status
                $subscription->update(['status' => 'expired']);

                // Revoke subscription-based package access
                UserPackageAccess::where('subscription_id', $subscription->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);

                // Revoke subscription-based feature access
                UserFeatureAccess::where('subscription_id', $subscription->id)
                    ->where('has_access', true)
                    ->update(['has_access' => false]);

                // Deactivate enrollments from this subscription
                Enrollment::where('enrollment_source', 'subscription')
                    ->whereHas('user.subscriptions', function ($query) use ($subscription) {
                        $query->where('id', $subscription->id);
                    })
                    ->where('status', 'active')
                    ->update([
                        'status' => 'expired',
                        'expires_at' => now(),
                    ]);

                // Send notification if enabled
                if ($shouldNotify && $subscription->user) {
                    // TODO: Send notification to user
                    // $subscription->user->notify(new SubscriptionExpiredNotification($subscription));
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return $count;
    }
}
