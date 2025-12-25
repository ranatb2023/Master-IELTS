<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_template_id',
        'certificate_number',
        'issue_date',
        'expiry_date',
        'file_path',
        'metadata',
        'verification_hash',
        'verification_url',
        'is_revoked',
        'revoked_at',
        'revoked_reason',
        'download_count',
        'last_downloaded_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'expiry_date' => 'date',
            'metadata' => 'array',
            'is_revoked' => 'boolean',
            'revoked_at' => 'datetime',
            'download_count' => 'int',
            'last_downloaded_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the certificate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course associated with this certificate.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the certificate template used for this certificate.
     */
    public function certificateTemplate(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class);
    }

    /**
     * Get the enrollment associated with this certificate.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'user_id', 'user_id')
            ->where('course_id', $this->course_id);
    }

    /**
     * Generate a unique certificate number.
     */
    public static function generateCertificateNumber(): string
    {
        do {
            $number = 'CERT-' . strtoupper(uniqid()) . '-' . now()->format('Y');
        } while (self::where('certificate_number', $number)->exists());

        return $number;
    }

    /**
     * Generate a unique verification hash.
     */
    public static function generateVerificationHash(): string
    {
        do {
            $hash = hash('sha256', uniqid() . time() . random_bytes(16));
        } while (self::where('verification_hash', $hash)->exists());

        return $hash;
    }

    /**
     * Get the verification URL attribute.
     */
    public function getVerificationUrlAttribute(): string
    {
        return route('certificates.verify') . '?hash=' . $this->verification_hash;
    }

    /**
     * Revoke the certificate with a given reason.
     */
    public function revoke(string $reason): bool
    {
        $this->is_revoked = true;
        $this->revoked_at = now();
        $this->revoked_reason = $reason;

        return $this->save();
    }

    /**
     * Restore a revoked certificate.
     */
    public function restore(): bool
    {
        $this->is_revoked = false;
        $this->revoked_at = null;
        $this->revoked_reason = null;

        return $this->save();
    }

    /**
     * Check if the certificate is revoked.
     */
    public function isRevoked(): bool
    {
        return $this->is_revoked === true;
    }

    /**
     * Check if the certificate is expired.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && now()->isAfter($this->expiry_date);
    }

    /**
     * Increment the download count and update the last download timestamp.
     */
    public function incrementDownloadCount(): bool
    {
        $this->download_count = ($this->download_count ?? 0) + 1;
        $this->last_downloaded_at = now();

        return $this->save();
    }
}
