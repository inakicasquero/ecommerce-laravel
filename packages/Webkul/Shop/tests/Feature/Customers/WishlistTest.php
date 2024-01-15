<?php

use Webkul\Customer\Models\Wishlist;
use Webkul\Faker\Helpers\Product as ProductFaker;
use Webkul\Product\Models\Product;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;

afterEach(function () {
    Product::query()->delete();
    Wishlist::query()->delete();
});

it('should returns the wishlist index page', function () {
    // Arrange
    $product = (new ProductFaker([
        'attributes' => [
            5  => 'new',
            6  => 'featured',
            11 => 'price',
            26 => 'guest_checkout',
        ],
        'attribute_value' => [
            'new' => [
                'boolean_value' => true,
            ],
            'featured' => [
                'boolean_value' => true,
            ],
            'price' => [
                'float_value' => fake()->randomFloat(2, 1000, 5000),
            ],
            'guest_checkout' => [
                'boolean_value' => true,
            ],
        ],
    ]))->getSimpleProductFactory()->create();

    // Act
    $customer = $this->loginAsCustomer();

    Wishlist::factory()->create([
        'channel_id'  => core()->getCurrentChannel()->id,
        'product_id'  => $product->id,
        'customer_id' => $customer->id,
    ]);

    get(route('shop.customers.account.wishlist.index'))
        ->assertOk()
        ->assertSeeText(trans('shop::app.customers.account.wishlist.page-title'));
});

it('should returns all the wishlisted items', function () {
    // Arrange
    $products = (new ProductFaker([
        'attributes' => [
            5  => 'new',
            6  => 'featured',
            11 => 'price',
            26 => 'guest_checkout',
        ],
        'attribute_value' => [
            'new' => [
                'boolean_value' => true,
            ],
            'featured' => [
                'boolean_value' => true,
            ],
            'price' => [
                'float_value' => fake()->randomFloat(2, 1000, 5000),
            ],
            'guest_checkout' => [
                'boolean_value' => true,
            ],
        ],
    ]))->getSimpleProductFactory()->count(2)->create();

    // Act
    $customer = $this->loginAsCustomer();

    $wishLists = [];

    foreach ($products as $product) {
        $wishLists[] = Wishlist::factory()->create([
            'channel_id'  => core()->getCurrentChannel()->id,
            'product_id'  => $product->id,
            'customer_id' => $customer->id,
        ]);
    }

    // Assert
    get(route('shop.api.customers.account.wishlist.index'))
        ->assertOk();

    foreach ($wishLists as $wishList) {
        $this->assertDatabaseHas('wishlist_items', [
            'id' => $wishList->id,
        ]);
    }
});

it('should add the products to the wishlist', function () {
    // Arrange
    $product = (new ProductFaker([
        'attributes' => [
            5  => 'new',
            6  => 'featured',
            11 => 'price',
            26 => 'guest_checkout',
        ],
        'attribute_value' => [
            'new' => [
                'boolean_value' => true,
            ],
            'featured' => [
                'boolean_value' => true,
            ],
            'price' => [
                'float_value' => fake()->randomFloat(2, 1000, 5000),
            ],
            'guest_checkout' => [
                'boolean_value' => true,
            ],
        ],
    ]))->getSimpleProductFactory()->create();

    // Act and Assert
    $this->loginAsCustomer();

    postJson(route('shop.api.customers.account.wishlist.store'), [
        'product_id' => $product->id,
    ])
        ->assertOk()
        ->assertJsonPath('data.message', trans('shop::app.customers.account.wishlist.success'));
});

it('should move wishlisted product to the cart', function () {
    // Arrange
    $product = (new ProductFaker([
        'attributes' => [
            5  => 'new',
            6  => 'featured',
            11 => 'price',
            26 => 'guest_checkout',
        ],
        'attribute_value' => [
            'new' => [
                'boolean_value' => true,
            ],
            'featured' => [
                'boolean_value' => true,
            ],
            'price' => [
                'float_value' => fake()->randomFloat(2, 1000, 5000),
            ],
            'guest_checkout' => [
                'boolean_value' => true,
            ],
        ],
    ]))->getSimpleProductFactory()->create();

    // Act
    $customer = $this->loginAsCustomer();

    $wishList = Wishlist::factory()->create([
        'channel_id'  => core()->getCurrentChannel()->id,
        'product_id'  => $product->id,
        'customer_id' => $customer->id,
    ]);

    // Assert
    postJson(route('shop.api.customers.account.wishlist.move_to_cart', $wishList->id))
        ->assertOk()
        ->assertJsonPath('message', trans('shop::app.customers.account.wishlist.moved-success'));
});

it('should remove all wishlisted items', function () {
    // Arrange
    $products = (new ProductFaker([
        'attributes' => [
            5  => 'new',
            6  => 'featured',
            11 => 'price',
            26 => 'guest_checkout',
        ],
        'attribute_value' => [
            'new' => [
                'boolean_value' => true,
            ],
            'featured' => [
                'boolean_value' => true,
            ],
            'price' => [
                'float_value' => fake()->randomFloat(2, 1000, 5000),
            ],
            'guest_checkout' => [
                'boolean_value' => true,
            ],
        ],
    ]))->getSimpleProductFactory()->count(2)->create();

    // Act
    $customer = $this->loginAsCustomer();

    $wishLists = [];

    foreach ($products as $product) {
        $wishLists[] = Wishlist::factory()->create([
            'channel_id'  => core()->getCurrentChannel()->id,
            'product_id'  => $product->id,
            'customer_id' => $customer->id,
        ]);
    }

    // Assert
    deleteJson(route('shop.api.customers.account.wishlist.destroy_all'))
        ->assertOk()
        ->assertJsonPath('data.message', trans('shop::app.customers.account.wishlist.removed'));
});

it('should remove specified wishlisted item', function () {
    // Arrange
    $product = (new ProductFaker([
        'attributes' => [
            5  => 'new',
            6  => 'featured',
            11 => 'price',
            26 => 'guest_checkout',
        ],
        'attribute_value' => [
            'new' => [
                'boolean_value' => true,
            ],
            'featured' => [
                'boolean_value' => true,
            ],
            'price' => [
                'float_value' => fake()->randomFloat(2, 1000, 5000),
            ],
            'guest_checkout' => [
                'boolean_value' => true,
            ],
        ],
    ]))->getSimpleProductFactory()->create();

    // Act
    $customer = $this->loginAsCustomer();

    $wishList = Wishlist::factory()->create([
        'channel_id'  => core()->getCurrentChannel()->id,
        'product_id'  => $product->id,
        'customer_id' => $customer->id,
    ]);

    // Assert
    deleteJson(route('shop.api.customers.account.wishlist.destroy', $wishList->id))
        ->assertOk()
        ->assertJsonPath('message', trans('shop::app.customers.account.wishlist.removed'));
});
