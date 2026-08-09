<?php

namespace App\Repositories\Implementations;

use App\Models\Community;
use App\Repositories\Contracts\CommunityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CommunityRepositoryImplement extends BaseRepositoryImplement implements CommunityRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Community);
    }

    public function findByUser(int $userId): Collection
    {
        return $this->model
            ->whereHas('users', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })
            ->with(['category', 'media'])
            ->get();
    }

    public function findByCategory(int $categoryId): Collection
    {
        return $this->model
            ->where('category_id', $categoryId)
            ->with(['category', 'users', 'media'])
            ->get();
    }

    public function findPopular(int $limit = 10): Collection
    {
        return $this->model
            ->where('is_public', true)
            ->withCount('users')
            ->with(['category', 'media'])
            ->orderByDesc('users_count')
            ->limit($limit)
            ->get();
    }

    public function findWithRelations(int $id): ?Community
    {
        return $this->model
            ->with(['user', 'category', 'users', 'messages.sender', 'media'])
            ->find($id);
    }
}
