<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentRouteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка метода get роута '/api/films/{id}/comments'
     *
     * @return void
     */
    public function test_get_comments()
    {
        // Проверка, если пользователь неаутентифицирован
        $response = $this->getJson("/api/films/{id}/comments");

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => []
            ]);
    }

    /**
     * Проверка метода post роута '/api/films/{id}/comments'
     *
     * @return void
     */
    public function test_post_comments()
    {
        // Проверка, если пользователь неаутентифицирован
        $response = $this->postJson("/api/films/{id}/comments");

        $response->assertUnauthorized();
    }

    /**
     * Проверка метода patch роута '/api/comments/{comments}'
     *
     * @return void
     */
    public function test_patch_comments()
    {
        $commentId = 1;

        // Проверка, если пользователь неаутентифицирован
        $response = $this->patchJson("/api/comments/{$commentId}");

        $response->assertUnauthorized();
    }

    /**
     * Проверка метода delete роута '/api/comments/{comments}'
     *
     * @return void
     */
    public function test_delete_comments()
    {
        $commentId = 1;

        // Проверка, если пользователь неаутентифицирован
        $response = $this->deleteJson("/api/comments/{$commentId}");

        $response->assertUnauthorized();
    }
}
