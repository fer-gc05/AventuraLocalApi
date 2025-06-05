<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Destination extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'latitude',
        'longitude',
        'price',
        'currency',
        'opening_hours',
        'contact_phone',
        'contact_email',
        'category_id',
        'user_id',
        'is_featured',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'destination_tag',
            'destination_id',
            'tag_id'
        );
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(
            Route::class,
            'route_destination',
            'destination_id',
            'route_id'
        );
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'destination_user')
            ->withPivot('is_favorite')
            ->withTimestamps();
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function nearbyDestinations(float $radius)
    {
        $earthRadius = 6371; // Radius of Earth in kilometers
    
        return Destination::select('*')
            ->selectRaw(
                "( $earthRadius * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance",
                [$this->latitude, $this->longitude, $this->latitude]
            )
            ->where('id', '!=', $this->id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');
    }
}
