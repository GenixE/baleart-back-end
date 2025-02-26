<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:5000',
            'score' => 'required|numeric|between:1,5',
            'space_id' => 'required|exists:spaces,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        $comment = Comment::create([
            'comment' => $validated['comment'],
            'score' => $validated['score'],
            'status' => 'n',
            'space_id' => $validated['space_id'],
            'user_id' => auth()->id()
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('comments', 'public');
                $comment->images()->create([
                    'url' => str_replace('public/', 'storage/', $path)
                ]);
            }
        }

        return response()->json($comment->load('user', 'images'), 201);
    }

    public function userComments(Request $request)
    {
        $comments = Comment::where('user_id', $request->user()->id)
            ->with([
                'space.address.municipality.island',
                'space.spaceType', // Changed from space_type to spaceType
                'images'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }
}
