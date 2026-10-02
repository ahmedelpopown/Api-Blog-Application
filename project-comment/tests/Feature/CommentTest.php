<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_comment_when_post_exists(): void
{
    config([
        'services.posts.url' => 'http://posts-service.test',
        'services.posts.secret' => 'test-secret',
    ]);

    Http::fake([
        'http://posts-service.test/api/internal/posts/1' => Http::response([
            'data' => [
                'id' => 1,
                'title' => 'Test Post',
                'content' => 'Test Content',
            ],
        ], 200),
    ]);

    $response = $this->postJson('/api/comments', [
        'post_id' => 1,
        'body' => 'Nice post',
    ]);

    $response
        ->assertStatus(201)
        ->assertJsonPath('data.post_id', 1)
        ->assertJsonPath('data.body', 'Nice post');

    $this->assertDatabaseHas('comments', [
        'post_id' => 1,
        'body' => 'Nice post',
    ]);

    Http::assertSent(function ($request) {
        return $request->url()
            === 'http://posts-service.test/api/internal/posts/1'
            && $request->header('X-Service-Key')[0]
            === 'test-secret';
    });
}


public function test_returns_bad_gateway_when_posts_service_fails(): void
{
    config([
        'services.posts.url' => 'http://posts-service.test',
    ]);

    Http::fake([
        'http://posts-service.test/api/internal/posts/1' => Http::response(
            [],
            500
        ),
    ]);

    $response = $this->postJson('/api/comments', [
        'post_id' => 1,
        'body' => 'Testing upstream failure',
    ]);

    $response
        ->assertStatus(502)
        ->assertJson([
            'message' => 'Posts service returned an error.',
        ]);

    $this->assertDatabaseMissing('comments', [
        'post_id' => 1,
        'body' => 'Testing upstream failure',
    ]);
}
public function test_retries_when_posts_service_has_connection_failure(): void
{
    config([
        'services.posts.url' => 'http://posts-service.test',
        'services.posts.secret' => 'test-secret',
    ]);

    Http::fake([
        'http://posts-service.test/api/internal/posts/1' => Http::sequence()
            ->pushFailedConnection('Connection refused')
            ->pushFailedConnection('Connection refused')
            ->push([
                'data' => [
                    'id' => 1,
                    'title' => 'Test Post',
                    'content' => 'Test Content',
                ],
            ], 200),
    ]);

    $response = $this->postJson('/api/comments', [
        'post_id' => 1,
        'body' => 'Comment after retry',
    ]);

    $response
        ->assertStatus(201)
        ->assertJsonPath('data.post_id', 1)
        ->assertJsonPath('data.body', 'Comment after retry');

    $this->assertDatabaseHas('comments', [
        'post_id' => 1,
        'body' => 'Comment after retry',
    ]);

    Http::assertSentCount(3);
}
}