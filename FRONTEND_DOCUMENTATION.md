# Frontend Development Documentation - MasterIELTS

## Overview
This document provides comprehensive details about the frontend development and view structure of the MasterIELTS Learning Management System. The application is built using Laravel 12 with Laravel Breeze for authentication and Tailwind CSS for styling.

---

## Technology Stack

### Core Technologies
- **Laravel 12** - PHP Framework
- **Laravel Breeze** - Authentication scaffolding with Blade components
- **Tailwind CSS 3.x** - Utility-first CSS framework
- **Alpine.js 3.x** - Lightweight JavaScript framework for interactivity
- **Blade Template Engine** - Laravel's templating system
- **Vite** - Modern frontend build tool

### Design System
- **Color Scheme**: Indigo/Purple primary colors with semantic color usage
- **Typography**: Figtree font family from Bunny Fonts
- **Icons**: Heroicons (SVG stroke icons)
- **Responsive Design**: Mobile-first approach with Tailwind breakpoints

---

## Directory Structure

```
resources/views/
├── layouts/
│   ├── app.blade.php                    # Main application layout (Breeze)
│   ├── admin.blade.php                  # Admin panel layout with sidebar
│   ├── guest.blade.php                  # Guest layout for auth pages
│   ├── navigation.blade.php             # Main navigation bar
│   └── partials/
│       ├── admin-header.blade.php       # Admin panel header
│       ├── admin-sidebar.blade.php      # Admin panel sidebar navigation
│       └── footer.blade.php             # Application footer
│
├── components/                          # Reusable Blade components (Breeze)
│   ├── application-logo.blade.php
│   ├── dropdown.blade.php
│   ├── dropdown-link.blade.php
│   ├── input-error.blade.php
│   ├── input-label.blade.php
│   ├── nav-link.blade.php
│   ├── primary-button.blade.php
│   ├── responsive-nav-link.blade.php
│   ├── secondary-button.blade.php
│   └── text-input.blade.php
│
├── admin/                               # Admin panel views
│   ├── dashboard.blade.php              # Admin dashboard
│   ├── courses/
│   │   └── index.blade.php              # Course management list
│   ├── categories/
│   │   └── index.blade.php              # Category management
│   └── users/
│       ├── index.blade.php              # User list
│       ├── show.blade.php               # User profile details
│       ├── edit.blade.php               # Edit user form
│       └── create.blade.php             # Create user form
│
├── tutor/                               # Tutor interface views
│   ├── dashboard.blade.php              # Tutor dashboard
│   ├── courses/
│   │   ├── index.blade.php              # My courses list
│   │   ├── create.blade.php             # Create new course
│   │   ├── edit.blade.php               # Edit course
│   │   └── show.blade.php               # Course management details
│   ├── topics/
│   │   └── index.blade.php              # Manage topics and lessons
│   ├── quizzes/
│   │   └── index.blade.php              # Quiz management
│   └── assignments/
│       ├── index.blade.php              # Assignment management
│       └── submissions.blade.php        # View submissions
│
├── student/                             # Student interface views
│   ├── dashboard.blade.php              # Student dashboard
│   ├── courses/
│   │   ├── index.blade.php              # Browse courses
│   │   ├── show.blade.php               # Course details
│   │   ├── learn.blade.php              # Learning interface
│   │   └── lesson.blade.php             # Lesson viewer
│   ├── enrollments/
│   │   └── index.blade.php              # My enrollments
│   ├── quizzes/
│   │   ├── show.blade.php               # Quiz details
│   │   ├── take.blade.php               # Quiz taking interface
│   │   └── result.blade.php             # Quiz results
│   ├── assignments/
│   │   ├── show.blade.php               # Assignment details
│   │   └── create.blade.php             # Submit assignment
│   └── certificates/
│       └── index.blade.php              # My certificates
│
├── courses/                             # Public course views
│   ├── index.blade.php                  # Course catalog
│   └── show.blade.php                   # Course detail page
│
├── pages/                               # Static pages
│   ├── about.blade.php                  # About us page
│   └── contact.blade.php                # Contact form page
│
├── auth/                                # Authentication views (Breeze)
│   ├── login.blade.php
│   ├── register.blade.php
│   ├── forgot-password.blade.php
│   ├── reset-password.blade.php
│   └── verify-email.blade.php
│
├── profile/                             # User profile views (Breeze)
│   ├── edit.blade.php
│   └── partials/
│       ├── update-profile-information-form.blade.php
│       ├── update-password-form.blade.php
│       └── delete-user-form.blade.php
│
├── home.blade.php                       # Homepage
└── dashboard.blade.php                  # Default dashboard (Breeze)
```

---

## Layout Components

### 1. Main Application Layout (`layouts/app.blade.php`)
**Purpose**: Primary layout for authenticated users with Breeze styling

**Features**:
- Laravel Breeze components integration
- Named slot for page header
- Main navigation bar
- Flash message support
- Responsive design
- Footer section

**Usage**:
```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Page Title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <!-- Page content -->
    </div>
</x-app-layout>
```

**Key Classes**:
- Background: `bg-gray-100`
- Font: `font-sans antialiased`
- Minimum height: `min-h-screen`

---

### 2. Admin Layout (`layouts/admin.blade.php`)
**Purpose**: Admin panel layout with sidebar navigation

**Features**:
- Two-panel layout (sidebar + content)
- Collapsible sidebar for mobile
- Alpine.js for sidebar toggle
- Flash message display
- Consistent admin styling

**Structure**:
```
┌─────────────────────────────────────┐
│          Admin Header               │
├─────────┬───────────────────────────┤
│         │                           │
│ Sidebar │    Main Content Area      │
│  (64w)  │                           │
│         │                           │
└─────────┴───────────────────────────┘
```

**Sections**:
- `@yield('title')` - Page title for browser tab
- `@yield('page-title')` - Header title display
- `@yield('content')` - Main content area
- `@stack('styles')` - Additional CSS
- `@stack('scripts')` - Additional JavaScript

---

### 3. Navigation Bar (`layouts/navigation.blade.php`)
**Purpose**: Main navigation with role-based menu items

**Menu Structure**:
- **Home** - Homepage link
- **Courses** - Browse courses
- **Role-based links**:
  - Super Admin → Admin panel
  - Tutor → My Courses
  - Student → My Learning
- **About** - About page
- **Contact** - Contact form
- **User dropdown** - Profile & Logout

**Responsive Behavior**:
- Desktop: Horizontal navigation
- Mobile: Hamburger menu with slide-down

**Key Features**:
- Active state highlighting (indigo underline)
- Alpine.js for mobile menu toggle
- Authenticated vs guest states
- Role-based menu display

---

### 4. Admin Sidebar (`layouts/partials/admin-sidebar.blade.php`)
**Purpose**: Side navigation for admin panel

**Color Scheme**:
- Background: White (`bg-white`)
- Border: Gray-200 (`border-gray-200`)
- Active: Indigo-50 with indigo-700 left border
- Hover: Gray-50

**Menu Items**:
1. **Dashboard** - Overview statistics
2. **Courses** - Course management
3. **Categories** - Category management
4. **Users** - User management
5. **Enrollments** - Enrollment tracking
6. **Quizzes** - Quiz management
7. **Subscriptions** - Plan management
8. **Reports** (Collapsible):
   - Overview
   - Revenue
   - Enrollments
   - Course Performance
   - Student Progress
   - Tutor Performance

**Features**:
- Active route highlighting
- Smooth transitions
- Collapsible Reports menu
- User profile at bottom
- Mobile responsive

---

### 5. Admin Header (`layouts/partials/admin-header.blade.php`)
**Purpose**: Top header bar for admin panel

**Components**:
- Mobile menu button (hamburger)
- Page title display
- Notification bell (with badge)
- "View Site" link
- User dropdown menu

**Breeze Styling**:
- Border-bottom instead of shadow
- Consistent typography
- Smooth transitions
- Proper spacing

---

## View Pages Documentation

## Public Views

### Homepage (`home.blade.php`)
**Route**: `/`
**Purpose**: Landing page for all visitors

**Sections**:
1. **Hero Section**
   - Gradient background (blue-600 to indigo-700)
   - Call-to-action buttons
   - Promotional text

2. **Stats Cards**
   - Total Courses
   - Active Students
   - Expert Instructors
   - Total Enrollments

3. **Featured Courses**
   - Grid layout (3 columns)
   - Course cards with thumbnails
   - Price, rating, and enrollment info

4. **Categories Section**
   - Icon-based category cards
   - Hover effects
   - Links to filtered courses

5. **Testimonials**
   - Student reviews
   - Star ratings
   - Profile images

6. **CTA Section**
   - Final call-to-action
   - Registration/browse buttons

**Design Pattern**: Large, visually engaging sections with gradients and cards

---

### Course Catalog (`courses/index.blade.php`)
**Route**: `/courses`
**Purpose**: Browse all available courses

**Features**:
- **Filter Panel**:
  - Search by keyword
  - Category dropdown
  - Level filter (Beginner/Intermediate/Advanced)
  - Price type (Free/Paid)
  - Apply/Reset buttons

- **Course Grid**:
  - Responsive grid (1/2/3 columns)
  - Course cards with:
    - Thumbnail image
    - Category badge
    - Level badge
    - Free badge (if applicable)
    - Title and description
    - Rating and reviews
    - Student count
    - Instructor info
    - Price
    - "View Details" button

- **Pagination**:
  - Laravel pagination links
  - Preserves filter parameters

**Empty State**: Centered icon with "No courses found" message

---

### Course Detail (`courses/show.blade.php`)
**Route**: `/courses/{slug}`
**Purpose**: Detailed information about a specific course

**Layout**: Two-column (2/3 main + 1/3 sidebar)

**Main Content**:
1. **Breadcrumb Navigation**
2. **Course Header**:
   - Title
   - Short description
   - Meta information (level, students, duration, rating)

3. **Course Image/Video**
4. **About This Course**: Full description
5. **What You'll Learn**:
   - Grid of learning outcomes
   - Green checkmark icons

6. **Course Content**:
   - Collapsible topics (HTML details/summary)
   - Lessons list with duration
   - Free preview indicators

7. **Instructor Section**:
   - Avatar
   - Name and bio

**Sidebar**:
- Price display (or "Free")
- Enroll button (context-aware):
  - Guest: "Login to Enroll"
  - Enrolled: "Continue Learning"
  - Student: "Enroll Now"
  - Non-student: Info message

- **This course includes**:
  - Video hours
  - Sections count
  - Quizzes count
  - Certificate availability
  - Lifetime access indicator

**Sticky Sidebar**: Stays visible on scroll

---

### About Page (`pages/about.blade.php`)
**Route**: `/about`
**Purpose**: Company information and values

**Sections**:
1. **Hero Section**: Gradient header with title
2. **Mission & Vision**: Side-by-side cards
3. **Impact Stats**: 4-column statistics
4. **Why Choose Us**: 6 benefit cards with icons
5. **Core Values**: 4 values with descriptions
6. **CTA Section**: Final call-to-action

**Design**: Professional, informative layout with plenty of whitespace

---

### Contact Page (`pages/contact.blade.php`)
**Route**: `/contact`
**Purpose**: Contact form and information

**Layout**: Two-column (1/3 info + 2/3 form)

**Contact Information Card**:
- Email addresses
- Phone number with hours
- Physical address
- Social media icons

**Contact Form**:
- Name field
- Email field
- Phone (optional)
- Subject dropdown
- Message textarea
- Submit button

**Features**:
- Success message display
- Validation error display
- Pre-filled for authenticated users
- Response time notice

---

## Student Views

### Student Dashboard (`student/dashboard.blade.php`)
**Route**: `/student/dashboard`
**Purpose**: Student's main hub

**Sections**:
1. **Welcome Card**: Gradient header with greeting
2. **Stats Cards** (4 columns):
   - Enrolled Courses
   - Completed Courses
   - Certificates Earned
   - Average Progress %

3. **Continue Learning**:
   - Grid of enrolled courses
   - Progress bars
   - "Continue Learning" buttons
   - Empty state with "Browse Courses" CTA

4. **Quick Actions** (3 cards):
   - Browse Courses
   - My Enrollments
   - My Certificates

**Design**: Focus on learning progress and easy access to courses

---

### Learning Interface (`student/courses/learn.blade.php`)
**Route**: `/student/courses/{course}/learn`
**Purpose**: Main learning environment

**Layout**: Full-screen with sidebar

**Features**:
- **Collapsible Sidebar** (280px):
  - Course title
  - Progress bar
  - Collapsible topics
  - Lesson list with icons
  - Completion checkmarks
  - Duration display

- **Top Bar**:
  - Sidebar toggle
  - Current lesson title
  - Previous/Next buttons
  - "Complete Course" (on last lesson)

- **Main Content Area**:
  - Video player (YouTube/direct video support)
  - Lesson content (HTML)
  - Downloadable resources
  - "Mark as Complete" button
  - Personal notes section

**Lesson Icons**:
- Video: Play circle icon
- Reading: Book icon
- Document: File icon

**Interactive Elements**:
- Save notes functionality
- Video playback
- Progress tracking
- Navigation controls

---

## Tutor Views

### Tutor Dashboard (`tutor/dashboard.blade.php`)
**Route**: `/tutor/dashboard`
**Purpose**: Tutor's control center

**Sections**:
1. **Welcome Card**: Blue gradient header
2. **Stats Cards** (4 columns):
   - Total Courses
   - Published Courses
   - Total Students
   - Total Revenue

3. **Quick Actions** (3 cards):
   - Create New Course
   - My Courses
   - Assignments

4. **My Courses Grid**:
   - Course cards with thumbnails
   - Status badges (Published/Draft/Archived)
   - Student count
   - Rating display
   - "Manage" button

5. **Pending Submissions** (if any):
   - Yellow background cards
   - Student name
   - Assignment title
   - Submission time
   - "Review" button

**Design**: Focused on course management and student oversight

---

### My Courses (`tutor/courses/index.blade.php`)
**Route**: `/tutor/courses`
**Purpose**: List and manage tutor's courses

**Features**:
- **Stats Cards** (4 columns):
  - Total Courses
  - Published
  - Draft
  - Total Students

- **Filter Panel**:
  - Search field
  - Status filter
  - Category filter
  - Apply/Reset buttons

- **Course List**:
  - Horizontal cards with:
    - Thumbnail (160px wide)
    - Title and status badge
    - Description
    - Category, students, rating
    - Price
    - Action buttons (Manage/Edit/Preview)

- **Empty State**: "Create Course" CTA

**Design**: List view with comprehensive information

---

### Create Course (`tutor/courses/create.blade.php`)
**Route**: `/tutor/courses/create`
**Purpose**: Course creation form

**Layout**: Two-column (2/3 form + 1/3 sidebar)

**Main Form Sections**:
1. **Basic Information**:
   - Title (auto-generates slug)
   - URL slug (optional)
   - Short description (160 chars)
   - Full description

2. **Learning Outcomes**:
   - Dynamic list (Alpine.js)
   - Add/remove fields
   - Bullet points of what students learn

3. **Requirements**:
   - Dynamic list (Alpine.js)
   - Prerequisites for enrollment

**Sidebar Sections**:
1. **Publish Settings**:
   - Status (Draft/Published)
   - Featured checkbox

2. **Course Details**:
   - Category dropdown
   - Level selection
   - Language selection
   - Duration (hours)

3. **Pricing**:
   - Free course checkbox
   - Price field
   - Sale price field

4. **Thumbnail Upload**:
   - Image file input
   - Recommended size info

5. **Certificate Settings**:
   - Certificate available checkbox
   - Lifetime access checkbox

**Interactive Features**:
- Dynamic field management
- Price field disable on "Free" check
- Form validation
- Preview before publish

---

## Admin Views

### Admin Dashboard (`admin/dashboard.blade.php`)
**Route**: `/admin/dashboard`
**Layout**: Admin layout with sidebar

**Sections**:
1. **Stats Cards** (4 columns):
   - Total Courses (with link)
   - Total Users (with link)
   - Total Enrollments (with link)
   - Total Revenue (with link)
   - Each has icon and footer link

2. **Recent Enrollments**:
   - List view with avatars
   - Student name
   - Course title
   - Time ago
   - Amount paid badge

3. **Popular Courses**:
   - Course title
   - Instructor name
   - Student count badge

4. **Quick Actions** (4 cards):
   - New Course
   - New User
   - Categories
   - Reports
   - Icon-based cards with hover effects

**Design**: Professional admin dashboard with KPIs and quick access

---

### User Management (`admin/users/index.blade.php`)
**Route**: `/admin/users`
**Purpose**: Manage all system users

**Features**:
- **Header**: Title + "Create User" button
- **Filter Panel**:
  - Search by name/email
  - Role filter
  - Status filter (Active/Inactive/Banned)
  - Filter/Reset buttons

- **User Table**:
  - Columns:
    - User (avatar + name + email)
    - Role (colored badges)
    - Status (active/inactive/banned badges)
    - Email Verified (checkmark icon)
    - Joined date
    - Actions (View/Edit/Delete)
  - Avatar placeholders (initials)
  - Conditional row styling
  - Pagination

**Empty State**: "No users found" with create button

---

### User Profile (`admin/users/show.blade.php`)
**Route**: `/admin/users/{user}`
**Purpose**: Detailed user information

**Layout**: Two-column (2/3 profile + 1/3 sidebar)

**Main Profile Card**:
- Large avatar (initials)
- Name and email
- Role badges
- **Basic Information Grid**:
  - Status (with ban info if applicable)
  - Email verification
  - Phone
  - Country
  - Joined date
  - Last updated
  - Last active
  - Bio (if available)

**Role-Specific Information**:
- **Tutor Info**:
  - Total courses
  - Published courses
  - Total students

- **Student Info**:
  - Enrolled courses
  - Completed courses
  - Certificates

**Sidebar**:
1. **Quick Actions**:
   - Edit Profile
   - Verify Email (if not verified)
   - Activate/Deactivate
   - Delete User

2. **Account Statistics**:
   - Total logins
   - Activity logs
   - Quiz attempts (students)

**Design**: Comprehensive user overview with actionable buttons

---

### Edit User (`admin/users/edit.blade.php`)
**Route**: `/admin/users/{user}/edit`
**Purpose**: Update user information

**Layout**: Two-column (2/3 form + 1/3 sidebar)

**Main Form**:
1. **Basic Information**:
   - Full name
   - Email
   - Phone
   - Country
   - Bio (textarea)

2. **Change Password**:
   - New password (optional)
   - Confirm password
   - Note: Leave blank to keep current

**Sidebar**:
1. **Roles**: Checkbox list
2. **Status**:
   - Active checkbox
   - Email verified checkbox

3. **Ban User**:
   - Ban until (datetime picker)
   - Ban reason (textarea)

4. **Action Buttons**:
   - Update User (primary)
   - Cancel (secondary)

**Features**:
- Pre-filled values
- Conditional fields
- Validation errors display
- Success message

---

### Create User (`admin/users/create.blade.php`)
**Route**: `/admin/users/create`
**Purpose**: Add new users to the system

**Similar to Edit User but includes**:
- Password fields (required)
- Password confirmation
- Send welcome email checkbox
- Additional settings:
  - Timezone selection
  - Language preference

**Default States**:
- Active account: checked
- Send welcome email: checked

---

### Category Management (`admin/categories/index.blade.php`)
**Route**: `/admin/categories`
**Purpose**: Manage course categories

**Features**:
- **Stats Cards** (4 columns):
  - Total Categories
  - Active Categories
  - Total Courses
  - Parent Categories

- **Filter Panel**:
  - Search
  - Status filter
  - Type filter (Parent/Sub-categories)

- **Category Table**:
  - Columns:
    - Category (icon + name + description)
    - Slug (monospace font)
    - Parent
    - Courses count badge
    - Status badge
    - Order number
    - Actions (View/Edit/Delete)

**Design**: Hierarchical structure display with visual hierarchy

---

## Design Patterns & Components

### Card Component Pattern
**Structure**:
```blade
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Card Title</h2>
    </div>
    <div class="p-6">
        <!-- Card content -->
    </div>
</div>
```

**Usage**: Consistent card style throughout application

---

### Stats Card Pattern
**Structure**:
```blade
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                <!-- Icon SVG -->
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Label</p>
                <p class="text-2xl font-semibold text-gray-900">Value</p>
            </div>
        </div>
    </div>
</div>
```

**Colors**:
- Indigo: Primary actions/courses
- Green: Success/completed
- Yellow: Warnings/pending
- Purple: Students/users
- Blue: Enrollments
- Red: Errors/critical

---

### Button Patterns

**Primary Button** (Breeze component):
```blade
<x-primary-button>
    Action Text
</x-primary-button>
```
- Background: Indigo-600
- Hover: Indigo-700
- Text: White
- Uppercase tracking

**Secondary Button**:
```blade
<x-secondary-button>
    Cancel
</x-secondary-button>
```
- Background: Gray-200
- Hover: Gray-300
- Text: Gray-700

**Danger Button**:
```blade
<button class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
    Delete
</button>
```

---

### Form Field Pattern

**Using Breeze Components**:
```blade
<div>
    <x-input-label for="field_name" :value="__('Field Label')" />
    <x-text-input id="field_name" name="field_name" type="text"
                  class="mt-1 block w-full" :value="old('field_name')"
                  required autofocus />
    <x-input-error class="mt-2" :messages="$errors->get('field_name')" />
    <p class="mt-1 text-sm text-gray-500">Helper text</p>
</div>
```

**Components**:
- `x-input-label`: Consistent label styling
- `x-text-input`: Styled input field with focus states
- `x-input-error`: Error message display

---

### Empty State Pattern

**Structure**:
```blade
<div class="text-center py-12">
    <svg class="mx-auto h-12 w-12 text-gray-400">
        <!-- Icon -->
    </svg>
    <h3 class="mt-2 text-sm font-medium text-gray-900">No items found</h3>
    <p class="mt-1 text-sm text-gray-500">Get started by creating a new item.</p>
    <div class="mt-6">
        <a href="#" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            Create Item
        </a>
    </div>
</div>
```

**Usage**: Displayed when lists/grids have no data

---

### Badge Pattern

**Status Badges**:
```blade
<!-- Active/Published -->
<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
    Active
</span>

<!-- Draft/Pending -->
<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
    Draft
</span>

<!-- Inactive/Archived -->
<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
    Inactive
</span>
```

**Color Mapping**:
- Green: Active, Published, Completed, Success
- Yellow: Draft, Pending, Warning
- Red: Inactive, Failed, Error
- Blue: Info, Tutor role
- Purple: Super Admin role
- Indigo: Primary, Featured

---

### Progress Bar Pattern

**Structure**:
```blade
<div class="mb-3">
    <div class="flex justify-between text-sm text-gray-600 mb-1">
        <span>Progress</span>
        <span>{{ $progress }}%</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2">
        <div class="bg-indigo-600 h-2 rounded-full transition-all"
             style="width: {{ $progress }}%"></div>
    </div>
</div>
```

**Usage**: Course progress, completion tracking

---

## Responsive Design

### Breakpoints (Tailwind)
- **sm**: 640px - Small tablets
- **md**: 768px - Tablets
- **lg**: 1024px - Small desktops
- **xl**: 1280px - Large desktops
- **2xl**: 1536px - Extra large screens

### Grid Patterns

**2-Column on Large Screens**:
```blade
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Content -->
</div>
```

**3-Column Product Grid**:
```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Course cards -->
</div>
```

**4-Column Stats**:
```blade
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <!-- Stat cards -->
</div>
```

### Mobile Considerations
- Hamburger menu for navigation
- Stacked cards on mobile
- Collapsible sections
- Touch-friendly button sizes (min 44x44px)
- Readable font sizes (minimum 16px)

---

## Interactive Components

### Alpine.js Usage

**Dropdown Menu**:
```blade
<div x-data="{ open: false }" @click.away="open = false">
    <button @click="open = !open">
        Toggle
    </button>
    <div x-show="open" x-transition>
        <!-- Dropdown content -->
    </div>
</div>
```

**Collapsible Section**:
```blade
<div x-data="{ expanded: false }">
    <button @click="expanded = !expanded">
        Toggle Section
    </button>
    <div x-show="expanded" x-collapse>
        <!-- Content -->
    </div>
</div>
```

**Dynamic Form Fields**:
```blade
<div x-data="{ outcomes: [''] }">
    <template x-for="(outcome, index) in outcomes" :key="index">
        <input x-model="outcomes[index]" :name="'outcomes[]'" />
    </template>
    <button @click="outcomes.push('')">Add More</button>
</div>
```

---

## Flash Messages

### Implementation
Flash messages are displayed in layouts for:
- Success messages (green)
- Error messages (red)
- Info messages (blue)

**Structure**:
```blade
@if (session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-green-700">{{ session('success') }}</p>
    </div>
@endif
```

**Placement**:
- Admin layout: Below header, above content
- App layout: In main content area
- Forms: Above form fields

---

## Accessibility Features

### Implemented Features
1. **Semantic HTML**: Proper use of headings, nav, main, footer
2. **ARIA Labels**: Screen reader support on icons
3. **Keyboard Navigation**: Tab through interactive elements
4. **Focus States**: Visible focus indicators on all interactive elements
5. **Color Contrast**: WCAG AA compliant color combinations
6. **Alt Text**: All images have descriptive alt attributes
7. **Form Labels**: All inputs properly labeled
8. **Error Messages**: Associated with form fields

### Focus States
All interactive elements have visible focus states:
```css
focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
```

---

## Performance Optimizations

### Image Optimization
- Placeholder images (via.placeholder.com) for development
- Lazy loading on course cards
- Responsive images with proper dimensions
- WebP format support (when implemented)

### CSS Optimization
- Tailwind CSS purge in production
- Vite for asset compilation
- Critical CSS inline (Breeze default)
- Minimal custom CSS

### JavaScript Optimization
- Alpine.js (lightweight, 15KB)
- Deferred script loading
- No jQuery dependency
- Minimal third-party libraries

### Loading States
- Skeleton screens (can be implemented)
- Loading spinners on form submission
- Progressive enhancement

---

## Browser Support

### Supported Browsers
- **Chrome**: Latest 2 versions
- **Firefox**: Latest 2 versions
- **Safari**: Latest 2 versions
- **Edge**: Latest 2 versions
- **Mobile Safari**: iOS 12+
- **Chrome Mobile**: Latest

### Polyfills
Not required for modern browsers. Alpine.js and Tailwind handle compatibility.

---

## Future Enhancements

### Planned Features
1. **Dark Mode**: Toggle between light/dark themes
2. **Internationalization**: Multi-language support
3. **PWA Support**: Offline capability
4. **Advanced Search**: Filters, sorting, pagination
5. **Real-time Notifications**: WebSockets integration
6. **Video Player**: Custom video player with controls
7. **Quiz Builder**: Drag-and-drop interface
8. **Analytics Dashboard**: Charts and graphs
9. **File Manager**: Upload and organize course materials
10. **Live Chat**: Student-tutor communication

### Component Library
Building a comprehensive component library for:
- Modals
- Tabs
- Accordions
- Tooltips
- Toast notifications
- Data tables with sorting
- Calendar/date picker
- Rich text editor

---

## Testing Considerations

### Manual Testing Checklist
- [ ] All forms validate correctly
- [ ] Flash messages display properly
- [ ] Navigation works on all screen sizes
- [ ] Images load correctly
- [ ] Links navigate to correct pages
- [ ] Buttons trigger expected actions
- [ ] Responsive design on mobile/tablet/desktop
- [ ] Cross-browser compatibility
- [ ] Accessibility with screen reader
- [ ] Form error states display

### Browser Testing
Test on:
- Desktop: Chrome, Firefox, Safari, Edge
- Mobile: Safari (iOS), Chrome (Android)
- Tablet: iPad Safari, Android Chrome

---

## Development Guidelines

### Code Style
1. **Indentation**: 4 spaces
2. **Naming**: kebab-case for CSS classes, camelCase for JavaScript
3. **Comments**: Explain complex logic
4. **Blade Directives**: Use spaces around braces `{{ }}`
5. **Tailwind**: Maintain class order (layout → spacing → colors → effects)

### Component Organization
- Keep components small and focused
- Extract reusable patterns
- Use Blade components over partials when possible
- Maintain consistent naming

### Git Workflow
- Feature branches for new views
- Descriptive commit messages
- Test before committing
- Keep views and controllers in sync

---

## Troubleshooting

### Common Issues

**Issue**: Styles not applying
- **Solution**: Run `npm run dev` or `npm run build`
- Check Vite is running
- Clear browser cache

**Issue**: Alpine.js not working
- **Solution**: Ensure Alpine script is loaded
- Check for JavaScript errors in console
- Verify x-data initialization

**Issue**: Layout broken on mobile
- **Solution**: Check responsive classes (sm:, md:, lg:)
- Test with browser dev tools
- Verify viewport meta tag

**Issue**: Images not displaying
- **Solution**: Check file paths
- Verify storage is linked (`php artisan storage:link`)
- Check image permissions

---

## Deployment Checklist

### Pre-Deployment
- [ ] Run `npm run build` for production assets
- [ ] Test all views in production mode
- [ ] Optimize images
- [ ] Verify all routes work
- [ ] Check mobile responsiveness
- [ ] Test cross-browser compatibility
- [ ] Review accessibility
- [ ] Check SEO meta tags
- [ ] Test forms and validation
- [ ] Verify error pages (404, 500)

### Post-Deployment
- [ ] Monitor for JavaScript errors
- [ ] Check page load times
- [ ] Verify all assets load correctly
- [ ] Test critical user flows
- [ ] Check analytics integration

---

## Resources

### Documentation
- [Laravel Breeze](https://laravel.com/docs/11.x/starter-kits#breeze)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Alpine.js](https://alpinejs.dev/)
- [Heroicons](https://heroicons.com/)

### Tools
- [Tailwind UI](https://tailwindui.com/) - Premium components
- [Headless UI](https://headlessui.com/) - Unstyled components
- [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) - Development tool

---

## Changelog

### Version 1.0.0 (Current)
- Initial frontend development
- All major views implemented
- Breeze integration complete
- Admin panel with sidebar
- Responsive design implemented
- Alpine.js interactivity added
- Role-based views created

---

## Credits

**Framework**: Laravel 12
**Starter Kit**: Laravel Breeze
**CSS Framework**: Tailwind CSS 3.x
**JavaScript Framework**: Alpine.js 3.x
**Icons**: Heroicons
**Fonts**: Figtree (Bunny Fonts)

---

## Contact & Support

For questions or issues related to frontend development:
- Check Laravel documentation
- Review Tailwind CSS docs
- Test in browser dev tools
- Verify component structure

---

**Document Version**: 1.0
**Last Updated**: 2025-11-11
**Total Views Created**: 50+
**Total Components**: 15+
**Lines of Blade Code**: 10,000+
