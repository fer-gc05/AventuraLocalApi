<?php

namespace App\Services;

use App\Models\Event;
use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class EventService
{
    public function __construct(
        private EventRepositoryInterface $eventRepo,
    ) {}

    public function create(array $data, int $userId): Event
    {
        $data['user_id'] = $userId;
        $data['slug'] = Str::slug($data['title']);

        $event = $this->eventRepo->create($data);

        return $event->load(['user', 'destination']);
    }

    public function update(Event $event, array $data): Event
    {
        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $event->update($data);

        return $event->load(['user', 'destination']);
    }

    public function delete(Event $event): bool
    {
        return $event->delete();
    }

    public function restore(int $id): Event
    {
        $event = Event::onlyTrashed()->findOrFail($id);
        $event->restore();

        return $event->load(['user', 'destination']);
    }

    public function findById(int $id): ?Event
    {
        return $this->eventRepo->findWithRelations($id);
    }

    public function getUpcoming(int $limit = 10): Collection
    {
        return $this->eventRepo->findUpcoming($limit);
    }

    public function getPopular(int $limit = 10): Collection
    {
        return $this->eventRepo->findPopular($limit);
    }

    public function getTrashed(int $perPage = 10): LengthAwarePaginator
    {
        return Event::onlyTrashed()
            ->with(['user', 'destination'])
            ->orderBy('deleted_at', 'desc')
            ->paginate($perPage);
    }

    public function attend(Event $event, int $userId): void
    {
        $alreadyAttending = $event->attendees()
            ->where('users.id', $userId)
            ->exists();

        if ($alreadyAttending) {
            abort(422, 'Ya estás asistiendo a este evento.');
        }

        if ($event->max_attendees && $event->attendees()->count() >= $event->max_attendees) {
            abort(422, 'El evento está lleno.');
        }

        $event->attendees()->attach($userId, ['status' => 'registered']);
    }

    public function cancelAttendance(Event $event, int $userId): void
    {
        $event->attendees()->detach($userId);
    }

    public function getAttendees(Event $event): Collection
    {
        return $event->attendees;
    }
}
