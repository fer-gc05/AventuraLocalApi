<?php

namespace App\Repositories\Implementations;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepositoryImplement extends BaseRepositoryImplement implements UserRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new User);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByRole(string $role): Collection
    {
        return $this->model->role($role, 'api')->get();
    }

    public function findGuides(): Collection
    {
        return $this->findByRole('Guide');
    }

    public function findTravelers(): Collection
    {
        return $this->findByRole('Traveler');
    }
}
