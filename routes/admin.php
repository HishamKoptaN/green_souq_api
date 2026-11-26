<?php

use App\Features\Admin\Present\Http\Controllers\AdminAuthController;
use App\Features\Admin\Present\Http\Controllers\AdminCrudController;
use App\Features\Admin\Present\Http\Controllers\AdminStatsController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AdminAuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/me', [AdminAuthController::class, 'me']);
    Route::post('/logout', [AdminAuthController::class, 'logout']);

    Route::get('/stats', [AdminStatsController::class, 'index']);
    Route::get('/stats/chart', [AdminStatsController::class, 'chart']);
    Route::get('/activity', [AdminStatsController::class, 'activity']);

    Route::prefix('/{resource}')->where(['resource' => 'users|shops|posts|ratings|tickets|complaints|suggestions|jobs|notifications'])->group(function () {
        Route::get('/', [AdminCrudController::class, 'index']);
        Route::get('/{id}', [AdminCrudController::class, 'show'])->whereNumber('id');
        Route::post('/', [AdminCrudController::class, 'store']);
        Route::put('/{id}', [AdminCrudController::class, 'update'])->whereNumber('id');
        Route::delete('/{id}', [AdminCrudController::class, 'destroy'])->whereNumber('id');
    });
});
