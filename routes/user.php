<?php

use App\Features\Notifications\Present\Controllers\NotificationsApiController;
use App\Features\Posts\Present\Controllers\PostsController;
use App\Features\Profile\Present\Controllers\ProfileUserController;
use App\Features\Ratings\Present\Controllers\RatingsController;
use App\Features\SalesEngineers\Present\Controllers\SalesEngineersController;
use App\Features\Shops\Present\Controllers\ShopsController;

Route::apiResource(
    'shops',
    ShopsController::class,
);
Route::apiResource(
    'sales-engineers',
    SalesEngineersController::class,
);
Route::apiResource(
    'ratings',
    RatingsController::class,
);
Route::apiResource(
    'posts',
    PostsController::class,
);
Route::get('/notifications', [NotificationsApiController::class, 'get']);
Route::apiResource('/profile', ProfileUserController::class);
Route::post('/profile/presigned', [ProfileUserController::class, 'presigned']);
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API routes are working 2 ✅',
        'timestamp' => now(),
        'route' => 'api route success',
    ], 200);
});
