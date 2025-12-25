<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@masterielts.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'phone' => '+1234567890',
                'country' => 'United States',
                'city' => 'New York',
                'timezone' => 'America/New_York',
                'language' => 'en',
                'is_active' => true,
                'is_verified' => true,
            ]
        );

        if (!$admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }

        // Create profile for admin
        UserProfile::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'headline' => 'Platform Administrator',
                'website' => 'https://masterielts.com',
            ]
        );

        // Create preferences for admin
        UserPreference::firstOrCreate(['user_id' => $admin->id]);

        // Create Demo Tutor
        $tutor = User::updateOrCreate(
            ['email' => 'tutor@masterielts.com'],
            [
                'name' => 'John Tutor',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'phone' => '+1234567891',
                'bio' => 'Experienced IELTS instructor with 10+ years of teaching experience.',
                'country' => 'United Kingdom',
                'city' => 'London',
                'timezone' => 'Europe/London',
                'is_active' => true,
                'is_verified' => true,
            ]
        );

        if (!$tutor->hasRole('tutor')) {
            $tutor->assignRole('tutor');
        }

        UserProfile::updateOrCreate(
            ['user_id' => $tutor->id],
            [
                'headline' => 'IELTS Expert Tutor',
                'skills' => ['IELTS Teaching', 'Academic Writing', 'Speaking Assessment'],
            ]
        );

        UserPreference::firstOrCreate(['user_id' => $tutor->id]);

        // Create Demo Student
        $student = User::updateOrCreate(
            ['email' => 'student@masterielts.com'],
            [
                'name' => 'Jane Student',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'phone' => '+1234567892',
                'country' => 'Pakistan',
                'city' => 'Rawalpindi',
                'timezone' => 'Asia/Karachi',
                'is_active' => true,
                'is_verified' => true,
            ]
        );

        if (!$student->hasRole('student')) {
            $student->assignRole('student');
        }

        UserProfile::updateOrCreate(
            ['user_id' => $student->id],
            [
                'headline' => 'IELTS Candidate',
                'interests' => ['Academic IELTS', 'UK Immigration'],
            ]
        );

        UserPreference::firstOrCreate(['user_id' => $student->id]);

        $this->command->info('Admin, Tutor, and Student users created successfully!');
        $this->command->info('Admin: admin@masterielts.com / password');
        $this->command->info('Tutor: tutor@masterielts.com / password');
        $this->command->info('Student: student@masterielts.com / password');
    }
}