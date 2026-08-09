<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuideProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GuideProfileController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = GuideProfile::with(['user']);

            if ($request->has('specialty')) {
                $query->whereJsonContains('specialties', $request->specialty);
            }

            if ($request->has('language')) {
                $query->whereJsonContains('languages', $request->language);
            }

            if ($request->has('is_verified')) {
                $query->where('is_verified', $request->boolean('is_verified'));
            }

            if ($request->has('search')) {
                $term = $request->search;
                $query->where(function ($q) use ($term) {
                    $q->where('bio', 'like', "%{$term}%")
                      ->orWhereHas('user', function ($uq) use ($term) {
                          $uq->where('name', 'like', "%{$term}%");
                      });
                });
            }

            $profiles = $query->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Guide profiles retrieved successfully',
                'data' => $profiles,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving guide profiles',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user->hasRole('Guide')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only users with Guide role can create a guide profile',
                ], 403);
            }

            $existing = GuideProfile::where('user_id', $user->id)->first();
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already has a guide profile',
                ], 422);
            }

            $validated = $request->validate([
                'bio' => 'required|string|max:2000',
                'phone' => 'nullable|string|max:20',
                'languages' => 'nullable|array',
                'languages.*' => 'string',
                'specialties' => 'nullable|array',
                'specialties.*' => 'string',
            ]);

            $validated['user_id'] = $user->id;
            $validated['verification_status'] = 'pending';
            $validated['is_verified'] = false;
            $validated['is_active'] = true;

            $profile = GuideProfile::create($validated);
            $profile->load(['user']);

            return response()->json([
                'success' => true,
                'message' => 'Guide profile created successfully',
                'data' => $profile,
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
                'message' => 'Error creating guide profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $profile = GuideProfile::with(['user'])
                ->findOrFail($id);

            $tours = \App\Models\Tour::where('guide_id', $profile->user_id)
                ->with(['category', 'destination'])
                ->get();

            $profile->tours = $tours;

            return response()->json([
                'success' => true,
                'message' => 'Guide profile retrieved successfully',
                'data' => $profile,
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Guide profile not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving guide profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $profile = GuideProfile::findOrFail($id);
            $user = auth('api')->user();

            if ($profile->user_id !== $user->id && !$user->hasRole('Administrator')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only update your own guide profile',
                ], 403);
            }

            $validated = $request->validate([
                'bio' => 'sometimes|string|max:2000',
                'phone' => 'nullable|string|max:20',
                'languages' => 'nullable|array',
                'languages.*' => 'string',
                'specialties' => 'nullable|array',
                'specialties.*' => 'string',
            ]);

            $profile->update($validated);
            $profile->load(['user']);

            return response()->json([
                'success' => true,
                'message' => 'Guide profile updated successfully',
                'data' => $profile,
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Guide profile not found',
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
                'message' => 'Error updating guide profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $profile = GuideProfile::findOrFail($id);
            $user = auth('api')->user();

            if ($profile->user_id !== $user->id && !$user->hasRole('Administrator')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own guide profile',
                ], 403);
            }

            $profile->delete();

            return response()->json([
                'success' => true,
                'message' => 'Guide profile deleted successfully',
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Guide profile not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting guide profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function verified()
    {
        try {
            $profiles = GuideProfile::where('is_verified', true)
                ->where('is_active', true)
                ->with(['user'])
                ->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Verified guide profiles retrieved successfully',
                'data' => $profiles,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving verified guide profiles',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
