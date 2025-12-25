# Permissions Fixed - Topics Now Visible ✅

## Issue

The Topics, Lessons, and Assignments menu items were not showing in the admin sidebar because the required permissions didn't exist in the database.

## Root Cause

The sidebar uses `@can('topic.view')`, `@can('lesson.view')`, and `@canany(['assignment.view', 'assignment.manage'])` directives to control visibility. Since these permissions didn't exist in the database, the menu items were hidden.

## Solution

Created migration `2025_11_13_094057_add_topic_lesson_assignment_permissions.php` to add all missing permissions.

---

## Permissions Created

### Topic Permissions
- ✅ `topic.view` - View topics
- ✅ `topic.create` - Create topics
- ✅ `topic.update` - Update topics
- ✅ `topic.delete` - Delete topics

### Lesson Permissions
- ✅ `lesson.view` - View lessons
- ✅ `lesson.create` - Create lessons
- ✅ `lesson.update` - Update lessons
- ✅ `lesson.delete` - Delete lessons

### Assignment Permissions
- ✅ `assignment.view` - View assignments
- ✅ `assignment.create` - Create assignments
- ✅ `assignment.update` - Update assignments
- ✅ `assignment.delete` - Delete assignments
- ✅ `assignment.manage` - Manage assignments
- ✅ `assignment.grade` - Grade assignment submissions

---

## Role Assignments

### Super Admin Role
Gets **ALL** permissions:
```php
- topic.view, topic.create, topic.update, topic.delete
- lesson.view, lesson.create, lesson.update, lesson.delete
- assignment.view, assignment.create, assignment.update, assignment.delete, assignment.manage, assignment.grade
```

### Tutor Role
Gets **limited** permissions:
```php
- topic.view (read-only)
- lesson.view (read-only)
- assignment.view, assignment.create, assignment.update, assignment.manage, assignment.grade
```

---

## Migration Details

**File:** `database/migrations/2025_11_13_094057_add_topic_lesson_assignment_permissions.php`

**What it does:**
1. Creates all topic, lesson, and assignment permissions in the `permissions` table
2. Automatically assigns all permissions to the `super_admin` role
3. Assigns limited permissions to the `tutor` role
4. Uses `firstOrCreate()` to prevent duplicates if run multiple times

**Rollback:**
```bash
php artisan migrate:rollback
```

This will remove all the created permissions.

---

## Commands Run

```bash
# Create the migration
php artisan make:migration add_topic_lesson_assignment_permissions

# Run the migration
php artisan migrate

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan permission:cache-reset
```

---

## Verification

After running the migration, you can verify the permissions exist:

```bash
php artisan tinker
```

Then in Tinker:
```php
// Check topic permissions
\Spatie\Permission\Models\Permission::where('name', 'like', 'topic%')->pluck('name');

// Check if super_admin has topic.view
\Spatie\Permission\Models\Role::findByName('super_admin')->hasPermissionTo('topic.view');

// Check all permissions of super_admin
\Spatie\Permission\Models\Role::findByName('super_admin')->permissions->pluck('name');
```

---

## What's Now Visible in Admin Sidebar

### Course Management Submenu (Expandable)
- ✅ All Courses (already visible)
- ✅ **Topics** (now visible!)
- ✅ **Lessons** (now visible!)

### Standalone Menu Items
- ✅ **Quizzes** (now visible if you have quiz.view permission)
- ✅ **Assignments** (now visible!)

---

## Testing Checklist

- [x] Migration created
- [x] Migration run successfully
- [x] Permissions created in database
- [x] Super admin role has all permissions
- [x] Tutor role has limited permissions
- [x] All caches cleared
- [x] Permission cache reset

### Browser Testing (Do this now!)
- [ ] Log out and log back in to refresh session
- [ ] Navigate to admin dashboard
- [ ] Verify "Course Management" submenu shows Topics and Lessons
- [ ] Click Topics - should navigate to `/admin/topics`
- [ ] Click Lessons - should navigate to `/admin/lessons`
- [ ] Verify Quizzes menu item is visible
- [ ] Click Quizzes - should navigate to `/admin/quizzes`
- [ ] Verify Assignments menu item is visible
- [ ] Click Assignments - should navigate to `/admin/assignments`

---

## Troubleshooting

### If you still can't see Topics/Lessons/Assignments:

1. **Clear your browser cache and cookies**
   - Hard refresh: Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)

2. **Verify you're logged in as super_admin**
   ```bash
   php artisan tinker
   ```
   ```php
   $user = \App\Models\User::where('email', 'your-admin@email.com')->first();
   $user->roles->pluck('name'); // Should show 'super_admin'
   ```

3. **Manually assign permission to your user**
   ```bash
   php artisan tinker
   ```
   ```php
   $user = \App\Models\User::where('email', 'your-admin@email.com')->first();
   $user->givePermissionTo('topic.view');
   $user->givePermissionTo('lesson.view');
   $user->givePermissionTo('assignment.view');
   ```

4. **Check if routes work directly**
   - Visit: `http://localhost:8000/admin/topics`
   - Visit: `http://localhost:8000/admin/lessons`
   - Visit: `http://localhost:8000/admin/assignments`

5. **Re-run permission cache reset**
   ```bash
   php artisan permission:cache-reset
   php artisan cache:clear
   ```

---

## Future Permissions

If you need to add more permissions in the future, use this pattern:

```php
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Create permission
Permission::create(['name' => 'your.permission', 'guard_name' => 'web']);

// Assign to role
$role = Role::findByName('super_admin');
$role->givePermissionTo('your.permission');

// Clear cache
\Artisan::call('permission:cache-reset');
```

---

## Status

✅ **FIXED** - All permissions created and assigned
✅ **Caches Cleared** - Application and permission caches reset
✅ **Ready to Test** - Log out, log back in, and check the sidebar!

---

**Date:** 2025-11-13
**Migration:** `2025_11_13_094057_add_topic_lesson_assignment_permissions.php`
**Status:** COMPLETED
