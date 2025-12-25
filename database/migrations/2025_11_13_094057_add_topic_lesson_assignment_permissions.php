<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create permissions for Topics
        $topicPermissions = [
            'topic.view' => 'View topics',
            'topic.create' => 'Create topics',
            'topic.update' => 'Update topics',
            'topic.delete' => 'Delete topics',
        ];

        // Create permissions for Lessons
        $lessonPermissions = [
            'lesson.view' => 'View lessons',
            'lesson.create' => 'Create lessons',
            'lesson.update' => 'Update lessons',
            'lesson.delete' => 'Delete lessons',
        ];

        // Create permissions for Assignments
        $assignmentPermissions = [
            'assignment.view' => 'View assignments',
            'assignment.create' => 'Create assignments',
            'assignment.update' => 'Update assignments',
            'assignment.delete' => 'Delete assignments',
            'assignment.manage' => 'Manage assignments',
            'assignment.grade' => 'Grade assignment submissions',
        ];

        // Create all permissions
        $allPermissions = array_merge($topicPermissions, $lessonPermissions, $assignmentPermissions);

        foreach ($allPermissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['description' => $description]
            );
        }

        // Assign all permissions to super_admin role
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(array_keys($allPermissions));
        }

        // Optionally assign some permissions to tutor role
        $tutor = Role::where('name', 'tutor')->first();
        if ($tutor) {
            $tutor->givePermissionTo([
                'topic.view',
                'lesson.view',
                'assignment.view',
                'assignment.create',
                'assignment.update',
                'assignment.manage',
                'assignment.grade',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove permissions
        $permissions = [
            'topic.view', 'topic.create', 'topic.update', 'topic.delete',
            'lesson.view', 'lesson.create', 'lesson.update', 'lesson.delete',
            'assignment.view', 'assignment.create', 'assignment.update', 'assignment.delete',
            'assignment.manage', 'assignment.grade',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
