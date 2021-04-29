<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Database\Factories\ProductFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ProductTest extends TestCase
{
    //  use WithFaker, DatabaseMigrations;

    public function test_a_product_create_and_requires_a_title()
    {
        $user = User::factory()->create();
        Auth::loginUsingId($user->id);
        $this->actingAs($user);

        $this->post(route('products.store'), ['price' => 5, 'user_id' => $user->id])
            ->assertStatus(422);
    }

    public function test_a_product_create_and_requires_a_price()
    {
        $user = User::factory()->create();
        Auth::loginUsingId($user->id);
        $this->actingAs($user);

        $this->post(route('products.store'), ['title' => 'Some title', 'user_id' => $user->id])
            ->assertStatus(422);
    }

    public function test_a_product_create_success()
    {
        $user = User::factory()->create();
        Auth::loginUsingId($user->id);
        $this->actingAs($user);

        $product_factory = ProductFactory::new();
        $product = $product_factory->definition();
        $product['user_id'] = $user->id;

        $response=  $this->post(route('products.store'), $product);

        $response->assertStatus(200);

        $response = json_decode($response->content(), true);
        unset($response['id']);

        $this->assertEquals($product, $response);
    }

    public function test_authenticated_user_should_not_update_other_user_product()
    {
        $user = User::factory()->create();

        $product = Product::factory()->create(['user_id' => User::factory()->create()->id]);

        Auth::loginUsingId($user->id);

        $response = $this->actingAs($user)->put(route('products.update',  ['product'=> $product]), ['title' => 'aqwe']);

        $response->assertStatus(403);
    }

    public function test_authenticated_user_should_not_delete_other_user_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['user_id' => User::factory()->create()->id]);
        Auth::loginUsingId($user->id);

        $response = $this->actingAs($user)->delete(route('products.destroy', ['product' => $product]), ['title' => 'aqwe']);

        $response->assertStatus(403);
    }

    public function test_products_list_from_low_to_high()
    {
        $this->testProductsListBy('lprice');
    }

    public function test_products_list_from_high_to_low()
    {
        $this->testProductsListBy ('hprice');
    }

    private function testProductsListBy($sortBy ='')
    {
        /** @var  \Illuminate\Database\Eloquent\Model $products */
        $products = Product::getAndSort($sortBy);
        $productsJson = $products->toJson();
        $response = $this->get(route('products.index', ['sort' => $sortBy]));

        $response->assertStatus(200);

        $this->assertEquals($productsJson, $response->getContent());
    }
}
