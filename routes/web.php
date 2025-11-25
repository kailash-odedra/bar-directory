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
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\BookingController;

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
    Route::post('bar/{id}/toggle-status', [BarController::class, 'toggleStatus'])->name('bar.toggleStatus');
    Route::get('bar/{id}/approve', [BarController::class, 'approve'])->name('bar.approve');
    Route::get('get-states/{country}', [LocationController::class, 'states'])
        ->name('location.states');
    Route::resource('claims', ClaimController::class)->only(['index','show','destroy']);
    Route::post('claims/{id}/approve', [ClaimController::class,'approve'])->name('claims.approve');
    Route::post('claims/{id}/reject', [ClaimController::class,'reject'])->name('claims.reject');
    Route::resource('bar-tags', BarTagController::class);
    Route::post('bar-tags/{id}/toggle-status', [BarTagController::class, 'toggleStatus'])->name('bar-tags.toggleStatus');
    Route::resource('events', EventController::class);
    Route::post('events/{id}/toggle-status', [EventController::class, 'toggleStatus'])->name('events.toggleStatus');
    Route::resource('bar-menu-categories', BarMenuCategoryController::class);
    Route::post('bar-menu-categories/{id}/toggle-status', [BarMenuCategoryController::class, 'toggleStatus'])->name('bar-menu-categories.toggleStatus');
    Route::resource('bar-menu-items', BarMenuItemController::class);
    Route::post('bar-menu-items/{id}/toggle-status', [BarMenuItemController::class, 'toggleStatus'])->name('bar-menu-categories.toggleStatus');
    Route::resource('bar-images', BarImageController::class);
    Route::resource('bar-reviews', BarReviewController::class);
    Route::post('bar-reviews/{id}/approve', [BarReviewController::class, 'approve']);
    Route::post('bar-reviews/{id}/hide', [BarReviewController::class, 'hide']);
    Route::resource('bookings', BookingController::class);
    Route::post('bookings/{id}/toggle-status', [BookingController::class,'toggleStatus'])
    ->name('admin.bookings.toggleStatus');
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
