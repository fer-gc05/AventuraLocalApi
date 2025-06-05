<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tours\StoreTourRequest;
use App\Http\Requests\Tours\UpdateTourRequest;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class TourController extends Controller
{
    /**
     * Get all tours
     * 
     * This method is used to get all tours
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function index(Request $request)
    {
        try {
            $cacheKey = 'tours_active_' . md5(json_encode($request->all()));
            $tours = Cache::tags(['tours'])->remember($cacheKey, now()->addMinutes(10), function () {
                return Tour::with([
                    'user' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'route' => function ($query) {
                        $query->select('id', 'name');
                    }
                ])
                    ->where('is_active', true)
                    ->orderBy('start_date', 'asc')
                    ->paginate(10);
            });

            if ($tours->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tours found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tours retrieved successfully',
                'data' => $tours
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving tours',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a tour
     * 
     * This method is used to create a tour
     * 
     * @param \App\Http\Requests\Tours\StoreTourRequest $request
     */
    public function store(StoreTourRequest $request)
    {
        try {
            $validated = $request->validated();

            $tour = DB::transaction(function () use ($validated) {
                return Tour::create([
                    'name' => $validated['name'],
                    'slug' => Str::slug($validated['name']),
                    'description' => $validated['description'],
                    'price' => $validated['price'],
                    'currency' => $validated['currency'],
                    'duration_days' => $validated['duration_days'],
                    'max_participants' => $validated['max_participants'],
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'route_id' => $validated['route_id'],
                    'user_id' => Auth::id(),
                    'is_active' => true,
                ]);
            });

            Cache::tags(['tours'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Tour created successfully',
                'data' => $tour->load([
                    'user' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'route' => function ($query) {
                        $query->select('id', 'name');
                    }
                ])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating tour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a tour
     * 
     * This method is used to get a tour
     * 
     * @param \App\Models\Tour $tour
     */
    public function show(Tour $tour)
    {
        try {
            $tour->load([
                'user' => function ($query) {
                    $query->select('id', 'name');
                },
                'route' => function ($query) {
                    $query->select('id', 'name');
                }
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tour recuperado exitosamente',
                'data' => $tour
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al recuperar el tour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a tour
     * 
     * This method is used to update a tour
     * 
     * @param \App\Http\Requests\Tours\UpdateTourRequest $request
     * @param \App\Models\Tour $tour
     */
    public function update(UpdateTourRequest $request, Tour $tour)
    {
        try {
            $validated = $request->validated();

            $tour = DB::transaction(function () use ($tour, $validated) {
                $tour->update([
                    'name' => $validated['name'] ?? $tour->name,
                    'slug' => isset($validated['name']) ? Str::slug($validated['name']) : $tour->slug,
                    'description' => $validated['description'] ?? $tour->description,
                    'price' => $validated['price'] ?? $tour->price,
                    'currency' => $validated['currency'] ?? $tour->currency,
                    'duration_days' => $validated['duration_days'] ?? $tour->duration_days,
                    'max_participants' => $validated['max_participants'] ?? $tour->max_participants,
                    'start_date' => $validated['start_date'] ?? $tour->start_date,
                    'end_date' => $validated['end_date'] ?? $tour->end_date,
                    'route_id' => $validated['route_id'] ?? $tour->route_id,
                    'is_active' => $validated['is_active'] ?? $tour->is_active,
                ]);
                return $tour;
            });

            Cache::tags(['tours'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Tour updated successfully',
                'data' => $tour->load([
                    'user' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'route' => function ($query) {
                        $query->select('id', 'name');
                    }
                ])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating tour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a tour
     * 
     * This method is used to delete a tour
     * 
     * @param \App\Models\Tour $tour
     */
    public function destroy(Tour $tour)
    {
        try {
            DB::transaction(function () use ($tour) {
                $tour->delete();
            });

            Cache::tags(['tours'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Tour deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting tour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all trashed tours
     * 
     * This method is used to get all trashed tours
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function trashed(Request $request)
    {
        try {
            $cacheKey = 'trashed_tours';
            $tours = Cache::tags(['tours', 'trashed_tours'])->remember($cacheKey, now()->addMinutes(10), function () {
                return Tour::onlyTrashed()->with([
                    'user' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'route' => function ($query) {
                        $query->select('id', 'name');
                    }
                ])
                    ->orderBy('deleted_at', 'desc')
                    ->paginate(10);
            });

            if ($tours->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tours found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Deleted tours retrieved successfully',
                'data' => $tours
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving deleted tours',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a tour
     * 
     * This method is used to restore a tour
     * 
     * @param int $id
     */
    public function restore($id)
    {
        try {
            $tour = Tour::onlyTrashed()->findOrFail($id);

            $tour = DB::transaction(function () use ($tour) {
                $tour->restore();
                return $tour;
            });

            Cache::tags(['tours', 'trashed_tours'])->flush();

            $tour->load([
                'user' => function ($query) {
                    $query->select('id', 'name');
                },
                'route' => function ($query) {
                    $query->select('id', 'name');
                }
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tour restored successfully',
                'data' => $tour
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tour not found or not deleted',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error restoring tour',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}