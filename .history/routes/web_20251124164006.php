<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BarController;
use App\Http\Controllers\Admin\BarTagController;
use App\Http\Controllers\Admin\BarMenuCategoryController;
use App\Http\Controllers\Admin\BarMenuItemController;
use App\Http\Controllers\Admin\BarImageController;
use App\Http\Controllers\Admin\BarReviewController;
use App\Http\Controllers\Admin\ClaimController;
use App\Http\Controllers\Admin\EventController;

/**
 * =======================
 *          Redirect
 * =======================
 */
Route::get('/', function () {
    return redirect(getRouterValue() . 'dashboard/analytics');
});

/**
 * =======================
 *          Dashboard
 * =======================
 */
Route::prefix('admin')->as('admin.')->group(function () {

    Route::resource('bar', BarController::class);
    
    // Ajax states by country
    Route::get('get-states/{country_id}', [BarController::class, 'getStates'])
        ->name('bar.getStates');

    Route::resource('bar-tags', BarTagController::class);
    Route::resource('bar-menu-categories', BarMenuCategoryController::class);
    Route::resource('bar-menu-items', BarMenuItemController::class);
    Route::resource('bar-images', BarImageController::class);
    Route::resource('bar-reviews', BarReviewController::class);
});



Route::prefix('dashboard')->group(function () {
    Route::get('/analytics', function () {
        return view('admin/dashboard/analytics',
            [
                'catName' => 'dashboard',
                'title' => 'CORK Admin - Multipurpose Bootstrap Dashboard Template',
                "breadcrumbs" => ["Dashboard", "Analytics"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('analytics');
    
    Route::get('/sales', function () {
        return view('admin/dashboard/sales',
            [
                'catName' => 'dashboard',
                'title' => 'Sales Admin',
                "breadcrumbs" => ["Dashboard", "Sales"],
                'scrollspy' => 0,
                'simplePage' => 0,
            ]
        );
    })->name('sales');
});
