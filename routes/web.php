<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\BarController;
use App\Http\Controllers\Admin\BarTagController;
use App\Http\Controllers\Admin\BarMenuCategoryController;
use App\Http\Controllers\Admin\BarMenuItemController;
use App\Http\Controllers\Admin\BarImageController;
use App\Http\Controllers\Admin\BarReviewController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ClaimController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ProfileController;

/**
 * =======================
 *          Frontend Vue.js App
 * =======================
 */
Route::get('/', function () {
    return view('frontend.app');
})->name('frontend.home');

/**
 * =======================
 *          Authentication (Public)
 * =======================
 */
Route::get('admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login')->middleware('guest');
Route::post('admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('admin/logout', [AuthController::class, 'logout'])->name('admin.logout')->middleware('auth');

/**
 * =======================
 *          Dashboard (Protected)
 * =======================
 */
Route::prefix('admin')->as('admin.')->middleware('auth')->group(function () {
    
    // Profile Routes
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    // Location helper routes (must come before resource routes)
    // Using countryId/stateId/cityId instead of country/state/city to avoid route model binding conflict
    Route::get('get-states/{countryId}', [LocationController::class, 'states'])
        ->name('location.states');
    Route::get('get-cities/{stateId}', [LocationController::class, 'cities'])
        ->name('location.cities');
    Route::get('get-regions/{cityId}', [LocationController::class, 'regions'])
        ->name('location.regions');

    Route::resource('bar', BarController::class);
    Route::post('bar/{bar}/toggle-status', [BarController::class, 'toggleStatus'])->name('bar.toggleStatus');
    Route::post('bar/{bar}/toggle-featured', [BarController::class, 'toggleFeatured'])->name('bar.toggleFeatured');
    Route::get('bar/{bar}/approve', [BarController::class, 'approve'])->name('bar.approve');
    Route::get('bars/pending-approval', [BarController::class, 'pendingApproval'])->name('bar.pendingApproval');
    Route::post('bars/bulk-approve', [BarController::class, 'bulkApprove'])->name('bar.bulkApprove');
    Route::resource('claims', ClaimController::class)->only(['index','show','destroy']);
    Route::post('claims/{claim}/approve', [ClaimController::class,'approve'])->name('claims.approve');
    Route::post('claims/{claim}/reject', [ClaimController::class,'reject'])->name('claims.reject');
    Route::post('claims/{claim}/request-more-info', [ClaimController::class,'requestMoreInfo'])->name('claims.requestMoreInfo');
    Route::post('claims/{claim}/add-notes', [ClaimController::class,'addNotes'])->name('claims.addNotes');
    Route::post('claims/{claim}/attach-documents', [ClaimController::class,'attachDocuments'])->name('claims.attachDocuments');
    Route::resource('bar-tags', BarTagController::class);
    Route::post('bar-tags/{barTag}/toggle-status', [BarTagController::class, 'toggleStatus'])->name('bar-tags.toggleStatus');
    Route::resource('events', EventController::class);
    Route::post('events/{event}/toggle-status', [EventController::class, 'toggleStatus'])->name('events.toggleStatus');
    Route::resource('bar-menu-categories', BarMenuCategoryController::class);
    Route::post('bar-menu-categories/{barMenuCategory}/toggle-status', [BarMenuCategoryController::class, 'toggleStatus'])->name('bar-menu-categories.toggleStatus');
    Route::resource('bar-menu-items', BarMenuItemController::class);
    Route::post('bar-menu-items/{barMenuItem}/toggle-status', [BarMenuItemController::class, 'toggleStatus'])->name('bar-menu-items.toggleStatus');
    Route::resource('bar-images', BarImageController::class);
    Route::resource('bar-reviews', BarReviewController::class);
    Route::post('bar-reviews/{barReview}/approve', [BarReviewController::class, 'approve'])->name('bar-reviews.approve');
    Route::post('bar-reviews/{barReview}/hide', [BarReviewController::class, 'hide'])->name('bar-reviews.hide');
    Route::resource('bookings', BookingController::class);
    Route::post('bookings/{booking}/toggle-status', [BookingController::class,'toggleStatus'])
    ->name('admin.bookings.toggleStatus');

    Route::resource('countries', CountryController::class)->except(['show']);
    Route::post('countries/{country}/toggle-status', [CountryController::class, 'toggleStatus'])->name('countries.toggleStatus');
    Route::resource('states', StateController::class)->except(['show']);
    Route::post('states/{state}/toggle-status', [StateController::class, 'toggleStatus'])->name('states.toggleStatus');
    Route::resource('cities', CityController::class)->except(['show']);
    Route::post('cities/{city}/toggle-status', [CityController::class, 'toggleStatus'])->name('cities.toggleStatus');
    Route::resource('regions', RegionController::class)->except(['show']);
    Route::post('regions/{region}/toggle-status', [RegionController::class, 'toggleStatus'])->name('regions.toggleStatus');
    
    Route::resource('sections', SectionController::class)->except(['show']);
    Route::post('sections/{section}/toggle-status', [SectionController::class, 'toggleStatus'])->name('sections.toggleStatus');
    
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::post('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggleStatus');
    Route::resource('permissions', PermissionController::class)->except(['show']);
});



// Public Claim Routes (using Admin controller but public access)
Route::get('claim-bar/{barId?}', [\App\Http\Controllers\Admin\ClaimController::class, 'createPublic'])->name('claims.create');
Route::post('claim-bar', [\App\Http\Controllers\Admin\ClaimController::class, 'storePublic'])->name('claims.store');
Route::get('claim-success/{claimRequestId}', [\App\Http\Controllers\Admin\ClaimController::class, 'successPublic'])->name('claims.success');

Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    
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

/**
 * =======================
 *          Frontend Vue.js Routes (Catch-all - must be last)
 * =======================
 */
Route::get('/{any}', function () {
    return view('frontend.app');
})->where('any', '^(?!admin|api|dashboard|claim-bar|claim-success).*');
