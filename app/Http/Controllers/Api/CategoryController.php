<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Categories\StoreCategoryRequest;
use App\Http\Requests\Categories\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Get all categories
     * 
     * This method is used to get all categories
     * 
     * @param \Illuminate\Http\Request $request
     * @unauthenticated
     */
    public function index(Request $request)
    {
        try {
            $cacheKey = 'categories_' . md5(json_encode($request->all()));
            $categories = Cache::tags(['categories'])->remember($cacheKey, now()->addMinutes(10), function () use ($request) {
                $query = Category::query();

                if ($request->has('name')) {
                    $query->where('name', 'like', '%' . $request->name . '%');
                }

                return $query->paginate(10);
            });


            if ($categories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categories not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving categories',
                'error' => $e->getMessage()
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }
    }

    /**
     * Create a category
     * 
     * This method is used to create a category
     * 
     * @param \App\Http\Requests\Categories\StoreCategoryRequest $request
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $validated = $request->validated();

            $category = Category::create($validated);

            Cache::tags(['categories'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a category
     * 
     * This method is used to get a category
     * 
     * @param \App\Models\Category $category
     * @unauthenticated
     */
    public function show(Category $category)
    {
        try {
            $category = Category::findOrFail($category->id);
            return response()->json([
                'success' => true,
                'message' => 'Category retrieved successfully',
                'data' => $category
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }
    }

    /**
     * Update a category
     * 
     * This method is used to update a category
     * 
     * @param \App\Http\Requests\Categories\UpdateCategoryRequest $request
     * @param \App\Models\Category $category
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $validated = $request->validated();

            $category->update($validated);

            Cache::tags(['categories'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating category',
                'error' => $e->getMessage()
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }
    }

    /**
     * Delete a category
     * 
     * This method is used to delete a category
     * 
     * @param \App\Models\Category $category
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();

            Cache::tags(['categories'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting category',
                'error' => $e->getMessage()
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }
    }

    /**
     * Get destinations for a category
     * 
     * This method is used to get destinations for a category
     * 
     * @param \App\Models\Category $category
     * @unauthenticated
     */
    public function destinations(Category $category)
    {
        try {
            $destinations = $category->destinations;
            if ($destinations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Destinations not found',
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
                'error' => $e->getMessage()
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }
    }

    /**
     * Restore a category
     * 
     * This method is used to restore a category
     * 
     * @param int $id
     */
    public function restore($id)
    {
        try {
            $category = Category::withTrashed()->findOrFail($id);
            $category->restore();

            Cache::tags(['categories'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Category restored successfully',
                'data' => $category
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error restoring category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all trashed categories
     * 
     * This method is used to get all trashed categories
     * 
     */
    public function trashed()
    {
        try {
            $categories = Category::onlyTrashed()->paginate(10);

            if ($categories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No trashed categories found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Trashed categories retrieved successfully',
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving trashed categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get popular categories
     * 
     * This method is used to get popular categories
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function getPopularCategories(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            $cacheKey = 'popular_categories_' . $limit;

            $categories = Cache::tags(['categories'])->remember($cacheKey, now()->addHours(1), function () use ($limit) {
                return Category::withCount(['destinations', 'events', 'communities'])
                    ->orderByRaw('(destinations_count + events_count + communities_count) DESC')
                    ->take($limit)
                    ->get();
            });

            if ($categories->isEmpty()) {

                return response()->json([
                    'success' => false,
                    'message' => 'No popular categories found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Popular categories retrieved successfully',
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving popular categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category statistics
     * 
     * This method is used to get category statistics
     * 
     * @param \App\Models\Category $category
     */
    public function getCategoryStatistics(Category $category)
    {
        try {
            $statistics = [
                'total_destinations' => $category->destinations()->count(),
                'total_events' => $category->events()->count(),
                'total_communities' => $category->communities()->count(),
                'active_events' => $category->events()
                    ->where('start_datetime', '>', now())
                    ->count(),
                'popular_destinations' => $this->getPopularDestinations($category),
                'user_engagement' => $this->calculateUserEngagement($category),
                'growth_rate' => $this->calculateGrowthRate($category)
            ];

            return response()->json([
                'success' => true,
                'message' => 'Category statistics retrieved successfully',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving category statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category events
     * 
     * This method is used to get category events
     * 
     * @param \App\Models\Category $category
     */
    public function getCategoryEvents(Category $category)
    {
        try {
            $events = $category->events()
                ->with(['organizer', 'destination'])
                ->where('start_datetime', '>', now())
                ->orderBy('start_datetime', 'asc')
                ->paginate(10);

            if ($events->isEmpty()) {

                return response()->json([
                    'success' => false,
                    'message' => 'No category events found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Category events retrieved successfully',
                'data' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving category events',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category routes
     * 
     * This method is used to get category routes
     * 
     * @param \App\Models\Category $category
     */
    public function getCategoryRoutes(Category $category)
    {
        try {
            $routes = $category->destinations()
                ->with([
                    'routes' => function ($query) {
                        $query->with(['user', 'destinations'])
                            ->orderBy('created_at', 'desc');
                    }
                ])
                ->get()
                ->pluck('routes')
                ->flatten()
                ->unique('id')
                ->values()
                ->paginate(10);

            if ($routes->isEmpty()) {

                return response()->json([
                    'success' => false,
                    'message' => 'No category routes found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Category routes retrieved successfully',
                'data' => $routes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving category routes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category communities
     * 
     * This method is used to get category communities
     * 
     * @param \App\Models\Category $category
     */
    public function getCategoryCommunities(Category $category)
    {
        try {
            $communities = $category->communities()
                ->with(['user', 'users', 'media'])
                ->withCount('users')
                ->orderBy('users_count', 'desc')
                ->paginate(10);

            if ($communities->isEmpty()) {

                return response()->json([
                    'success' => false,
                    'message' => 'No category communities found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Category communities retrieved successfully',
                'data' => $communities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving category communities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get popular destinations
     * 
     * This method is used to get popular destinations
     * 
     * @param \App\Models\Category $category
     */
    private function getPopularDestinations(Category $category)
    {
        return $category->destinations()
            ->withCount([
                'users as favorites_count' => function ($query) {
                    $query->where('is_favorite', true);
                }
            ])
            ->orderBy('favorites_count', 'desc')
            ->take(3)
            ->get();
    }

    /**
     * Calculate user engagement
     * 
     * This method is used to calculate user engagement
     * 
     * @param \App\Models\Category $category
     */
    private function calculateUserEngagement(Category $category)
    {
        $totalUsers = $category->communities()
            ->withCount('users')
            ->get()
            ->sum('users_count');

        if ($totalUsers === 0) {
            return 0;
        }

        $activeUsers = $category->communities()
            ->withCount([
                'users' => function ($query) {
                    $query->where('last_active_at', '>=', now()->subDays(30));
                }
            ])
            ->get()
            ->sum('users_count');

        return round(($activeUsers / $totalUsers) * 100, 2);
    }

    /**
     * Calculate growth rate
     * 
     * This method is used to calculate growth rate
     * 
     * @param \App\Models\Category $category
     */
    private function calculateGrowthRate(Category $category)
    {
        $lastMonthItems = $category->destinations()
            ->where('created_at', '<', now()->subMonth())
            ->count() +
            $category->events()
                ->where('created_at', '<', now()->subMonth())
                ->count() +
            $category->communities()
                ->where('created_at', '<', now()->subMonth())
                ->count();

        if ($lastMonthItems === 0) {
            return 0;
        }

        $currentItems = $category->destinations()->count() +
            $category->events()->count() +
            $category->communities()->count();

        return round((($currentItems - $lastMonthItems) / $lastMonthItems) * 100, 2);
    }
}
