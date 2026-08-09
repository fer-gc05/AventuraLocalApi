<?php

namespace App\Repositories\Implementations;

use App\Models\Destination;
use App\Repositories\Contracts\DestinationRepositoryInterface;
use App\Traits\HasNearbyScope;
use Illuminate\Database\Eloquent\Collection;

class DestinationRepositoryImplement extends BaseRepositoryImplement implements DestinationRepositoryInterface
{
    use HasNearbyScope;

    public function __construct()
    {
        parent::__construct(new Destination);
    }

    public function findNearby(float $lat, float $lng, float $radius): Collection
    {
        return $this->nearbyQuery(
            $this->model->newQuery()->where('is_approved', true),
            $lat, $lng, $radius
        )->with(['category', 'media'])->get();
    }

    public function findByCategory(int $categoryId): Collection
    {
        return $this->model
            ->where('category_id', $categoryId)
            ->with(['category', 'media'])
            ->get();
    }

    public function findPopular(int $limit = 10): Collection
    {
        return $this->model
            ->where('is_approved', true)
            ->withCount(['reviews', 'events'])
            ->with(['category', 'media'])
            ->orderByDesc('reviews_count')
            ->limit($limit)
            ->get();
    }

    public function search(string $term): Collection
    {
        return $this->model
            ->where('is_approved', true)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('city', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            })
            ->with(['category'])
            ->get();
    }

    public function findWithDetails(int $id): ?Destination
    {
        return $this->model
            ->with(['category', 'user', 'tags', 'media', 'reviews.user', 'events', 'tours'])
            ->find($id);
    }

    public function findByUser(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->with(['category', 'media'])
            ->get();
    }
}
