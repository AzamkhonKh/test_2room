<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\Request;
use App\Models\User;
use PHPUnit\Runner\Exception;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_login()
    {
        $user = User::first();
        if(empty($user)){
            throw new Exception('do not have user in database');
        }
        $resp = $this->post('/api/login',['email' => $user->email, 'password' => 'password']);
        $resp->assertStatus(200);
    }
    public function test_get_list_without_auth()
    {
        $resp = $this->get('/api/users');
        $resp->assertStatus(200);
        $resp->assertExactJson(["status" => "Authorization Token not found"]);
    }
    public function test_get_list_with_auth()
    {
        $user = User::first();
        if(empty($user)){
            throw new Exception('do not have user in database');
        }
        $resp_login = $this->post('/api/login',['email' => $user->email, 'password' => 'password']);
        $resp = $this->withHeader('Authorization','Bearer ' . json_decode($resp_login->content())->token)->get('/api/users');
        $resp->assertStatus(200)
            ->assertJson(User::all()->toArray());
    }
}
