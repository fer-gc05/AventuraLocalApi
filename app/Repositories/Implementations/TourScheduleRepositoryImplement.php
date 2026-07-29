<?php

namespace App\Repositories\Implementations;

use App\Models\TourSchedule;
use App\Repositories\Contracts\TourScheduleRepositoryInterface;

class TourScheduleRepositoryImplement extends BaseRepositoryImplement implements TourScheduleRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new TourSchedule);
    }
}
