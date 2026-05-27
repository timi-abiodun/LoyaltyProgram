<?php

use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::prefix('v1')->group(function () {
    
    // Auth: Restricted by 'auth' rate limiter (5 req/min)
    Route::middleware('throttle:auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    });

    // Protected: Requires valid token
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/user', fn (Request $request) => $request->user());
        
        // Purchases: Restricted by 'purchases' rate limiter (3 req/min)
        Route::post('/purchases', [PurchaseController::class, 'store'])
            ->middleware('throttle:purchases') 
            ->name('purchases.store');

        Route::get('/users/{user}/achievements', [UserDashboardController::class, 'show'])
            ->name('dashboard.show');
            
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('auth.logout');
    });
});
