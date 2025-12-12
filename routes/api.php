<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BarController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API Routes
Route::prefix('v1')->group(function () {
    
    // Authentication Routes
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    
    // Public Bar Routes
    Route::get('/bars', [BarController::class, 'index'])->name('api.bars.index');
    Route::get('/bars/{bar}', [BarController::class, 'show'])->name('api.bars.show');
    
    // Public Review Routes
    Route::get('/bars/{bar}/reviews', [ReviewController::class, 'index'])->name('api.reviews.index');
    
    // Protected Routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // User info
        Route::get('/user', [AuthController::class, 'user'])->name('api.user');
        
        // Update user profile
        Route::put('/user', [AuthController::class, 'updateProfile'])->name('api.user.update');
        
        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
        
        // Review Routes (authenticated users can create reviews)
        Route::post('/bars/{bar}/reviews', [ReviewController::class, 'store'])->name('api.reviews.store');
    });
});

