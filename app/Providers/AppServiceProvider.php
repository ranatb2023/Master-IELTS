<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Assignment;
use App\Models\Review;
use App\Models\Lesson;
use App\Models\Topic;
use App\Observers\UserObserver;
use App\Observers\CourseObserver;
use App\Observers\EnrollmentObserver;
use App\Observers\QuizAttemptObserver;
use App\Observers\ReviewObserver;
use App\Observers\LessonObserver;
use App\Observers\TopicObserver;
use App\Policies\UserPolicy;
use App\Policies\CoursePolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\QuizPolicy;
use App\Policies\AssignmentPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Course::class => CoursePolicy::class,
        Enrollment::class => EnrollmentPolicy::class,
        Quiz::class => QuizPolicy::class,
        Assignment::class => AssignmentPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register observers
        User::observe(UserObserver::class);
        Course::observe(CourseObserver::class);
        Topic::observe(TopicObserver::class);
        Lesson::observe(LessonObserver::class);
        Enrollment::observe(EnrollmentObserver::class);
        QuizAttempt::observe(QuizAttemptObserver::class);
        Review::observe(ReviewObserver::class);

        // Register policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}