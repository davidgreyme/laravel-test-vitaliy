<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_user_register_name_must_be_unique_cannot_use_already_used_name_during_registration()
    {
        $user = User::factory()->create();

        $response = $this->post(route('users.store'), ['name' => $user->name]);

        $response->assertStatus(422);
    }

    public function test_authenticated_user_cannot_update_details_of_other_users()
    {
        $user = User::factory()->create();
        $user_another = User::factory()->create();
        Auth::loginUsingId($user->id);

        $response = $this->actingAs($user)->put(route('users.update', ['user' => $user_another]), ['name' => 'aqwe']);

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_update_own_details()
    {
        $user = User::factory()->create();
        Auth::loginUsingId($user->id);
        $user_factory = UserFactory::new();
        $data = $user_factory->definition();

        $this->actingAs($user)->put(route('users.update', ['user' => $user]), ['name' => $data['name']]);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $data['name']]);
    }

    public function test_user_create_name_must_saved_lower_case()
    {
        $user_factory = UserFactory::new();
        $data = $user_factory->definition();
        $response = $this->post(route('users.store'), $data);

        $user_decode = json_decode($response->getContent());

        $this->assertSame($user_decode->name, strtolower($data['name']));
    }

    public function test_user_update_name_must_saved_lower_case()
    {
        $user = User::factory()->create();
        Auth::loginUsingId($user->id);
        $user_factory = UserFactory::new();
        $data = $user_factory->definition();

        $response = $this->put(route('users.update', ['user' => $user]), ['name' => $data['name']]);

        $user_decode = json_decode($response->getContent());
        $this->assertSame($user_decode->name, strtolower($data['name']));
    }
}
