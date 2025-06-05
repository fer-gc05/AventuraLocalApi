<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reviews\StoreReviewRequest;
use App\Http\Requests\Reviews\UpdateReviewRequest;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReviewController extends Controller
{
    /**
     * Get all reviews
     * 
     * This method is used to get all reviews
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('reviewable_type')) {
                $reviewableClass = $this->getReviewableClass($request->reviewable_type);
                if (!$reviewableClass) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid reviewable type',
                    ], 400);
                }
            }

            $cacheKey = 'user_reviews_' . Auth::id() . '_' . md5(json_encode($request->all()));
            $reviews = Cache::tags(['reviews', 'user_reviews'])->remember($cacheKey, now()->addMinutes(10), function () use ($request) {
                $query = Review::with([
                    'user' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'reviewable'
                ])
                    ->where('user_id', Auth::id());

                if ($request->has('reviewable_type')) {
                    $query->where('reviewable_type', $this->getReviewableClass($request->reviewable_type));
                }

                if ($request->has('reviewable_id')) {
                    $query->where('reviewable_id', $request->reviewable_id);
                }

                if ($request->has('status')) {
                    $query->where('status', $request->status);
                }

                return $query->orderBy('created_at', 'desc')->paginate(10);
            });

            if ($reviews->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No reviews found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User reviews retrieved successfully',
                'data' => $reviews
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving user reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a review
     * 
     * This method is used to create a review
     * 
     * @param \App\Http\Requests\Reviews\StoreReviewRequest $request
     */
    public function store(StoreReviewRequest $request)
    {
        try {
            $validated = $request->validated();

            $reviewableClass = $this->getReviewableClass($validated['reviewable_type']);
            $reviewable = $reviewableClass::findOrFail($validated['reviewable_id']);

            $review = DB::transaction(function () use ($validated) {
                return Review::create([
                    'content' => $validated['content'],
                    'rating' => $validated['rating'],
                    'user_id' => Auth::id(),
                    'status' => 'pending',
                    'reviewable_type' => $this->getReviewableClass($validated['reviewable_type']),
                    'reviewable_id' => $validated['reviewable_id'],
                ]);
            });

            Cache::tags(['reviews'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Review created successfully, pending approval',
                'data' => $review->load([
                    'user' => function ($query) {
                        $query->select('id', 'name');
                    }
                ])
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Entity not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a review
     * 
     * This method is used to get a review
     * 
     * @param \App\Models\Review $review
     */
    public function show(Review $review)
    {
        try {

            $review->load([
                'user' => function ($query) {
                    $query->select('id', 'name');
                },
                'reviewable'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Review retrieved successfully',
                'data' => $review
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a review
     * 
     * This method is used to update a review
     * 
     * @param \App\Http\Requests\Reviews\UpdateReviewRequest $request
     * @param \App\Models\Review $review
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        try {
            $validated = $request->validated();

            $review = DB::transaction(function () use ($review, $validated) {
                $review->update([
                    'content' => $validated['content'] ?? $review->content,
                    'rating' => $validated['rating'] ?? $review->rating,
                    'status' => 'pending',
                ]);
                return $review;
            });

            Cache::tags(['reviews'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully, pending approval',
                'data' => $review->load([
                    'user' => function ($query) {
                        $query->select('id', 'name');
                    }
                ])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a review
     * 
     * This method is used to delete a review
     * 
     * @param \App\Models\Review $review
     */
    public function destroy(Review $review)
    {
        try {

            DB::transaction(function () use ($review) {
                $review->delete();
            });

            Cache::tags(['reviews'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the reviewable class
     * 
     * This method is used to get the reviewable class
     * 
     * @param string $reviewableType
     */
    protected function getReviewableClass(string $reviewableType): ?string
    {
        $map = [
            'event' => \App\Models\Event::class,
            'route' => \App\Models\Route::class,
            'destination' => \App\Models\Destination::class,
        ];

        return $map[strtolower($reviewableType)] ?? null;
    }

    /**
     * Restore a review
     * 
     * This method is used to restore a review
     * 
     * @param int $id
     */
    public function restore($id)
    {
        try {
            $review = Review::onlyTrashed()->findOrFail($id);
            $review->restore();
            Cache::tags(['reviews'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Review restored successfully',
                'data' => $review->load([
                    'user' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'reviewable'
                ])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error restoring review',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}