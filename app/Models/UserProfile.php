<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'headline',
        'website',
        'twitter',
        'facebook',
        'linkedin',
        'youtube',
        'github',
        'interests',
        'skills',
        'education',
        'experience',
    ];

    protected function casts(): array
    {
        return [
            'interests' => 'array',
            'skills' => 'array',
            'education' => 'array',
            'experience' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}