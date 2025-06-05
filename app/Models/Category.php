<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description'
    ];

    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }

    public function communities(): HasMany
    {
        return $this->hasMany(Community::class);
    }
}
