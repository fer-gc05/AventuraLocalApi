<?php

namespace App\Repositories\Contracts;

use App\Models\GuideProfile;
use Illuminate\Database\Eloquent\Collection;

interface GuideProfileRepositoryInterface extends BaseRepository
{
    public function findByUser(int $userId): ?GuideProfile;
    public function findVerified(): Collection;
    public function findBySpecialty(string $specialty): Collection;
    public function findByLanguage(string $language): Collection;
    public function search(string $term): Collection;
    public function findWithTours(int $id): ?GuideProfile;
}
