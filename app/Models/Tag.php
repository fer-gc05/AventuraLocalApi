<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'color'
    ];

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
