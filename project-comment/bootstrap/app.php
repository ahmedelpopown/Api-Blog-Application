<?php

use App\Exceptions\PostsServiceFailedException;
use App\Exceptions\PostsServiceUnavailableException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (
        PostsServiceUnavailableException $exception,
        Request $request
    ) {
        return response()->json([
            'message' => $exception->getMessage(),
        ], 503);
    });

    $exceptions->render(function (
        PostsServiceFailedException $exception,
        Request $request
    ) {
        return response()->json([
            'message' => $exception->getMessage(),
        ], 502);
    });
})->create();
