<?php

use Illuminate\Support\Facades\Route;
use Webkul\Shop\Http\Controllers\API\AddressController;
use Webkul\Shop\Http\Controllers\API\CartController;
use Webkul\Shop\Http\Controllers\API\CategoryController;
use Webkul\Shop\Http\Controllers\API\CompareController;
use Webkul\Shop\Http\Controllers\API\ProductController;
use Webkul\Shop\Http\Controllers\API\ReviewController;
use Webkul\Shop\Http\Controllers\API\WishlistController;
use Webkul\Shop\Http\Controllers\API\OnepageController;


Route::group(['middleware' => ['locale', 'theme', 'currency'], 'prefix' => 'api'], function () {
    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'index')
            ->name('shop.api.products.index');

        Route::get('products/{id}/related', 'relatedProducts')
            ->name('shop.api.products.related.index');

        Route::get('products/{id}/up-sell', 'upSellProducts')
            ->name('shop.api.products.up-sell.index');
    });

    Route::controller(ReviewController::class)->group(function () {
        Route::get('product/{id}/reviews', 'index')
            ->name('shop.api.products.reviews.index');

        Route::post('product/{id}/review', 'store')
            ->name('shop.api.products.reviews.store');
    });

    Route::controller(CategoryController::class)->prefix('categories')->group(function () {
        Route::get('', 'index')->name('shop.api.categories.index');

        Route::get('{id}/attributes', 'getAttributes')->name('shop.api.categories.attributes');

        Route::get('{id}/max-price', 'getProductMaxPrice')->name('shop.api.categories.max_price');
    });

    Route::controller(CartController::class)->prefix('checkout/cart')->group(function () {
        Route::get('', 'index')->name('shop.api.checkout.cart.index');

        Route::post('', 'store')->name('shop.api.checkout.cart.store');

        Route::put('', 'update')->name('shop.api.checkout.cart.update');

        Route::delete('', 'destroy')->name('shop.api.checkout.cart.destroy');
    });

    Route::controller(CompareController::class)->prefix('compare-items')->group(function () {
        Route::get('', 'index')->name('shop.api.compare.index');

        Route::post('', 'store')->name('shop.api.compare.store');

        Route::delete('', 'destroy')->name('shop.api.compare.destroy');

        Route::delete('all', 'destroyAll')->name('shop.api.compare.destroy_all');
    });
    
    Route::controller(OnepageController::class)->prefix('checkout/onepage')->group(function () {
        Route::get('summary', 'summary')->name('shop.checkout.onepage.summary');
    
        Route::post('addresses', 'storeAddress')->name('shop.checkout.onepage.addresses.store');
    
        Route::post('shipping-methods', 'storeShippingMethod')->name('shop.checkout.onepage.shipping_methods.store');
    
        Route::post('payment-methods', 'storePaymentMethod')->name('shop.checkout.onepage.payment_methods.store');

        Route::post('orders', 'storeOrder')->name('shop.checkout.onepage.orders.store');
    
        Route::post('check-minimum-order', 'checkMinimumOrder')->name('shop.checkout.onepage.check_minimum_order');
    });

    Route::group(['middleware' => ['customer'], 'prefix' => 'customer'], function () {
        Route::controller(AddressController::class)->prefix('addresses')->group(function () {
            Route::get('', 'index')->name('api.shop.customers.account.addresses.index');

            Route::post('', 'store')->name('api.shop.customers.account.addresses.store');
        });
        
        Route::controller(WishlistController::class)->prefix('wishlist')->group(function () {
            Route::get('', 'index')->name('shop.api.customers.account.wishlist.index');

            Route::post('', 'store')->name('shop.api.customers.account.wishlist.store');

            Route::post('{id}/move-to-cart', 'moveToCart')->name('shop.api.customers.account.wishlist.move_to_cart');

            Route::delete('all', 'destroyAll')->name('shop.api.customers.account.wishlist.destroy_all');
            
            Route::delete('{id}', 'destroy')->name('shop.api.customers.account.wishlist.destroy');
        });
    });
});
