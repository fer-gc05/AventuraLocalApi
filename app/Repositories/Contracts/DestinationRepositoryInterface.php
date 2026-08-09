<?php

namespace App\Repositories\Contracts;

use App\Models\Destination;
use Illuminate\Database\Eloquent\Collection;

interface DestinationRepositoryInterface extends BaseRepository
{
    public function findNearby(float $lat, float $lng, float $radius): Collection;
    public function findByCategory(int $categoryId): Collection;
    public function findPopular(int $limit = 10): Collection;
    public function search(string $term): Collection;
    public function findWithDetails(int $id): ?Destination;
    public function findByUser(int $userId): Collection;
}
