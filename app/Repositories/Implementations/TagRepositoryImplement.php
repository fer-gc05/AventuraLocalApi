<?php

namespace App\Repositories\Implementations;

use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;

class TagRepositoryImplement extends BaseRepositoryImplement implements TagRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Tag);
    }
}
