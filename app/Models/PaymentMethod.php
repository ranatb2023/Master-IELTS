<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'provider',
        'provider_payment_method_id',
        'last_four',
        'brand',
        'exp_month',
        'exp_year',
        'is_default',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'exp_month' => 'integer',
            'exp_year' => 'integer',
            'is_default' => 'boolean',
            'metadata' => 'array',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    // Methods
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    public function isExpired(): bool
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        if ($this->exp_year < $currentYear) {
            return true;
        }

        if ($this->exp_year == $currentYear && $this->exp_month < $currentMonth) {
            return true;
        }

        return false;
    }

    public function setAsDefault()
    {
        // Remove default from other payment methods
        $this->user->paymentMethods()
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);

        return $this;
    }

    public function getDisplayNameAttribute()
    {
        if ($this->brand && $this->last_four) {
            return ucfirst($this->brand) . ' •••• ' . $this->last_four;
        }

        return ucfirst($this->type);
    }
}
