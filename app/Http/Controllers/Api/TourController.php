<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Services\TourService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function __construct(
        private TourService $tourService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $tours = $this->tourService->getActive($request->input('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'Tours retrieved successfully',
            'data' => $tours,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'destination_id' => 'required|exists:destinations,id',
            'meeting_point' => 'required|string',
            'meeting_latitude' => 'required|numeric',
            'meeting_longitude' => 'required|numeric',
            'price_per_person' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'duration_minutes' => 'required|integer|min:1',
            'max_participants' => 'required|integer|min:1',
            'min_participants' => 'nullable|integer|min:1',
            'languages' => 'nullable|array',
            'includes' => 'nullable|array',
            'excludes' => 'nullable|array',
            'what_to_bring' => 'nullable|array',
            'difficulty' => 'nullable|string|in:easy,medium,hard',
        ]);

        $tour = $this->tourService->create($validated, auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Tour created successfully',
            'data' => $tour,
        ], 201);
    }

    public function show(Tour $tour): JsonResponse
    {
        $tour = $this->tourService->findById($tour->id);

        return response()->json([
            'success' => true,
            'message' => 'Tour retrieved successfully',
            'data' => $tour,
        ]);
    }

    public function update(Request $request, Tour $tour): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
            'destination_id' => 'sometimes|exists:destinations,id',
            'meeting_point' => 'sometimes|string',
            'meeting_latitude' => 'sometimes|numeric',
            'meeting_longitude' => 'sometimes|numeric',
            'price_per_person' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|max:3',
            'duration_minutes' => 'sometimes|integer|min:1',
            'max_participants' => 'sometimes|integer|min:1',
            'min_participants' => 'nullable|integer|min:1',
            'languages' => 'nullable|array',
            'includes' => 'nullable|array',
            'excludes' => 'nullable|array',
            'what_to_bring' => 'nullable|array',
            'difficulty' => 'nullable|string|in:easy,medium,hard',
            'is_active' => 'sometimes|boolean',
        ]);

        $tour = $this->tourService->update($tour, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Tour updated successfully',
            'data' => $tour,
        ]);
    }

    public function destroy(Tour $tour): JsonResponse
    {
        $this->tourService->delete($tour);

        return response()->json([
            'success' => true,
            'message' => 'Tour deleted successfully',
        ]);
    }

    public function trashed(): JsonResponse
    {
        $tours = $this->tourService->getTrashed();

        return response()->json([
            'success' => true,
            'message' => 'Deleted tours retrieved successfully',
            'data' => $tours,
        ]);
    }

    public function restore($id): JsonResponse
    {
        $tour = $this->tourService->restore($id);

        return response()->json([
            'success' => true,
            'message' => 'Tour restored successfully',
            'data' => $tour,
        ]);
    }
}
