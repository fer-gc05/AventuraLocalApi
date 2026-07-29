<?php

namespace App\Repositories\Implementations;

use App\Models\Destination;
use App\Repositories\Contracts\DestinationRepositoryInterface;

class DestinationRepositoryImplement extends BaseRepositoryImplement implements DestinationRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Destination);
    }
}
