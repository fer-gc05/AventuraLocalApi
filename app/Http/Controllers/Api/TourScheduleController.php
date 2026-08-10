<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuideProfile;
use App\Models\Tour;
use App\Models\TourSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourScheduleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = TourSchedule::with(['tour']);

            if ($request->has('tour_id')) {
                $query->where('tour_id', $request->tour_id);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('from_date')) {
                $query->where('start_datetime', '>=', $request->from_date);
            }

            $schedules = $query->orderBy('start_datetime', 'asc')->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Tour schedules retrieved successfully',
                'data' => $schedules,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving tour schedules',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tour_id' => 'required|exists:tours,id',
                'start_datetime' => 'required|date',
                'end_datetime' => 'required|date|after:start_datetime',
                'max_spots' => 'required|integer|min:1',
                'price_override' => 'nullable|numeric|min:0',
                'status' => 'nullable|string|in:active,cancelled,completed',
            ]);

            $guide = auth('api')->user();
            $guideProfile = GuideProfile::where('user_id', $guide->id)->first();
            $tour = Tour::findOrFail($validated['tour_id']);

            if (!$guideProfile || $tour->guide_id !== $guideProfile->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only create schedules for your own tours',
                ], 403);
            }

            $validated['guide_id'] = $guideProfile->id;
            $validated['booked_spots'] = 0;
            $validated['is_active'] = true;

            $schedule = TourSchedule::create($validated);
            $schedule->load(['tour']);

            return response()->json([
                'success' => true,
                'message' => 'Tour schedule created successfully',
                'data' => $schedule,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating tour schedule',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $schedule = TourSchedule::with(['tour', 'reservations'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Tour schedule retrieved successfully',
                'data' => $schedule,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tour schedule not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving tour schedule',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $schedule = TourSchedule::findOrFail($id);
            $guideProfile = \App\Models\GuideProfile::where('user_id', auth('api')->id())->first();

            if (!$guideProfile || $schedule->guide_id !== $guideProfile->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only update your own schedules',
                ], 403);
            }

            $validated = $request->validate([
                'start_datetime' => 'sometimes|date',
                'end_datetime' => 'sometimes|date|after:start_datetime',
                'max_spots' => 'sometimes|integer|min:1',
                'price_override' => 'nullable|numeric|min:0',
                'status' => 'sometimes|string|in:active,cancelled,completed',
            ]);

            $schedule->update($validated);
            $schedule->load(['tour']);

            return response()->json([
                'success' => true,
                'message' => 'Tour schedule updated successfully',
                'data' => $schedule,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tour schedule not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating tour schedule',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $schedule = TourSchedule::findOrFail($id);
            $guideProfile = \App\Models\GuideProfile::where('user_id', auth('api')->id())->first();

            if (!$guideProfile || $schedule->guide_id !== $guideProfile->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own schedules',
                ], 403);
            }

            $schedule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tour schedule deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tour schedule not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting tour schedule',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
