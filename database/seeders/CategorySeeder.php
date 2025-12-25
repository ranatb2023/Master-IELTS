<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CourseCategory;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'IELTS Preparation',
                'slug' => 'ielts-preparation',
                'description' => 'Complete IELTS test preparation courses',
                'icon' => 'book-open',
                'color' => '#3B82F6',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Academic IELTS',
                'slug' => 'academic-ielts',
                'description' => 'Academic IELTS courses for university admissions',
                'icon' => 'academic-cap',
                'color' => '#8B5CF6',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'General Training IELTS',
                'slug' => 'general-training-ielts',
                'description' => 'General Training IELTS for immigration and work',
                'icon' => 'briefcase',
                'color' => '#10B981',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Speaking',
                'slug' => 'speaking',
                'description' => 'IELTS Speaking module preparation',
                'icon' => 'microphone',
                'color' => '#F59E0B',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Writing',
                'slug' => 'writing',
                'description' => 'IELTS Writing tasks 1 and 2',
                'icon' => 'pencil',
                'color' => '#EF4444',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Reading',
                'slug' => 'reading',
                'description' => 'IELTS Reading comprehension strategies',
                'icon' => 'book',
                'color' => '#06B6D4',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Listening',
                'slug' => 'listening',
                'description' => 'IELTS Listening practice and techniques',
                'icon' => 'volume-up',
                'color' => '#EC4899',
                'order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            CourseCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('Course categories seeded successfully!');
    }
}
