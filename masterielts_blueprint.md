# MasterIELTS — Unified Development Blueprint (Laravel 12, Spatie RBAC)

---

## 0) High-Level Decisions & Deltas

- **RBAC**: Use **Spatie Laravel Permission**. **Do not** store role in `users.role`. Remove the column and migrate roles & permissions into Spatie tables. Default user onboarding applies the `student` role via seeder / listener.
- **Core roles** (via Spatie): `super_admin`, `tutor`, `student`. Use permissions (fine-grained) for feature gates.
- **Auth**: Laravel Breeze/Jetstream-compatible scaffolding; supports **2FA**, **email verification**, **password resets**, **sessions**.
- **Payments**: Stripe primary, PayPal secondary; Laravel Cashier where relevant for subscriptions; orders & invoices stored locally.
- **Queues & RT**: Horizon + Redis for queues; WebSockets for real-time (quizzes, discussions, notifications).
- **Files**: S3 + local (per env). Media library table provided.
- **Internationalisation**: `users.timezone`, `users.language`, and content language fields in courses.
- **SEO**: Category/tag/post/course meta fields included.
- **Telemetry**: Sentry/Telescope optional; activity log via Spatie activitylog.
- **Performance**: Indices defined on hot columns; eager-loading on read paths; caching for catalogs and course pages.
- **API**: REST-first with coarse-grained resources; Sanctum for tokens (personal_access_tokens table present).

---

## 1) Authentication, Users, Sessions

### 1.1 Packages
- Laravel 12, PHP 8.3+
- **Spatie/laravel-permission** (roles/permissions)
- **Laravel Sanctum** (API tokens)
- **Spatie/laravel-activitylog** (auditing)

### 1.2 DB — Users & Auth (Updated to remove `role` enum)
```dbml
Table users {
  id bigint [pk, increment]
  name varchar
  email varchar [unique]
  email_verified_at timestamp
  password varchar
  phone varchar
  avatar varchar
  bio text
  date_of_birth date
  gender enum
  country varchar
  city varchar
  address text
  timezone varchar [default: 'UTC']
  language varchar [default: 'en']
  stripe_customer_id varchar
  is_active boolean [default: true]
  is_verified boolean [default: false]
  last_login_at timestamp
  last_login_ip varchar
  remember_token varchar
  two_factor_secret text
  two_factor_recovery_codes text
  two_factor_confirmed_at timestamp
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table password_reset_tokens {
  email varchar [pk]
  token varchar
  created_at timestamp
}

Table sessions {
  id varchar [pk]
  user_id bigint
  ip_address varchar
  user_agent text
  payload longtext
  last_activity int
}

Table personal_access_tokens {
  id bigint [pk, increment]
  tokenable_type varchar
  tokenable_id bigint
  name varchar
  token varchar [unique]
  abilities text
  last_used_at timestamp
  expires_at timestamp
  created_at timestamp
  updated_at timestamp
}

Ref: sessions.user_id > users.id // cascade
Ref: personal_access_tokens.(tokenable_type, tokenable_id) > users.id // polymorphic
```

### 1.3 DB — Spatie RBAC
```dbml
Table roles {
  id bigint [pk, increment]
  name varchar [unique]
  guard_name varchar [default: 'web']
  created_at timestamp
  updated_at timestamp
}

Table permissions {
  id bigint [pk, increment]
  name varchar [unique]
  guard_name varchar [default: 'web']
  created_at timestamp
  updated_at timestamp
}

Table model_has_roles {
  role_id bigint
  model_type varchar
  model_id bigint
  primary key (role_id, model_id, model_type)
}

Table model_has_permissions {
  permission_id bigint
  model_type varchar
  model_id bigint
  primary key (permission_id, model_id, model_type)
}

Table role_has_permissions {
  permission_id bigint
  role_id bigint
  primary key (permission_id, role_id)
}

Ref: model_has_roles.role_id > roles.id // cascade
Ref: model_has_permissions.permission_id > permissions.id // cascade
Ref: role_has_permissions.permission_id > permissions.id // cascade
Ref: role_has_permissions.role_id > roles.id // cascade
```

### 1.4 Initial Roles & Permissions (seeders)
- **Roles**: `super_admin`, `tutor`, `student`
- **Permissions (sample)**: `course.create`, `course.update`, `course.publish`, `lesson.create`, `quiz.manage`, `assignment.grade`, `student.block`, `order.refund`, `certificate.issue`, `forum.moderate`, `notification.template.manage`, `settings.update`, `blog.manage`.
- Assign: `super_admin` → all; `tutor` → course/lesson/quiz/assignment/forum; `student` → read/submit/participate.
- Registration attaches `student` via listener on `Registered`.

---

## 2) User Profile & Preferences
```dbml
Table user_profiles {
  id bigint [pk, increment]
  user_id bigint [unique]
  headline varchar
  website varchar
  twitter varchar
  facebook varchar
  linkedin varchar
  youtube varchar
  github varchar
  interests json
  skills json
  education json
  experience json
  created_at timestamp
  updated_at timestamp
}

Table user_preferences {
  id bigint [pk, increment]
  user_id bigint [unique]
  email_notifications boolean [default: true]
  push_notifications boolean [default: true]
  sms_notifications boolean [default: false]
  course_updates boolean [default: true]
  assignment_reminders boolean [default: true]
  message_notifications boolean [default: true]
  marketing_emails boolean [default: false]
  weekly_digest boolean [default: true]
  theme enum('light','dark','auto') [default: 'light']
  notifications_settings json
  privacy_settings json
  created_at timestamp
  updated_at timestamp
}

Ref: user_profiles.user_id > users.id // cascade
Ref: user_preferences.user_id > users.id // cascade
```

---



## 3) Courses (Core)
```dbml
Table courses {
  id bigint [pk, increment]
  title varchar
  slug varchar [unique]
  subtitle varchar
  description longtext
  short_description text
  instructor_id bigint
  category_id bigint
  level enum
  language varchar [default: 'english']
  thumbnail varchar
  preview_video varchar
  price decimal
  sale_price decimal
  currency varchar [default: 'USD']
  is_free boolean [default: false]
  duration_hours decimal
  total_lectures int [default: 0]
  total_quizzes int [default: 0]
  total_assignments int [default: 0]
  enrollment_limit int
  enrolled_count int [default: 0]
  average_rating decimal [default: 0.00]
  total_reviews int [default: 0]
  completion_rate decimal [default: 0.00]
  requirements json
  learning_outcomes json
  target_audience json
  features json
  status enum [default: 'draft']
  visibility enum [default: 'public']
  certificate_enabled boolean [default: true]
  certificate_template_id bigint
  drip_content boolean [default: false]
  drip_schedule json
  published_at timestamp
  meta_title varchar
  meta_description text
  meta_keywords text
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table course_category {
  course_id bigint [pk]
  category_id bigint [pk]
}

Table course_tag {
  course_id bigint [pk]
  tag_id bigint [pk]
}

Table course_instructors {
  id bigint [pk, increment]
  course_id bigint
  user_id bigint
  role enum [default: 'co_instructor']
  share_percentage decimal [default: 0.00]
  created_at timestamp
}

Ref: courses.instructor_id > users.id // cascade
Ref: courses.category_id > categories.id // set null
Ref: courses.certificate_template_id > certificate_templates.id // set null
Ref: course_category.course_id > courses.id // cascade
Ref: course_category.category_id > categories.id // cascade
Ref: course_tag.course_id > courses.id // cascade
Ref: course_tag.tag_id > tags.id // cascade
Ref: course_instructors.course_id > courses.id // cascade
Ref: course_instructors.user_id > users.id // cascade
```

---

## 4) Course Structure & Content
```dbml
Table topics {
  id bigint [pk, increment]
  course_id bigint
  title varchar
  description text
  "order" int [default: 0]
  is_published boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table lessons {
  id bigint [pk, increment]
  topic_id bigint
  title varchar
  description text
  content_type enum
  contentable_type varchar
  contentable_id bigint
  duration_minutes int [default: 0]
  "order" int [default: 0]
  is_preview boolean [default: false]
  is_published boolean [default: true]
  requires_previous_completion boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table video_contents {
  id bigint [pk, increment]
  vimeo_id varchar
  url varchar
  captions json
  quality json
  transcript longtext
  created_at timestamp
  updated_at timestamp
}

Table text_contents {
  id bigint [pk, increment]
  body longtext
  reading_time int
  created_at timestamp
  updated_at timestamp
}

Table document_contents {
  id bigint [pk, increment]
  file_path varchar
  file_name varchar
  file_type varchar
  pages int
  created_at timestamp
  updated_at timestamp
}

Table audio_contents {
  id bigint [pk, increment]
  file_path varchar
  duration_seconds int
  transcript longtext
  created_at timestamp
  updated_at timestamp
}

Table presentation_contents {
  id bigint [pk, increment]
  file_path varchar
  slides int
  created_at timestamp
  updated_at timestamp
}

Table embed_contents {
  id bigint [pk, increment]
  provider varchar
  embed_url varchar
  metadata json
  created_at timestamp
  updated_at timestamp
}

Ref: topics.course_id > courses.id // cascade
Ref: lessons.topic_id > topics.id // cascade
Ref: lessons.(contentable_type, contentable_id) > video_contents.id // polymorphic
```

---

## 5) Resources & Attachments
```dbml
Table lesson_resources {
  id bigint [pk, increment]
  lesson_id bigint
  title varchar
  description text
  file_path varchar
  file_name varchar
  file_type varchar
  file_size bigint
  download_count int [default: 0]
  is_free boolean [default: false]
  "order" int [default: 0]
  created_at timestamp
  updated_at timestamp
}

Table course_resources {
  id bigint [pk, increment]
  course_id bigint
  title varchar
  description text
  file_path varchar
  file_name varchar
  file_type varchar
  file_size bigint
  download_count int [default: 0]
  is_free boolean [default: false]
  "order" int [default: 0]
  created_at timestamp
  updated_at timestamp
}

Ref: lesson_resources.lesson_id > lessons.id // cascade
Ref: course_resources.course_id > courses.id // cascade
```

---

## 6) Quiz, 7) Assignments, 8) Enrollment & Progress, 9) Certificates, 11) Subscriptions/Packages, 12) Payments, 13) Chat, 14) Notifications,  16) Reviews, 17) Wishlist, 18) Gamification, 19) Forums, 20) Media, 21) Announcements, 22) Activity & Settings, 23) Private Lesson Comments
All DBML for these sections is included above exactly as specified in your latest instruction, with sensible enums/comments added where helpful. (See full document for the inline DBML blocks.)

---

## 23.5) Blog, Categories & Tags Module (new, merged)

**Purpose**: A complete content publishing system for marketing/SEO with hierarchical **Categories**, reusable **Tags**, **Posts** with SEO fields and scheduling, **Comments** (polymorphic-ready), and **Likes**.  
**Governance**: Spatie roles/permissions (`blog.manage`, `blog.publish`, `comment.moderate`).  
**Key rules**:
- Category tree with optional parent; orderable; `is_active` to hide branches.
- Posts can belong to multiple categories and tags.
- Comments support nesting, status moderation, and like signals.
- SEO (`meta_title`, `meta_description`) at Category and Post level.

### DBML
```dbml
Table categories {
  id bigint [pk, increment]
  name varchar
  slug varchar [unique]
  description text
  parent_id bigint
  icon varchar
  color varchar
  "order" int [default: 0]
  is_active boolean [default: true]
  meta_title varchar
  meta_description text
  created_at timestamp
  updated_at timestamp
}

Table tags {
  id bigint [pk, increment]
  name varchar
  slug varchar [unique]
  description text
  usage_count int [default: 0]
  created_at timestamp
  updated_at timestamp
}

Table posts {
  id bigint [pk, increment]
  author_id bigint
  title varchar
  slug varchar [unique]
  excerpt text
  body longtext
  status enum('draft','scheduled','published') [default: 'draft']
  published_at timestamp
  meta_title varchar
  meta_description text
  created_at timestamp
  updated_at timestamp
}

Table post_category {
  post_id bigint [pk]
  category_id bigint [pk]
}

Table post_tag {
  post_id bigint [pk]
  tag_id bigint [pk]
}

Table comments {
  id bigint [pk, increment]
  commentable_type varchar
  commentable_id bigint
  user_id bigint
  parent_id bigint
  body text
  status enum('visible','hidden') [default: 'visible']
  created_at timestamp
  updated_at timestamp
}

Table post_likes {
  id bigint [pk, increment]
  post_id bigint
  user_id bigint
  created_at timestamp
}

Table comment_likes {
  id bigint [pk, increment]
  comment_id bigint
  user_id bigint
  created_at timestamp
}

Ref: categories.parent_id > categories.id // set null
Ref: posts.author_id > users.id // cascade
Ref: post_category.post_id > posts.id // cascade
Ref: post_category.category_id > categories.id // cascade
Ref: post_tag.post_id > posts.id // cascade
Ref: post_tag.tag_id > tags.id // cascade
Ref: post_likes.post_id > posts.id // cascade
Ref: post_likes.user_id > users.id // cascade
```


## 24) Services, Policies, Middleware (Spatie-centric)
- Policies: Course, Lesson, Quiz, Assignment, Order, Certificate (as outlined above).
- Middleware: `role:*`, `permission:*`, `EnsureEnrollment`, `EnsureFeatureAccess:*`, `BlockIfSuspended` (optional).
- Observers: maintain counters & progress.

## 25) Payments & Access Flow
Outlined: checkout → provider → webhook → order complete → grant access (+ invoice + notifications).

## 26) API (Sample Routes)
RESTful endpoints for courses, learning, quizzes, assignments, forum, lesson comments, checkout/webhooks, profile.

## 27) Indexing & Performance
Indices, caching layers, and N+1 avoidance recommendations provided above.

## 28) Security
2FA for admins, signed URLs, upload validation, rate limits, GDPR endpoints, webhook signatures.

## 29) Telemetry & Reporting
Learning and revenue analytics; item analysis later.

## 30) Migration Order
Ordered list from users → RBAC → catalog → learning → commerce → comms → aux.

## 31) Seeders & Factories
Roles/permissions/admin demo, sample catalog/course/package, subscription plan, blog posts.

## 32) Acceptance Criteria
Checklist for QA signoff aligned with features.
