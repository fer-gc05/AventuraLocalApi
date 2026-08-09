<?php

namespace App\Repositories\Contracts;

use App\Models\Route;
use Illuminate\Database\Eloquent\Collection;

interface RouteRepositoryInterface extends BaseRepository
{
    public function findByDestination(int $destinationId): Collection;
    public function findPopular(int $limit = 10): Collection;
    public function findByUser(int $userId): Collection;
    public function findWithRelations(int $id): ?Route;
}
