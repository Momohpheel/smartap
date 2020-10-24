<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Model\User;
class ExampleTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function user(){
        $user = User::create([
                 'phone_number'=> 'ffff',
                 'geolocation' => '3eeee',
                 'name' =>'rfff',
                 'email' => 'momohmayowa@gmail.com'
        ]);

        return $user;
    }

    public function testAddPlateNumber(){

        $this->withoutExceptionHandling();

        $data = [
            'plate_number' => '123444ed',
        ];

        $this->actingAs($this->user());
        $this->post("api/v1/user/plate/".$this->user()->id."/add",$data)
                ->assertStatus(201);

    }
}
