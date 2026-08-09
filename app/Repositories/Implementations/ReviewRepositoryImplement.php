<?php

namespace App\Repositories\Implementations;

use App\Models\Review;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ReviewRepositoryImplement extends BaseRepositoryImplement implements ReviewRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Review);
    }

    public function findByReviewable(string $type, int $id): Collection
    {
        return $this->model
            ->where('reviewable_type', $type)
            ->where('reviewable_id', $id)
            ->where('status', 'approved')
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByUser(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->with(['reviewable'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAverageRating(string $type, int $id): float
    {
        $result = $this->model
            ->where('reviewable_type', $type)
            ->where('reviewable_id', $id)
            ->where('status', 'approved')
            ->avg('rating');

        return round($result ?? 0, 2);
    }

    public function findPending(): Collection
    {
        return $this->model
            ->where('status', 'pending')
            ->with(['user', 'reviewable'])
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
