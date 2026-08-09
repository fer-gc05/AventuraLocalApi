<?php

namespace App\Repositories\Implementations;

use App\Models\Reservation;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ReservationRepositoryImplement extends BaseRepositoryImplement implements ReservationRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Reservation);
    }

    public function findByUser(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->with(['tour.destination', 'guide.user', 'tourSchedule'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByTourSchedule(int $scheduleId): Collection
    {
        return $this->model
            ->where('tour_schedule_id', $scheduleId)
            ->with(['user', 'tour'])
            ->get();
    }

    public function findByTour(int $tourId): Collection
    {
        return $this->model
            ->where('tour_id', $tourId)
            ->with(['user', 'tourSchedule'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByGuide(int $guideId): Collection
    {
        return $this->model
            ->where('guide_id', $guideId)
            ->with(['user', 'tour', 'tourSchedule'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByReference(string $reference): ?Reservation
    {
        return $this->model
            ->where('reference_number', $reference)
            ->with(['user', 'tour', 'guide.user'])
            ->first();
    }

    public function findByStatus(string $status): Collection
    {
        return $this->model
            ->where('status', $status)
            ->with(['user', 'tour', 'tourSchedule'])
            ->get();
    }

    public function findByPaymentStatus(string $status): Collection
    {
        return $this->model
            ->where('payment_status', $status)
            ->with(['user', 'tour', 'tourSchedule'])
            ->get();
    }

    public function countConfirmedForSchedule(int $scheduleId): int
    {
        return $this->model
            ->where('tour_schedule_id', $scheduleId)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->sum('participants');
    }

    public function findWithRelations(int $id): ?Reservation
    {
        return $this->model
            ->with(['user', 'tour.destination', 'guide.user', 'tourSchedule', 'cancelledByUser'])
            ->find($id);
    }
}
