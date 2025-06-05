<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model   
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'total_distance',
        'estimated_duration',
        'difficulty',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'total_distance' => 'decimal:2',
            'estimated_duration' => 'integer',
        ];
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(
            Destination::class,
            'route_destination',
            'route_id',
            'destination_id'
        );
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }


    public function communities(): BelongsToMany
    {
        return $this->belongsToMany(
            Community::class,
            'route_community',
            'route_id',
            'community_id'
        );
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(
            Event::class,
            'route_event',
            'route_id',
            'event_id'
        );
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'route_user',
            'route_id',
            'user_id'
        );
    }
}
