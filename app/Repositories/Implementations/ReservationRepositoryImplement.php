<?php

namespace App\Repositories\Implementations;

use App\Models\Reservation;
use App\Repositories\Contracts\ReservationRepositoryInterface;

class ReservationRepositoryImplement extends BaseRepositoryImplement implements ReservationRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Reservation);
    }
}
