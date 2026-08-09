<?php

namespace App\Repositories\Contracts;

use App\Models\Community;
use Illuminate\Database\Eloquent\Collection;

interface CommunityRepositoryInterface extends BaseRepository
{
    public function findByUser(int $userId): Collection;
    public function findByCategory(int $categoryId): Collection;
    public function findPopular(int $limit = 10): Collection;
    public function findWithRelations(int $id): ?Community;
}
