<?php

namespace App\Repositories\Implementations;

use App\Models\Message;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MessageRepositoryImplement extends BaseRepositoryImplement implements MessageRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Message);
    }

    public function findByConversation(int $user1Id, int $user2Id): Collection
    {
        return $this->model
            ->where(function ($q) use ($user1Id, $user2Id) {
                $q->where('sender_id', $user1Id)->where('receiver_id', $user2Id);
            })
            ->orWhere(function ($q) use ($user1Id, $user2Id) {
                $q->where('sender_id', $user2Id)->where('receiver_id', $user1Id);
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function findByCommunity(int $communityId): Collection
    {
        return $this->model
            ->where('community_id', $communityId)
            ->with(['sender', 'parent'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function findByUser(int $userId): Collection
    {
        return $this->model
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findUnreadForUser(int $userId): Collection
    {
        return $this->model
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->with(['sender'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
