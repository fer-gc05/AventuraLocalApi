<?php

namespace App\Repositories\Contracts;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;

interface ReservationRepositoryInterface extends BaseRepository
{
    public function findByUser(int $userId): Collection;
    public function findByTourSchedule(int $scheduleId): Collection;
    public function findByTour(int $tourId): Collection;
    public function findByGuide(int $guideId): Collection;
    public function findByReference(string $reference): ?Reservation;
    public function findByStatus(string $status): Collection;
    public function findByPaymentStatus(string $status): Collection;
    public function countConfirmedForSchedule(int $scheduleId): int;
    public function findWithRelations(int $id): ?Reservation;
}
