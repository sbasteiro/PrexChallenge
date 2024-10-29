<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\FavoriteGif;
use Laravel\Passport\Passport;

class AuthControllerTest extends TestCase {

    use RefreshDatabase;
    protected $user;

    protected function setUp(): void {
        parent::setUp();
        
        $this->user = User::factory()->create();
        Passport::actingAs($this->user); 
    }

    /** @test */
    public function test_user_can_search_gifs() {

        $response = $this->getJson('/api/gifs/search?query=funny');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [],
                    ],
                ]);
    }

    /** @test */
    public function test_user_can_get_gif_by_id() {
       
        $gifId = "0r9AnKXGbeWPHdqPiE"; 

        $response = $this->getJson("/api/gifs/{$gifId}");

        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    /** @test */
    public function test_user_can_store_favorite_gif() {
        $response = $this->postJson('/api/gifs/favorites', [
            'gif_id' => '123',
            'alias' => 'Mi GIF Favorito',
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'GIF agregado a favoritos.']);
        
        $this->assertDatabaseHas('favorite_gifs', [
            'user_id' => $this->user->id,
            'gif_id' => '123',
            'alias' => 'Mi GIF Favorito',
        ]);
    }

    /** @test */
    public function test_user_cannot_store_duplicate_favorite_gif() {
        FavoriteGif::create([
            'user_id' => $this->user->id,
            'gif_id' => '123',
            'alias' => 'Mi GIF Favorito',
        ]);

        $response = $this->postJson('/api/gifs/favorites', [
            'gif_id' => '123',
            'alias' => 'Otro Alias',
        ]);

        $response->assertStatus(409)
                 ->assertJson(['message' => 'Este GIF ya existe en tus favoritos.']);
    }
}
