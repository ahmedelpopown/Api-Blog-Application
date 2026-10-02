<?php

namespace App\Services;

use App\Exceptions\PostsServiceFailedException;
use App\Exceptions\PostsServiceUnavailableException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PostsService
{
    public function exists(int $postId): bool
    {
        try {

            $response = Http::acceptJson() //   بي الشكل ده  Response  انا متوقع 
                ->withHeaders([
                    'X-Service-Key' => config('services.posts.secret'),
                ])
                ->timeout(3)
                ->retry(
                    3,
                    200,
                    function (\Throwable $exception): bool {
                        return $exception instanceof ConnectionException;
                    },
                    throw: false
                )
                ->get(
                    rtrim(config('services.posts.url'), '/')
                        . "/api/internal/posts/{$postId}"
                );


            if ($response->status() === 404) {
                return false;
            }

            if ($response->failed()) {
                throw new PostsServiceFailedException();
            }

            return true;
        } catch (ConnectionException $exception) {
            throw new PostsServiceUnavailableException(
                'Posts service is unavailable.'
            );
        }
    }
    public function find(int $postId): ?array
    {
        try {
            $response = Http::acceptJson()
                ->withHeader(
                    'X-Service-Key',
                    config('services.posts.secret')
                )
                ->timeout(3)
                ->retry(
                    3,
                    200,
                    function (\Throwable $exception): bool {
                        return $exception instanceof ConnectionException;
                    },
                    throw: false
                )
                ->get(
                    rtrim(config('services.posts.url'), '/')
                        . "/api/internal/posts/{$postId}"
                );

            if ($response->status() === 404) {
                return null;
            }

            if ($response->failed()) {
                throw new PostsServiceFailedException();
            }

            return $response->json('data');
        } catch (ConnectionException $exception) {
            throw new PostsServiceUnavailableException(
                'Posts service is unavailable.'
            );
        }
    }
}
