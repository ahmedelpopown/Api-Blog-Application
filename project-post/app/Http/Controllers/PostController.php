<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function index()
    {
        return PostResource::collection(
            Post::latest()->get()
        );
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = Post::create($request->validated());

        return response()->json([
            'message' => 'Post created successfully',
            'data' => new PostResource($post),
        ], 201);
    }

    public function show(Post $post): PostResource
    {
        return new PostResource($post);
    }

    public function update(
        StorePostRequest $request,
        Post $post
    ): JsonResponse {
        $post->update($request->validated());

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => new PostResource($post->fresh()),
        ]);
    }

    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }
}