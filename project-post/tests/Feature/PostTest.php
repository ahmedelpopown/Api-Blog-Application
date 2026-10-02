<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_post(): void
    {
        $response = $this->postJson('/api/posts', [
            'title' => 'Test Post',
            'content' => 'Test Content',
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.title', 'Test Post');

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'content' => 'Test Content',
        ]);
    }

    public function test_can_get_post(): void
    {
        $post = Post::create([
            'title' => 'Test Post',
            'content' => 'Test Content',
        ]);

        $response = $this->getJson("/api/posts/{$post->id}");

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.id', $post->id);
    }

    public function test_returns_404_when_post_does_not_exist(): void
    {
        $response = $this->getJson('/api/posts/999999');

        $response->assertStatus(404);
    }

 
}