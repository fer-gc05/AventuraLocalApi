<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $reservations = $this->bookingService->findByUser(auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Reservations retrieved successfully',
            'data' => $reservations,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tour_schedule_id' => 'required|exists:tour_schedules,id',
            'participants' => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $reservation = $this->bookingService->create($validated, auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Reservation created successfully, pending confirmation',
            'data' => $reservation,
        ], 201);
    }

    public function show(Reservation $reservation): JsonResponse
    {
        $reservation = $this->bookingService->findById($reservation->id);

        return response()->json([
            'success' => true,
            'message' => 'Reservation retrieved successfully',
            'data' => $reservation,
        ]);
    }

    public function cancel(Request $request, Reservation $reservation): JsonResponse
    {
        $reservation = $this->bookingService->cancel(
            $reservation,
            auth()->id(),
            $request->input('cancellation_reason')
        );

        return response()->json([
            'success' => true,
            'message' => 'Reservation cancelled successfully',
            'data' => $reservation,
        ]);
    }

    public function confirm(Reservation $reservation): JsonResponse
    {
        $reservation = $this->bookingService->confirm($reservation);

        return response()->json([
            'success' => true,
            'message' => 'Reservation confirmed successfully',
            'data' => $reservation,
        ]);
    }

    public function destroy(Reservation $reservation): JsonResponse
    {
        $reservation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reservation deleted successfully',
        ]);
    }

    public function trashed(): JsonResponse
    {
        $reservations = Reservation::onlyTrashed()
            ->with(['user', 'tour', 'tourSchedule'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Deleted reservations retrieved successfully',
            'data' => $reservations,
        ]);
    }

    public function restore($id): JsonResponse
    {
        $reservation = Reservation::onlyTrashed()->findOrFail($id);
        $reservation->restore();

        $reservation->load(['user', 'tour', 'tourSchedule']);

        return response()->json([
            'success' => true,
            'message' => 'Reservation restored successfully',
            'data' => $reservation,
        ]);
    }
}
