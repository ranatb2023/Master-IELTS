# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Master IELTS is a Laravel 12 application for IELTS tutoring platform with role-based access control supporting three user types: super_admin, tutor, and student.

## Development Commands

### Initial Setup
```bash
composer setup
# Runs: composer install, creates .env, key:generate, migrate, npm install & build
```

### Development Server
```bash
composer dev
# Concurrently runs: php artisan serve, queue:listen, pail (logs), and npm run dev
# Server: http://localhost:8000 (default)
```

### Alternative: Manual Commands
```bash
php artisan serve                    # Start development server
php artisan queue:listen --tries=1   # Queue worker
php artisan pail --timeout=0         # Real-time log viewer (Laravel Pail)
npm run dev                          # Vite development server
```

### Testing
```bash
composer test
# Runs: config:clear && php artisan test
php artisan test --filter=TestName   # Run specific test
```

### Code Quality
```bash
./vendor/bin/pint                    # Laravel Pint code formatter
./vendor/bin/pint --test             # Check formatting without changing
```

### Frontend
```bash
npm run dev                          # Vite dev server with HMR
npm run build                        # Production build
```

### Database
```bash
php artisan migrate                  # Run migrations
php artisan migrate:fresh --seed     # Fresh migration with seeders
php artisan db:seed                  # Run seeders only
```

## Architecture

### User System

**User Model** (`app/Models/User.php`)
- Uses Spatie Permission package for role-based access (HasRoles trait)
- Spatie Activity Log for tracking user changes (LogsActivity trait)
- Implements MustVerifyEmail for email verification
- Uses SoftDeletes for safe deletion
- Has helper methods: `isAdmin()`, `isTutor()`, `isStudent()`
- Scopes: `active()`, `verified()`

**Automatic Relationships** (via `UserObserver`)
- When a user is created, `UserProfile` and `UserPreference` records are automatically created
- Default role 'student' is auto-assigned if no role is specified
- Observer is registered in `app/Providers/AppServiceProvider.php`

**User Relationships**
- `profile()` → UserProfile (one-to-one)
- `preferences()` → UserPreference (one-to-one)

### Role-Based Access Control

Three primary roles defined:
- `super_admin` - Full system access (admin dashboard)
- `tutor` - Tutor-specific features
- `student` - Default role for new users

**Route Protection**
Routes use role middleware: `->middleware(['role:super_admin'])`
See `routes/web.php` for role-based route groups.

**Custom Middleware**
- `CheckUserActive` - Ensures user account is active
- `RoleMiddleware` - Checks user role
- `PermissionMiddleware` - Checks specific permissions

### Controller Organization

Controllers are namespaced by role:
- `App\Http\Controllers\Admin\*` - Admin functionality
- `App\Http\Controllers\Tutor\*` - Tutor functionality
- `App\Http\Controllers\Student\*` - Student functionality
- `App\Http\Controllers\Auth\*` - Authentication (Breeze)
- `App\Http\Controllers\ProfileController` - User profile management

### Frontend Stack

- **Build Tool**: Vite (with HMR)
- **CSS Framework**: TailwindCSS 3.x with @tailwindcss/forms
- **JavaScript**: Alpine.js for interactivity
- **Views**: Blade templates in `resources/views/`
  - `layouts/` - Base layouts
  - `components/` - Reusable Blade components
  - `auth/` - Authentication views (Breeze)
  - `profile/` - User profile pages
  - Role-specific dashboards

### Key Packages & Services

- **Laravel Breeze** - Authentication scaffolding
- **Laravel Sanctum** - API token authentication
- **Spatie Permission** - Role and permission management
- **Spatie Activity Log** - User activity tracking
- **Laravel Telescope** - Debugging and monitoring (dev only)
- **Laravel Debugbar** - Debug toolbar (dev only)
- **Intervention Image** - Image processing (avatar uploads)
- **Barryvdh DomPDF** - PDF generation

### Queue & Jobs

- Queue driver: `database` (see `QUEUE_CONNECTION` in `.env`)
- Run queue worker: `php artisan queue:listen --tries=1`
- Jobs are stored in `database/jobs` table

### Database

- Default: SQLite (`database/database.sqlite`)
- Configured in `.env` via `DB_CONNECTION=sqlite`
- For MySQL/PostgreSQL, update DB_* variables in `.env`

**Important Tables**
- `users` - User accounts (soft deletes enabled)
- `user_profiles` - Extended user profile data
- `user_preferences` - User preferences and settings
- `roles` & `permissions` - Spatie Permission tables
- `activity_log` - Spatie Activity Log entries

**Seeders**
- `RolePermissionSeeder` - Creates roles and permissions
- `AdminUserSeeder` - Creates admin user account
- Run with: `php artisan db:seed`

### Storage & File Uploads

- User avatars stored in `storage/app/public/`
- Public access via symlink: `public/storage/` → `storage/app/public/`
- Create symlink: `php artisan storage:link`
- Avatar accessor: `$user->avatar_url` returns full URL

## Development Patterns

### Adding New User Roles

1. Add role in `database/seeders/RolePermissionSeeder.php`
2. Create route group in `routes/web.php` with role middleware
3. Create namespaced controller in `app/Http/Controllers/{Role}/`
4. Add helper method to User model: `isRoleName()`

### Creating Controllers

Controllers should extend `App\Http\Controllers\Controller` and use form request validation when accepting user input.

### Form Requests

Store validation logic in `app/Http/Requests/` for complex validations.

### Activity Logging

Models that need activity logging should:
1. Use `LogsActivity` trait from Spatie
2. Implement `getActivitylogOptions()` method
3. Configure which attributes to log

### Profile Updates

User profile data is split across three tables:
- `users` - Core authentication data (name, email, password, etc.)
- `user_profiles` - Extended profile (controlled via ProfileController)
- `user_preferences` - User settings and preferences

See `ProfileController` for update patterns.

## Important Notes

- **UserObserver**: Automatically creates profile and preferences when user is created. Don't create these manually.
- **Soft Deletes**: Users are soft deleted. Use `withTrashed()` to include deleted users in queries.
- **Role Assignment**: New users get 'student' role by default (via UserObserver).
- **Email Verification**: Enabled via `MustVerifyEmail` interface. Routes require `verified` middleware.
- **Telescope**: Only enabled in local environment. Access at `/telescope` when running locally.
- **Debugbar**: Only enabled in local environment with `APP_DEBUG=true`.

## Environment Setup

Key environment variables to configure:
- `APP_KEY` - Auto-generated via `php artisan key:generate`
- `APP_URL` - Application URL
- `DB_CONNECTION` - Database driver (sqlite/mysql/pgsql)
- `MAIL_*` - Email configuration (default: log driver for local dev)
- `QUEUE_CONNECTION` - Queue driver (default: database)
- `SESSION_DRIVER` - Session storage (default: database)
