<?php

namespace App\Http\Controllers\API;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    /**
     * Display a listing of comments.
     */
    public function index()
    {
        $comments = Comment::with('task', 'user')->get();
        return response()->json($comments, 200);
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'user_id' => 'required|exists:app_users,id',
            'comment' => 'required|string',
        ]);

        $comment = Comment::create($request->all());
        return response()->json([
            'status' => 'success',
            'data' => $comment
        ], 200);
    }

    /**
     * Display the specified comment.
     */
    public function show($id)
    {
        $comment = Comment::with('task', 'user')->findOrFail($id);
        return response()->json($comment, 200);
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'comment' => 'string',
        ]);

        $comment = Comment::findOrFail($id);
        $comment->update($request->only(['comment'])); 

        return response()->json([
            'status' => 'success',
            'data' => $comment
        ], 200);
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment deleted successfully'
        ], 200);
    }
}


