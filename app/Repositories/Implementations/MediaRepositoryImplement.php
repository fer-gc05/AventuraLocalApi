<?php

namespace App\Repositories\Implementations;

use App\Models\Media;
use App\Repositories\Contracts\MediaRepositoryInterface;

class MediaRepositoryImplement extends BaseRepositoryImplement implements MediaRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Media);
    }
}
