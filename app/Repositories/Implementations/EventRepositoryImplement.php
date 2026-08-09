<?php

namespace App\Repositories\Implementations;

use App\Models\Event;
use App\Repositories\Contracts\EventRepositoryInterface;
use App\Traits\HasNearbyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class EventRepositoryImplement extends BaseRepositoryImplement implements EventRepositoryInterface
{
    use HasNearbyScope;

    public function __construct()
    {
        parent::__construct(new Event);
    }

    public function findNearby(float $lat, float $lng, float $radius): Collection
    {
        return $this->nearbyQuery(
            $this->model->newQuery()->where('start_datetime', '>', now()),
            $lat, $lng, $radius
        )->with(['user', 'destination'])->get();
    }

    public function findUpcoming(int $limit = 10): Collection
    {
        return $this->model
            ->where('start_datetime', '>', now())
            ->with(['user', 'destination'])
            ->orderBy('start_datetime', 'asc')
            ->limit($limit)
            ->get();
    }

    public function findPopular(int $limit = 10): Collection
    {
        return $this->model
            ->where('start_datetime', '>', now())
            ->withCount('attendees')
            ->with(['user', 'destination'])
            ->orderByDesc('attendees_count')
            ->limit($limit)
            ->get();
    }

    public function findByDateRange(Carbon $start, Carbon $end): Collection
    {
        return $this->model
            ->whereBetween('start_datetime', [$start, $end])
            ->with(['user', 'destination'])
            ->orderBy('start_datetime', 'asc')
            ->get();
    }

    public function findByDestination(int $destinationId): Collection
    {
        return $this->model
            ->where('destination_id', $destinationId)
            ->where('start_datetime', '>', now())
            ->with(['user'])
            ->orderBy('start_datetime', 'asc')
            ->get();
    }

    public function findByCategory(int $categoryId): Collection
    {
        return $this->model
            ->whereHas('destination', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->where('start_datetime', '>', now())
            ->with(['user', 'destination'])
            ->get();
    }

    public function search(string $term): Collection
    {
        return $this->model
            ->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhere('location', 'like', "%{$term}%");
            })
            ->with(['user', 'destination'])
            ->get();
    }

    public function findWithRelations(int $id): ?Event
    {
        return $this->model
            ->with(['user', 'destination', 'attendees', 'reviews.user', 'routes'])
            ->find($id);
    }
}
