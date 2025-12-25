<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Models\CertificateTemplate;
use App\Services\CertificateIssuerService;
use App\Notifications\CertificateEarnedNotification;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display a listing of certificates with filters.
     */
    public function index(Request $request)
    {
        $query = Certificate::with(['user', 'course', 'certificateTemplate']);

        // Apply filters
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'revoked') {
                $query->where('is_revoked', true);
            } elseif ($request->status === 'active') {
                $query->where('is_revoked', false);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $certificates = $query->latest('issue_date')->paginate(20);

        // Get courses for filter dropdown
        $courses = Course::select('id', 'title')->get();

        // Get statistics
        $stats = [
            'total' => Certificate::count(),
            'active' => Certificate::where('is_revoked', false)->count(),
            'revoked' => Certificate::where('is_revoked', true)->count(),
            'this_month' => Certificate::whereMonth('issue_date', now()->month)
                ->whereYear('issue_date', now()->year)
                ->count(),
        ];

        return view('admin.certificates.index', compact('certificates', 'courses', 'stats'));
    }

    /**
     * Show the form for creating a new certificate (manual issuance).
     */
    public function create()
    {
        $users = User::role('student')->select('id', 'name', 'email')->get();
        $courses = Course::where('status', 'published')
            ->where('certificate_enabled', true)
            ->select('id', 'title')
            ->get();
        $templates = CertificateTemplate::active()->get();

        return view('admin.certificates.create', compact('users', 'courses', 'templates'));
    }

    /**
     * Store a newly created certificate (manual issuance).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'metadata' => 'nullable|array',
        ]);

        // Check if certificate already exists
        $existing = Certificate::where('user_id', $validated['user_id'])
            ->where('course_id', $validated['course_id'])
            ->first();

        if ($existing) {
            return back()->withErrors(['error' => 'Certificate already exists for this user and course.']);
        }

        // Get course and template
        $course = Course::findOrFail($validated['course_id']);
        $templateId = $validated['certificate_template_id'] ??
            $course->certificate_template_id ??
            CertificateTemplate::where('is_default', true)->first()?->id;

        // Create certificate
        $certificate = Certificate::create([
            'user_id' => $validated['user_id'],
            'course_id' => $validated['course_id'],
            'certificate_template_id' => $templateId,
            'certificate_number' => Certificate::generateCertificateNumber(),
            'verification_hash' => Certificate::generateVerificationHash(),
            'issue_date' => $validated['issue_date'] ?? now(),
            'expiry_date' => $validated['expiry_date'] ?? null,
            'metadata' => $validated['metadata'] ?? [],
            'is_revoked' => false,
        ]);

        // Generate PDF
        try {
            $service = app(\App\Services\CertificateGeneratorService::class);
            $service->generate($certificate);
        } catch (\Exception $e) {
            \Log::error('Failed to generate certificate PDF: ' . $e->getMessage());
        }

        // Send notification
        $user = User::find($validated['user_id']);
        $user->notify(new CertificateEarnedNotification($certificate));

        return redirect()->route('admin.certificates.show', $certificate)
            ->with('success', 'Certificate issued successfully!');
    }

    /**
     * Display the specified certificate.
     */
    public function show(Certificate $certificate)
    {
        $certificate->load(['user', 'course', 'certificateTemplate']);

        return view('admin.certificates.show', compact('certificate'));
    }

    /**
     * Revoke the specified certificate.
     */
    public function revoke(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $certificate->revoke($validated['reason']);

        return back()->with('success', 'Certificate revoked successfully.');
    }

    /**
     * Restore a revoked certificate.
     */
    public function restore(Certificate $certificate)
    {
        if (!$certificate->isRevoked()) {
            return back()->withErrors(['error' => 'Certificate is not revoked.']);
        }

        $certificate->restore();

        return back()->with('success', 'Certificate restored successfully.');
    }

    /**
     * Preview certificate in browser.
     */
    public function preview(Certificate $certificate)
    {
        $certificate->load(['user', 'course', 'certificateTemplate']);

        // Parse template data
        $data = [
            'student_name' => $certificate->user->name,
            'student_email' => $certificate->user->email,
            'course_name' => $certificate->course->title,
            'completion_date' => $certificate->issue_date->format('F j, Y'),
            'certificate_number' => $certificate->certificate_number,
            'verification_url' => route('certificates.verify', ['hash' => $certificate->verification_hash]),
            'instructor_name' => $certificate->course->instructor->name ?? 'Instructor',
            'platform_name' => config('app.name', 'Master IELTS'),
        ];

        return view('certificates.pdf-template', [
            'certificate' => $certificate,
            'template' => $certificate->certificateTemplate,
            'data' => $data,
        ]);
    }

    /**
     * Download certificate PDF.
     */
    public function download(Certificate $certificate)
    {
        if ($certificate->isRevoked()) {
            abort(403, 'Cannot download revoked certificate.');
        }

        // Track download
        $certificate->incrementDownloadCount();

        // Generate PDF
        $service = app(\App\Services\CertificateGeneratorService::class);
        return $service->downloadPDF($certificate);
    }

    /**
     * Remove the specified certificate from storage.
     */
    public function destroy(Certificate $certificate)
    {
        // Delete the file if it exists
        if ($certificate->file_path && \Storage::exists($certificate->file_path)) {
            \Storage::delete($certificate->file_path);
        }

        $certificate->delete();

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate deleted successfully.');
    }

    /**
     * Bulk regenerate certificates for a course.
     */
    public function bulkRegenerate(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $certificates = Certificate::where('course_id', $validated['course_id'])
            ->where('is_revoked', false)
            ->get();

        $service = app(\App\Services\CertificateGeneratorService::class);
        $count = 0;

        foreach ($certificates as $certificate) {
            try {
                $service->generate($certificate);
                $count++;
            } catch (\Exception $e) {
                \Log::error("Failed to regenerate certificate {$certificate->id}: " . $e->getMessage());
            }
        }

        return back()->with('success', "Successfully regenerated {$count} certificates.");
    }

    /**
     * Display certificate analytics.
     */
    public function analytics()
    {
        // Total certificates
        $totalCertificates = Certificate::count();

        // Certificates by status
        $activeCertificates = Certificate::where('is_revoked', false)->count();
        $revokedCertificates = Certificate::where('is_revoked', true)->count();

        // Certificates by month (last 12 months)
        $certificatesByMonth = Certificate::selectRaw('DATE_FORMAT(issue_date, "%Y-%m") as month, COUNT(*) as count')
            ->where('issue_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top courses by certificates issued
        $topCourses = Certificate::selectRaw('course_id, COUNT(*) as count')
            ->groupBy('course_id')
            ->with('course:id,title')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Recent certificates
        $recentCertificates = Certificate::with(['user', 'course'])
            ->latest('issue_date')
            ->limit(10)
            ->get();

        // Download statistics
        $totalDownloads = Certificate::sum('download_count');
        $avgDownloadsPerCertificate = $totalCertificates > 0
            ? round($totalDownloads / $totalCertificates, 2)
            : 0;

        return view('admin.certificates.analytics', compact(
            'totalCertificates',
            'activeCertificates',
            'revokedCertificates',
            'certificatesByMonth',
            'topCourses',
            'recentCertificates',
            'totalDownloads',
            'avgDownloadsPerCertificate'
        ));
    }
}
