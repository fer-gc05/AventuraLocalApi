<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Route;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Routes\StoreRouteRequest;
use App\Http\Requests\Routes\UpdateRouteRequest;

class RouteController extends Controller
{
    /**
     * Get all routes
     * 
     * This method is used to get all routes
     * 
     * @param \Illuminate\Http\Request $request
     * @unauthenticated
     */
    public function index(Request $request)
    {
        try {
            $cacheKey = 'routes_' . md5(json_encode($request->all()));
            $routes = Cache::tags(['routes'])->remember($cacheKey, now()->addMinutes(10), function () use ($request) {
                $query = Route::with(['user', 'destinations', 'tours', 'reviews']);

                if ($request->has('name')) {
                    $query->where('name', 'like', '%' . $request->name . '%');
                }

                if ($request->has('difficulty')) {
                    $query->where('difficulty', $request->difficulty);
                }

                if ($request->has('user_id')) {
                    $query->where('user_id', $request->user_id);
                }

                if ($request->has('destination_id')) {
                    $query->whereHas('destinations', function ($query) use ($request) {
                        $query->where('destination_id', $request->destination_id);
                    });
                }

                return $query->paginate($request->per_page ?? 10);
            });

            if ($routes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No routes found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Routes retrieved successfully',
                'data' => $routes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving routes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a route
     * 
     * This method is used to create a route
     * 
     * @param \App\Http\Requests\Routes\StoreRouteRequest $request
     */
    public function store(StoreRouteRequest $request)
    {
        try {
            $route = Route::create($request->all());

            if ($request->has('destinations')) {
                $route->destinations()->sync($request->destinations);
            }

            $route->load(['user', 'destinations', 'tours', 'reviews']);
            Cache::tags(['routes'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Route created successfully',
                'data' => $route
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating route',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a route
     * 
     * This method is used to get a route
     * 
     * @param \App\Models\Route $route
     * @unauthenticated
     */
    public function show(Route $route)
    {
        try {
            $route = $route->load(['user', 'destinations', 'tours', 'reviews']);

            if (!$route) {
                return response()->json([
                    'success' => false,
                    'message' => 'Route not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Route retrieved successfully',
                'data' => $route
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving route',
                'error' => $e->getMessage(),
            ], 500);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving route',
                'error' => $e->getMessage(),
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Route not found',
            ], 404);
        }
    }

    /**
     * Update a route
     * 
     * This method is used to update a route
     * 
     * @param \App\Http\Requests\Routes\UpdateRouteRequest $request
     * @param \App\Models\Route $route
     */
    public function update(UpdateRouteRequest $request, Route $route)
    {
        try{
            $route->update($request->all());

            if($request->has('destinations')){
                $route->destinations()->sync($request->destinations);
            }

            $route->load(['user', 'destinations', 'tours', 'reviews']);

            Cache::tags(['routes'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Route updated successfully',
                'data' => $route
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating route',
                'error' => $e->getMessage(),
            ], 500);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating route',
                'error' => $e->getMessage(),
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Route not found',
            ], 404);
        }
    }

    /**
     * Delete a route
     * 
     * This method is used to delete a route
     * 
     * @param \App\Models\Route $route
     */
    public function destroy(Route $route)
    {
        try{
            $route->delete();

            Cache::tags(['routes'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Route deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting route',
                'error' => $e->getMessage(),
            ], 500);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting route',
                'error' => $e->getMessage(),
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Route not found',
            ], 404);
        }
    }

    /**
     * Restore a route
     * 
     * This method is used to restore a route
     * 
     * @param int $id
     */
    public function restore($id)
    {
        try {
            $route = Route::withTrashed()->findOrFail($id);
            $route->restore();

            Cache::tags(['routes'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Route restored successfully',
                'data' => $route->load(['user', 'destinations', 'tours', 'reviews'])
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Route not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error restoring route',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all trashed routes
     * 
     * This method is used to get all trashed routes
     * 
     */
    public function trashed()
    {
        try {
            $routes = Route::onlyTrashed()->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Trashed routes retrieved successfully',
                'data' => $routes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving trashed routes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get popular routes
     * 
     * This method is used to get popular routes
     * 
     * @param \Illuminate\Http\Request $request
     * @unauthenticated
     */
    public function getPopularRoutes(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            $cacheKey = 'popular_routes_' . $limit;

            $routes = Cache::tags(['routes'])->remember($cacheKey, now()->addHours(1), function () use ($limit) {
                return Route::with(['user', 'destinations'])
                    ->withCount(['users as favorites_count' => function ($query) {
                        $query->where('is_favorite', true);
                    }])
                    ->withCount(['users as completed_count' => function ($query) {
                        $query->where('status', 'completed');
                    }])
                    ->orderByRaw('(favorites_count + completed_count) DESC')
                    ->take($limit)
                    ->get();
            });

            if ($routes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No popular routes found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Popular routes retrieved successfully',
                'data' => $routes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving popular routes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get route reviews
     * 
     * This method is used to get route reviews
     * 
     * @param \App\Models\Route $route
     */
    public function getRouteReviews(Route $route)
    {
        try {
            $reviews = $route->reviews()
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
                'message' => 'Route reviews retrieved successfully',
                'data' => $reviews
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving route reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get route communities
     * 
     * This method is used to get route communities
     * 
     * @param \App\Models\Route $route
     */
    public function getRouteCommunities(Route $route)
    {
        try {
            $communities = $route->communities()
                ->with(['user', 'media'])
                ->paginate(10);

            if ($communities->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No communities found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Route communities retrieved successfully',
                'data' => $communities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving route communities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get route events
     * 
     * This method is used to get route events
     * 
     * @param \App\Models\Route $route
     */
    public function getRouteEvents(Route $route)
    {
        try {
            $events = $route->events()
                ->with(['user', 'destination', 'media'])
                ->where('start_date', '>', now())
                ->paginate(10);

            if ($events->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No events found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Route events retrieved successfully',
                'data' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving route events',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Attach community to route
     * 
     * This method is used to attach community to route
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Route $route
     */
    public function attachCommunity(Request $request, Route $route)
    {
        try {
            $request->validate([
                'community_id' => 'required|exists:communities,id'
            ]);

            $route->communities()->attach($request->community_id);

            return response()->json([
                'success' => true,
                'message' => 'Community attached to route successfully',
                'data' => [
                    'route_id' => $route->id,
                    'community_id' => $request->community_id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error attaching community to route',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detach community from route
     * 
     * This method is used to detach community from route
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Route $route
     */
    public function detachCommunity(Request $request, Route $route)
    {
        try {
            $request->validate([
                'community_id' => 'required|exists:communities,id'
            ]);

            $route->communities()->detach($request->community_id);

            return response()->json([
                'success' => true,
                'message' => 'Community detached from route successfully',
                'data' => [
                    'route_id' => $route->id,
                    'community_id' => $request->community_id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error detaching community from route',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Attach event to route
     * 
     * This method is used to attach event to route
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Route $route
     */
    public function attachEvent(Request $request, Route $route)
    {
        try {
            $request->validate([
                'event_id' => 'required|exists:events,id'
            ]);

            $route->events()->attach($request->event_id);

            return response()->json([
                'success' => true,
                'message' => 'Event attached to route successfully',
                'data' => [
                    'route_id' => $route->id,
                    'event_id' => $request->event_id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error attaching event to route',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detach event from route
     * 
     * This method is used to detach event from route
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Route $route
     */
    public function detachEvent(Request $request, Route $route)
    {
        try {
            $request->validate([
                'event_id' => 'required|exists:events,id'
            ]);

            $route->events()->detach($request->event_id);

            return response()->json([
                'success' => true,
                'message' => 'Event detached from route successfully',
                'data' => [
                    'route_id' => $route->id,
                    'event_id' => $request->event_id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error detaching event from route',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate completion rate
     * 
     * This method is used to calculate completion rate
     * 
     * @param \App\Models\Route $route
     */
    private function calculateCompletionRate(Route $route)
    {
        $totalAttempts = $route->users()->count();
        if ($totalAttempts === 0) {
            return 0;
        }

        $completedAttempts = $route->users()->where('status', 'completed')->count();
        return round(($completedAttempts / $totalAttempts) * 100, 2);
    }

    /**
     * Get popular destinations
     * 
     * This method is used to get popular destinations
     * 
     * @param \App\Models\Route $route
     */
    private function getPopularDestinations(Route $route)
    {
        return $route->destinations()
            ->withCount(['users as favorites_count' => function ($query) {
                $query->where('is_favorite', true);
            }])
            ->orderBy('favorites_count', 'desc')
            ->take(3)
            ->get();
    }

    /**
     * Calculate average completion time
     * 
     * This method is used to calculate average completion time
     * 
     * @param \App\Models\Route $route
     */
    private function calculateAverageCompletionTime(Route $route)
    {
        $completedRoutes = $route->users()
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->get();

        if ($completedRoutes->isEmpty()) {
            return 0;
        }

        $totalTime = $completedRoutes->sum(function ($userRoute) {
            return $userRoute->completed_at->diffInMinutes($userRoute->created_at);
        });

        return round($totalTime / $completedRoutes->count(), 2);
    }
}
