<?php

namespace App\Repositories\Implementations;

use App\Models\Review;
use App\Repositories\Contracts\ReviewRepositoryInterface;

class ReviewRepositoryImplement extends BaseRepositoryImplement implements ReviewRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Review);
    }
}
