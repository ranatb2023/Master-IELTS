
/* MasterIELTS — DBML Schema (Spatie RBAC) 
   Generated: 2025-11-10 05:20 UTC
 
*/

//// 1) Authentication, Users, Sessions ///////////////////////////////////////////////////////////

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
// Polymorphic note: personal_access_tokens.(tokenable_type, tokenable_id) -> users.id
// (Left as annotation for developers.)


//// 1.3) Spatie RBAC /////////////////////////////////////////////////////////////////////////////

Table roles {
  id bigint [pk, increment]
  name varchar [unique] // super_admin, tutor, student
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
  Note: 'primary key (role_id, model_id, model_type)'
}

Table model_has_permissions {
  permission_id bigint
  model_type varchar
  model_id bigint
  Note: 'primary key (permission_id, model_id, model_type)'
}

Table role_has_permissions {
  permission_id bigint
  role_id bigint
  Note: 'primary key (permission_id, role_id)'
}

Ref: model_has_roles.role_id > roles.id // cascade
Ref: model_has_permissions.permission_id > permissions.id // cascade
Ref: role_has_permissions.permission_id > permissions.id // cascade
Ref: role_has_permissions.role_id > roles.id // cascade


//// 2) User Profiles & Preferences ///////////////////////////////////////////////////////////////

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




//// 4) Courses (Core) ////////////////////////////////////////////////////////////////////////////

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
  status enum [default: 'draft'] // draft/review/published/archived
  visibility enum [default: 'public'] // public/private/unlisted
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


//// 5) Course Structure (Topics → Lessons → Content) /////////////////////////////////////////////

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
  content_type enum // video,text,document,audio,presentation,embed
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

// Content tables
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
  provider varchar // youtube, loom, etc.
  embed_url varchar
  metadata json
  created_at timestamp
  updated_at timestamp
}

Ref: topics.course_id > courses.id // cascade
Ref: lessons.topic_id > topics.id // cascade
// Polymorphic note: lessons.(contentable_type, contentable_id) -> content tables


//// 6) Resources & Attachments ///////////////////////////////////////////////////////////////////

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


//// 7) Quiz System /////////////////////////////////////////////////////////////////////////////

Table quizzes {
  id bigint [pk, increment]
  topic_id bigint
  title varchar
  description text
  instructions text
  time_limit int
  passing_score decimal [default: 70.00]
  max_attempts int
  shuffle_questions boolean [default: false]
  shuffle_answers boolean [default: false]
  show_answers enum [default: 'after_submission']
  show_correct_answers boolean [default: true]
  require_passing boolean [default: false]
  certificate_eligible boolean [default: false]
  "order" int [default: 0]
  is_published boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table questions {
  id bigint [pk, increment]
  quiz_id bigint
  type enum // mcq_single, mcq_multiple, true_false, short_answer, passage_mcq
  question text
  description text
  points decimal [default: 1.00]
  "order" int [default: 0]
  media_type enum [default: 'none']
  media_url varchar
  explanation text
  difficulty enum [default: 'medium']
  created_at timestamp
  updated_at timestamp
}

Table question_options {
  id bigint [pk, increment]
  question_id bigint
  option_text text
  is_correct boolean [default: false]
  "order" int [default: 0]
  explanation text
  created_at timestamp
  updated_at timestamp
}

Table quiz_attempts {
  id bigint [pk, increment]
  quiz_id bigint
  user_id bigint
  score decimal
  total_points decimal
  percentage decimal
  passed boolean [default: false]
  time_taken int
  started_at timestamp
  submitted_at timestamp
  graded_at timestamp
  attempt_number int
  answers json
  created_at timestamp
  updated_at timestamp
}

Table quiz_answers {
  id bigint [pk, increment]
  attempt_id bigint
  question_id bigint
  answer text
  selected_options json
  is_correct boolean
  points_earned decimal [default: 0.00]
  feedback text
  created_at timestamp
  updated_at timestamp
}

Ref: quizzes.topic_id > topics.id // cascade
Ref: questions.quiz_id > quizzes.id // cascade
Ref: question_options.question_id > questions.id // cascade
Ref: quiz_attempts.quiz_id > quizzes.id // cascade
Ref: quiz_attempts.user_id > users.id // cascade
Ref: quiz_answers.attempt_id > quiz_attempts.id // cascade
Ref: quiz_answers.question_id > questions.id // cascade


//// 8) Assignment System ////////////////////////////////////////////////////////////////////////

Table assignments {
  id bigint [pk, increment]
  topic_id bigint
  title varchar
  description longtext
  instructions text
  max_points decimal [default: 100.00]
  passing_points decimal [default: 70.00]
  due_date timestamp
  allow_late_submission boolean [default: false]
  late_penalty decimal [default: 0.00]
  max_file_size int [default: 10]
  allowed_file_types json
  max_files int [default: 5]
  auto_grade boolean [default: false]
  require_passing boolean [default: false]
  "order" int [default: 0]
  is_published boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table assignment_submissions {
  id bigint [pk, increment]
  assignment_id bigint
  user_id bigint
  content longtext
  files json
  score decimal
  feedback text
  status enum [default: 'draft'] // draft/submitted/graded/returned
  passed boolean
  submitted_at timestamp
  graded_at timestamp
  graded_by bigint
  is_late boolean [default: false]
  attempt_number int [default: 1]
  created_at timestamp
  updated_at timestamp
}

Table assignment_files {
  id bigint [pk, increment]
  submission_id bigint
  file_path varchar
  file_name varchar
  file_type varchar
  file_size bigint
  created_at timestamp
}

Table assignment_rubrics {
  id bigint [pk, increment]
  assignment_id bigint
  criteria varchar
  description text
  max_points decimal
  "order" int [default: 0]
  created_at timestamp
  updated_at timestamp
}

Table submission_rubric_scores {
  id bigint [pk, increment]
  submission_id bigint
  rubric_id bigint
  points decimal
  feedback text
  created_at timestamp
  updated_at timestamp
}

Ref: assignments.topic_id > topics.id // cascade
Ref: assignment_submissions.assignment_id > assignments.id // cascade
Ref: assignment_submissions.user_id > users.id // cascade
Ref: assignment_submissions.graded_by > users.id // set null
Ref: assignment_files.submission_id > assignment_submissions.id // cascade
Ref: assignment_rubrics.assignment_id > assignments.id // cascade
Ref: submission_rubric_scores.submission_id > assignment_submissions.id // cascade
Ref: submission_rubric_scores.rubric_id > assignment_rubrics.id // cascade


//// 9) Enrollment & Progress Tracking ////////////////////////////////////////////////////////////

Table enrollments {
  id bigint [pk, increment]
  user_id bigint
  course_id bigint
  package_access_id bigint
  enrolled_at timestamp
  expires_at timestamp
  status enum [default: 'active'] // active/expired/canceled
  progress_percentage decimal [default: 0.00]
  last_accessed_at timestamp
  completed_at timestamp
  certificate_issued boolean [default: false]
  created_at timestamp
  updated_at timestamp
}

Table progress {
  id bigint [pk, increment]
  user_id bigint
  progressable_type varchar
  progressable_id bigint
  status enum [default: 'not_started'] // not_started/in_progress/completed
  completed_at timestamp
  time_spent int [default: 0]
  score decimal
  last_position varchar
  notes text
  created_at timestamp
  updated_at timestamp
}

Table course_progress {
  id bigint [pk, increment]
  user_id bigint
  course_id bigint
  progress_percentage decimal [default: 0.00]
  completed_lessons int [default: 0]
  total_lessons int
  completed_quizzes int [default: 0]
  total_quizzes int
  completed_assignments int [default: 0]
  total_assignments int
  average_quiz_score decimal
  average_assignment_score decimal
  total_time_spent int [default: 0]
  last_accessed_at timestamp
  started_at timestamp
  completed_at timestamp
  created_at timestamp
  updated_at timestamp
}

Table learning_sessions {
  id bigint [pk, increment]
  user_id bigint
  course_id bigint
  lesson_id bigint
  started_at timestamp
  ended_at timestamp
  duration int
  activity_data json
  created_at timestamp
}

Ref: enrollments.user_id > users.id // cascade
Ref: enrollments.course_id > courses.id // cascade
Ref: enrollments.package_access_id > user_package_access.id // set null
Ref: progress.user_id > users.id // cascade
Ref: course_progress.user_id > users.id // cascade
Ref: course_progress.course_id > courses.id // cascade
Ref: learning_sessions.user_id > users.id // cascade
Ref: learning_sessions.course_id > courses.id // cascade
Ref: learning_sessions.lesson_id > lessons.id // set null


//// 10) Certificate System ///////////////////////////////////////////////////////////////////////

Table certificate_templates {
  id bigint [pk, increment]
  name varchar
  description text
  design json
  fields json
  orientation enum [default: 'landscape']
  page_size varchar [default: 'A4']
  background_image varchar
  is_default boolean [default: false]
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table certificates {
  id bigint [pk, increment]
  user_id bigint
  course_id bigint
  certificate_template_id bigint
  certificate_number varchar [unique]
  issue_date date
  expiry_date date
  file_path varchar
  metadata json
  verification_hash varchar [unique]
  verification_url varchar
  is_revoked boolean [default: false]
  revoked_at timestamp
  revoked_reason text
  download_count int [default: 0]
  last_downloaded_at timestamp
  created_at timestamp
  updated_at timestamp
}

Ref: certificates.user_id > users.id // cascade
Ref: certificates.course_id > courses.id // cascade
Ref: certificates.certificate_template_id > certificate_templates.id // required


//// 11) Subscription & Package System ////////////////////////////////////////////////////////////

Table subscription_plans {
  id bigint [pk, increment]
  name varchar
  slug varchar [unique]
  description text
  price decimal
  currency varchar [default: 'USD']
  interval enum('day','week','month','year')
  trial_days int
  stripe_price_id varchar
  paypal_plan_id varchar
  features json
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table packages {
  id bigint [pk, increment]
  name varchar
  slug varchar [unique]
  description text
  price decimal
  sale_price decimal
  features json
  has_quiz_feature boolean [default: true]
  has_tutor_support boolean [default: true]
  duration_days int
  is_featured boolean [default: false]
  status enum('draft','published','archived') [default: 'draft']
  created_at timestamp
  updated_at timestamp
}

Table package_courses {
  id bigint [pk, increment]
  package_id bigint
  course_id bigint
  sort_order int [default: 0]
  created_at timestamp
  updated_at timestamp
}

Table user_subscriptions {
  id bigint [pk, increment]
  user_id bigint
  subscription_plan_id bigint
  stripe_subscription_id varchar
  stripe_customer_id varchar
  stripe_price_id varchar
  paypal_subscription_id varchar
  payment_method enum [default: 'stripe']
  status enum [default: 'active']
  current_period_start timestamp
  current_period_end timestamp
  trial_ends_at timestamp
  canceled_at timestamp
  cancel_at_period_end boolean [default: false]
  ends_at timestamp
  paused_at timestamp
  pause_collection json
  metadata json
  created_at timestamp
  updated_at timestamp
}

Table user_package_access {
  id bigint [pk, increment]
  user_id bigint
  package_id bigint
  order_id bigint
  subscription_id bigint
  access_type enum // purchase/subscription/manual
  starts_at timestamp
  expires_at timestamp
  is_active boolean [default: true]
  features_access json
  created_at timestamp
  updated_at timestamp
}

Ref: user_subscriptions.user_id > users.id // cascade
Ref: user_subscriptions.subscription_plan_id > subscription_plans.id
Ref: user_package_access.user_id > users.id // cascade
Ref: user_package_access.package_id > packages.id // cascade
Ref: user_package_access.order_id > orders.id // set null
Ref: user_package_access.subscription_id > user_subscriptions.id // set null
Ref: package_courses.package_id > packages.id // cascade
Ref: package_courses.course_id > courses.id // cascade


//// 12) Payment & Order System ///////////////////////////////////////////////////////////////////

Table orders {
  id bigint [pk, increment]
  user_id bigint
  order_number varchar [unique]
  type enum('course','package','subscription','addon')
  subtotal decimal
  discount decimal
  tax decimal
  total decimal
  currency varchar [default: 'USD']
  status enum('pending','processing','completed','failed','refunded','canceled')
  payment_method enum('stripe','paypal','bank')
  payment_id varchar
  notes text
  created_at timestamp
  updated_at timestamp
}

Table order_items {
  id bigint [pk, increment]
  order_id bigint
  item_type varchar
  item_id bigint
  name varchar
  quantity int [default: 1]
  unit_price decimal
  total decimal
  created_at timestamp
  updated_at timestamp
}

Table transactions {
  id bigint [pk, increment]
  user_id bigint
  order_id bigint
  provider enum('stripe','paypal','manual')
  provider_ref varchar
  amount decimal
  currency varchar [default: 'USD']
  type enum('charge','refund','payout')
  status enum('pending','succeeded','failed')
  payload json
  created_at timestamp
  updated_at timestamp
}

Table coupons {
  id bigint [pk, increment]
  code varchar [unique]
  description text
  type enum('percent','fixed')
  value decimal
  max_uses int
  max_uses_per_user int
  starts_at timestamp
  ends_at timestamp
  min_subtotal decimal
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table coupon_usage {
  id bigint [pk, increment]
  coupon_id bigint
  user_id bigint
  order_id bigint
  used_at timestamp
}

Table payment_methods {
  id bigint [pk, increment]
  user_id bigint
  provider enum('stripe','paypal')
  reference varchar
  brand varchar
  last4 varchar
  exp_month int
  exp_year int
  is_default boolean [default: false]
  created_at timestamp
  updated_at timestamp
}

Table invoices {
  id bigint [pk, increment]
  user_id bigint
  order_id bigint
  subscription_id bigint
  number varchar [unique]
  pdf_path varchar
  data json
  issued_at timestamp
  created_at timestamp
  updated_at timestamp
}

Ref: orders.user_id > users.id // cascade
Ref: order_items.order_id > orders.id // cascade
Ref: transactions.user_id > users.id // cascade
Ref: transactions.order_id > orders.id // set null
Ref: coupon_usage.coupon_id > coupons.id // cascade
Ref: coupon_usage.user_id > users.id // cascade
Ref: coupon_usage.order_id > orders.id // set null
Ref: payment_methods.user_id > users.id // cascade
Ref: invoices.user_id > users.id // cascade
Ref: invoices.order_id > orders.id // set null
Ref: invoices.subscription_id > user_subscriptions.id // set null


//// 13) Chat & Messaging /////////////////////////////////////////////////////////////////////////

Table conversations {
  id bigint [pk, increment]
  title varchar
  created_by bigint
  is_group boolean [default: false]
  created_at timestamp
  updated_at timestamp
}

Table messages {
  id bigint [pk, increment]
  conversation_id bigint
  user_id bigint
  parent_id bigint
  body text
  created_at timestamp
  updated_at timestamp
}

Table conversation_users {
  id bigint [pk, increment]
  conversation_id bigint
  user_id bigint
  role enum('owner','member','moderator') [default: 'member']
  joined_at timestamp
}

Table message_reads {
  id bigint [pk, increment]
  message_id bigint
  user_id bigint
  read_at timestamp
}

Table message_attachments {
  id bigint [pk, increment]
  message_id bigint
  file_path varchar
  file_name varchar
  file_type varchar
  file_size bigint
  created_at timestamp
}

Ref: conversations.created_by > users.id // set null
Ref: messages.conversation_id > conversations.id // cascade
Ref: messages.user_id > users.id // cascade
Ref: messages.parent_id > messages.id // cascade
Ref: conversation_users.conversation_id > conversations.id // cascade
Ref: conversation_users.user_id > users.id // cascade
Ref: message_reads.message_id > messages.id // cascade
Ref: message_reads.user_id > users.id // cascade
Ref: message_attachments.message_id > messages.id // cascade


//// 14) Notification System //////////////////////////////////////////////////////////////////////

Table notifications {
  id char(36) [pk]
  type varchar
  notifiable_type varchar
  notifiable_id bigint
  data json
  read_at timestamp
  created_at timestamp
  updated_at timestamp
}

Table notification_preferences {
  id bigint [pk, increment]
  user_id bigint [unique]
  channels json
  quiet_hours json
  created_at timestamp
  updated_at timestamp
}

Table notification_templates {
  id bigint [pk, increment]
  key varchar [unique]
  subject varchar
  body longtext
  channel enum('email','sms','push','inapp')
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Ref: notification_preferences.user_id > users.id // cascade


//// 15) Blog System /////////////////////////////////////////////////////////////////////////////

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

Ref: posts.author_id > users.id // cascade
Ref: post_category.post_id > posts.id // cascade
Ref: post_category.category_id > categories.id // cascade
Ref: post_tag.post_id > posts.id // cascade
Ref: post_tag.tag_id > tags.id // cascade
Ref: post_likes.post_id > posts.id // cascade
Ref: post_likes.user_id > users.id // cascade


//// 16) Reviews & Ratings ////////////////////////////////////////////////////////////////////////

Table reviews {
  id bigint [pk, increment]
  reviewable_type varchar
  reviewable_id bigint
  user_id bigint
  rating int
  title varchar
  body text
  is_verified_purchase boolean [default: false]
  created_at timestamp
  updated_at timestamp
}

Table review_helpfulness {
  id bigint [pk, increment]
  review_id bigint
  user_id bigint
  is_helpful boolean
  created_at timestamp
  updated_at timestamp
}

Ref: reviews.user_id > users.id // cascade
Ref: review_helpfulness.review_id > reviews.id // cascade
Ref: review_helpfulness.user_id > users.id // cascade


//// 17) Wishlist & Bookmarks /////////////////////////////////////////////////////////////////////

Table wishlists {
  id bigint [pk, increment]
  user_id bigint
  wishable_type varchar
  wishable_id bigint
  created_at timestamp
  updated_at timestamp
}

Table bookmarks {
  id bigint [pk, increment]
  user_id bigint
  bookmarkable_type varchar
  bookmarkable_id bigint
  created_at timestamp
  updated_at timestamp
}

Ref: wishlists.user_id > users.id // cascade
Ref: bookmarks.user_id > users.id // cascade


//// 18) Gamification /////////////////////////////////////////////////////////////////////////////

Table badges {
  id bigint [pk, increment]
  name varchar
  slug varchar [unique]
  description text
  icon varchar
  criteria json
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table user_badges {
  id bigint [pk, increment]
  user_id bigint
  badge_id bigint
  awarded_at timestamp
  created_at timestamp
  updated_at timestamp
}

Table points {
  id bigint [pk, increment]
  user_id bigint
  reason varchar
  points int
  context json
  created_at timestamp
  updated_at timestamp
}

Table leaderboards {
  id bigint [pk, increment]
  user_id bigint
  period enum('daily','weekly','monthly','all_time')
  score int
  created_at timestamp
  updated_at timestamp
}

Ref: user_badges.user_id > users.id // cascade
Ref: user_badges.badge_id > badges.id // cascade
Ref: points.user_id > users.id // cascade
Ref: leaderboards.user_id > users.id // cascade


//// 19) Forums / Q&A ////////////////////////////////////////////////////////////////////////////

Table forums {
  id bigint [pk, increment]
  title varchar
  description text
  parent_id bigint
  is_private boolean [default: false]
  created_at timestamp
  updated_at timestamp
}

Table forum_topics {
  id bigint [pk, increment]
  forum_id bigint
  user_id bigint
  title varchar
  created_at timestamp
  updated_at timestamp
}

Table forum_posts {
  id bigint [pk, increment]
  topic_id bigint
  user_id bigint
  parent_id bigint
  body text
  is_best_answer boolean [default: false]
  created_at timestamp
  updated_at timestamp
}

Ref: forums.parent_id > forums.id // cascade
Ref: forum_topics.forum_id > forums.id // cascade
Ref: forum_topics.user_id > users.id // cascade
Ref: forum_posts.topic_id > forum_topics.id // cascade
Ref: forum_posts.user_id > users.id // cascade
Ref: forum_posts.parent_id > forum_posts.id // cascade


//// 20) Media Library ////////////////////////////////////////////////////////////////////////////

Table media {
  id bigint [pk, increment]
  user_id bigint
  disk varchar [default: 's3']
  directory varchar
  file_name varchar
  mime_type varchar
  size bigint
  width int
  height int
  alt text
  focal_point json
  conversions json
  created_at timestamp
  updated_at timestamp
}

Table media_thumbnails {
  id bigint [pk, increment]
  media_id bigint
  variant varchar
  path varchar
  width int
  height int
  created_at timestamp
  updated_at timestamp
}

Ref: media.user_id > users.id // cascade
Ref: media_thumbnails.media_id > media.id // cascade


//// 21) Announcements ////////////////////////////////////////////////////////////////////////////

Table announcements {
  id bigint [pk, increment]
  title varchar
  body text
  audience enum('all','tutors','students','custom')
  audience_filter json
  is_pinned boolean [default: false]
  scheduled_at timestamp
  created_by bigint
  created_at timestamp
  updated_at timestamp
}

Table announcement_reads {
  id bigint [pk, increment]
  announcement_id bigint
  user_id bigint
  read_at timestamp
}

Ref: announcements.created_by > users.id // cascade
Ref: announcement_reads.announcement_id > announcements.id // cascade
Ref: announcement_reads.user_id > users.id // cascade


//// 22) Activity & Settings //////////////////////////////////////////////////////////////////////

Table activity_log {
  id bigint [pk, increment]
  log_name varchar
  description text
  subject_id bigint
  subject_type varchar
  causer_id bigint
  causer_type varchar
  properties json
  batch_uuid char(36)
  created_at timestamp
}

Table user_activity {
  id bigint [pk, increment]
  user_id bigint
  action varchar
  context json
  ip varchar
  user_agent text
  created_at timestamp
}

Table settings {
  id bigint [pk, increment]
  key varchar [unique]
  value json
  created_at timestamp
  updated_at timestamp
}

Table site_settings {
  id bigint [pk, increment]
  group varchar
  key varchar
  value json
  created_at timestamp
  updated_at timestamp
}

Table email_templates {
  id bigint [pk, increment]
  key varchar [unique]
  subject varchar
  body longtext
  created_at timestamp
  updated_at timestamp
}

Table email_logs {
  id bigint [pk, increment]
  to_email varchar
  template_key varchar
  subject varchar
  body longtext
  payload json
  status enum('queued','sent','failed')
  error text
  sent_at timestamp
  created_at timestamp
  updated_at timestamp
}


//// 23) Private Lesson Comments (1:1) ////////////////////////////////////////////////////////////

Table lesson_comments {
  id bigint [pk, increment]
  lesson_id bigint
  user_id bigint
  parent_id bigint
  comment text
  is_from_tutor boolean [default: false]
  is_pinned boolean [default: false]
  created_at timestamp
  updated_at timestamp
}

Ref: lesson_comments.lesson_id > lessons.id // cascade
Ref: lesson_comments.user_id > users.id // cascade
Ref: lesson_comments.parent_id > lesson_comments.id // cascade
