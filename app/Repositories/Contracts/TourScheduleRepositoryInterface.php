<?php

namespace App\Repositories\Contracts;

use App\Models\TourSchedule;
use Illuminate\Database\Eloquent\Collection;

interface TourScheduleRepositoryInterface extends BaseRepository
{
    public function findByTour(int $tourId): Collection;
    public function findUpcoming(int $tourId): Collection;
    public function findAvailable(int $tourId): Collection;
    public function getAvailableSpots(int $scheduleId): int;
    public function incrementBookedSpots(int $scheduleId, int $quantity = 1): bool;
    public function decrementBookedSpots(int $scheduleId, int $quantity = 1): bool;
    public function findWithTour(int $id): ?TourSchedule;
}
