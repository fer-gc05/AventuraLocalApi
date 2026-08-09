<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface extends BaseRepository
{
    public function findBySlug(string $slug): ?Category;
    public function findPopular(int $limit = 10): Collection;
}
