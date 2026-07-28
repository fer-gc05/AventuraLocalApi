<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuideProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'bio',
        'phone',
        'languages',
        'specialties',
        'documents',
        'verification_status',
        'rejection_reason',
        'is_verified',
        'average_rating',
        'total_reviews',
        'available_balance',
        'pending_balance',
        'total_earnings',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'specialties' => 'array',
            'documents' => 'array',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'average_rating' => 'decimal:2',
            'available_balance' => 'decimal:2',
            'pending_balance' => 'decimal:2',
            'total_earnings' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TourSchedule::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
