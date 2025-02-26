<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpaceResource;
use App\Models\Island;
use App\Models\Modality;
use App\Models\Service;
use App\Models\Space;
use App\Models\Comment;
use App\Models\Image;
use App\Models\SpaceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SpaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $islandId = $request->query('island_id');

            $query = Space::with([
                'address.municipality.island',  // Changed to include island
                'address.zone',
                'spaceType',
                'modalities',
                'services',
                'user',
                'comments.images'
            ]);

            // Filter by island if provided
            if ($islandId) {
                $query->whereHas('address.municipality', function ($q) use ($islandId) {
                    $q->where('island_id', $islandId);
                });
            }

            $spaces = $query->get();

            return SpaceResource::collection($spaces);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate the main space data
            $validator = Validator::make($request->all(), [
                // Required fields from spaces table (no nullable or default)
                'name' => 'required|string|max:100',
                'reg_number' => 'required|string|max:10|unique:spaces',
                'observation_CA' => 'required|string|max:5000',
                'observation_ES' => 'required|string|max:5000',
                'observation_EN' => 'required|string|max:5000',
                'email' => 'required|string|max:100',
                'phone' => 'required|string|max:100',
                'website' => 'required|string|max:100',
                'accessType' => 'required|in:B,M,A',
                'address_id' => 'required|exists:addresses,id',
                'space_type_id' => 'required|exists:space_types,id',
                'user_id' => 'required|exists:users,id',

                // Comments validation
                'comments' => 'array',
                'comments.*.comment' => 'required|string|max:5000',
                'comments.*.score' => 'required|numeric',
                'comments.*.status' => 'required|string|max:1',
                'comments.*.space_id' => 'required|exists:spaces,id',
                'comments.*.user_id' => 'required|exists:users,id',

                // Images validation
                'comments.*.images' => 'array',
                'comments.*.images.*.url' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create the space
            $space = Space::create([
                'name' => $request->name,
                'reg_number' => $request->reg_number,
                'observation_CA' => $request->observation_CA,
                'observation_ES' => $request->observation_ES,
                'observation_EN' => $request->observation_EN,
                'email' => $request->email,
                'phone' => $request->phone,
                'website' => $request->website,
                'accessType' => $request->accessType,
                'totalScore' => 0,
                'countScore' => 0,
                'address_id' => $request->address_id,
                'space_type_id' => $request->space_type_id,
                'user_id' => $request->user_id
            ]);

            // Handle comments and their images
            if ($request->has('comments')) {
                foreach ($request->comments as $commentData) {
                    // Create comment
                    $comment = Comment::create([
                        'comment' => $commentData['comment'],
                        'score' => $commentData['score'],
                        'status' => $commentData['status'],
                        'space_id' => $space->id,
                        'user_id' => $commentData['user_id']
                    ]);

                    // Handle images for this comment
                    if (isset($commentData['images']) && is_array($commentData['images'])) {
                        foreach ($commentData['images'] as $imageData) {
                            Image::create([
                                'url' => $imageData['url'],
                                'comment_id' => $comment->id
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            // Load the relationships for the response
            $space->load(['comments.images']);

            return response()->json([
                'message' => 'Space created successfully with comments and images',
                'data' => $space
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating space',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $space = Space::with([
                'address.municipality.island',
                'address.zone',
                'spaceType',
                'modalities',
                'services',
                'user',
                'comments.images',
                'comments.user' // Eager-load the user relationship for comments
            ])->findOrFail($id);

            return new SpaceResource($space);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Space not found'
            ], 404);
        }
    }

    public function showByRegNumber($regNumber)
    {
        try {
            $space = Space::with([
                'address.municipality.island',
                'address.zone',
                'spaceType',
                'modalities',
                'services',
                'user',
                'comments.images'
            ])->where('reg_number', $regNumber)->firstOrFail();

            return new SpaceResource($space);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Space not found with registration number: ' . $regNumber
            ], 404);
        }
    }

    public function getSpaceTypes()
    {
        $spaceTypes = SpaceType::all();
        return response()->json($spaceTypes);
    }

    public function getModalities()
    {
        $modalities = Modality::all();
        return response()->json($modalities);
    }

    public function getServices()
    {
        $services = Service::all();
        return response()->json($services);
    }

    public function getIslands()
    {
        $islands = Island::all();
        return response()->json($islands);
    }

    public function search() {
        $search = request()->query('search');
        $spaces = Space::where('name', 'like', "%$search%")->get();
        return response()->json($spaces);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
