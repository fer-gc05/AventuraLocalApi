<?php

namespace App\Repositories\Implementations;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepositoryImplement extends BaseRepositoryImplement implements CategoryRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Category);
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function findPopular(int $limit = 10): Collection
    {
        return $this->model
            ->withCount(['destinations', 'communities'])
            ->orderByDesc('destinations_count')
            ->limit($limit)
            ->get();
    }
}
