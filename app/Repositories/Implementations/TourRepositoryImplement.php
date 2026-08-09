<?php

namespace App\Repositories\Implementations;

use App\Models\Tour;
use App\Repositories\Contracts\TourRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TourRepositoryImplement extends BaseRepositoryImplement implements TourRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Tour);
    }

    public function findByGuide(int $guideId): Collection
    {
        return $this->model
            ->where('guide_id', $guideId)
            ->with(['category', 'destination', 'schedules'])
            ->get();
    }

    public function findByCategory(int $categoryId): Collection
    {
        return $this->model
            ->where('category_id', $categoryId)
            ->with(['guide.user', 'destination', 'schedules'])
            ->get();
    }

    public function findByDestination(int $destinationId): Collection
    {
        return $this->model
            ->where('destination_id', $destinationId)
            ->with(['guide.user', 'category', 'schedules'])
            ->get();
    }

    public function findUpcoming(): Collection
    {
        return $this->model
            ->where('is_active', true)
            ->where('status', 'active')
            ->whereHas('schedules', function ($q) {
                $q->where('start_datetime', '>', now())
                  ->where('is_active', true);
            })
            ->with(['guide.user', 'category', 'destination'])
            ->get();
    }

    public function findPopular(int $limit = 10): Collection
    {
        return $this->model
            ->where('is_active', true)
            ->withCount(['reviews', 'reservations'])
            ->with(['guide.user', 'category'])
            ->orderByDesc('reviews_count')
            ->limit($limit)
            ->get();
    }

    public function searchByLocation(float $lat, float $lng, float $radius): Collection
    {
        $haversine = "(6371 * acos(
            cos(radians(?)) * cos(radians(meeting_latitude))
            * cos(radians(meeting_longitude) - radians(?))
            + sin(radians(?)) * sin(radians(meeting_latitude))
        ))";

        return $this->model
            ->select('*')
            ->selectRaw("{$haversine} AS distance", [$lat, $lng, $lat])
            ->where('is_active', true)
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->with(['guide.user', 'category', 'destination'])
            ->get();
    }

    public function search(string $term): Collection
    {
        return $this->model
            ->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('short_description', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            })
            ->where('is_active', true)
            ->with(['guide.user', 'category'])
            ->get();
    }

    public function findWithRelations(int $id): ?Tour
    {
        return $this->model
            ->with(['guide.user', 'category', 'destination', 'schedules', 'reviews.user', 'media'])
            ->find($id);
    }
}
