<?php

use Pest\Expectation;
use Webkul\Category\Models\Category as CategoryModel;
use Webkul\Faker\Helpers\Category as CategoryFaker;
use Webkul\Faker\Helpers\Product as ProductFaker;
use Webkul\Product\Models\Product as ProductModel;

use function Pest\Laravel\getJson;

afterEach(function () {
    /**
     * Cleaning up rows which are created.
     */
    ProductModel::query()->delete();

    /**
     * Clean categories, excluding ID 1 (i.e., the root category). A fresh instance will always have ID 1.
     */
    CategoryModel::query()
        ->whereNot('id', 1)
        ->delete();
});

it('returns a new products listing', function () {
    // Arrange
    $newProductOptions = [
        'attributes' => [
            5 => 'new',
        ],

        'attribute_value' => [
            'new' => [
                'boolean_value' => true,
            ],
        ],
    ];

    (new ProductFaker($newProductOptions))
        ->getSimpleProductFactory()
        ->create();

    // Act
    $response = getJson(route('shop.api.products.index', ['new' => 1]))
        ->assertOk()
        ->collect();

    // Assert
    expect($response['data'])->each(function (Expectation $product) {
        return $product->is_new->toBeTrue();
    });
});

it('returns a featured products listing', function () {
    // Arrange
    $featuredProductOptions = [
        'attributes' => [
            6 => 'featured',
        ],

        'attribute_value' => [
            'featured' => [
                'boolean_value' => true,
            ],
        ],
    ];

    (new ProductFaker($featuredProductOptions))
        ->getSimpleProductFactory()
        ->create();

    // Act
    $response = getJson(route('shop.api.products.index', ['featured' => 1]))
        ->assertOk()
        ->collect();

    // Assert
    expect($response['data'])->each(function (Expectation $product) {
        return $product->is_featured->toBeTrue();
    });
});

it('returns all products listing', function () {
    // Arrange
    $product = (new ProductFaker())
        ->getSimpleProductFactory()
        ->create();

    // Act & Assert
    getJson(route('shop.api.products.index'))
        ->assertOk()
        ->assertJsonIsArray('data')
        ->assertJsonFragment([
            'id' => $product->id,
        ]);
});

it('returns category products sorted by name descending', function () {
    // Arrange
    $specifiedCategory = (new CategoryFaker())->factory()->create();

    $products = (new ProductFaker())
        ->getSimpleProductFactory()
        ->hasAttached($specifiedCategory)
        ->count(6)
        ->create();

    $expectedNamesInDescOrder = $products
        ->map(fn ($product) => $product->name)
        ->sortDesc()
        ->toArray();

    // Act & Assert
    getJson(route('shop.api.products.index', ['category_id' => $specifiedCategory->id, 'sort' => 'name-desc']))
        ->assertOk()
        ->assertSeeTextInOrder($expectedNamesInDescOrder);
});

it('returns category products sorted by name ascending', function () {
    // Arrange
    $specifiedCategory = (new CategoryFaker())->factory()->create();

    $products = (new ProductFaker())
        ->getSimpleProductFactory()
        ->hasAttached($specifiedCategory)
        ->count(6)
        ->create();

    $expectedNamesInAscOrder = $products
        ->map(fn ($product) => $product->name)
        ->sort()
        ->toArray();

    // Act & Assert
    getJson(route('shop.api.products.index', ['category_id' => $specifiedCategory->id, 'sort' => 'name-asc']))
        ->assertOk()
        ->assertSeeTextInOrder($expectedNamesInAscOrder);
});

it('returns category products sorted by created_at descending', function () {
    // Arrange
    $specifiedCategory = (new CategoryFaker())->factory()->create();

    $simpleProductFactory = (new ProductFaker())
        ->getSimpleProductFactory()
        ->hasAttached($specifiedCategory);

    $firstProduct = $simpleProductFactory->create([
        'created_at' => now()->subYear(),
    ]);

    $secondProduct = $simpleProductFactory->create([
        'created_at' => now()->subMonth(),
    ]);

    $lastProduct = $simpleProductFactory->create([
        'created_at' => now(),
    ]);

    // Act & Assert
    getJson(route('shop.api.products.index', ['category_id' => $specifiedCategory->id, 'sort' => 'created_at-desc']))
        ->assertOk()
        ->assertSeeTextInOrder([
            $lastProduct->id,
            $secondProduct->id,
            $firstProduct->id,
        ]);
});

it('returns category products sorted by created_at ascending', function () {
    // Arrange
    $specifiedCategory = (new CategoryFaker())->factory()->create();

    $simpleProductFactory = (new ProductFaker())
        ->getSimpleProductFactory()
        ->hasAttached($specifiedCategory);

    $firstProduct = $simpleProductFactory->create([
        'created_at' => now()->subYear(),
    ]);

    $secondProduct = $simpleProductFactory->create([
        'created_at' => now()->subMonth(),
    ]);

    $lastProduct = $simpleProductFactory->create([
        'created_at' => now(),
    ]);

    // Act & Assert
    getJson(route('shop.api.products.index', ['category_id' => $specifiedCategory->id, 'sort' => 'created_at-asc']))
        ->assertOk()
        ->assertSeeTextInOrder([
            $firstProduct->id,
            $secondProduct->id,
            $lastProduct->id,
        ]);
});

it('returns category products sorted by price descending', function () {
    // Arrange
    $specifiedCategory = (new CategoryFaker())->factory()->create();

    $products = (new ProductFaker())
        ->getSimpleProductFactory()
        ->hasAttached($specifiedCategory)
        ->count(6)
        ->create();

    $expectedPricesInDescOrder = $products
        ->map(fn ($product) => $product->getTypeInstance()->getMinimalPrice())
        ->sortDesc()
        ->map(fn ($price) =>  core()->formatPrice($price))
        ->toArray();

    // Act & Assert
    getJson(route('shop.api.products.index', ['category_id' => $specifiedCategory->id, 'sort' => 'price-desc']))
        ->assertOk()
        ->assertSeeTextInOrder($expectedPricesInDescOrder);
});

it('returns category products sorted by price ascending', function () {
    // Arrange
    $specifiedCategory = (new CategoryFaker())->factory()->create();

    $products = (new ProductFaker())
        ->getSimpleProductFactory()
        ->hasAttached($specifiedCategory)
        ->count(6)
        ->create();

    $expectedPricesInAscOrder = $products
        ->map(fn ($product) => $product->getTypeInstance()->getMinimalPrice())
        ->sort()
        ->map(fn ($price) =>  core()->formatPrice($price))
        ->toArray();

    // Act & Assert
    getJson(route('shop.api.products.index', ['category_id' => $specifiedCategory->id, 'sort' => 'price-asc']))
        ->assertOk()
        ->assertSeeTextInOrder($expectedPricesInAscOrder);
});
