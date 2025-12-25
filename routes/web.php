<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicCourseController;
use App\Http\Controllers\LessonContentController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\CourseCategoryController as AdminCourseCategoryController;
use App\Http\Controllers\Admin\CourseTagController as AdminCourseTagController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\TopicController as AdminTopicController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\EnrollmentController as AdminEnrollmentController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\QuizAttemptController as AdminQuizAttemptController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\AssignmentController as AdminAssignmentController;
use App\Http\Controllers\Admin\AssignmentSubmissionController as AdminAssignmentSubmissionController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Admin\PackageFeatureController as AdminPackageFeatureController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\ReportController;

// Tutor Controllers
use App\Http\Controllers\Tutor\DashboardController as TutorDashboard;
use App\Http\Controllers\Tutor\CourseController as TutorCourseController;
use App\Http\Controllers\Tutor\TopicController;
use App\Http\Controllers\Tutor\LessonController;
use App\Http\Controllers\Tutor\QuizController as TutorQuizController;
use App\Http\Controllers\Tutor\AssignmentController as TutorAssignmentController;

// Student Controllers
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\CoursePurchaseController as StudentCoursePurchaseController;
use App\Http\Controllers\Student\SubscriptionController as StudentSubscriptionController;
use App\Http\Controllers\Student\EnrollmentController as StudentEnrollmentController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\LessonProgressController as StudentLessonProgressController;
use App\Http\Controllers\Student\PackageController as StudentPackageController;
use App\Http\Controllers\Student\PackagePurchaseController as StudentPackagePurchaseController;
use App\Http\Controllers\Student\CertificateController;

use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Pages
Route::get('/reading', [HomeController::class, 'reading'])->name('reading');
Route::get('/listening', [HomeController::class, 'listening'])->name('listening');
Route::get('/writing', [HomeController::class, 'writing'])->name('writing');
Route::get('/speaking', [HomeController::class, 'speaking'])->name('speaking');

// Static Pages
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');

// Search
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Stripe Webhook (must be outside auth middleware)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

// Public Course Routes
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [PublicCourseController::class, 'index'])->name('index');
    Route::get('/{course:slug}', [PublicCourseController::class, 'show'])->name('show');
    Route::get('/category/{category:slug}', [PublicCourseController::class, 'byCategory'])->name('by-category');
    Route::get('/instructor/{instructor}', [PublicCourseController::class, 'byInstructor'])->name('by-instructor');
});

// Public Package Routes
Route::prefix('packages')->name('packages.')->group(function () {
    Route::get('/', [\App\Http\Controllers\PackageController::class, 'index'])->name('index');
    Route::get('/{package}', [\App\Http\Controllers\PackageController::class, 'show'])->name('show');
});

// Certificate Verification (Public)
Route::get('/certificates/verify', [CertificateController::class, 'verify'])->name('certificates.verify');
Route::post('/certificates/verify', [CertificateController::class, 'verify'])->name('certificates.verify.check');

// Contact Form
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit');

// Stripe Webhooks (must be outside auth middleware)
Route::post('/stripe/webhook', [\App\Http\Controllers\WebhookController::class, 'handleWebhook'])->name('cashier.webhook');

// Protected Content Streaming (requires authentication and enrollment)
Route::middleware(['auth'])->group(function () {
    Route::get('/lessons/{lesson}/play', [LessonContentController::class, 'show'])->name('lessons.play');
    Route::get('/lessons/{lesson}/video', [LessonContentController::class, 'streamVideo'])->name('lessons.video.stream');
    Route::get('/lessons/{lesson}/audio', [LessonContentController::class, 'streamAudio'])->name('lessons.audio.stream');
    Route::get('/lessons/{lesson}/document', [LessonContentController::class, 'streamDocument'])->name('lessons.document.stream');
    Route::get('/lessons/{lesson}/presentation', [LessonContentController::class, 'streamPresentation'])->name('lessons.presentation.stream');
});

// Authentication routes (provided by Breeze)
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard Redirection
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasAnyAdminRole()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('tutor')) {
            return redirect()->route('tutor.dashboard');
        } elseif ($user->hasRole('student')) {
            return redirect()->route('student.dashboard');
        }
        return redirect('/');
    })->name('dashboard');


    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/update', [ProfileController::class, 'update'])->name('update');
        Route::patch('/profile-info', [ProfileController::class, 'updateProfile'])->name('update-info');
        Route::patch('/preferences', [ProfileController::class, 'updatePreferences'])->name('update-preferences');
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar'])->name('delete-avatar');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    | Uses 'admin.role' middleware which allows ANY admin role (except tutor/student)
    */
    Route::middleware(['admin.role'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard & Reports
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        Route::get('/reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');
        Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('/reports/enrollments', [ReportController::class, 'enrollments'])->name('reports.enrollments');
        Route::get('/reports/course-performance', [ReportController::class, 'coursePerformance'])->name('reports.course-performance');
        Route::get('/reports/student-progress', [ReportController::class, 'studentProgress'])->name('reports.student-progress');
        Route::get('/reports/tutor-performance', [ReportController::class, 'tutorPerformance'])->name('reports.tutor-performance');
        Route::get('/reports/assessments', [ReportController::class, 'assessments'])->name('reports.assessments');
        Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');

        // Courses
        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', [AdminCourseController::class, 'index'])->name('index');
            Route::get('/trash', [AdminCourseController::class, 'trash'])->name('trash');
            Route::get('/create', [AdminCourseController::class, 'create'])->name('create');
            Route::post('/', [AdminCourseController::class, 'store'])->name('store');
            Route::post('/bulk-action', [AdminCourseController::class, 'bulkAction'])->name('bulk-action');

            // Specific course routes (must be before /{course})
            Route::get('/{course}/topics', [AdminCourseController::class, 'getTopics'])->name('topics');
            Route::get('/{course}/edit', [AdminCourseController::class, 'edit'])->name('edit');
            Route::post('/{course}/duplicate', [AdminCourseController::class, 'duplicate'])->name('duplicate');
            Route::post('/{course}/reorder-topics', [AdminCourseController::class, 'reorderTopics'])->name('reorder-topics');
            Route::post('/{course}/reorder-content', [AdminCourseController::class, 'reorderContent'])->name('reorder-content');
            Route::post('/{id}/restore', [AdminCourseController::class, 'restoreCourse'])->name('restore');
            Route::delete('/{id}/force-delete', [AdminCourseController::class, 'forceDelete'])->name('force-delete');

            // Generic course routes (must be after specific routes)
            Route::get('/{course}', [AdminCourseController::class, 'show'])->name('show');
            Route::put('/{course}', [AdminCourseController::class, 'update'])->name('update');
            Route::get('/{course}/enrollment-progress', [AdminCourseController::class, 'checkEnrollmentProgress'])->name('enrollment-progress');
            Route::delete('/{course}', [AdminCourseController::class, 'destroy'])->name('destroy');
        });

        // Course Categories
        Route::resource('course-categories', AdminCourseCategoryController::class);

        // Course Tags
        Route::resource('course-tags', AdminCourseTagController::class);

        // Topics
        Route::prefix('topics')->name('topics.')->group(function () {
            Route::get('/', [AdminTopicController::class, 'index'])->name('index');
            Route::get('/trash', [AdminTopicController::class, 'trash'])->name('trash');
            Route::get('/create', [AdminTopicController::class, 'create'])->name('create');
            Route::post('/', [AdminTopicController::class, 'store'])->name('store');
            Route::get('/{topic}', [AdminTopicController::class, 'show'])->name('show');
            Route::get('/{topic}/edit', [AdminTopicController::class, 'edit'])->name('edit');
            Route::put('/{topic}', [AdminTopicController::class, 'update'])->name('update');
            Route::delete('/{topic}', [AdminTopicController::class, 'destroy'])->name('destroy');
            Route::post('/{topic}/restore', [AdminTopicController::class, 'restore'])->name('restore');
            Route::delete('/{topic}/force-delete', [AdminTopicController::class, 'forceDelete'])->name('force-delete');
        });

        // Lessons
        Route::prefix('lessons')->name('lessons.')->group(function () {
            Route::get('/', [AdminLessonController::class, 'index'])->name('index');
            Route::get('/trash', [AdminLessonController::class, 'trash'])->name('trash');
            Route::get('/create', [AdminLessonController::class, 'create'])->name('create');
            Route::post('/', [AdminLessonController::class, 'store'])->name('store');
            Route::get('/{lesson}', [AdminLessonController::class, 'show'])->name('show');
            Route::get('/{lesson}/edit', [AdminLessonController::class, 'edit'])->name('edit');
            Route::put('/{lesson}', [AdminLessonController::class, 'update'])->name('update');
            Route::delete('/{lesson}', [AdminLessonController::class, 'destroy'])->name('destroy');
            Route::post('/{lesson}/restore', [AdminLessonController::class, 'restore'])->name('restore');
            Route::delete('/{lesson}/force-delete', [AdminLessonController::class, 'forceDelete'])->name('force-delete');
        });

        // Lesson Comments
        Route::prefix('lesson-comments')->name('lesson-comments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\LessonCommentController::class, 'index'])->name('index');
            Route::post('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonCommentController::class, 'store'])->name('store');
            Route::put('/{comment}', [\App\Http\Controllers\Admin\LessonCommentController::class, 'update'])->name('update');
            Route::delete('/{comment}', [\App\Http\Controllers\Admin\LessonCommentController::class, 'destroy'])->name('destroy');
            Route::post('/{comment}/toggle-pin', [\App\Http\Controllers\Admin\LessonCommentController::class, 'togglePin'])->name('toggle-pin');
        });

        // Quizzes routes are defined below (line ~247)

        // Assignments
        Route::prefix('assignments')->name('assignments.')->group(function () {
            Route::get('/', [AdminAssignmentController::class, 'index'])->name('index');
            Route::get('/create', [AdminAssignmentController::class, 'create'])->name('create');
            Route::post('/', [AdminAssignmentController::class, 'store'])->name('store');
            Route::get('/{assignment}', [AdminAssignmentController::class, 'show'])->name('show');
            Route::get('/{assignment}/edit', [AdminAssignmentController::class, 'edit'])->name('edit');
            Route::put('/{assignment}', [AdminAssignmentController::class, 'update'])->name('update');
            Route::delete('/{assignment}', [AdminAssignmentController::class, 'destroy'])->name('destroy');
        });

        // Assignment Submissions
        Route::prefix('assignment-submissions')->name('assignment-submissions.')->group(function () {
            Route::get('/', [AdminAssignmentSubmissionController::class, 'index'])->name('index');
            Route::get('/{submission}', [AdminAssignmentSubmissionController::class, 'show'])->name('show');
            Route::get('/{submission}/grade', [AdminAssignmentSubmissionController::class, 'grade'])->name('grade');
            Route::post('/{submission}/submit-grade', [AdminAssignmentSubmissionController::class, 'submitGrade'])->name('submit-grade');
            Route::get('/{submission}/download-all', [AdminAssignmentSubmissionController::class, 'downloadAll'])->name('download-all');
            Route::delete('/{submission}', [AdminAssignmentSubmissionController::class, 'destroy'])->name('destroy');
        });

        // Assignment Files
        Route::get('/assignment-files/{file}/download', [AdminAssignmentSubmissionController::class, 'downloadFile'])->name('assignment-files.download');
        Route::get('/assignment-files/{file}/view', [AdminAssignmentSubmissionController::class, 'viewFile'])->name('assignment-files.view');

        // Categories
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [AdminCategoryController::class, 'index'])->name('index');
            Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');
            Route::post('/', [AdminCategoryController::class, 'store'])->name('store');
            Route::get('/{category}', [AdminCategoryController::class, 'show'])->name('show');
            Route::get('/{category}/edit', [AdminCategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [AdminCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [AdminCategoryController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [AdminCategoryController::class, 'reorder'])->name('reorder');
            Route::post('/{category}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Users
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('/trash', [AdminUserController::class, 'trash'])->name('trash');
            Route::get('/create', [AdminUserController::class, 'create'])->name('create');
            Route::post('/', [AdminUserController::class, 'store'])->name('store');
            Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
            Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/restore', [AdminUserController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [AdminUserController::class, 'forceDelete'])->name('force-delete');
            Route::post('/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{user}/verify-email', [AdminUserController::class, 'verifyEmail'])->name('verify-email');
            Route::post('/bulk-action', [AdminUserController::class, 'bulkAction'])->name('bulk-action');
            Route::post('/{user}/impersonate', [AdminUserController::class, 'impersonate'])->name('impersonate');
            Route::post('/stop-impersonating', [AdminUserController::class, 'stopImpersonating'])->name('stop-impersonating');
        });

        // Roles
        Route::resource('roles', AdminRoleController::class);

        // Permissions
        Route::resource('permissions', AdminPermissionController::class);

        // Enrollments
        Route::prefix('enrollments')->name('enrollments.')->group(function () {
            Route::get('/', [AdminEnrollmentController::class, 'index'])->name('index');
            Route::get('/create', [AdminEnrollmentController::class, 'create'])->name('create');
            Route::post('/', [AdminEnrollmentController::class, 'store'])->name('store');
            Route::get('/{enrollment}', [AdminEnrollmentController::class, 'show'])->name('show');
            Route::get('/{enrollment}/edit', [AdminEnrollmentController::class, 'edit'])->name('edit');
            Route::put('/{enrollment}', [AdminEnrollmentController::class, 'update'])->name('update');
            Route::delete('/{enrollment}', [AdminEnrollmentController::class, 'destroy'])->name('destroy');
            Route::post('/{enrollment}/extend', [AdminEnrollmentController::class, 'extend'])->name('extend');
            Route::post('/{enrollment}/refund', [AdminEnrollmentController::class, 'refund'])->name('refund');
            Route::post('/{enrollment}/reset-progress', [AdminEnrollmentController::class, 'resetProgress'])->name('reset-progress');
            Route::get('/export', [AdminEnrollmentController::class, 'export'])->name('export');
            Route::post('/bulk-action', [AdminEnrollmentController::class, 'bulkAction'])->name('bulk-action');
        });

        // Quizzes
        Route::prefix('quizzes')->name('quizzes.')->group(function () {
            Route::get('/', [AdminQuizController::class, 'index'])->name('index');
            Route::get('/create', [AdminQuizController::class, 'create'])->name('create');
            Route::post('/', [AdminQuizController::class, 'store'])->name('store');
            Route::get('/{quiz}', [AdminQuizController::class, 'show'])->name('show');
            Route::get('/{quiz}/edit', [AdminQuizController::class, 'edit'])->name('edit');
            Route::put('/{quiz}', [AdminQuizController::class, 'update'])->name('update');
            Route::delete('/{quiz}', [AdminQuizController::class, 'destroy'])->name('destroy');
            Route::get('/{quiz}/questions', [AdminQuizController::class, 'questions'])->name('questions');
            Route::post('/{quiz}/questions', [AdminQuizController::class, 'addQuestion'])->name('add-question');
            Route::put('/{quiz}/questions/{question}', [AdminQuizController::class, 'updateQuestion'])->name('update-question');
            Route::delete('/{quiz}/questions/{question}', [AdminQuizController::class, 'deleteQuestion'])->name('delete-question');
            Route::post('/{quiz}/duplicate', [AdminQuizController::class, 'duplicate'])->name('duplicate');
            Route::get('/{quiz}/attempts', [AdminQuizController::class, 'attempts'])->name('attempts');
            Route::post('/{quiz}/toggle-status', [AdminQuizController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Questions
        Route::prefix('questions')->name('questions.')->group(function () {
            Route::get('/', [AdminQuestionController::class, 'index'])->name('index');
            Route::get('/create', [AdminQuestionController::class, 'create'])->name('create');
            Route::post('/', [AdminQuestionController::class, 'store'])->name('store');
            Route::get('/question-types/{questionType}/schema', [AdminQuestionController::class, 'getQuestionTypeSchema'])->name('type-schema');
            Route::get('/{question}', [AdminQuestionController::class, 'show'])->name('show');
            Route::get('/{question}/edit', [AdminQuestionController::class, 'edit'])->name('edit');
            Route::put('/{question}', [AdminQuestionController::class, 'update'])->name('update');
            Route::delete('/{question}', [AdminQuestionController::class, 'destroy'])->name('destroy');
        });

        // Quiz Attempts
        Route::prefix('quiz-attempts')->name('quiz-attempts.')->group(function () {
            Route::get('/', [AdminQuizAttemptController::class, 'index'])->name('index');
            Route::get('/{quizAttempt}', [AdminQuizAttemptController::class, 'show'])->name('show');
            Route::get('/{quizAttempt}/grade', [AdminQuizAttemptController::class, 'grade'])->name('grade');
            Route::post('/{quizAttempt}/submit-grade', [AdminQuizAttemptController::class, 'submitGrade'])->name('submit-grade');
            Route::post('/{quizAttempt}/reset', [AdminQuizAttemptController::class, 'reset'])->name('reset');
            Route::post('/{quizAttempt}/regrade', [AdminQuizAttemptController::class, 'regrade'])->name('regrade');
            Route::delete('/{quizAttempt}', [AdminQuizAttemptController::class, 'destroy'])->name('destroy');
        });

        // Packages
        Route::prefix('packages')->name('packages.')->group(function () {
            Route::get('/', [AdminPackageController::class, 'index'])->name('index');
            Route::get('/create', [AdminPackageController::class, 'create'])->name('create');
            Route::post('/', [AdminPackageController::class, 'store'])->name('store');
            Route::get('/{package}', [AdminPackageController::class, 'show'])->name('show');
            Route::get('/{package}/edit', [AdminPackageController::class, 'edit'])->name('edit');
            Route::put('/{package}', [AdminPackageController::class, 'update'])->name('update');
            Route::delete('/{package}', [AdminPackageController::class, 'destroy'])->name('destroy');
            Route::post('/{package}/toggle-status', [AdminPackageController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{package}/toggle-featured', [AdminPackageController::class, 'toggleFeatured'])->name('toggle-featured');
            Route::post('/{package}/duplicate', [AdminPackageController::class, 'duplicate'])->name('duplicate');
            Route::get('/{package}/courses', [AdminPackageController::class, 'getCourses'])->name('courses');
            Route::post('/{package}/reorder-courses', [AdminPackageController::class, 'reorderCourses'])->name('reorder-courses');
        });

        // Package Features
        Route::prefix('package-features')->name('package-features.')->group(function () {
            Route::get('/', [AdminPackageFeatureController::class, 'index'])->name('index');
            Route::get('/create', [AdminPackageFeatureController::class, 'create'])->name('create');
            Route::post('/', [AdminPackageFeatureController::class, 'store'])->name('store');
            Route::get('/{packageFeature}', [AdminPackageFeatureController::class, 'show'])->name('show');
            Route::get('/{packageFeature}/edit', [AdminPackageFeatureController::class, 'edit'])->name('edit');
            Route::put('/{packageFeature}', [AdminPackageFeatureController::class, 'update'])->name('update');
            Route::delete('/{packageFeature}', [AdminPackageFeatureController::class, 'destroy'])->name('destroy');
            Route::post('/{packageFeature}/toggle-status', [AdminPackageFeatureController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{packageFeature}/grant-access', [AdminPackageFeatureController::class, 'grantAccess'])->name('grant-access');
            Route::post('/{packageFeature}/revoke-access', [AdminPackageFeatureController::class, 'revokeAccess'])->name('revoke-access');
            Route::get('/{packageFeature}/users', [AdminPackageFeatureController::class, 'getUsers'])->name('users');
        });

        // Subscription Plans
        Route::prefix('subscription-plans')->name('subscription-plans.')->group(function () {
            Route::get('/', [SubscriptionPlanController::class, 'index'])->name('index');
            Route::get('/create', [SubscriptionPlanController::class, 'create'])->name('create');
            Route::post('/', [SubscriptionPlanController::class, 'store'])->name('store');
            Route::get('/{subscriptionPlan}', [SubscriptionPlanController::class, 'show'])->name('show');
            Route::get('/{subscriptionPlan}/edit', [SubscriptionPlanController::class, 'edit'])->name('edit');
            Route::put('/{subscriptionPlan}', [SubscriptionPlanController::class, 'update'])->name('update');
            Route::delete('/{subscriptionPlan}', [SubscriptionPlanController::class, 'destroy'])->name('destroy');
            Route::post('/{subscriptionPlan}/toggle-status', [SubscriptionPlanController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{subscriptionPlan}/toggle-featured', [SubscriptionPlanController::class, 'toggleFeatured'])->name('toggle-featured');
            Route::post('/reorder', [SubscriptionPlanController::class, 'reorder'])->name('reorder');
            Route::get('/{subscriptionPlan}/subscribers', [SubscriptionPlanController::class, 'subscribers'])->name('subscribers');
            Route::delete('/subscriptions/{subscription}', [SubscriptionPlanController::class, 'destroySubscription'])->name('subscriptions.destroy');
        });

        // Certificates
        Route::prefix('certificates')->name('certificates.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\CertificateController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\CertificateController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\CertificateController::class, 'store'])->name('store');
            Route::get('/analytics', [App\Http\Controllers\Admin\CertificateController::class, 'analytics'])->name('analytics');
            Route::post('/bulk-regenerate', [App\Http\Controllers\Admin\CertificateController::class, 'bulkRegenerate'])->name('bulkRegenerate');
            Route::get('/{certificate}', [App\Http\Controllers\Admin\CertificateController::class, 'show'])->name('show');
            Route::get('/{certificate}/preview', [App\Http\Controllers\Admin\CertificateController::class, 'preview'])->name('preview');
            Route::post('/{certificate}/revoke', [App\Http\Controllers\Admin\CertificateController::class, 'revoke'])->name('revoke');
            Route::post('/{certificate}/restore', [App\Http\Controllers\Admin\CertificateController::class, 'restore'])->name('restore');
            Route::get('/{certificate}/download', [App\Http\Controllers\Admin\CertificateController::class, 'download'])->name('download');
            Route::delete('/{certificate}', [App\Http\Controllers\Admin\CertificateController::class, 'destroy'])->name('destroy');
        });

        // Certificate Templates
        Route::prefix('certificate-templates')->name('certificate-templates.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\CertificateTemplateController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\CertificateTemplateController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\CertificateTemplateController::class, 'store'])->name('store');
            Route::get('/{certificateTemplate}', [App\Http\Controllers\Admin\CertificateTemplateController::class, 'show'])->name('show');
            Route::get('/{certificateTemplate}/edit', [App\Http\Controllers\Admin\CertificateTemplateController::class, 'edit'])->name('edit');
            Route::put('/{certificateTemplate}', [App\Http\Controllers\Admin\CertificateTemplateController::class, 'update'])->name('update');
            Route::get('/{certificateTemplate}/preview', [App\Http\Controllers\Admin\CertificateTemplateController::class, 'preview'])->name('preview');
            Route::post('/{certificateTemplate}/duplicate', [App\Http\Controllers\Admin\CertificateTemplateController::class, 'duplicate'])->name('duplicate');
            Route::post('/{certificateTemplate}/set-default', [App\Http\Controllers\Admin\CertificateTemplateController::class, 'setDefault'])->name('setDefault');
            Route::delete('/{certificateTemplate}', [App\Http\Controllers\Admin\CertificateTemplateController::class, 'destroy'])->name('destroy');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
            Route::post('/{id}/read', [App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/read-all', [App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('readAll');
            Route::delete('/{id}', [App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('destroy');
            Route::post('/clear-all', [App\Http\Controllers\Admin\NotificationController::class, 'clearAll'])->name('clearAll');
            Route::get('/system', [App\Http\Controllers\Admin\NotificationController::class, 'systemNotifications'])->name('system');
            Route::post('/broadcast', [App\Http\Controllers\Admin\NotificationController::class, 'broadcastNotification'])->name('broadcast');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Tutor Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:tutor'])->prefix('tutor')->name('tutor.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [TutorDashboard::class, 'index'])->name('dashboard');

        // Courses
        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', [TutorCourseController::class, 'index'])->name('index');
            Route::get('/create', [TutorCourseController::class, 'create'])->name('create');
            Route::post('/', [TutorCourseController::class, 'store'])->name('store');

            // Trash routes
            Route::get('/trash', [TutorCourseController::class, 'trash'])->name('trash');
            Route::post('/{id}/restore', [TutorCourseController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [TutorCourseController::class, 'forceDelete'])->name('force-delete');

            Route::get('/{course}', [TutorCourseController::class, 'show'])->name('show');
            Route::get('/{course}/edit', [TutorCourseController::class, 'edit'])->name('edit');
            Route::put('/{course}', [TutorCourseController::class, 'update'])->name('update');
            Route::post('/{course}/submit-review', [TutorCourseController::class, 'submitForReview'])->name('submit-review');
            Route::post('/{course}/publish', [TutorCourseController::class, 'publish'])->name('publish');
            Route::post('/{course}/archive', [TutorCourseController::class, 'archive'])->name('archive');
            Route::get('/{course}/analytics', [TutorCourseController::class, 'analytics'])->name('analytics');
            Route::get('/{course}/students', [TutorCourseController::class, 'students'])->name('students');

            // Topics
            Route::prefix('{course}/topics')->name('topics.')->group(function () {
                Route::get('/', [TopicController::class, 'index'])->name('index');
                Route::post('/', [TopicController::class, 'store'])->name('store');
                Route::get('/create', [TopicController::class, 'create'])->name('create');
                Route::get('/{topic}/edit', [TopicController::class, 'edit'])->name('edit');
                Route::put('/{topic}', [TopicController::class, 'update'])->name('update');
                Route::delete('/{topic}', [TopicController::class, 'destroy'])->name('destroy');
                Route::post('/reorder', [TopicController::class, 'reorder'])->name('reorder');

                // Lessons
                Route::prefix('{topic}/lessons')->name('lessons.')->group(function () {
                    Route::get('/', [LessonController::class, 'index'])->name('index');
                    Route::get('/create', [LessonController::class, 'create'])->name('create');
                    Route::post('/', [LessonController::class, 'store'])->name('store');
                    Route::get('/{lesson}', [LessonController::class, 'show'])->name('show');
                    Route::get('/{lesson}/edit', [LessonController::class, 'edit'])->name('edit');
                    Route::put('/{lesson}', [LessonController::class, 'update'])->name('update');
                    Route::delete('/{lesson}', [LessonController::class, 'destroy'])->name('destroy');
                    Route::post('/reorder', [LessonController::class, 'reorder'])->name('reorder');
                });
            });
        });

        // Standalone Topics Routes (all topics across courses)
        Route::prefix('topics')->name('topics.')->group(function () {
            Route::get('/', [TopicController::class, 'allTopics'])->name('all');
            Route::get('/trash', [TopicController::class, 'trash'])->name('trash');
            Route::post('/{id}/restore', [TopicController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [TopicController::class, 'forceDelete'])->name('force-delete');
            Route::get('/{topic}', [TopicController::class, 'show'])->name('show');
        });

        // Standalone Lessons Routes (all lessons across courses)
        Route::prefix('lessons')->name('lessons.')->group(function () {
            Route::get('/', [LessonController::class, 'allLessons'])->name('all');
            Route::get('/trash', [LessonController::class, 'trash'])->name('trash');
            Route::post('/{id}/restore', [LessonController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [LessonController::class, 'forceDelete'])->name('force-delete');
        });

        // Enrollments (Students)
        Route::prefix('enrollments')->name('enrollments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tutor\EnrollmentController::class, 'index'])->name('index');
            Route::get('/{enrollment}', [\App\Http\Controllers\Tutor\EnrollmentController::class, 'show'])->name('show');
        });

        // Quizzes
        Route::prefix('quizzes')->name('quizzes.')->group(function () {
            Route::get('/', [TutorQuizController::class, 'index'])->name('index');
            Route::get('/create', [TutorQuizController::class, 'create'])->name('create');
            Route::post('/', [TutorQuizController::class, 'store'])->name('store');
            Route::get('/{quiz}', [TutorQuizController::class, 'show'])->name('show');
            Route::get('/{quiz}/edit', [TutorQuizController::class, 'edit'])->name('edit');
            Route::put('/{quiz}', [TutorQuizController::class, 'update'])->name('update');
            Route::get('/{quiz}/results', [TutorQuizController::class, 'results'])->name('results');
        });

        // Questions
        Route::resource('questions', \App\Http\Controllers\Tutor\QuestionController::class);

        // Quiz Attempts
        Route::prefix('quiz-attempts')->name('quiz-attempts.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tutor\QuizAttemptController::class, 'index'])->name('index');
            Route::get('/{quizAttempt}', [\App\Http\Controllers\Tutor\QuizAttemptController::class, 'show'])->name('show');
            Route::post('/{quizAttempt}/grade', [\App\Http\Controllers\Tutor\QuizAttemptController::class, 'grade'])->name('grade');
        });

        // Assignments
        Route::prefix('assignments')->name('assignments.')->group(function () {
            Route::get('/', [TutorAssignmentController::class, 'index'])->name('index');
            Route::get('/create', [TutorAssignmentController::class, 'create'])->name('create');
            Route::post('/', [TutorAssignmentController::class, 'store'])->name('store');
            Route::get('/{assignment}', [TutorAssignmentController::class, 'show'])->name('show');
            Route::get('/{assignment}/edit', [TutorAssignmentController::class, 'edit'])->name('edit');
            Route::put('/{assignment}', [TutorAssignmentController::class, 'update'])->name('update');
            Route::get('/{assignment}/submissions', [TutorAssignmentController::class, 'submissions'])->name('submissions');
            Route::post('/{assignment}/submissions/{submission}/grade', [TutorAssignmentController::class, 'grade'])->name('grade');
            Route::get('/{assignment}/submissions/{submission}', [TutorAssignmentController::class, 'viewSubmission'])->name('viewSubmission');
        });

        // Analytics
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Tutor\AnalyticsController::class, 'dashboard'])->name('dashboard');
            Route::get('/course-performance', [\App\Http\Controllers\Tutor\AnalyticsController::class, 'coursePerformance'])->name('course-performance');
            Route::get('/student-engagement', [\App\Http\Controllers\Tutor\AnalyticsController::class, 'studentEngagement'])->name('student-engagement');
            Route::get('/revenue', [\App\Http\Controllers\Tutor\AnalyticsController::class, 'revenue'])->name('revenue');
        });

        // Certificates
        Route::prefix('certificates')->name('certificates.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tutor\CertificateController::class, 'index'])->name('index');
            Route::get('/{certificate}', [\App\Http\Controllers\Tutor\CertificateController::class, 'show'])->name('show');
        });

        // Lesson Comments
        Route::prefix('lesson-comments')->name('lesson-comments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tutor\LessonCommentController::class, 'index'])->name('index');
            Route::post('/lessons/{lesson}', [\App\Http\Controllers\Tutor\LessonCommentController::class, 'store'])->name('store');
            Route::put('/{comment}', [\App\Http\Controllers\Tutor\LessonCommentController::class, 'update'])->name('update');
            Route::delete('/{comment}', [\App\Http\Controllers\Tutor\LessonCommentController::class, 'destroy'])->name('destroy');
            Route::post('/{comment}/toggle-pin', [\App\Http\Controllers\Tutor\LessonCommentController::class, 'togglePin'])->name('toggle-pin');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [App\Http\Controllers\Tutor\NotificationController::class, 'index'])->name('index');
            Route::post('/{id}/read', [App\Http\Controllers\Tutor\NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/read-all', [App\Http\Controllers\Tutor\NotificationController::class, 'markAllAsRead'])->name('readAll');
            Route::delete('/{id}', [App\Http\Controllers\Tutor\NotificationController::class, 'destroy'])->name('destroy');
            Route::post('/clear-all', [App\Http\Controllers\Tutor\NotificationController::class, 'clearAll'])->name('clearAll');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Student Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [StudentDashboardController::class, 'stats'])->name('dashboard.stats');
        Route::get('/progress', [StudentDashboardController::class, 'progress'])->name('progress');

        // Browse Courses
        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', [StudentCourseController::class, 'index'])->name('index');
            Route::get('/{course:slug}', [StudentCourseController::class, 'show'])->name('show');
            Route::post('/{course}/enroll', [StudentCourseController::class, 'enroll'])->name('enroll');
            Route::get('/{course}/learn', [StudentCourseController::class, 'learn'])->name('learn');
            Route::get('/{course}/topics/{topic}/lessons/{lesson}', [StudentCourseController::class, 'viewLesson'])->name('view-lesson');
            Route::post('/{course}/topics/{topic}/lessons/{lesson}/complete', [StudentCourseController::class, 'completeLesson'])->name('complete-lesson');
            Route::post('/{course}/topics/{topic}/lessons/{lesson}/update-progress', [StudentCourseController::class, 'updateLessonProgress'])->name('update-lesson-progress');

            // Course Purchase Routes
            Route::get('/{course}/check-eligibility', [StudentCoursePurchaseController::class, 'checkPurchaseEligibility'])->name('check-eligibility');
            Route::post('/{course}/purchase', [StudentCoursePurchaseController::class, 'initiatePurchase'])->name('purchase');
            Route::get('/{order}/purchase-success', [StudentCoursePurchaseController::class, 'purchaseSuccess'])->name('purchase.success');
            Route::get('/{order}/purchase-cancel', [StudentCoursePurchaseController::class, 'purchaseCancel'])->name('purchase.cancel');
        });

        // Quizzes (simplified routes without course parameter)
        Route::prefix('quizzes')->name('quizzes.')->group(function () {
            Route::get('/{quiz}', [StudentQuizController::class, 'show'])->name('show');
            Route::post('/{quiz}/start', [StudentQuizController::class, 'start'])->name('start');
            Route::get('/{quiz}/attempts/{attempt}', [StudentQuizController::class, 'take'])->name('take');
            Route::post('/{quiz}/attempts/{attempt}/save-answer', [StudentQuizController::class, 'saveAnswer'])->name('save-answer');
            Route::post('/{quiz}/attempts/{attempt}/submit', [StudentQuizController::class, 'submit'])->name('submit');
            Route::post('/{quiz}/attempts/{attempt}/force-complete', [StudentQuizController::class, 'forceComplete'])->name('force-complete');
            Route::get('/{quiz}/attempts/{attempt}/result', [StudentQuizController::class, 'result'])->name('result');
        });

        // Assignments (simplified routes without course parameter)
        Route::prefix('assignments')->name('assignments.')->group(function () {
            Route::get('/{assignment}', [StudentAssignmentController::class, 'show'])->name('show');
            Route::get('/{assignment}/submit', [StudentAssignmentController::class, 'create'])->name('create');
            Route::post('/{assignment}/submit', [StudentAssignmentController::class, 'store'])->name('store');
            Route::get('/{assignment}/submissions/{submission}', [StudentAssignmentController::class, 'viewSubmission'])->name('view-submission');
            Route::get('/{assignment}/files/{file}/view', [StudentAssignmentController::class, 'viewFile'])->name('view-file');
            Route::get('/{assignment}/files/{file}/download', [StudentAssignmentController::class, 'downloadFile'])->name('download-file');
        });

        // Lesson Comments
        Route::prefix('lessons/{lesson}/comments')->name('lessons.comments.')->group(function () {
            Route::post('/', [\App\Http\Controllers\Student\LessonCommentController::class, 'store'])->name('store');
            Route::put('/{comment}', [\App\Http\Controllers\Student\LessonCommentController::class, 'update'])->name('update');
            Route::delete('/{comment}', [\App\Http\Controllers\Student\LessonCommentController::class, 'destroy'])->name('destroy');
        });

        // Student Notes
        Route::prefix('notes')->name('notes.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Student\NoteController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Student\NoteController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Student\NoteController::class, 'store'])->name('store');
            Route::get('/trash', [\App\Http\Controllers\Student\NoteController::class, 'trashed'])->name('trashed');
            Route::get('/{note}', [\App\Http\Controllers\Student\NoteController::class, 'show'])->name('show');
            Route::get('/{note}/edit', [\App\Http\Controllers\Student\NoteController::class, 'edit'])->name('edit');
            Route::put('/{note}', [\App\Http\Controllers\Student\NoteController::class, 'update'])->name('update');
            Route::delete('/{note}', [\App\Http\Controllers\Student\NoteController::class, 'destroy'])->name('destroy');
            Route::post('/{note}/toggle-pin', [\App\Http\Controllers\Student\NoteController::class, 'togglePin'])->name('toggle-pin');
            Route::post('/{id}/restore', [\App\Http\Controllers\Student\NoteController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [\App\Http\Controllers\Student\NoteController::class, 'forceDelete'])->name('force-delete');
            Route::get('/{note}/attachments/{attachment}/download', [\App\Http\Controllers\Student\NoteController::class, 'downloadAttachment'])->name('attachments.download');
            Route::get('/{note}/attachments/{attachment}/view', [\App\Http\Controllers\Student\NoteController::class, 'viewAttachment'])->name('attachments.view');
        });

        // My Enrollments
        Route::prefix('enrollments')->name('enrollments.')->group(function () {
            Route::get('/', [StudentEnrollmentController::class, 'index'])->name('index');
            Route::get('/{enrollment}', [StudentEnrollmentController::class, 'show'])->name('show');
            Route::get('/{enrollment}/progress', [StudentEnrollmentController::class, 'progress'])->name('progress');
        });

        // Webhook route (placeholder, as the actual route was not provided in the instruction)
        // Route::post('/webhook', [WebhookController::class, 'handle'])->name('webhook');

        // Subscriptions
        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::get('/', [StudentSubscriptionController::class, 'index'])->name('index');
            Route::get('/plans/{plan}', [StudentSubscriptionController::class, 'show'])->name('show');
            Route::get('/checkout/{plan}', [StudentSubscriptionController::class, 'checkout'])->name('checkout');
            Route::post('/subscribe/{plan}', [StudentSubscriptionController::class, 'subscribe'])->name('subscribe');
            Route::get('/success', [StudentSubscriptionController::class, 'success'])->name('success');
            Route::get('/manage', [StudentSubscriptionController::class, 'manage'])->name('manage');
            Route::post('/cancel', [StudentSubscriptionController::class, 'cancel'])->name('cancel');
            Route::post('/resume', [StudentSubscriptionController::class, 'resume'])->name('resume');
            Route::post('/update-payment-method', [StudentSubscriptionController::class, 'updatePaymentMethod'])->name('update-payment-method');
            Route::delete('/delete-payment-method', [StudentSubscriptionController::class, 'deletePaymentMethod'])->name('delete-payment-method');
            Route::get('/payment-method', [StudentSubscriptionController::class, 'paymentMethod'])->name('payment-method');
            Route::get('/preview-plan-change/{plan}', [StudentSubscriptionController::class, 'previewPlanChange'])->name('preview-plan-change');
            Route::post('/confirm-plan-change/{plan}', [StudentSubscriptionController::class, 'confirmPlanChange'])->name('confirm-plan-change');
            Route::post('/change-plan/{plan}', [StudentSubscriptionController::class, 'changePlan'])->name('change-plan');
            Route::get('/invoices', [StudentSubscriptionController::class, 'invoices'])->name('invoices');
            Route::get('/invoices/{invoice}/download', [StudentSubscriptionController::class, 'downloadInvoice'])->name('invoice.download');
        });

        // Packages
        Route::prefix('packages')->name('packages.')->group(function () {
            Route::get('/', [App\Http\Controllers\Student\PackageController::class, 'index'])->name('index');
            Route::get('/my-packages', [App\Http\Controllers\Student\PackageController::class, 'myPackages'])->name('my-packages');
            Route::get('/{package}', [App\Http\Controllers\Student\PackageController::class, 'show'])->name('show');
            Route::get('/{package}/checkout', [App\Http\Controllers\Student\PackageController::class, 'checkout'])->name('checkout');
            Route::post('/{package}/create-payment-intent', [App\Http\Controllers\Student\PackageController::class, 'createPaymentIntent'])->name('create-payment-intent');
            Route::post('/{package}/process-purchase', [App\Http\Controllers\Student\PackageController::class, 'processPurchase'])->name('process-purchase');
            Route::get('/{package}/purchase-complete', [App\Http\Controllers\Student\PackageController::class, 'purchaseComplete'])->name('purchase-complete');
        });

        // Invoices
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [App\Http\Controllers\Student\InvoiceController::class, 'index'])->name('index');
            Route::get('/{invoice}', [App\Http\Controllers\Student\InvoiceController::class, 'show'])->name('show');
            Route::get('/{invoice}/download', [App\Http\Controllers\Student\InvoiceController::class, 'download'])->name('download');
        });

        // Certificates
        Route::prefix('certificates')->name('certificates.')->group(function () {
            Route::get('/', [CertificateController::class, 'index'])->name('index');
            Route::get('/{certificate}', [CertificateController::class, 'show'])->name('show');
            Route::get('/{certificate}/download', [CertificateController::class, 'download'])->name('download');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [App\Http\Controllers\Student\NotificationController::class, 'index'])->name('index');
            Route::post('/{id}/read', [App\Http\Controllers\Student\NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/read-all', [App\Http\Controllers\Student\NotificationController::class, 'markAllAsRead'])->name('readAll');
            Route::delete('/{id}', [App\Http\Controllers\Student\NotificationController::class, 'destroy'])->name('destroy');
            Route::post('/clear-all', [App\Http\Controllers\Student\NotificationController::class, 'clearAll'])->name('clearAll');
        });
    });

    // API Routes for Notifications (all authenticated users)
    Route::get('/api/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'unread'])->name('notifications.unreadCount');
});