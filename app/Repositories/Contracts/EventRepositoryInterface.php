<?php

namespace App\Repositories\Contracts;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface EventRepositoryInterface extends BaseRepository
{
    public function findNearby(float $lat, float $lng, float $radius): Collection;
    public function findUpcoming(int $limit = 10): Collection;
    public function findPopular(int $limit = 10): Collection;
    public function findByDateRange(Carbon $start, Carbon $end): Collection;
    public function findByDestination(int $destinationId): Collection;
    public function findByCategory(int $categoryId): Collection;
    public function search(string $term): Collection;
    public function findWithRelations(int $id): ?Event;
}
