<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tags\StoreTagRequest;
use App\Http\Requests\Tags\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TagController extends Controller
{
    /**
     * Get all tags
     * 
     * This method is used to get all tags
     * 
     * @param \Illuminate\Http\Request $request
     * @unauthenticated
     */
    public function index(Request $request)
    {
        try {

            $cacheKey = 'tags_' . md5(json_encode($request->all()));
            $tags = Cache::tags(['tags'])->remember($cacheKey, now()->addMinutes(10), function () use ($request) {
                $query = Tag::query();

                if ($request->has('name')) {
                    $query->where('name', 'like', '%' . $request->name . '%');
                }

                if ($request->has('color')) {
                    $query->where('color', $request->color);
                }

                $sortBy = $request->input('sort_by', 'name');
                $sortOrder = $request->input('sort_order', 'asc');
                $query->orderBy($sortBy, $sortOrder);

                return $query->paginate($request->input('per_page', 10));
            });

            if ($tags->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tags found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tags retrieved successfully',
                'data' => $tags
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving tags',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a tag
     * 
     * This method is used to create a tag
     * 
     * @param \App\Http\Requests\Tags\StoreTagRequest $request
     */
    public function store(StoreTagRequest $request)
    {
        try {
            $validated = $request->validated();

            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            $tag = Tag::create($validated);

            Cache::tags(['tags'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Tag created successfully',
                'data' => $tag
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating tag',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a tag
     * 
     * This method is used to get a tag
     * 
     * @param int $id
     * @unauthenticated
     */
    public function show($id)
    {
        try {
            $tag = Tag::with('destinations')->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Tag retrieved successfully',
                'data' => $tag
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found',
            ], 404);
        }
    }

    /**
     * Update a tag
     * 
     * This method is used to update a tag
     * 
     * @param \App\Http\Requests\Tags\UpdateTagRequest $request
     * @param \App\Models\Tag $tag
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        try {
            $validated = $request->validated();

            if (isset($validated['name']) && empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            $tag->update($validated);

            Cache::tags(['tags'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Tag updated successfully',
                'data' => $tag->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating tag',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a tag
     * 
     * This method is used to delete a tag
     * 
     * @param \App\Models\Tag $tag
     */
    public function destroy(Tag $tag)
    {
        try {
            $tag->delete();

            Cache::tags(['tags'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Tag deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting tag',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get destinations for a tag
     * 
     * This method is used to get destinations for a tag
     * 
     * @param \App\Models\Tag $tag
     */
    public function destinations(Tag $tag)
    {
        try {
            $destinations = $tag->destinations()->paginate(10);

            if ($destinations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No destinations found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Destinations for tag retrieved successfully',
                'data' => $destinations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving destinations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a tag
     * 
     * This method is used to restore a tag
     * 
     * @param int $id
     */
    public function restore($id)
    {
        try {
            $tag = Tag::withTrashed()->findOrFail($id);
            $tag->restore();

            Cache::tags(['tags'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Tag restored successfully',
                'data' => $tag
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error restoring tag',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all trashed tags
     * 
     * This method is used to get all trashed tags
     * 
     */
    public function trashed()
    {
        try {
            $tags = Tag::onlyTrashed()->paginate(10);

            if ($tags->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tags found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Trashed tags retrieved successfully',
                'data' => $tags
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving trashed tags',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
