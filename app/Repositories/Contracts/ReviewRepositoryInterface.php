<?php

namespace App\Repositories\Contracts;

use App\Models\Review;
use Illuminate\Database\Eloquent\Collection;

interface ReviewRepositoryInterface extends BaseRepository
{
    public function findByReviewable(string $type, int $id): Collection;
    public function findByUser(int $userId): Collection;
    public function getAverageRating(string $type, int $id): float;
    public function findPending(): Collection;
}
