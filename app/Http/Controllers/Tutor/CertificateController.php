<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display a list of certificates issued for tutor's courses.
     */
    public function index(Request $request)
    {
        $query = Certificate::with(['user', 'course'])
            ->whereHas('course', function ($q) {
                $q->where('instructor_id', auth()->id());
            });

        // Apply filters
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $certificates = $query->latest()->paginate(20);

        // Get tutor's courses for filter
        $courses = auth()->user()->createdCourses()
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        return view('tutor.certificates.index', compact('certificates', 'courses'));
    }

    /**
     * Display the specified certificate.
     */
    public function show(Certificate $certificate)
    {
        // Ensure certificate belongs to tutor's course
        if ($certificate->course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this certificate.');
        }

        $certificate->load(['user', 'course', 'template']);

        return view('tutor.certificates.show', compact('certificate'));
    }
}
