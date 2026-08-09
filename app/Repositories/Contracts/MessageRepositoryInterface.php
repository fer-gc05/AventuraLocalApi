<?php

namespace App\Repositories\Contracts;

use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

interface MessageRepositoryInterface extends BaseRepository
{
    public function findByConversation(int $user1Id, int $user2Id): Collection;
    public function findByCommunity(int $communityId): Collection;
    public function findByUser(int $userId): Collection;
    public function findUnreadForUser(int $userId): Collection;
}
