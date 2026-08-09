<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tag extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'color'
    ];

    protected static function booted(): void
    {
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(
            Destination::class,
            'destination_tag',
            'tag_id',
            'destination_id'
        );
    }
}
