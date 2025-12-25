<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Course Management
            'course.create',
            'course.view',
            'course.update',
            'course.delete',
            'course.publish',
            'course.enroll',

            // Lesson Management
            'lesson.create',
            'lesson.view',
            'lesson.update',
            'lesson.delete',

            // Quiz Management
            'quiz.create',
            'quiz.view',
            'quiz.update',
            'quiz.delete',
            'quiz.take',
            'quiz.manage',

            // Assignment Management
            'assignment.create',
            'assignment.view',
            'assignment.update',
            'assignment.delete',
            'assignment.submit',
            'assignment.grade',

            // Certificate Management
            'certificate.view',
            'certificate.issue',
            'certificate.revoke',

            // User Management
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
            'student.block',

            // Order Management
            'order.view',
            'order.create',
            'order.refund',

            // Forum Management
            'forum.view',
            'forum.create',
            'forum.update',
            'forum.delete',
            'forum.moderate',

            // Blog Management
            'blog.view',
            'blog.create',
            'blog.update',
            'blog.delete',
            'blog.publish',
            'blog.manage',

            // Comment Management
            'comment.create',
            'comment.update',
            'comment.delete',
            'comment.moderate',

            // Notification Management
            'notification.send',
            'notification.template.manage',

            // Settings
            'settings.view',
            'settings.update',

            // Reports
            'reports.view',
            'reports.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin - has all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // Tutor Role
        $tutor = Role::firstOrCreate(['name' => 'tutor']);
        $tutor->syncPermissions([
            'course.create',
            'course.view',
            'course.update',
            'course.publish',
            'lesson.create',
            'lesson.view',
            'lesson.update',
            'lesson.delete',
            'quiz.create',
            'quiz.view',
            'quiz.update',
            'quiz.delete',
            'quiz.manage',
            'assignment.create',
            'assignment.view',
            'assignment.update',
            'assignment.delete',
            'assignment.grade',
            'certificate.view',
            'certificate.issue',
            'forum.view',
            'forum.create',
            'forum.moderate',
            'comment.create',
            'comment.update',
            'comment.moderate',
            'notification.send',
            'reports.view',
        ]);

        // Student Role
        $student = Role::firstOrCreate(['name' => 'student']);
        $student->syncPermissions([
            'course.view',
            'course.enroll',
            'lesson.view',
            'quiz.view',
            'quiz.take',
            'assignment.view',
            'assignment.submit',
            'certificate.view',
            'forum.view',
            'forum.create',
            'comment.create',
            'blog.view',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}