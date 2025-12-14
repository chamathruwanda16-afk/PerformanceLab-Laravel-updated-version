<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_products_list_is_public()
{
    $this->getJson('/api/products')->assertOk();
}

public function test_order_creation_requires_auth()
{
    $this->postJson('/api/orders', ['items'=>[]])->assertUnauthorized();
}

public function test_order_creation_works_with_token()
{
    $user = \App\Models\User::factory()->create();
    $token = $user->createToken('t')->plainTextToken;

    $cat = \App\Models\Category::factory()->create();
    $product = \App\Models\Product::factory()->create(['category_id'=>$cat->id,'price'=>100]);

    $this->withToken($token)
         ->postJson('/api/orders', ['items'=>[['product_id'=>$product->id,'qty'=>2]]])
         ->assertCreated()
         ->assertJsonPath('total', 200.0);
}

}
