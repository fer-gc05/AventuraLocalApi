<?php

namespace App\Repositories\Implementations;

use App\Models\Tour;
use App\Repositories\Contracts\TourRepositoryInterface;

class TourRepositoryImplement extends BaseRepositoryImplement implements TourRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Tour);
    }
}
