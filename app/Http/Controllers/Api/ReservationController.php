<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reservations\StoreReservationRequest;
use App\Http\Requests\Reservations\UpdateReservationRequest;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReservationController extends Controller
{
    /**
     * Get all reservations
     * 
     * This method is used to get all reservations
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function index(Request $request)
    {
        try {
            $cacheKey = 'reservations_' . (Auth::user()->hasRole('Administrator') ? 'all' : Auth::id());
            $reservations = Cache::tags(['reservations'])->remember($cacheKey, now()->addMinutes(10), function () {
                $query = Reservation::with([
                    'user' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'tour' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'destination' => function ($query) {
                        $query->select('id', 'name');
                    }
                ]);

                if (!Auth::user()->hasRole('Administrator')) {
                    $query->where('user_id', Auth::id());
                }

                return $query->orderBy('created_at', 'desc')->paginate(10);
            });

            if ($reservations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No reservations found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reservations retrieved successfully',
                'data' => $reservations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving reservations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a reservation
     * 
     * This method is used to create a reservation
     * 
     * @param \App\Http\Requests\Reservations\StoreReservationRequest $request
     */
    public function store(StoreReservationRequest $request)
    {
        try {
            $validated = $request->validated();

            $reservation = DB::transaction(function () use ($validated) {
                return Reservation::create([
                    'user_id' => Auth::id(),
                    'tour_id' => $validated['tour_id'],
                    'destination_id' => $validated['destination_id'],
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'participants' => $validated['participants'],
                    'total_price' => $validated['total_price'],
                    'currency' => $validated['currency'],
                    'status' => 'pending',
                    'special_requests' => $validated['special_requests'] ?? null,
                ]);
            });

            Cache::tags(['reservations'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Reservation created successfully, pending confirmation',
                'data' => $reservation->load([
                    'user' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'tour' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'destination' => function ($query) {
                        $query->select('id', 'name');
                    }
                ])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating reservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a reservation
     * 
     * This method is used to get a reservation
     * 
     * @param \App\Models\Reservation $reservation
     */
    public function show(Reservation $reservation)
    {
        try {

            $reservation->load([
                'user' => function ($query) {
                    $query->select('id', 'name');
                },
                'tour' => function ($query) {
                    $query->select('id', 'name');
                },
                'destination' => function ($query) {
                    $query->select('id', 'name');
                }
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reservation retrieved successfully',
                'data' => $reservation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving reservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a reservation
     * 
     * This method is used to update a reservation
     * 
     * @param \App\Http\Requests\Reservations\UpdateReservationRequest $request
     * @param \App\Models\Reservation $reservation
     */
    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        try {

            $validated = $request->validated();

            $reservation = DB::transaction(function () use ($reservation, $validated) {
                $reservation->update([
                    'start_date' => $validated['start_date'] ?? $reservation->start_date,
                    'end_date' => $validated['end_date'] ?? $reservation->end_date,
                    'participants' => $validated['participants'] ?? $reservation->participants,
                    'total_price' => $validated['total_price'] ?? $reservation->total_price,
                    'currency' => $validated['currency'] ?? $reservation->currency,
                    'status' => $validated['status'] ?? $reservation->status,
                    'special_requests' => $validated['special_requests'] ?? $reservation->special_requests,
                ]);
                return $reservation;
            });

            Cache::tags(['reservations'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Reservation updated successfully',
                'data' => $reservation->load([
                    'user' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'tour' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'destination' => function ($query) {
                        $query->select('id', 'name');
                    }
                ])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating reservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a reservation
     * 
     * This method is used to delete a reservation
     * 
     * @param \App\Models\Reservation $reservation
     */
    public function destroy(Reservation $reservation)
    {
        try {

            DB::transaction(function () use ($reservation) {
                $reservation->delete();
            });

            Cache::tags(['reservations'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Reservation deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting reservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all trashed reservations
     * 
     * This method is used to get all trashed reservations
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function trashed(Request $request)
    {
        try {

            $cacheKey = 'trashed_reservations';
            $reservations = Cache::tags(['reservations', 'trashed_reservations'])->remember($cacheKey, now()->addMinutes(10), function () {
                return Reservation::onlyTrashed()->with([
                    'user' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'tour' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'destination' => function ($query) {
                        $query->select('id', 'name');
                    }
                ])
                    ->orderBy('deleted_at', 'desc')
                    ->paginate(10);
            });

            if ($reservations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No deleted reservations found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Deleted reservations retrieved successfully',
                'data' => $reservations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving deleted reservations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a reservation
     * 
     * This method is used to restore a reservation
     * 
     * @param int $id
     */
    public function restore($id)
    {
        try {


            $reservation = Reservation::onlyTrashed()->findOrFail($id);

            $reservation = DB::transaction(function () use ($reservation) {
                $reservation->restore();
                return $reservation;
            });

            Cache::tags(['reservations', 'trashed_reservations'])->flush();

            $reservation->load([
                'user' => function ($query) {
                    $query->select('id', 'name');
                },
                'tour' => function ($query) {
                    $query->select('id', 'name');
                },
                'destination' => function ($query) {
                    $query->select('id', 'name');
                }
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reservation restored successfully',
                'data' => $reservation
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found or not deleted',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error restoring reservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}