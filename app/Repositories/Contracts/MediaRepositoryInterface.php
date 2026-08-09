<?php

namespace App\Repositories\Contracts;

use App\Models\Media;
use Illuminate\Database\Eloquent\Collection;

interface MediaRepositoryInterface extends BaseRepository
{
    public function findByModel(string $type, int $id): Collection;
}
