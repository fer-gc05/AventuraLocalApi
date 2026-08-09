<?php

namespace App\Repositories\Implementations;

use App\Models\Route;
use App\Repositories\Contracts\RouteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RouteRepositoryImplement extends BaseRepositoryImplement implements RouteRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Route);
    }

    public function findByDestination(int $destinationId): Collection
    {
        return $this->model
            ->whereHas('destinations', function ($q) use ($destinationId) {
                $q->where('destinations.id', $destinationId);
            })
            ->with(['user', 'destinations'])
            ->get();
    }

    public function findPopular(int $limit = 10): Collection
    {
        return $this->model
            ->withCount(['users as favorites_count' => function ($q) {
                $q->wherePivot('is_favorite', true);
            }])
            ->with(['user', 'destinations'])
            ->orderByDesc('favorites_count')
            ->limit($limit)
            ->get();
    }

    public function findByUser(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->with(['destinations'])
            ->get();
    }

    public function findWithRelations(int $id): ?Route
    {
        return $this->model
            ->with(['user', 'destinations', 'tours', 'reviews.user', 'communities'])
            ->find($id);
    }
}
