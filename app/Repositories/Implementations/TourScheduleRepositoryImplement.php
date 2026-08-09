<?php

namespace App\Repositories\Implementations;

use App\Models\TourSchedule;
use App\Repositories\Contracts\TourScheduleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TourScheduleRepositoryImplement extends BaseRepositoryImplement implements TourScheduleRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new TourSchedule);
    }

    public function findByTour(int $tourId): Collection
    {
        return $this->model
            ->where('tour_id', $tourId)
            ->orderBy('start_datetime', 'asc')
            ->get();
    }

    public function findUpcoming(int $tourId): Collection
    {
        return $this->model
            ->where('tour_id', $tourId)
            ->where('start_datetime', '>', now())
            ->where('is_active', true)
            ->orderBy('start_datetime', 'asc')
            ->get();
    }

    public function findAvailable(int $tourId): Collection
    {
        return $this->model
            ->where('tour_id', $tourId)
            ->where('start_datetime', '>', now())
            ->where('is_active', true)
            ->whereColumn('booked_spots', '<', 'max_spots')
            ->orderBy('start_datetime', 'asc')
            ->get();
    }

    public function getAvailableSpots(int $scheduleId): int
    {
        $schedule = $this->find($scheduleId);

        if (! $schedule) {
            return 0;
        }

        return max(0, $schedule->max_spots - $schedule->booked_spots);
    }

    public function incrementBookedSpots(int $scheduleId, int $quantity = 1): bool
    {
        $affected = $this->model
            ->where('id', $scheduleId)
            ->where('is_active', true)
            ->whereColumn('booked_spots', '<', 'max_spots')
            ->increment('booked_spots', $quantity);

        return $affected > 0;
    }

    public function decrementBookedSpots(int $scheduleId, int $quantity = 1): bool
    {
        $affected = $this->model
            ->where('id', $scheduleId)
            ->where('is_active', true)
            ->where('booked_spots', '>=', $quantity)
            ->decrement('booked_spots', $quantity);

        return $affected > 0;
    }

    public function findWithTour(int $id): ?TourSchedule
    {
        return $this->model
            ->with(['tour', 'guide.user'])
            ->find($id);
    }
}
