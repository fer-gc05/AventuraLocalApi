<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Communities\StoreCommunityRequest;
use App\Http\Requests\Communities\UpdateCommunityRequest;
use App\Models\Community;
use App\Models\Event;
use App\Models\Route;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    /**
     * Get all communities
     * 
     * This method is used to get all communities
     * 
     * @param \Illuminate\Http\Request $request
     * @unauthenticated
     */
    public function index(Request $request)
    {
        try {
            $cacheKey = 'communities_' . md5(json_encode($request->all()));
            $communities = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
                $query = Community::with(['user', 'category', 'users', 'media']);

                if ($request->has('name')) {
                    $query->where('name', 'like', '%' . $request->name . '%');
                }

                if ($request->has('category_id')) {
                    $query->where('category_id', $request->category_id);
                }

                if ($request->has('is_public')) {
                    $query->where('is_public', filter_var($request->is_public, FILTER_VALIDATE_BOOLEAN));
                }

                return $query->orderBy('created_at', 'desc')->paginate(10);
            });

            if ($communities->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No communities found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Communities fetched successfully',
                'data' => $communities,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching communities',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a community
     * 
     * This method is used to get a community
     * 
     * @param \App\Models\Community $community
     * @unauthenticated
     */
    public function show(Community $community)
    {
        try {
            $community = $community->load(['user', 'category', 'users', 'media']);

            return response()->json([
                'success' => true,
                'message' => 'Community fetched successfully',
                'data' => $community,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Community not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching community',
                'error' => $e->getMessage(),
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Community not found',
            ], 404);
        }
    }

    /**
     * Create a community
     * 
     * This method is used to create a community
     * 
     * @param \App\Http\Requests\Communities\StoreCommunityRequest $request
     */
    public function store(StoreCommunityRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();

            $community = Community::create($data);


            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('communities', $fileName, 'public');

                    $community->media()->create([
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'url' => asset('storage/' . $filePath),
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'custom_properties' => ['type' => 'community_image'],
                    ]);
                }
            }

            $community->users()->syncWithoutDetaching([
                $data['user_id'] => ['role' => 'admin']
            ]);

            DB::commit();
            Cache::tags(['communities'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Community created successfully',
                'data' => $community->load(['user', 'category', 'users', 'media'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating community',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a community
     * 
     * This method is used to update a community
     * 
     * @param \App\Http\Requests\Communities\UpdateCommunityRequest $request
     * @param \App\Models\Community $community
     */
    public function update(UpdateCommunityRequest $request, Community $community)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $community->update($data);

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('communities', $fileName, 'public');

                    $community->media()->create([
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'url' => asset('storage/' . $filePath),
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'custom_properties' => ['type' => 'community_image'],
                    ]);
                }
            }

            DB::commit();
            Cache::tags(['communities'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Community updated successfully',
                'data' => $community->load(['user', 'category', 'users', 'media'])
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Community not found',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating community',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a community
     * 
     * This method is used to delete a community
     * 
     * @param \App\Models\Community $community
     */
    public function destroy(Community $community)
    {
        DB::beginTransaction();
        try {
            $community->delete();

            DB::commit();
            Cache::tags(['communities'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Community deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Community not found',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting community',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore a community
     * 
     * This method is used to restore a community
     * 
     * @param int $id
     */
    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $community = Community::withTrashed()->findOrFail($id);
            $community->restore();

            DB::commit();
            Cache::tags(['communities'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Community restored successfully',
                'data' => $community
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Community not found',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error restoring community',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all trashed communities
     * 
     * This method is used to get all trashed communities
     * 
     */
    public function trashed()
    {
        try {
            $communities = Community::onlyTrashed()->paginate(10);

            if ($communities->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No trashed communities found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Trashed communities retrieved successfully',
                'data' => $communities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving trashed communities',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Join a community
     * 
     * This method is used to join a community
     * 
     * @param \App\Models\Community $community
     */
    public function join(Community $community)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            if (!$community->is_public && !$community->users()->where('user_id', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot join private community without invitation',
                ], 403);
            }

            $community->users()->syncWithoutDetaching([
                $user->id => ['role' => 'member']
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Joined community successfully',
                'data' => [
                    'community_id' => $community->id,
                    'role' => 'member'
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Community not found',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error joining community',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get popular communities
     * 
     * This method is used to get popular communities
     * 
     * @param \Illuminate\Http\Request $request
     * @unauthenticated
     */
        public function getPopularCommunities(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            $cacheKey = 'popular_communities_' . $limit;

            $communities = Cache::tags(['communities'])->remember($cacheKey, now()->addHours(1), function () use ($limit) {
                return Community::with(['category', 'media'])
                    ->withCount('users')
                    ->orderBy('users_count', 'desc')
                    ->take($limit)
                    ->get();
            });

            if ($communities->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No popular communities found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Popular communities retrieved successfully',
                'data' => $communities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving popular communities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get community events
     * 
     * This method is used to get community events
     * 
     * @param \App\Models\Community $community
     * @param \Illuminate\Http\Request $request
     */
     public function getCommunityEvents(Community $community, Request $request)
    {
        try {
            $userIds = $community->users()->pluck('users.id');

            $events = Event::whereIn('user_id', $userIds)
                ->where('start_datetime', '>', now())
                ->orderBy('start_datetime', 'asc')
                ->with(['user', 'destination'])
                ->paginate(10);

            if ($events->isEmpty()) {

                return response()->json([
                    'success' => false,
                    'message' => 'No community events found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Community events retrieved successfully',
                'data' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving community events',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get community routes
     * 
     * This method is used to get community routes
     * 
     * @param \App\Models\Community $community
     * @param \Illuminate\Http\Request $request
     */
    public function getCommunityRoutes(Community $community, Request $request)
    {
        try {
            $userIds = $community->users()->pluck('users.id');

            $routes = Route::whereExists(function ($query) use ($userIds) {
                $query->select(DB::raw(1))
                    ->from('route_user')
                    ->whereColumn('route_user.route_id', 'routes.id')
                    ->whereIn('route_user.user_id', $userIds);
            })
                ->orderBy('created_at', 'desc')
                ->with(['users'])
                ->paginate(10);

            if ($routes->isEmpty()) {

                return response()->json([
                    'success' => false,
                    'message' => 'No community routes found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Community routes retrieved successfully',
                'data' => $routes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving community routes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get community recommendations
     * 
     * This method is used to get community recommendations
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function getCommunityRecommendations(Request $request)
    {
        try {
            $user = auth()->user();
            $limit = $request->query('limit', 5);

            $recommendations = Cache::tags(['communities'])->remember('community_recommendations_' . $user->id, now()->addHours(1), function () use ($user, $limit) {
                $userCategories = $user->communities()
                    ->with('category')
                    ->get()
                    ->pluck('category.id')
                    ->unique();

                return Community::with(['category', 'media'])
                    ->whereIn('category_id', $userCategories)
                    ->whereNotIn('id', $user->communities()->pluck('communities.id'))
                    ->where('is_public', true)
                    ->withCount('users')
                    ->orderBy('users_count', 'desc')
                    ->take($limit)
                    ->get();
            });

            if ($recommendations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No community recommendations found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Community recommendations retrieved successfully',
                'data' => $recommendations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving community recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}