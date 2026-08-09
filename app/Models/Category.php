<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description'
    ];

    protected static function booted(): void
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }

    public function communities(): HasMany
    {
        return $this->hasMany(Community::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
