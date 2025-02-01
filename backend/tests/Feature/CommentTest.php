<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function owner_can_delete_comment()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        $comment = $recipe->comments()->create([
            'content' => 'This is a test comment',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/recipes/{$recipe->id}/comments/{$comment->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    #[Test]
    public function test_non_owner_cannot_delete_comment()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()->create();

        $comment = $recipe->comments()->create([
            'content' => 'This is a test comment',
            'user_id' => $owner->id,
        ]);

        $response = $this->actingAs($otherUser)->deleteJson("/api/recipes/{$recipe->id}/comments/{$comment->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);
    }

    #[Test]
    public function index_returns_paginated_comments()
    {
        $recipe = Recipe::factory()->create();
        $user = User::factory()->create();
    
        for ($i = 1; $i <= 15; $i++) {
            $recipe->comments()->create([
                'content' => "Test comment {$i}",
                'user_id' => $user->id,
            ]);
        }
    
        $response = $this->getJson("/api/recipes/{$recipe->id}/comments");
    
        $response->assertStatus(200);
    
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'content',
                    'user' => [
                        'id',
                        'name',
                    ],
                ],
            ],
            'current_page',
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ]);
    }
    
}
