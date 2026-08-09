<?php

namespace App\Repositories\Implementations;

use App\Models\Media;
use App\Repositories\Contracts\MediaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MediaRepositoryImplement extends BaseRepositoryImplement implements MediaRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Media);
    }

    public function findByModel(string $type, int $id): Collection
    {
        return $this->model
            ->where('model_type', $type)
            ->where('model_id', $id)
            ->get();
    }
}
