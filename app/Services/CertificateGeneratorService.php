<?php

namespace App\Services;

use App\Models\Certificate;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class CertificateGeneratorService
{
    /**
     * Generate PDF certificate and save to storage.
     */
    public function generate(Certificate $certificate): string
    {
        // Load relationships
        $certificate->load(['user', 'course', 'certificateTemplate']);

        // Parse template data
        $data = $this->parseTemplateData($certificate);

        // Generate QR code for verification
        $qrCode = $this->generateQRCode($certificate);

        // Merge QR code into data
        $data['qr_code'] = $qrCode;

        // Generate PDF
        $pdf = Pdf::view('certificates.pdf-template', [
            'certificate' => $certificate,
            'template' => $certificate->certificateTemplate,
            'data' => $data,
        ])
            ->format($certificate->certificateTemplate->page_size ?? 'a4')
            ->orientation($certificate->certificateTemplate->orientation ?? 'landscape')
            ->name("certificate_{$certificate->certificate_number}.pdf");

        // Save PDF to storage
        $filePath = $this->savePDF($certificate, $pdf);

        // Update certificate with file path
        $certificate->update([
            'file_path' => $filePath,
            'verification_url' => route('certificates.verify', ['hash' => $certificate->verification_hash]),
        ]);

        return $filePath;
    }

    /**
     * Download PDF certificate.
     */
    public function downloadPDF(Certificate $certificate)
    {
        $filePath = storage_path("app/{$certificate->file_path}");

        // Generate PDF if it doesn't exist
        if (!file_exists($filePath)) {
            $this->generate($certificate);
        }

        return response()->download(
            $filePath,
            "certificate_{$certificate->certificate_number}.pdf"
        );
    }

    /**
     * Parse template data with dynamic placeholders.
     */
    protected function parseTemplateData(Certificate $certificate): array
    {
        return [
            'student_name' => $certificate->user->name,
            'student_email' => $certificate->user->email,
            'course_name' => $certificate->course->title,
            'course_description' => $certificate->course->description,
            'completion_date' => $certificate->issue_date->format('F j, Y'),
            'issue_date' => $certificate->issue_date->format('F j, Y'),
            'certificate_number' => $certificate->certificate_number,
            'verification_url' => route('certificates.verify', ['hash' => $certificate->verification_hash]),
            'verification_hash' => $certificate->verification_hash,
            'instructor_name' => $certificate->course->instructor->name ?? 'Instructor',
            'course_duration' => $certificate->course->duration_hours
                ? $certificate->course->duration_hours . ' hours'
                : 'N/A',
            'current_year' => now()->year,
            'platform_name' => config('app.name', 'Master IELTS'),
        ];
    }

    /**
     * Generate QR code for certificate verification.
     */
    protected function generateQRCode(Certificate $certificate): string
    {
        $verificationUrl = route('certificates.verify', ['hash' => $certificate->verification_hash]);

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);

        return $writer->writeString($verificationUrl);
    }

    /**
     * Save PDF to storage.
     */
    protected function savePDF(Certificate $certificate, $pdf): string
    {
        $fileName = "certificate_{$certificate->id}_{$certificate->certificate_number}.pdf";
        $filePath = "certificates/{$fileName}";

        // Save PDF directly to storage
        $fullPath = storage_path("app/{$filePath}");

        // Ensure directory exists
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Save the PDF
        $pdf->save($fullPath);

        return $filePath;
    }

    /**
     * Delete certificate PDF from storage.
     */
    public function deletePDF(Certificate $certificate): bool
    {
        if ($certificate->file_path && Storage::exists($certificate->file_path)) {
            return Storage::delete($certificate->file_path);
        }

        return false;
    }
}
