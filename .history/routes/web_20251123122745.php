<?php

use Illuminate\Support\Facades\Route;

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
