<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Services\PostsService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function index(int $post)
    {
        $comments = Comment::query()
            ->where('post_id', $post)
            ->latest()
            ->get();

        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request,PostsService $postsService): JsonResponse {
        $postExists = $postsService->exists(
            $request->integer('post_id')
        );

       if (! $postExists) {
        return response()->json([
            'message' => 'Post not found.',
        ], 404);
    }

        $comment = Comment::create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Comment created successfully',
            'data' => new CommentResource($comment),
        ], 201);
    }

    public function show(Comment $comment): CommentResource
    {
        return new CommentResource($comment);
    }

    public function update(
        UpdateCommentRequest $request,
        Comment $comment
    ): JsonResponse {
        $comment->update($request->validated());

        return response()->json([
            'message' => 'Comment updated successfully',
            'data' => new CommentResource($comment->fresh()),
        ]);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }

    public function showPostWithComments(int $post,PostsService $postsService): JsonResponse {
        
    $postData = $postsService->find($post);

    if ($postData === null) {
        return response()->json([
            'message' => 'Post not found.',
        ], 404);
    }

    $comments = Comment::query()
        ->where('post_id', $post)
        ->latest()
        ->get();

    return response()->json([
        'message' => 'Post fetched successfully.',
        'data' => [
            'post' => $postData,
            'comments' => CommentResource::collection($comments),
        ],
    ]);
}
}
