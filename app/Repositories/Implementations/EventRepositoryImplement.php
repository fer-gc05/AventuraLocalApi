<?php

namespace App\Repositories\Implementations;

use App\Models\Event;
use App\Repositories\Contracts\EventRepositoryInterface;

class EventRepositoryImplement extends BaseRepositoryImplement implements EventRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Event);
    }
}
