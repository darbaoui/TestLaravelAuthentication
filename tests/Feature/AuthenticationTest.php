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
        
        $token2 = $user2->createToken('token-name_2', ['*'])->plainTextToken;

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

    /**
     * A basic test example.
     */
    public function test_logout_a_user_but_still_retrieve_data(): void
    {

        $user = User::factory()->create([
           'email' => $email = 'user1@email.com'
       ]);



        $token = $user->createToken('token-name_1', ['*'])->plainTextToken;
        

        $response1 = $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->getJson('api/user');
        

        $response1->assertStatus(200);

        $response1
             ->assertJson(
                 static fn (AssertableJson $json) => $json
                     ->where('email', static fn (string $email) => str($email)->is($email))
                     ->etc()
             )
        ;

        $response2 = $this->withHeaders([
           'Authorization' => 'Bearer '.$token
        ])->getJson('api/logout');

        $response2->assertStatus(200);

        $this->assertEquals(0, $user->tokens()->count()); // we make sure the token removed from DB

        // But if i try to get the user using the token instead of get response status = 401, i get 200


        $response3= $this->withHeaders([
          'Authorization' => 'Bearer '.$token
       ])->getJson('api/logout');

        $response3->assertStatus(401);// return 200


    }
}
