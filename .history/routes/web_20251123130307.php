<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BarController;

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
Route::prefix('admin')->group(function () {
    Route::resource('bar', Admin\BarController::class);
    Route::resource('bar-tags', Admin\BarTagController::class);
    Route::resource('bar-menu-categories', Admin\BarMenuCategoryController::class);
    Route::resource('bar-menu-items', Admin\BarMenuItemController::class);
    Route::resource('bar-images', Admin\BarImageController::class);
    Route::resource('bar-reviews', Admin\BarReviewController::class);
    Route::resource('claims', Admin\ClaimController::class);
    Route::resource('events', Admin\EventController::class);
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
