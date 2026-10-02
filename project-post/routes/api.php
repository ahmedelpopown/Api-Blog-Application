<?php

use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
 
Route::get('/health', function () {
    return response()->json([
        'service' => 'posts-service',
        'status' => 'ok',
    ]);
});

Route::apiResource('posts', PostController::class);

Route::middleware('internal.service')->group(function () {
    Route::get('/internal/posts/{post}', [
        PostController::class,
        'show',
    ]);
});