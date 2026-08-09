<?php

namespace App\Repositories\Implementations;

use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TagRepositoryImplement extends BaseRepositoryImplement implements TagRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Tag);
    }

    public function findPopular(int $limit = 10): Collection
    {
        return $this->model
            ->withCount('destinations')
            ->orderByDesc('destinations_count')
            ->limit($limit)
            ->get();
    }

    public function search(string $term): Collection
    {
        return $this->model
            ->where('name', 'like', "%{$term}%")
            ->get();
    }
}
