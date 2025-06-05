<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'file_name',
        'file_path',
        'url',
        'file_type',
        'file_size',
        'model_id',
        'model_type',
        'custom_properties',
    ];

    protected $casts = [
        'custom_properties' => 'array',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
