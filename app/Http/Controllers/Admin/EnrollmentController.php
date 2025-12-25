<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of enrollments
     */
    public function index(Request $request)
    {
        $query = Enrollment::with(['user', 'course']);

        // Search
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            })->orWhereHas('course', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by course
        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('enrolled_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('enrolled_at', '<=', $request->to_date);
        }

        $enrollments = $query->latest('enrolled_at')->paginate(20);
        $courses = Course::orderBy('title')->get();

        return view('admin.enrollments.index', compact('enrollments', 'courses'));
    }

    /**
     * Show the form for creating a new enrollment
     */
    public function create()
    {
        $courses = Course::orderBy('title')->get();
        $students = User::role('student')->orderBy('name')->get();

        return view('admin.enrollments.create', compact('courses', 'students'));
    }

    /**
     * Store a newly created enrollment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'payment_status' => 'required|in:pending,completed,failed,refunded,free',
            'amount_paid' => 'nullable|numeric|min:0',
            'enrollment_source' => 'nullable|in:manual,self,package,admin,import',
            'expires_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Check if already enrolled
        $existing = Enrollment::where('user_id', $validated['user_id'])
            ->where('course_id', $validated['course_id'])
            ->first();

        if ($existing) {
            return back()
                ->withInput()
                ->with('error', 'User is already enrolled in this course.');
        }

        $course = Course::findOrFail($validated['course_id']);

        $enrollment = Enrollment::create([
            'user_id' => $validated['user_id'],
            'course_id' => $validated['course_id'],
            'enrolled_at' => now(),
            'status' => 'active',
            'payment_status' => $validated['payment_status'],
            'amount_paid' => $validated['amount_paid'] ?? 0,
            'enrollment_source' => $validated['enrollment_source'] ?? 'admin',
            'expires_at' => $validated['expires_at'] ?? ($course->has_lifetime_access ? null : now()->addDays($course->access_duration_days ?? 365)),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Send notification to student about enrollment
        $user = \App\Models\User::find($validated['user_id']);
        $user->notify(new \App\Notifications\CourseEnrolledNotification($course, $enrollment));

        // Notify admins about new enrollment
        $admins = \App\Models\User::role('super_admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewEnrollmentNotification($enrollment));
        }

        // Notify course instructor about new student
        if ($course->instructor) {
            $course->instructor->notify(new \App\Notifications\NewStudentEnrolledNotification($course, $user, $enrollment));
        }

        return redirect()
            ->route('admin.enrollments.show', $enrollment)
            ->with('success', 'Enrollment created successfully!');
    }

    /**
     * Display the specified enrollment
     */
    public function show(Enrollment $enrollment)
    {
        $enrollment->load([
            'user.profile',
            'course.topics.lessons',
            'course.topics.quizzes',
            'course.topics.assignments',
            'course.instructor',
            'packageAccess.package',
            'packageAccess.order',
        ]);

        // Get progress, quiz attempts, and assignment submissions for this user in this course
        $progress = $enrollment->course_progress;
        $quizAttempts = $enrollment->course_quiz_attempts;
        $assignmentSubmissions = $enrollment->course_assignment_submissions;

        // Calculate stats for the enrollment
        $stats = [
            'completed_lessons' => $progress->where('status', 'completed')->count(),
            'quiz_attempts' => $quizAttempts->count(),
            'assignments_submitted' => $assignmentSubmissions->count(),
        ];

        return view('admin.enrollments.show', compact('enrollment', 'progress', 'quizAttempts', 'assignmentSubmissions', 'stats'));
    }

    /**
     * Show the form for editing the specified enrollment
     */
    public function edit(Enrollment $enrollment)
    {
        return view('admin.enrollments.edit', compact('enrollment'));
    }

    /**
     * Update the specified enrollment
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,completed,expired,canceled,suspended',
            'payment_status' => 'required|in:pending,completed,failed,refunded,free',
            'amount_paid' => 'nullable|numeric|min:0',
            'progress_percentage' => 'nullable|numeric|min:0|max:100',
            'expires_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $enrollment->update($validated);

        return redirect()
            ->route('admin.enrollments.show', $enrollment)
            ->with('success', 'Enrollment updated successfully!');
    }

    /**
     * Remove the specified enrollment
     */
    public function destroy(Enrollment $enrollment)
    {
        $userName = $enrollment->user->name;
        $courseName = $enrollment->course->title;

        DB::beginTransaction();
        try {
            // Delete enrollment (will trigger cascade delete of related data via model event)
            $enrollment->delete();

            DB::commit();

            return redirect()
                ->route('admin.enrollments.index')
                ->with('success', "Enrollment deleted successfully! All related data (quiz attempts, assignments, progress) for {$userName} in {$courseName} has been removed.");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting enrollment: ' . $e->getMessage());

            return redirect()
                ->route('admin.enrollments.index')
                ->with('error', 'Failed to delete enrollment: ' . $e->getMessage());
        }
    }

    /**
     * Extend enrollment expiry
     */
    public function extend(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'extend_days' => 'required|integer|min:1|max:365',
        ]);

        $extendDays = (int) $validated['extend_days'];
        $currentExpiry = $enrollment->expires_at ?? now();

        $enrollment->update([
            'expires_at' => $currentExpiry->addDays($extendDays),
        ]);

        return back()->with('success', 'Enrollment extended by ' . $extendDays . ' days.');
    }

    /**
     * Refund enrollment
     */
    public function refund(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'refund_reason' => 'required|string|max:500',
            'refund_amount' => 'required|numeric|min:0|max:' . $enrollment->amount_paid,
        ]);

        DB::beginTransaction();
        try {
            // Get the order and payment intent ID
            $order = $enrollment->packageAccess?->order;
            $paymentIntentId = $order?->payment_id;

            // Process Stripe refund if payment intent exists
            if ($paymentIntentId && $order->payment_method === 'stripe') {
                try {
                    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

                    $refund = \Stripe\Refund::create([
                        'payment_intent' => $paymentIntentId,
                        'amount' => $validated['refund_amount'] * 100, // Convert to cents
                        'reason' => 'requested_by_customer',
                        'metadata' => [
                            'enrollment_id' => $enrollment->id,
                            'user_id' => $enrollment->user_id,
                            'refund_reason' => $validated['refund_reason'],
                        ],
                    ]);

                    \Log::info("Stripe refund processed successfully", [
                        'refund_id' => $refund->id,
                        'enrollment_id' => $enrollment->id,
                        'amount' => $validated['refund_amount'],
                    ]);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    \Log::error("Stripe refund failed: " . $e->getMessage(), [
                        'enrollment_id' => $enrollment->id,
                        'payment_intent' => $paymentIntentId,
                    ]);
                    throw new \Exception("Failed to process refund through Stripe: " . $e->getMessage());
                }
            }

            // Update enrollment status
            $enrollment->update([
                'payment_status' => 'refunded',
                'status' => 'canceled', // Fixed: ENUM uses 'canceled' not 'cancelled'
                'refunded_at' => now(),
                'refund_amount' => $validated['refund_amount'],
                'refund_reason' => $validated['refund_reason'],
            ]);

            DB::commit();

            // Send refund confirmation email to student
            try {
                \Mail::to($enrollment->user)->send(new \App\Mail\RefundProcessed(
                    $enrollment,
                    $validated['refund_amount'],
                    $validated['refund_reason']
                ));
                \Log::info("Refund confirmation email sent", [
                    'enrollment_id' => $enrollment->id,
                    'user_id' => $enrollment->user_id,
                ]);
            } catch (\Exception $e) {
                \Log::error("Failed to send refund email: " . $e->getMessage());
                // Continue - don't fail refund if email fails
            }

            return back()->with('success', 'Enrollment refunded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to refund enrollment: ' . $e->getMessage());
        }
    }

    /**
     * Reset enrollment progress
     */
    public function resetProgress(Enrollment $enrollment)
    {
        DB::beginTransaction();
        try {
            // Delete progress records for this user in this course
            \App\Models\Progress::where('user_id', $enrollment->user_id)
                ->whereHasMorph('progressable', ['App\Models\Lesson'], function ($query) use ($enrollment) {
                    $query->whereHas('topic', function ($q) use ($enrollment) {
                        $q->where('course_id', $enrollment->course_id);
                    });
                })
                ->delete();

            // Delete quiz attempts for this user in this course
            \App\Models\QuizAttempt::where('user_id', $enrollment->user_id)
                ->whereHas('quiz.topic', function ($q) use ($enrollment) {
                    $q->where('course_id', $enrollment->course_id);
                })
                ->delete();

            // Delete assignment submissions for this user in this course
            \App\Models\AssignmentSubmission::where('user_id', $enrollment->user_id)
                ->whereHas('assignment.topic', function ($q) use ($enrollment) {
                    $q->where('course_id', $enrollment->course_id);
                })
                ->delete();

            // Reset course progress record
            \App\Models\CourseProgress::where('user_id', $enrollment->user_id)
                ->where('course_id', $enrollment->course_id)
                ->update([
                    'completed_lessons' => 0,
                    'completed_quizzes' => 0,
                    'progress_percentage' => 0,
                ]);

            // Reset enrollment data
            $enrollment->update([
                'progress_percentage' => 0,
                'last_accessed_at' => null,
                'completed_at' => null,
                'status' => 'active',
            ]);

            DB::commit();

            return back()->with('success', 'Enrollment progress reset successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reset progress: ' . $e->getMessage());
        }
    }

    /**
     * Export enrollments
     */
    public function export(Request $request)
    {
        // TODO: Implement CSV/Excel export
        return response()->json(['message' => 'Export functionality coming soon']);
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,suspend,cancel,delete',
            'enrollment_ids' => 'required|array',
            'enrollment_ids.*' => 'exists:enrollments,id',
        ]);

        $enrollments = Enrollment::whereIn('id', $validated['enrollment_ids']);

        switch ($validated['action']) {
            case 'activate':
                $enrollments->update(['status' => 'active']);
                $message = 'Enrollments activated successfully!';
                break;
            case 'suspend':
                $enrollments->update(['status' => 'suspended']);
                $message = 'Enrollments suspended successfully!';
                break;
            case 'cancel':
                $enrollments->update(['status' => 'canceled']);
                $message = 'Enrollments canceled successfully!';
                break;
            case 'delete':
                $enrollments->delete();
                $message = 'Enrollments deleted successfully!';
                break;
        }

        return back()->with('success', $message);
    }
}
