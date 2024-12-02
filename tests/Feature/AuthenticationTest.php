<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_authentication_can_retriev_only_the_first_user(): void
    {
        $user1 = User::factory()->create([
            'email' => $email1 = 'user1@email.com'
        ]);


        $user2 = User::factory()->create([
            'email' => $email2 = 'user2@email.com'
        ]);

        $token1 = $user1->createToken('token-name_1', ['*'])->plainTextToken;
        
        $token2 = $user2->createToken('token-name_1', ['*'])->plainTextToken;

        $response1 = $this->withHeaders([
            'Authorization' => 'Bearer '.$token1
        ])->getJson('api/user');
        

        $response1->assertStatus(200);

        $response1
             ->assertJson(
                 static fn (AssertableJson $json) => $json
                     ->where('email', static fn (string $email) => str($email)->is($email1))
                     ->etc()
             )
        ;

        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer '.$token2
        ])->getJson('api/user');
        

        $response2->assertStatus(200);

        $response2
             ->assertJson(
                 static fn (AssertableJson $json) => $json
                     ->where('email', static fn (string $email) => str($email)->is($email2))
                     ->etc()
             )
        ;

    }
}
