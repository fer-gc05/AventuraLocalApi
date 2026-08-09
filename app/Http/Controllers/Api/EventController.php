<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Repositories\Contracts\EventRepositoryInterface;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(
        private EventService $eventService,
        private EventRepositoryInterface $eventRepo,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Event::with(['user', 'destination', 'attendees']);

        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->has('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        if ($request->has('upcoming')) {
            $query->where('start_datetime', '>=', now());
        }

        $events = $query->orderBy('start_datetime')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Events fetched successfully',
            'data' => $events,
        ]);
    }

    public function show(Event $event): JsonResponse
    {
        $event = $this->eventService->findById($event->id);

        return response()->json([
            'success' => true,
            'message' => 'Event fetched successfully',
            'data' => $event,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'max_attendees' => 'nullable|integer|min:1',
            'destination_id' => 'nullable|exists:destinations,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $event = $this->eventService->create($validated, auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => $event,
        ], 201);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'sometimes|date',
            'end_datetime' => 'sometimes|date|after:start_datetime',
            'location' => 'sometimes|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'max_attendees' => 'nullable|integer|min:1',
            'destination_id' => 'nullable|exists:destinations,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $event = $this->eventService->update($event, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => $event,
        ]);
    }

    public function destroy(Event $event): JsonResponse
    {
        $this->eventService->delete($event);

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully',
        ]);
    }

    public function restore($id): JsonResponse
    {
        $event = $this->eventService->restore($id);

        return response()->json([
            'success' => true,
            'message' => 'Event restored successfully',
            'data' => $event,
        ]);
    }

    public function trashed(): JsonResponse
    {
        $events = $this->eventService->getTrashed();

        return response()->json([
            'success' => true,
            'message' => 'Trashed events retrieved successfully',
            'data' => $events,
        ]);
    }

    public function nearby(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:100',
        ]);

        $events = $this->eventRepo->findNearby(
            $validated['latitude'],
            $validated['longitude'],
            $validated['radius'] ?? 10
        );

        return response()->json([
            'success' => true,
            'message' => 'Nearby events retrieved successfully',
            'data' => $events,
        ]);
    }

    public function attend(Event $event): JsonResponse
    {
        $this->eventService->attend($event, auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Attendance confirmed successfully',
        ]);
    }

    public function cancelAttendance(Event $event): JsonResponse
    {
        $this->eventService->cancelAttendance($event, auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Attendance cancelled successfully',
        ]);
    }

    public function getAttendees(Event $event): JsonResponse
    {
        $attendees = $this->eventService->getAttendees($event);

        return response()->json([
            'success' => true,
            'message' => 'Attendees fetched successfully',
            'data' => $attendees,
        ]);
    }

    public function getPopularEvents(Request $request): JsonResponse
    {
        $limit = $request->query('limit', 10);
        $events = $this->eventService->getPopular($limit);

        return response()->json([
            'success' => true,
            'message' => 'Popular events retrieved successfully',
            'data' => $events,
        ]);
    }

    public function getEventStatistics(Event $event): JsonResponse
    {
        $attendeeCount = $event->attendees()->count();
        $max = $event->max_attendees;

        $statistics = [
            'total_attendees' => $attendeeCount,
            'attendance_rate' => $max > 0 ? round(($attendeeCount / $max) * 100, 2) : 0,
            'days_until_event' => now()->diffInDays($event->start_datetime),
            'is_full' => $max > 0 && $attendeeCount >= $max,
            'remaining_spots' => $max > 0 ? $max - $attendeeCount : null,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Event statistics retrieved successfully',
            'data' => $statistics,
        ]);
    }

    public function getUpcomingEvents(Request $request): JsonResponse
    {
        $days = $request->query('days', 7);

        $events = Event::with(['user', 'destination'])
            ->where('start_datetime', '>', now())
            ->where('start_datetime', '<=', now()->addDays($days))
            ->orderBy('start_datetime', 'asc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Upcoming events retrieved successfully',
            'data' => $events,
        ]);
    }

    public function getEventRecommendations(Request $request): JsonResponse
    {
        $user = auth()->user();
        $limit = $request->query('limit', 5);

        $userCategories = $user->attendedEvents()
            ->with('category')
            ->get()
            ->pluck('category.id')
            ->filter()
            ->unique();

        $events = Event::with(['user', 'destination', 'category'])
            ->whereIn('category_id', $userCategories)
            ->where('start_datetime', '>', now())
            ->whereNotIn('id', $user->attendedEvents()->pluck('events.id'))
            ->orderBy('start_datetime', 'asc')
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Event recommendations retrieved successfully',
            'data' => $events,
        ]);
    }

    public function getEventCalendar(Request $request): JsonResponse
    {
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        $events = Event::with(['user', 'destination'])
            ->whereYear('start_datetime', $year)
            ->whereMonth('start_datetime', $month)
            ->orderBy('start_datetime', 'asc')
            ->get()
            ->groupBy(function ($event) {
                return $event->start_datetime->format('Y-m-d');
            });

        return response()->json([
            'success' => true,
            'message' => 'Event calendar retrieved successfully',
            'data' => $events,
        ]);
    }
}
