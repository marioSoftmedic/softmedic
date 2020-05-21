<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp():void
    {
        parent::setUp();
        // $this->seed();
    }
    /**
     * A basic test example.
     *
     * @return void
     */

    /** @test */
    public function user_can_register()
    {
        $user= [
            'name'=>'mario inostroza',
            'username'=>'mario',
            'email'=>'mario.inostroza.m@gmail.com',
            'password'=>'password',
            'password_confirmation'=>'password'
        ];
        $response = $this->post('api/register', $user);

        $response->assertStatus(200);

        //remove pass and confirm pass
        array_splice($user, 3);


        $this->assertDatabaseHas('users', $user);
    }
}
