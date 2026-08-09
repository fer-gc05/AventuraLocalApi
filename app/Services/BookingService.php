<?php

namespace App\Services;

use App\Models\Reservation;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use App\Repositories\Contracts\TourScheduleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class BookingService
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepo,
        private TourScheduleRepositoryInterface $scheduleRepo,
    ) {}

    public function create(array $data, int $userId): Reservation
    {
        $scheduleId = $data['tour_schedule_id'];

        $incremented = $this->scheduleRepo->incrementBookedSpots($scheduleId, $data['participants'] ?? 1);

        if (! $incremented) {
            abort(422, 'No hay cupos disponibles para este horario.');
        }

        $data['user_id'] = $userId;
        $data['reference_number'] = strtoupper(Str::random(10));
        $data['status'] = 'pending';
        $data['payment_status'] = 'pending';

        $schedule = $this->scheduleRepo->findWithTour($scheduleId);
        $data['tour_id'] = $schedule->tour_id;
        $data['guide_id'] = $schedule->guide_id;
        $data['total_price'] = $data['participants'] * ($schedule->price_override ?? $schedule->tour->price_per_person);
        $data['currency'] = $schedule->tour->currency;

        return $this->reservationRepo->create($data)->load(['tour', 'tourSchedule', 'guide.user']);
    }

    public function cancel(Reservation $reservation, int $userId, ?string $reason = null): Reservation
    {
        if ($reservation->status === 'cancelled') {
            abort(422, 'La reserva ya está cancelada.');
        }

        $this->scheduleRepo->decrementBookedSpots(
            $reservation->tour_schedule_id,
            $reservation->participants
        );

        $reservation->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => $userId,
            'cancellation_reason' => $reason,
        ]);

        return $reservation->load(['tour', 'tourSchedule', 'guide.user']);
    }

    public function confirm(Reservation $reservation): Reservation
    {
        $reservation->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        return $reservation->load(['tour', 'tourSchedule', 'guide.user']);
    }

    public function findById(int $id): ?Reservation
    {
        return $this->reservationRepo->findWithRelations($id);
    }

    public function findByUser(int $userId): Collection
    {
        return $this->reservationRepo->findByUser($userId);
    }

    public function findByGuide(int $guideId): Collection
    {
        return $this->reservationRepo->findByGuide($guideId);
    }

    public function findBySchedule(int $scheduleId): Collection
    {
        return $this->reservationRepo->findByTourSchedule($scheduleId);
    }
}
