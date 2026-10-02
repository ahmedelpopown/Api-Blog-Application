<?php

use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::get('/health', function () {
    return response()->json([
        'service' => 'comments-service',
        'status' => 'ok',
    ]);
});

Route::get('/posts/{post}/comments', [
    CommentController::class,
    'index',
]);
Route::get('/posts/{post}/with-comments', [
    CommentController::class,
    'showPostWithComments',
]);

Route::apiResource('comments', CommentController::class)
    ->except(['index']);