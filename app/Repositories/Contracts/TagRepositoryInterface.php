<?php

namespace App\Repositories\Contracts;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

interface TagRepositoryInterface extends BaseRepository
{
    public function findPopular(int $limit = 10): Collection;
    public function search(string $term): Collection;
}
