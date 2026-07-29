<?php

namespace App\Repositories\Implementations;

use App\Models\Route;
use App\Repositories\Contracts\RouteRepositoryInterface;

class RouteRepositoryImplement extends BaseRepositoryImplement implements RouteRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Route);
    }
}
