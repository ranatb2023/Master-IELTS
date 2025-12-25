<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\User;
use App\Models\Course;
use App\Models\CertificateTemplate;
use App\Notifications\CertificateEarnedNotification;
use Illuminate\Support\Facades\Log;

class CertificateIssuerService
{
    /**
     * Issue a certificate for a user completing a course.
     */
    public function issueCertificate(User $user, Course $course): ?Certificate
    {
        // Check eligibility
        if (!$this->checkEligibility($user, $course)) {
            Log::info("User {$user->id} not eligible for certificate in course {$course->id}");
            return null;
        }

        // Check if certificate already exists
        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            Log::info("Certificate already exists for user {$user->id} in course {$course->id}");
            return $existing;
        }

        // Get certificate template
        $template = $this->getCertificateTemplate($course);

        if (!$template) {
            Log::error("No certificate template found for course {$course->id}");
            return null;
        }

        // Create certificate
        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'certificate_template_id' => $template->id,
            'certificate_number' => Certificate::generateCertificateNumber(),
            'verification_hash' => Certificate::generateVerificationHash(),
            'issue_date' => now(),
            'expiry_date' => null, // No expiry by default
            'metadata' => [
                'auto_issued' => true,
                'issued_at' => now()->toIso8601String(),
            ],
            'is_revoked' => false,
        ]);

        // Generate PDF asynchronously
        try {
            $generatorService = app(CertificateGeneratorService::class);
            $generatorService->generate($certificate);
        } catch (\Exception $e) {
            Log::error("Failed to generate PDF for certificate {$certificate->id}: " . $e->getMessage());
        }

        // Send notification
        try {
            $user->notify(new CertificateEarnedNotification($certificate));
        } catch (\Exception $e) {
            Log::error("Failed to send certificate notification: " . $e->getMessage());
        }

        Log::info("Certificate {$certificate->certificate_number} issued for user {$user->id} in course {$course->id}");

        return $certificate;
    }

    /**
     * Check if user is eligible for a certificate.
     */
    public function checkEligibility(User $user, Course $course): bool
    {
        // Check if certificates are enabled for this course
        if (!$course->certificate_enabled) {
            return false;
        }

        // Get user's enrollment
        $enrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return false;
        }

        // Check if enrollment is completed or active
        if (!in_array($enrollment->status, ['completed', 'active'])) {
            return false;
        }

        // Check progress percentage (must be 100% as per user requirement)
        if ($enrollment->progress_percentage < 100) {
            return false;
        }

        return true;
    }

    /**
     * Get the certificate template for a course.
     */
    protected function getCertificateTemplate(Course $course): ?CertificateTemplate
    {
        // Use course-specific template if set
        if ($course->certificate_template_id) {
            return CertificateTemplate::find($course->certificate_template_id);
        }

        // Fall back to default template
        return CertificateTemplate::where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Manually issue certificate (for admin override).
     */
    public function manualIssue(User $user, Course $course, ?int $templateId = null, array $metadata = []): Certificate
    {
        // Get template
        if ($templateId) {
            $template = CertificateTemplate::findOrFail($templateId);
        } else {
            $template = $this->getCertificateTemplate($course);
        }

        // Create certificate
        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'certificate_template_id' => $template->id,
            'certificate_number' => Certificate::generateCertificateNumber(),
            'verification_hash' => Certificate::generateVerificationHash(),
            'issue_date' => now(),
            'expiry_date' => $metadata['expiry_date'] ?? null,
            'metadata' => array_merge([
                'manually_issued' => true,
                'issued_at' => now()->toIso8601String(),
            ], $metadata),
            'is_revoked' => false,
        ]);

        // Generate PDF
        try {
            $generatorService = app(CertificateGeneratorService::class);
            $generatorService->generate($certificate);
        } catch (\Exception $e) {
            Log::error("Failed to generate PDF for manual certificate {$certificate->id}: " . $e->getMessage());
        }

        // Send notification
        try {
            $user->notify(new CertificateEarnedNotification($certificate));
        } catch (\Exception $e) {
            Log::error("Failed to send manual certificate notification: " . $e->getMessage());
        }

        return $certificate;
    }

    /**
     * Batch issue certificates for multiple users.
     */
    public function batchIssue(Course $course): array
    {
        $issued = [];

        // Get eligible users
        $eligibleEnrollments = $course->enrollments()
            ->where('progress_percentage', '>=', 100)
            ->whereIn('status', ['completed', 'active'])
            ->with('user')
            ->get();

        foreach ($eligibleEnrollments as $enrollment) {
            $certificate = $this->issueCertificate($enrollment->user, $course);

            if ($certificate) {
                $issued[] = $certificate;
            }
        }

        return $issued;
    }
}
