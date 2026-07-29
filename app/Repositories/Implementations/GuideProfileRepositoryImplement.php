<?php

namespace App\Repositories\Implementations;

use App\Models\GuideProfile;
use App\Repositories\Contracts\GuideProfileRepositoryInterface;

class GuideProfileRepositoryImplement extends BaseRepositoryImplement implements GuideProfileRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new GuideProfile);
    }
}
