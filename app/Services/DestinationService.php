<?php

namespace App\Services;

use App\Models\Destination;
use App\Repositories\Contracts\DestinationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class DestinationService
{
    public function __construct(
        private DestinationRepositoryInterface $destinationRepo,
    ) {}

    public function create(array $data, int $userId): Destination
    {
        $data['user_id'] = $userId;
        $data['slug'] = Str::slug($data['name']);

        $destination = $this->destinationRepo->create($data);

        if (! empty($data['tags'])) {
            $destination->tags()->sync($data['tags']);
        }

        return $destination->load(['category', 'tags', 'media']);
    }

    public function update(Destination $destination, array $data): Destination
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $destination->update($data);

        if (array_key_exists('tags', $data)) {
            $destination->tags()->sync($data['tags'] ?? []);
        }

        return $destination->load(['category', 'tags', 'media']);
    }

    public function delete(Destination $destination): bool
    {
        return $destination->delete();
    }

    public function restore(int $id): Destination
    {
        $destination = Destination::onlyTrashed()->findOrFail($id);
        $destination->restore();

        return $destination->load(['category', 'tags', 'media']);
    }

    public function findById(int $id): ?Destination
    {
        return $this->destinationRepo->findWithDetails((int) $id);
    }

    public function getPopular(int $limit = 10): Collection
    {
        return $this->destinationRepo->findPopular($limit);
    }

    public function findNearby(float $lat, float $lng, float $radius): Collection
    {
        return $this->destinationRepo->findNearby($lat, $lng, $radius);
    }

    public function search(string $term): Collection
    {
        return $this->destinationRepo->search($term);
    }

    public function getTrashed(int $perPage = 10): LengthAwarePaginator
    {
        return Destination::onlyTrashed()
            ->with(['category', 'media'])
            ->orderBy('deleted_at', 'desc')
            ->paginate($perPage);
    }
}
