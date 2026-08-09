<?php

namespace App\Repositories\Implementations;

use App\Models\GuideProfile;
use App\Repositories\Contracts\GuideProfileRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GuideProfileRepositoryImplement extends BaseRepositoryImplement implements GuideProfileRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new GuideProfile);
    }

    public function findByUser(int $userId): ?GuideProfile
    {
        return $this->model
            ->where('user_id', $userId)
            ->with(['user'])
            ->first();
    }

    public function findVerified(): Collection
    {
        return $this->model
            ->where('is_verified', true)
            ->where('is_active', true)
            ->with(['user'])
            ->get();
    }

    public function findBySpecialty(string $specialty): Collection
    {
        return $this->model
            ->where('is_verified', true)
            ->whereJsonContains('specialties', $specialty)
            ->with(['user'])
            ->get();
    }

    public function findByLanguage(string $language): Collection
    {
        return $this->model
            ->where('is_verified', true)
            ->whereJsonContains('languages', $language)
            ->with(['user'])
            ->get();
    }

    public function search(string $term): Collection
    {
        return $this->model
            ->where('is_verified', true)
            ->where(function ($q) use ($term) {
                $q->where('bio', 'like', "%{$term}%")
                  ->orWhereHas('user', function ($uq) use ($term) {
                      $uq->where('name', 'like', "%{$term}%");
                  });
            })
            ->with(['user'])
            ->get();
    }

    public function findWithTours(int $id): ?GuideProfile
    {
        return $this->model
            ->with(['user', 'tours.category', 'tours.destination'])
            ->find($id);
    }
}
