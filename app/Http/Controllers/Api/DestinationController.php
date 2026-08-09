<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Repositories\Contracts\DestinationRepositoryInterface;
use App\Services\DestinationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DestinationController extends Controller
{
    public function __construct(
        private DestinationService $destinationService,
        private DestinationRepositoryInterface $destinationRepo,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Destination::with(['category', 'media']);

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $destinations = $query->paginate(6);

        return response()->json([
            'success' => true,
            'message' => 'Destinations fetched successfully',
            'data' => $destinations,
        ]);
    }

    public function show($id): JsonResponse
    {
        $destination = $this->destinationService->findById((int) $id);

        if (! $destination) {
            return response()->json([
                'success' => false,
                'message' => 'Destination not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Destination fetched successfully',
            'data' => $destination,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'opening_hours' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'difficulty_level' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'duration_hours' => 'nullable|numeric|min:0',
            'is_featured' => 'nullable|boolean',
            'images' => 'nullable|array',
        ]);

        $destination = $this->destinationService->create($validated, (int) auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Destination created successfully',
            'data' => $destination,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $destination = Destination::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'opening_hours' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'category_id' => 'sometimes|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $destination = $this->destinationService->update($destination, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Destination updated successfully',
            'data' => $destination,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $destination = Destination::findOrFail($id);
        $this->destinationService->delete($destination);

        return response()->json([
            'success' => true,
            'message' => 'Destination deleted successfully',
        ]);
    }

    public function restore($id): JsonResponse
    {
        $destination = $this->destinationService->restore($id);

        return response()->json([
            'success' => true,
            'message' => 'Destination restored successfully',
            'data' => $destination,
        ]);
    }

    public function trashed(): JsonResponse
    {
        $destinations = $this->destinationService->getTrashed();

        return response()->json([
            'success' => true,
            'message' => 'Trashed destinations retrieved successfully',
            'data' => $destinations,
        ]);
    }

    public function nearby(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:100',
        ]);

        $destinations = $this->destinationService->findNearby(
            $validated['latitude'],
            $validated['longitude'],
            $validated['radius'] ?? 10
        );

        return response()->json([
            'success' => true,
            'message' => 'Nearby destinations fetched successfully',
            'data' => $destinations,
        ]);
    }

    public function events(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'destination_id' => 'required|exists:destinations,id',
        ]);

        $destination = Destination::findOrFail($validated['destination_id']);
        $events = $destination->events()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Events fetched successfully',
            'data' => $events,
        ]);
    }

    public function getPopularDestinations(Request $request): JsonResponse
    {
        $limit = $request->query('limit', 10);
        $destinations = $this->destinationService->getPopular($limit);

        return response()->json([
            'success' => true,
            'message' => 'Popular destinations retrieved successfully',
            'data' => $destinations,
        ]);
    }

    public function getDestinationReviews($id): JsonResponse
    {
        $destination = Destination::findOrFail($id);
        $reviews = $destination->reviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Destination reviews retrieved successfully',
            'data' => $reviews,
        ]);
    }

    public function getDestinationRoutes($id): JsonResponse
    {
        $destination = Destination::findOrFail($id);
        $routes = $destination->routes()
            ->with(['user', 'destinations'])
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Destination routes retrieved successfully',
            'data' => $routes,
        ]);
    }

    public function getDestinationEvents($id): JsonResponse
    {
        $destination = Destination::findOrFail($id);
        $events = $destination->events()
            ->where('start_datetime', '>', now())
            ->with(['user', 'category'])
            ->orderBy('start_datetime', 'asc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Upcoming destination events retrieved successfully',
            'data' => $events,
        ]);
    }

    public function getDestinationStatistics($id): JsonResponse
    {
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
            'data' => $statistics,
        ]);
    }
}
