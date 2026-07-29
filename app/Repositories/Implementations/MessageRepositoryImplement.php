<?php

namespace App\Repositories\Implementations;

use App\Models\Message;
use App\Repositories\Contracts\MessageRepositoryInterface;

class MessageRepositoryImplement extends BaseRepositoryImplement implements MessageRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Message);
    }
}
