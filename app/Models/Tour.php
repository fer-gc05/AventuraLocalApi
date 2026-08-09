<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tour extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'guide_id',
        'category_id',
        'destination_id',
        'meeting_point',
        'meeting_latitude',
        'meeting_longitude',
        'price_per_person',
        'currency',
        'duration_minutes',
        'max_participants',
        'min_participants',
        'languages',
        'includes',
        'excludes',
        'what_to_bring',
        'difficulty',
        'status',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function ($tour) {
            if (empty($tour->slug)) {
                $tour->slug = Str::slug($tour->title);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'meeting_latitude' => 'decimal:8',
            'meeting_longitude' => 'decimal:8',
            'price_per_person' => 'decimal:2',
            'duration_minutes' => 'integer',
            'max_participants' => 'integer',
            'min_participants' => 'integer',
            'languages' => 'array',
            'includes' => 'array',
            'excludes' => 'array',
            'what_to_bring' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function guide(): BelongsTo
    {
        return $this->belongsTo(GuideProfile::class, 'guide_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TourSchedule::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }
}
