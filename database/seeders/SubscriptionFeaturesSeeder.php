<?php

namespace Database\Seeders;

use App\Models\PackageFeature;
use Illuminate\Database\Seeder;

class SubscriptionFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $features = [
            // Functional Features (Backend Access Control)
            [
                'feature_key' => 'quiz_access',
                'feature_name' => 'Quiz Access',
                'description' => 'Take quizzes and track your progress',
                'type' => 'functional',
                'is_active' => true,
            ],
            [
                'feature_key' => 'assignment_submission',
                'feature_name' => 'Assignment Submission',
                'description' => 'Submit assignments for tutor review',
                'type' => 'functional',
                'is_active' => true,
            ],
            [
                'feature_key' => 'tutor_support',
                'feature_name' => 'Live Tutor Support',
                'description' => '1-on-1 support from expert tutors',
                'type' => 'functional',
                'is_active' => true,
            ],
            [
                'feature_key' => 'certificate_download',
                'feature_name' => 'Certificate Download',
                'description' => 'Download completion certificates',
                'type' => 'functional',
                'is_active' => true,
            ],
            [
                'feature_key' => 'progress_analytics',
                'feature_name' => 'Advanced Analytics',
                'description' => 'Detailed progress reports and insights',
                'type' => 'functional',
                'is_active' => true,
            ],
            [
                'feature_key' => 'offline_download',
                'feature_name' => 'Offline Download',
                'description' => 'Download course content for offline viewing',
                'type' => 'functional',
                'is_active' => true,
            ],
            [
                'feature_key' => 'priority_support',
                'feature_name' => 'Priority Support',
                'description' => 'Faster response times for support tickets',
                'type' => 'functional',
                'is_active' => true,
            ],

            // Display Features (UI Badges - Optional)
            [
                'feature_key' => 'unlimited_courses',
                'feature_name' => 'Unlimited Course Access',
                'description' => 'Access all courses in the library',
                'type' => 'display',
                'is_active' => true,
            ],
            [
                'feature_key' => 'hd_video',
                'feature_name' => 'HD Video Streaming',
                'description' => 'Watch videos in 1080p quality',
                'type' => 'display',
                'is_active' => true,
            ],
            [
                'feature_key' => 'mobile_app',
                'feature_name' => 'Mobile App Access',
                'description' => 'iOS and Android apps included',
                'type' => 'display',
                'is_active' => true,
            ],
        ];

        foreach ($features as $feature) {
            PackageFeature::updateOrCreate(
                ['feature_key' => $feature['feature_key']],
                $feature
            );
        }

        $this->command->info('✓ Created ' . count($features) . ' subscription features');
    }
}
