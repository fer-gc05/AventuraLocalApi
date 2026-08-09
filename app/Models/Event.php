<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
        'latitude',
        'longitude',
        'price',
        'currency',
        'max_attendees',
        'user_id',
        'destination_id',
        'category_id',
    ];

    protected static function booted(): void
    {
        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'price' => 'decimal:2',
            'max_attendees' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function routes()
    {
        return $this->belongsToMany(
            Route::class,
            'route_event',
            'event_id',
            'route_id'
        );
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'event_user',
            'event_id',
            'user_id'
        )->withPivot('status');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
