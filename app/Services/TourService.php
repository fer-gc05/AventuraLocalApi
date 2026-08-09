<?php

namespace App\Services;

use App\Models\GuideProfile;
use App\Models\Tour;
use App\Repositories\Contracts\TourRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class TourService
{
    public function __construct(
        private TourRepositoryInterface $tourRepo,
    ) {}

    public function create(array $data, int $userId): Tour
    {
        $guideProfile = GuideProfile::where('user_id', $userId)->firstOrFail();

        $data['guide_id'] = $guideProfile->id;
        $data['slug'] = Str::slug($data['title']);
        $data['is_active'] = true;
        $data['status'] = 'draft';

        $tour = $this->tourRepo->create($data);

        return $tour->load(['guide.user', 'category', 'destination']);
    }

    public function update(Tour $tour, array $data): Tour
    {
        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $tour->update($data);

        return $tour->load(['guide.user', 'category', 'destination']);
    }

    public function delete(Tour $tour): bool
    {
        return $tour->delete();
    }

    public function restore(int $id): Tour
    {
        $tour = Tour::onlyTrashed()->findOrFail($id);
        $tour->restore();

        return $tour->load(['guide.user', 'category', 'destination']);
    }

    public function findById(int $id): ?Tour
    {
        return $this->tourRepo->findWithRelations($id);
    }

    public function getActive(int $perPage = 10): LengthAwarePaginator
    {
        return Tour::with(['guide.user', 'category', 'destination'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getTrashed(int $perPage = 10): LengthAwarePaginator
    {
        return Tour::onlyTrashed()
            ->with(['guide.user', 'category', 'destination'])
            ->orderBy('deleted_at', 'desc')
            ->paginate($perPage);
    }

    public function findByGuide(int $guideId): Collection
    {
        return $this->tourRepo->findByGuide($guideId);
    }

    public function findUpcoming(): Collection
    {
        return $this->tourRepo->findUpcoming();
    }

    public function findPopular(int $limit = 10): Collection
    {
        return $this->tourRepo->findPopular($limit);
    }
}
