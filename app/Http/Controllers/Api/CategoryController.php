<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Category::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $categories = $query->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully',
            'data' => $categories,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Category retrieved successfully',
            'data' => $category,
        ]);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category->fresh(),
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }

    public function destinations(Category $category): JsonResponse
    {
        $destinations = $category->destinations;

        return response()->json([
            'success' => true,
            'message' => 'Destinations retrieved successfully',
            'data' => $destinations,
        ]);
    }

    public function restore($id): JsonResponse
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->restore();

        return response()->json([
            'success' => true,
            'message' => 'Category restored successfully',
            'data' => $category,
        ]);
    }

    public function trashed(): JsonResponse
    {
        $categories = Category::onlyTrashed()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Trashed categories retrieved successfully',
            'data' => $categories,
        ]);
    }

    public function getPopularCategories(Request $request): JsonResponse
    {
        $limit = $request->query('limit', 10);

        $categories = Category::withCount(['destinations', 'events', 'communities'])
            ->orderByRaw('(destinations_count + events_count + communities_count) DESC')
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Popular categories retrieved successfully',
            'data' => $categories,
        ]);
    }

    public function getCategoryStatistics(Category $category): JsonResponse
    {
        $statistics = [
            'total_destinations' => $category->destinations()->count(),
            'total_events' => $category->events()->count(),
            'total_communities' => $category->communities()->count(),
            'active_events' => $category->events()
                ->where('start_datetime', '>', now())
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Category statistics retrieved successfully',
            'data' => $statistics,
        ]);
    }

    public function getCategoryEvents(Category $category): JsonResponse
    {
        $events = $category->events()
            ->with(['user', 'destination'])
            ->where('start_datetime', '>', now())
            ->orderBy('start_datetime', 'asc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Category events retrieved successfully',
            'data' => $events,
        ]);
    }

    public function getCategoryRoutes(Category $category): JsonResponse
    {
        $routeIds = $category->destinations()
            ->with('routes')
            ->get()
            ->pluck('routes')
            ->flatten()
            ->pluck('id')
            ->unique();

        $routes = \App\Models\Route::with(['user', 'destinations'])
            ->whereIn('id', $routeIds)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Category routes retrieved successfully',
            'data' => $routes,
        ]);
    }

    public function getCategoryCommunities(Category $category): JsonResponse
    {
        $communities = $category->communities()
            ->with(['user', 'users', 'media'])
            ->withCount('users')
            ->orderBy('users_count', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Category communities retrieved successfully',
            'data' => $communities,
        ]);
    }
}
