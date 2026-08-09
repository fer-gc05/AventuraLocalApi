<?php

namespace App\Repositories\Contracts;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Collection;

interface TourRepositoryInterface extends BaseRepository
{
    public function findByGuide(int $guideId): Collection;
    public function findByCategory(int $categoryId): Collection;
    public function findByDestination(int $destinationId): Collection;
    public function findUpcoming(): Collection;
    public function findPopular(int $limit = 10): Collection;
    public function searchByLocation(float $lat, float $lng, float $radius): Collection;
    public function search(string $term): Collection;
    public function findWithRelations(int $id): ?Tour;
}
