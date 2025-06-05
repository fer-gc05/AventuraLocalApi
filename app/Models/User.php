<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the JWT token.
     *
     * @return string
     */

    public function getJWTIdentifier(): string
    {
        return (string)$this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array<string, mixed>
     */

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function routes(): BelongsToMany
{
    return $this->belongsToMany(Route::class, 'route_user', 'user_id', 'route_id')
        ->withPivot('is_favorite', 'status', 'completed_at');
}

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function createdCommunities(): HasMany
    {
        return $this->hasMany(Community::class);
    }

    public function communities(): BelongsToMany
    {
        return $this->belongsToMany(
            Community::class,
            'community_user',
            'user_id',
            'community_id',
        )->withPivot('role');
    }

    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(
            Event::class,
            'event_user',
            'user_id',
            'event_id'
        )->withPivot('status');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function attendedEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_user')
            ->wherePivot('status', 'attended');
    }

    public function favoriteRoutes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'route_user')
            ->wherePivot('is_favorite', true);
    }

    public function completedRoutes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'route_user')
            ->wherePivot('status', 'completed');
    }

    public function favoriteDestinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class, 'destination_user')
            ->wherePivot('is_favorite', true);
    }
}
