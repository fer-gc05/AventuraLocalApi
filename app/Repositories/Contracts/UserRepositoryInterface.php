<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface extends BaseRepository
{
    public function findByEmail(string $email): ?User;
    public function findByRole(string $role): Collection;
    public function findGuides(): Collection;
    public function findTravelers(): Collection;
}
