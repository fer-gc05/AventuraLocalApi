<?php

namespace App\Repositories\Implementations;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepositoryImplement extends BaseRepositoryImplement implements CategoryRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Category);
    }
}
