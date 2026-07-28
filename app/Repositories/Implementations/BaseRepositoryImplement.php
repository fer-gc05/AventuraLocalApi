<?php

namespace App\Repositories\Implementations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepositoryImplement
{
    public function __construct(protected Model $model){}

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ? Model
    {
        return $this->model->find($id);
    }

    public function findBy(string $field, mixed $value): ? Model
    {
        return $this->model->where($field, $value)->first();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

}