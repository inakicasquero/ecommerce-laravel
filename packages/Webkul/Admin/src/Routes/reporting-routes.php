<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Reporting\CustomerController;
use Webkul\Admin\Http\Controllers\Reporting\ProductController;
use Webkul\Admin\Http\Controllers\Reporting\SaleController;

/**
 * Reporting routes.
 */
Route::group(['middleware' => ['admin'], 'prefix' => config('app.admin_url')], function () {
    Route::prefix('reporting')->group(function () {
        /**
         * CUstomer routes.
         */
        Route::controller(CustomerController::class)->prefix('customers')->group(function () {
            Route::get('', 'index')->name('admin.reporting.customers.index');
        });

        /**
         * Product routes.
         */
        Route::controller(ProductController::class)->prefix('products')->group(function () {
            Route::get('', 'index')->name('admin.reporting.products.index');
        });

        /**
         * Sale routes.
         */
        Route::controller(SaleController::class)->prefix('sales')->group(function () {
            Route::get('', 'index')->name('admin.reporting.sales.index');
        });
    });
});