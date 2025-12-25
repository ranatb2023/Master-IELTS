<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display all earned certificates
     */
    public function index()
    {
        $certificates = auth()->user()->certificates()
            ->with(['course', 'certificateTemplate'])
            ->latest('issue_date')
            ->paginate(12);

        return view('student.certificates.index', compact('certificates'));
    }

    /**
     * Display certificate details
     */
    public function show(Certificate $certificate)
    {
        // Ensure user owns this certificate
        if ($certificate->user_id !== auth()->id()) {
            abort(403);
        }

        $certificate->load(['course', 'user', 'certificateTemplate']);

        return view('student.certificates.show', compact('certificate'));
    }

    /**
     * Download certificate as PDF
     */
    public function download(Certificate $certificate)
    {
        // Ensure user owns this certificate
        if ($certificate->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if certificate is revoked
        if ($certificate->isRevoked()) {
            abort(403, 'This certificate has been revoked.');
        }

        // Track download
        $certificate->incrementDownloadCount();

        // Generate PDF
        $service = app(\App\Services\CertificateGeneratorService::class);
        return $service->downloadPDF($certificate);
    }

    /**
     * Verify certificate by hash or number
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'hash' => 'nullable|string',
            'number' => 'nullable|string',
        ]);

        $query = Certificate::with(['user', 'course', 'certificateTemplate']);

        if (!empty($validated['hash'])) {
            $certificate = $query->where('verification_hash', $validated['hash'])->first();
        } elseif (!empty($validated['number'])) {
            $certificate = $query->where('certificate_number', $validated['number'])->first();
        } else {
            return view('student.certificates.verify', ['certificate' => null]);
        }

        if (!$certificate) {
            return view('student.certificates.verify', [
                'certificate' => null,
                'error' => 'Invalid certificate code or hash.'
            ]);
        }

        return view('student.certificates.verify', compact('certificate'));
    }
}
