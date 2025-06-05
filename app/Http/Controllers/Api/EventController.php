<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Events\NearbyEventsRequest;
use App\Http\Requests\Events\StoreEventRequest;
use App\Http\Requests\Events\updateEventRequest;
use App\Models\Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * Get all events
     * 
     * This method is used to get all events
     * 
     * @param \Illuminate\Http\Request $request
     * @unauthenticated
     */
    public function index(Request $request)
    {
        try {
            $cacheKey = 'events_' . md5(json_encode($request->all()));
            $events = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
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

                return $query->orderBy('start_datetime')->paginate(10);
            });

            if ($events->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No events found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Events fetched successfully',
                'data' => $events,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching events',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get an event
     * 
     * This method is used to get an event
     * 
     * @param \App\Models\Event $event
     * @unauthenticated
     */
    public function show(Event $event)
    {
        try {
            $event = $event->load(['user', 'destination', 'attendees']);

            return response()->json([
                'success' => true,
                'message' => 'Event fetched successfully',
                'data' => $event,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create an event
     * 
     * This method is used to create an event
     * 
     * @param \App\Http\Requests\Events\StoreEventRequest $request
     */
    public function store(StoreEventRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['user_id'] = auth()->id();

            $event = Event::create($data);

            DB::commit();
            Cache::tags(['events'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Event created successfully',
                'data' => $event->load(['user', 'destination', 'attendees'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating event',
                'error' => $e->getMessage()
            ], 500);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Event not found!!!',
            ], 404);
        }

    }

    /**
     * Update an event
     * 
     * This method is used to update an event
     * 
     * @param \App\Http\Requests\Events\updateEventRequest $request
     * @param \App\Models\Event $event
     */
    public function update(updateEventRequest $request, Event $event)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $event->update($data);

            DB::commit();
            Cache::tags(['events'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully',
                'data' => $event->load(['user', 'destination', 'attendees'])
            ]);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Event not found!!!',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an event
     * 
     * This method is used to delete an event
     * 
     * @param \App\Models\Event $event
     */
    public function destroy(Event $event)
    {
        DB::beginTransaction();
        try {
            $event->delete();

            DB::commit();
            Cache::tags(['events'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Event not found!!!',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore an event
     * 
     * This method is used to restore an event
     * 
     * @param int $id
     */
    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $event = Event::withTrashed()->findOrFail($id);
            $event->restore();

            DB::commit();
            Cache::tags(['events'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Event restored successfully',
                'data' => $event
            ]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Event not found!!!',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error restoring event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all trashed events
     * 
     * This method is used to get all trashed events
     * 
     */
    public function trashed()
    {
        try {
            $events = Event::onlyTrashed()->paginate(10);

            if ($events->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No events found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Trashed events retrieved successfully',
                'data' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving trashed events',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get nearby events
     * 
     * This method is used to get nearby events
     * 
     * @param \App\Http\Requests\Events\NearbyEventsRequest $request
     */
    public function nearby(NearbyEventsRequest $request)
    {
        try {
            $validated = $request->validated();

            $event = Event::findOrFail($validated['event_id']);

            $radius = $validated['radius'] ?? 10;
            $searchTerm = $validated['searchTerm'] ?? null;

            $cacheKey = 'nearby_events_' . md5(json_encode($validated));
            $nearbyEvents = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($event, $radius, $searchTerm) {
                return $event->nearbyEvents($radius, $searchTerm)
                    ->take(10)
                    ->get()
                    ->load(['user', 'destination']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Eventos cercanos recuperados exitosamente',
                'data' => [
                    'origin' => [
                        'event_id' => $event->id,
                        'title' => $event->title,
                        'latitude' => $event->latitude,
                        'longitude' => $event->longitude
                    ],
                    'radius_km' => $radius,
                    'results' => $nearbyEvents,
                    'count' => $nearbyEvents->count()
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Evento no encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al recuperar eventos cercanos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Attend an event
     * 
     * This method is used to attend an event
     * 
     * @param \App\Models\Event $event
     */
    public function attend(Event $event)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            if ($event->max_attendees && $event->attendees()->count() >= $event->max_attendees) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event has reached maximum attendees',
                ], 400);
            }

            $event->attendees()->syncWithoutDetaching([
                $user->id => ['status' => 'registered']
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Attendance request sent successfully',
                'data' => [
                    'event_id' => $event->id,
                    'status' => 'pending'
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Event not found!!!',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error attending event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel attendance
     * 
     * This method is used to cancel attendance
     * 
     * @param \App\Models\Event $event
     */
    public function cancelAttendance(Event $event)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $event->attendees()->detach($user->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Attendance cancelled successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Event not found!!!',
            ], 404);
        }
    }

    /**
     * Get attendees
     * 
     * This method is used to get attendees
     * 
     * @param \App\Models\Event $event
     */
    public function getAttendees(Event $event)
    {
        try {
            $attendees = $event->attendees;
            return response()->json([
                'success' => true,
                'message' => 'Attendees fetched successfully',
                'data' => $attendees,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching attendees',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get popular events
     * 
     * This method is used to get popular events
     * 
     * @param \Illuminate\Http\Request $request
     * @unauthenticated
     */
    public function getPopularEvents(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            $cacheKey = 'popular_events_' . $limit;

            $events = Cache::tags(['events'])->remember($cacheKey, now()->addHours(1), function () use ($limit) {
                return Event::with(['user', 'destination'])
                    ->withCount('attendees')
                    ->where('start_datetime', '>', now())
                    ->orderBy('attendees_count', 'desc')
                    ->take($limit)
                    ->get();
            });

            if ($events->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No events found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Popular events retrieved successfully',
                'data' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving popular events',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get event statistics
     * 
     * This method is used to get event statistics
     * 
     * @param \App\Models\Event $event
     */
    public function getEventStatistics(Event $event)
    {
        try {
            $statistics = [
                'total_attendees' => $event->attendees()->count(),
                'attendance_rate' => $event->capacity > 0 ?
                    round(($event->attendees()->count() / $event->capacity) * 100, 2) : 0,
                'days_until_event' => now()->diffInDays($event->start_datetime),
                'is_full' => $event->capacity > 0 && $event->attendees()->count() >= $event->capacity,
                'remaining_spots' => $event->capacity > 0 ?
                    $event->capacity - $event->attendees()->count() : null
            ];

            return response()->json([
                'success' => true,
                'message' => 'Event statistics retrieved successfully',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving event statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upcoming events
     * 
     * This method is used to get upcoming events
     * 
     * @param \Illuminate\Http\Request $request
     * @unauthenticated
     */
    public function getUpcomingEvents(Request $request)
    {
        try {
            $days = $request->query('days', 7);
            $cacheKey = 'upcoming_events_' . $days;

            $events = Cache::tags(['events'])->remember($cacheKey, now()->addHours(1), function () use ($days) {
                return Event::with(['user', 'destination'])
                    ->where('start_datetime', '>', now())
                    ->where('start_datetime', '<=', now()->addDays($days))
                    ->orderBy('start_datetime', 'asc')
                    ->paginate(10);
            });

            if ($events->isEmpty()) {

                return response()->json([
                    'success' => false,
                    'message' => 'No events found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Upcoming events retrieved successfully',
                'data' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving upcoming events',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get event recommendations
     * 
     * This method is used to get event recommendations
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function getEventRecommendations(Request $request)
    {
        try {
            $user = auth()->user();
            $limit = $request->query('limit', 5);

            $recommendations = Cache::tags(['events'])->remember('event_recommendations_' . $user->id, now()->addHours(1), function () use ($user, $limit) {
                // Obtener eventos basados en categorías de eventos anteriores
                $userCategories = $user->attendedEvents()
                    ->with('category')
                    ->get()
                    ->pluck('category.id')
                    ->unique();

                return Event::with(['user', 'destination', 'category'])
                    ->whereIn('category_id', $userCategories)
                    ->where('start_datetime', '>', now())
                    ->whereNotIn('id', $user->attendedEvents()->pluck('events.id'))
                    ->orderBy('start_datetime', 'asc')
                    ->take($limit)
                    ->get();
            });

            if ($recommendations->isEmpty()) {

                return response()->json([
                    'success' => false,
                    'message' => 'No event recommendations found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Event recommendations retrieved successfully',
                'data' => $recommendations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving event recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get event calendar
     * 
     * This method is used to get event calendar
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function getEventCalendar(Request $request)
    {
        try {
            $month = $request->query('month', now()->month);
            $year = $request->query('year', now()->year);

            $events = Cache::tags(['events'])->remember("events_calendar_{$year}_{$month}", now()->addHours(1), function () use ($month, $year) {
                return Event::with(['user', 'destination'])
                    ->whereYear('start_datetime', $year)
                    ->whereMonth('start_datetime', $month)
                    ->orderBy('start_datetime', 'asc')
                    ->get()
                    ->groupBy(function ($event) {
                        return $event->start_datetime->format('Y-m-d');
                    });
            });

            if ($events->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No events found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Event calendar retrieved successfully',
                'data' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving event calendar',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}