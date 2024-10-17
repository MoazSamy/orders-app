<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Testing registering a user.
     */
    public function testRegisterUserEndpoint(): void
    {
        $userData = [
            "name" => "testUser",
            "email" => "test@email.com",
            "password" => "testtest123"
        ];
        $response = $this->post('/api/users/register', $userData);

        $response->assertStatus(201)->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "name",
                "email",
                "updated_at",
                "created_at",
                "id"
            ]
        ]);
    }

    public function testSuccessLoginUserEndpoint():void
    {
        $user = User::factory()->create([
            "email" => "test@email.com",
            "password" => bcrypt("testtest123")
        ]);
        $userLoginData = [
            "email" => "test@email.com",
            "password" => "testtest123"
        ];
        $response = $this->post('/api/users/login', $userLoginData);

        $response->assertStatus(200);
    }
    
    public function testFailureLoginUserEndpoint():void
    {
        $user = User::factory()->create([
            "email" => "test@email.com",
            "password" => bcrypt("testtest123")
        ]);
        $userLoginData = [
            "email" => "test@email.com",
            "password" => "testtest"
        ];
        $response = $this->post('/api/users/login', $userLoginData);

        $response->assertStatus(400);
    }

    public function testSuccessLogoutUserEndpoint():void
    {
        $user = User::factory()->create([
            "email" => "test@email.com",
            "password" => bcrypt("testtest123")
        ]);
        Sanctum::actingAs($user);
        $response = $this->get("/api/users/logout");


        $response->assertStatus(200);
    }
    public function testFailureLogoutUserEndpoint():void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . "fakeToken",
            'Accept' => 'application/json'
        ])->get("/api/users/logout");


        $response->assertStatus(401);
    }
}
