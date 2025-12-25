<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'subscription_id',
        'number',
        'pdf_path',
        'data',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'data' => 'array',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid')
            ->whereNotNull('paid_at');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid')
            ->orWhere('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now());
    }

    // Methods
    public function isPaid(): bool
    {
        return $this->status === 'paid' && !is_null($this->paid_at);
    }

    public function isOverdue(): bool
    {
        if ($this->isPaid()) {
            return false;
        }

        return $this->due_at && $this->due_at->isPast();
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return $this;
    }

    public function markAsUnpaid()
    {
        $this->update([
            'status' => 'unpaid',
            'paid_at' => null,
        ]);

        return $this;
    }

    public function markAsVoid()
    {
        $this->update([
            'status' => 'void',
        ]);

        return $this;
    }

    public function getFormattedTotalAttribute()
    {
        $currencySymbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ];

        $symbol = $currencySymbols[$this->currency] ?? $this->currency . ' ';
        return $symbol . number_format($this->total, 2);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->number)) {
                $invoice->number = 'INV-' . strtoupper(uniqid());
            }

            if (empty($invoice->issued_at)) {
                $invoice->issued_at = now();
            }
        });
    }
}
