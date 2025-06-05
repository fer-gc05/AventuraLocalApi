<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Destinations\NearbyDestinationRequest;
use App\Http\Requests\Destinations\SearchEventsRequest;
use App\Http\Requests\Destinations\StoreDestinationRequest;
use App\Http\Requests\Destinations\UpdateDestinationRequest;
use App\Models\Destination;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DestinationController extends Controller
{
    /**
     * Get all destinations
     * 
     * This method is used to get all destinations
     * 
     * @param \Illuminate\Http\Request $request
     * unauthenticated
     */
    public function index(Request $request)
    {
        try {
            $cacheKey = 'destinations_' . md5(json_encode($request->all()));
            $destinations = Cache::tags(['destinations'])->remember($cacheKey, now()->addMinutes(10), function () use ($request) {
                $query = Destination::query();

                if ($request->has('name')) {
                    $query->where('name', 'like', '%' . $request->name . '%');
                }

                if ($request->has('category_id')) {
                    $query->where('category_id', $request->category_id);
                }

                return $query->paginate(6);
            });

            if ($destinations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No destinations found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Destinations fetched successfully',
                'data' => $destinations,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching destinations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a destination
     * 
     * This method is used to get a destination
     * 
     * @param int $id
     * @unauthenticated
     */
    public function show($id)
    {
        try {
            $destination = Destination::with(['tags', 'media', 'category', 'user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Destination fetched successfully',
                'data' => $destination,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Destination not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching destination',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a destination
     * 
     * This method is used to create a destination
     * 
     * @param \App\Http\Requests\Destinations\StoreDestinationRequest $request
     */
    public function store(StoreDestinationRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['user_id'] = auth()->id();

            $destination = Destination::create($data);

            $destination->tags()->sync($data['tags']);

            if ($request->hasFile('media')) {   
            foreach ($request->file('media') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('destinations', $fileName, 'public');

                $destination->media()->create([
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'url' => asset('storage/' . $filePath),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'custom_properties' => ['type' => 'destination_image'],
                    ]);
                }
            }

            DB::commit();

            Cache::tags(['destinations'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Destination created successfully',
                'data' => $destination->load(['tags', 'media', 'category', 'user'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating destination',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a destination
     * 
     * This method is used to update a destination
     * 
     * @param \App\Http\Requests\Destinations\UpdateDestinationRequest $request
     * @param int $id
     */
    public function update(UpdateDestinationRequest $request, $id)
    {
        try {
            $destination = Destination::findOrFail($id);

            $data = $request->all();

            $destination->update($data);

            if ($request->has('tags')) {
                $destination->tags()->sync($request->tags);
            }

            if ($request->has('media')) {
                foreach ($request->file('media') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('destinations', $fileName, 'public');

                    $destination->media()->create([
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'url' => asset('storage/' . $filePath),
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'custom_properties' => ['type' => 'destination_image'],
                    ]);
                }
            }

            Cache::tags(['destinations'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Destination updated successfully',
                'data' => $destination,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Destination not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating destination',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a destination
     * 
     * This method is used to delete a destination
     * 
     * @param int $id
     */
    public function destroy($id)
    {
        try {
            $destination = Destination::findOrFail($id);
            $destination->delete();

            Cache::tags(['destinations'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Destination deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Destination not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting destination',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore a destination
     * 
     * This method is used to restore a destination
     * 
     * @param int $id
     */
    public function restore($id)
    {
        try {
            $destination = Destination::withTrashed()->findOrFail($id);
            $destination->restore();

            Cache::tags(['destinations'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Destination restored successfully',
                'data' => $destination
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Destination not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error restoring destination',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all trashed destinations
     * 
     * This method is used to get all trashed destinations
     * 
     */
    public function trashed()
    {
        try {
            $destinations = Destination::onlyTrashed()->paginate(10);

            if ($destinations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No trashed destinations found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Trashed destinations retrieved successfully',
                'data' => $destinations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving trashed destinations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get nearby destinations
     * 
     * This method is used to get nearby destinations
     * 
     * @param \App\Http\Requests\Destinations\NearbyDestinationRequest $request
     */
    public function nearby(NearbyDestinationRequest $request)
    {
        try {
            $validated = $request->validated();
            $destination = Destination::findOrFail($validated['destination_id']);

            $radius = $validated['radius'] ?? 10;
            $searchTerm = $validated['searchTerm'] ?? null;
            $limit = $validated['limit'] ?? 10;

            $cacheKey = 'nearby_' . md5(json_encode($validated));
            $nearbyDestinations = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($destination, $radius, $searchTerm, $limit) {
                return $destination->nearbyDestinations($radius)
                    ->having('distance', '<=', $radius)
                    ->when($searchTerm, function ($query) use ($searchTerm) {
                        return $query->where('name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('description', 'like', '%' . $searchTerm . '%');
                    })
                    ->orderBy('distance')
                    ->take($limit)
                    ->get();
            });

            return response()->json([
                'success' => true,
                'message' => 'Nearby destinations fetched successfully',
                'data' => [
                    'origin' => $destination->only(['id', 'name', 'latitude', 'longitude']),
                    'radius_km' => $radius,
                    'results' => $nearbyDestinations,
                    'count' => $nearbyDestinations->count()
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Destination not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching nearby destinations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get events for a destination
     * 
     * This method is used to get events for a destination
     * 
     * @param \App\Http\Requests\Destinations\SearchEventsRequest $request
     */
    public function events(SearchEventsRequest $request)
    {
        try {
            $destination = Destination::findOrFail($request->destination_id);
            $events = $destination->events()->paginate(10);

            if ($events->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Events not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Events fetched successfully',
                'data' => $events
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
     * Get popular destinations
     * 
     * This method is used to get popular destinations
     * 
     * @param \Illuminate\Http\Request $request
     * @unauthenticated
     */
    public function getPopularDestinations(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            $cacheKey = 'popular_destinations_' . $limit;

            $destinations = Cache::tags(['destinations'])->remember($cacheKey, now()->addHours(1), function () use ($limit) {
                return Destination::with(['category', 'media'])
                    ->withCount(['reviews', 'events'])
                    ->orderBy('reviews_count', 'desc')
                    ->orderBy('events_count', 'desc')
                    ->take($limit)
                    ->get();
            });

            if ($destinations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No popular destinations found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Popular destinations retrieved successfully',
                'data' => $destinations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving popular destinations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reviews for a destination
     * 
     * This method is used to get reviews for a destination
     * 
     * @param int $id
     * @unauthenticated
     */
    public function getDestinationReviews($id)
    {
        try {
            $destination = Destination::findOrFail($id);
            $reviews = $destination->reviews()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            if ($reviews->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No reviews found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Destination reviews retrieved successfully',
                'data' => $reviews
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Destination not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving destination reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get routes for a destination
     * 
     * This method is used to get routes for a destination
     * 
     * @param int $id
     * @unauthenticated
     */
    public function getDestinationRoutes($id)
    {
        try {
            $destination = Destination::findOrFail($id);
            $routes = $destination->routes()
                ->with(['user', 'destinations'])
                ->paginate(10);

            if ($routes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No routes found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Destination routes retrieved successfully',
                'data' => $routes
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Destination not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving destination routes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get events for a destination
     * 
     * This method is used to get events for a destination
     * 
     * @param int $id
     * @unauthenticated
     */
    public function getDestinationEvents($id)
    {
        try {
            $destination = Destination::findOrFail($id);
            $events = $destination->events()
                ->where('start_datetime', '>', now())
                ->with(['user', 'category'])
                ->orderBy('start_datetime', 'asc')
                ->paginate(10);

            if ($events->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No events found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Upcoming destination events retrieved successfully',
                'data' => $events
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Destination not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving destination events',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics for a destination
     * 
     * This method is used to get statistics for a destination
     * 
     * @param int $id
     */
    public function getDestinationStatistics($id)
    {
        try {
            $destination = Destination::findOrFail($id);

            $statistics = [
                'total_reviews' => $destination->reviews()->count(),
                'average_rating' => $destination->reviews()->avg('rating') ?? 0,
                'total_events' => $destination->events()->count(),
                'upcoming_events_count' => $destination->events()
                    ->where('start_datetime', '>=', now())
                    ->count(),
                'total_routes' => $destination->routes()->count(),
                'total_visitors' => DB::table('event_user')
                    ->whereIn('event_id', $destination->events()->select('id'))
                    ->distinct('user_id')
                    ->count('user_id'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Destination statistics retrieved successfully',
                'data' => $statistics
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Destination not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving destination statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
