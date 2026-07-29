<?php

namespace App\Repositories\Implementations;

use App\Models\Community;
use App\Repositories\Contracts\CommunityRepositoryInterface;

class CommunityRepositoryImplement extends BaseRepositoryImplement implements CommunityRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Community);
    }
}
