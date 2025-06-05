<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateProfilePhotoRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Destination;
use App\Models\Route;

class UserController extends Controller
{
    /**
     * Get all users
     * 
     * This method is used to get all users
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function index(Request $request)
    {
        try {
            $cacheKey = 'users_' . md5(json_encode($request->all()));
            $users = Cache::tags(['users'])->remember($cacheKey, now()->addMinutes(10), function () use ($request) {
                $filters = [
                    'name' => $request->query('name'),
                    'email' => $request->query('email'),
                    'role' => $request->query('role')
                ];
                $perPage = $request->query('per_page', 10);

                $query = User::query()->with('media');

                if ($filters['name']) {
                    $query->where('name', 'like', '%' . $filters['name'] . '%');
                }

                if ($filters['email']) {
                    $query->where('email', 'like', '%' . $filters['email'] . '%');
                }

                if ($filters['role']) {
                    $query->whereHas('roles', function ($q) use ($filters) {
                        $q->where('name', $filters['role']);
                    });
                }

                return $query->paginate($perPage);
            });

            if($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data' => $users
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $e->getMessage()
            ], 500);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving users token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a user
     * 
     * This method is used to create a user
     * 
     * @param \App\Http\Requests\Users\StoreUserRequest $request
     */
    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create($request->validated());
            $user->assignRole($request->role);

            DB::commit();
            Cache::tags(['users'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a user
     * 
     * This method is used to get a user
     * 
     * @param \App\Models\User $user
     */
    public function show(User $user)
    {
        try {
            $cacheKey = 'user_' . $user->id;
            $cachedUser = Cache::tags(['users'])->remember($cacheKey, now()->addMinutes(10), function () use ($user) {
                return $user->load('media');
            });

            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => $cachedUser
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving user token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a user
     * 
     * This method is used to update a user
     * 
     * @param \App\Http\Requests\Users\UpdateUserRequest $request
     * @param \App\Models\User $user
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $user->update($request->validated());

            DB::commit();
            Cache::tags(['users'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating user token',
                'error' => $e->getMessage()
            ], 500);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a user
     * 
     * This method is used to delete a user
     * 
     * @param \App\Models\User $user
     */
    public function destroy(User $user)
    {
        DB::beginTransaction();
        try {
            $user->delete();

            DB::commit();
            Cache::tags(['users'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a user
     * 
     * This method is used to restore a user
     * 
     * @param int $id
     */
    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();

            DB::commit();
            Cache::tags(['users'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'User restored successfully',
                'data' => $user
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error restoring user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all trashed users
     * 
     * This method is used to get all trashed users
     * 
     */
    public function trashed()
    {
        try {
            $users = User::onlyTrashed()->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Trashed users retrieved successfully',
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving trashed users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a user's profile photo
     * 
     * This method is used to update a user's profile photo
     * 
     * @param \App\Http\Requests\Users\UpdateProfilePhotoRequest $request
     */
    public function updateProfilePhoto(UpdateProfilePhotoRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = JWTAuth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            if (!$request->hasFile('profile_photo')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No profile photo provided',
                ], 400);
            }

            $file = $request->file('profile_photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('profile_photos', $fileName, 'public');
            $url = asset('storage/' . $filePath);

            $media = $user->media()->updateOrCreate(
                ['custom_properties->type' => 'profile_photo'],
                [
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'url' => $url,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'custom_properties' => ['type' => 'profile_photo'],
                ]
            );

            DB::commit();
            Cache::tags(['users'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully',
                'profile_photo' => $media->url,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile photo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a user's destinations
     * 
     * This method is used to get a user's destinations
     * 
     * @param \App\Models\User $user
     */
    public function getDestinations(User $user)
    {
        try {
            $cacheKey = 'user_destinations_' . $user->id;
            $destinations = Cache::tags(['users'])->remember($cacheKey, now()->addMinutes(10), function () use ($user) {
                return $user->destinations()->with('media')->get();
            });

            if ($destinations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No destinations found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Destinations retrieved successfully',
                'data' => $destinations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving destinations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a user's communities
     * 
     * This method is used to get a user's communities
     * 
     * @param \App\Models\User $user
     */
    public function getCommunities(User $user)
    {
        try {
            $communities = $user->communities()->with(['category', 'media'])->paginate(10);

            if ($communities->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No communities found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User communities retrieved successfully',
                'data' => $communities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving user communities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a user's event history
     * 
     * This method is used to get a user's event history
     * 
     * @param \App\Models\User $user
     */
    public function getEventHistory(User $user)
    {
        try {
            $events = $user->attendedEvents()
                ->with(['destination', 'user'])
                ->orderBy('start_datetime', 'desc')
                ->paginate(10);

            if ($events->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No event history found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User event history retrieved successfully',
                'data' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving user event history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a user's favorite routes
     * 
     * This method is used to get a user's favorite routes
     * 
     * @param \App\Models\User $user
     */
    public function getFavoriteRoutes(User $user)
    {
        try {
            $routes = $user->favoriteRoutes()
                ->with(['user', 'destinations'])
                ->paginate(10);

            if ($routes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No favorite routes found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Favorite routes retrieved successfully',
                'data' => $routes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving favorite routes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a user's favorite destinations
     * 
     * This method is used to get a user's favorite destinations
     * 
     * @param \App\Models\User $user
     */
    public function getFavoriteDestinations(User $user)
    {
        try {
            $destinations = $user->favoriteDestinations()
                ->with(['category', 'media'])
                ->paginate(10);

            if ($destinations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No favorite destinations found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Favorite destinations retrieved successfully',
                'data' => $destinations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving favorite destinations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle a user's favorite destination
     * 
     * This method is used to toggle a user's favorite destination
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Destination $destination
     */
    public function toggleFavoriteDestination(Request $request, Destination $destination)
    {
        try {
            $user = auth()->user();
            $isFavorite = $user->favoriteDestinations()->where('destination_id', $destination->id)->exists();

            if ($isFavorite) {
                $user->favoriteDestinations()->detach($destination->id);
                $message = 'Destination removed from favorites';
            } else {
                $user->favoriteDestinations()->attach($destination->id, ['is_favorite' => true]);
                $message = 'Destination added to favorites';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'destination_id' => $destination->id,
                    'is_favorite' => !$isFavorite
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling favorite destination',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle a user's favorite route
     * 
     * This method is used to toggle a user's favorite route
     * 
     * @param \Illuminate\Http\Request $request     
     * @param \App\Models\Route $route
     */
    public function toggleFavoriteRoute(Request $request, Route $route)
    {
        try {
            $user = auth()->user();
            $isFavorite = $user->favoriteRoutes()->where('route_id', $route->id)->exists();

            if ($isFavorite) {
                $user->favoriteRoutes()->detach($route->id);
                $message = 'Route removed from favorites';
            } else {
                $user->favoriteRoutes()->attach($route->id, ['is_favorite' => true]);
                $message = 'Route added to favorites';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'route_id' => $route->id,
                    'is_favorite' => !$isFavorite
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling favorite route',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a route's status
     * 
     * This method is used to update a route's status
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Route $route
     */
    public function updateRouteStatus(Request $request, Route $route)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,in_progress,completed'
            ]);

            $user = auth()->user();
            $data = [
                'status' => $request->status
            ];

            if ($request->status === 'completed') {
                $data['completed_at'] = now();
            }

            $user->routes()->syncWithoutDetaching([
                $route->id => $data
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Route status updated successfully',
                'data' => [
                    'route_id' => $route->id,
                    'status' => $request->status,
                    'completed_at' => $data['completed_at'] ?? null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating route status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a user's reviews
     * 
     * This method is used to get a user's reviews
     * 
     * @param \App\Models\User $user
     */
    public function getReviews(User $user)
    {
        try {
            $reviews = $user->reviews()
                ->with(['reviewable'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'User reviews retrieved successfully',
                'data' => $reviews
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving user reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a user's statistics
     * 
     * This method is used to get a user's statistics
     * 
     * @param \App\Models\User $user
     */
    public function getStatistics(User $user)
    {
        try {
            $statistics = [
                'events_attended' => $user->attendedEvents()->count(),
                'routes_completed' => $user->completedRoutes()->count(),
                'communities_joined' => $user->communities()->count(),
                'reviews_written' => $user->reviews()->count(),
                'favorite_routes' => $user->favoriteRoutes()->count(),
                'favorite_destinations' => $user->favoriteDestinations()->count(),
                'upcoming_events' => $user->attendedEvents()
                    ->where('start_datetime', '>', now())
                    ->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'User statistics retrieved successfully',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving user statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}