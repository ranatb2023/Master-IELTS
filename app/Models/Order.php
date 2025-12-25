<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'type',
        'subtotal',
        'discount',
        'tax',
        'total',
        'currency',
        'status',
        'payment_method',
        'payment_id',
        'notes',
        'metadata',
        'payment_intent_id',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'metadata' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Alias for orderItems
    public function items()
    {
        return $this->orderItems();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function couponUsage()
    {
        return $this->hasOne(CouponUsage::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Methods
    public function isPaid(): bool
    {
        return $this->status === 'completed';
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'completed',
        ]);

        return $this;
    }

    public function markAsFailed()
    {
        $this->update([
            'status' => 'failed',
        ]);

        return $this;
    }

    public function markAsPending()
    {
        $this->update([
            'status' => 'pending',
        ]);

        return $this;
    }

    public function markAsProcessing()
    {
        $this->update([
            'status' => 'processing',
        ]);

        return $this;
    }

    public function markAsRefunded()
    {
        $this->update([
            'status' => 'refunded',
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

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }
}
