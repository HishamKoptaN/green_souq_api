<?php

use App\Features\Jobs\Present\Controllers\JobsController;
use App\Features\Notifications\Present\Controllers\NotificationsApiController;
use App\Features\Posts\Present\Controllers\CmntsController;
use App\Features\Posts\Present\Controllers\LikesController;
use App\Features\Posts\Present\Controllers\PostsController;
use App\Features\Profile\Present\Controllers\ProfileUserController;
use App\Features\Ratings\Present\Controllers\RatingsController;
use App\Features\SalesEngineers\Present\Controllers\SalesEngineersController;
use App\Features\Shops\Present\Controllers\ShopsController;
use App\Features\Storage\Present\Controllers\DirectUploadController;
use App\Features\SupportTickets\Present\Controllers\SupportTicketsController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/posts', PostsController::class);
    Route::apiResource('/likes', LikesController::class);
    Route::apiResource('/cmnts', CmntsController::class);
    Route::post(
        '/upload',
        [
            DirectUploadController::class,
            'upload',
        ],
    );
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
    Route::apiResource('/support-tickets', SupportTicketsController::class);

    Route::get('/jobs', [JobsController::class, 'index']);
    Route::get('/notifications', [NotificationsApiController::class, 'get']);
    Route::apiResource('/profile', ProfileUserController::class);
    Route::post('/profile/presigned', [ProfileUserController::class, 'presigned']);
});

Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API routes are working 2 ✅',
        'timestamp' => now(),
        'route' => 'api route success',
    ], 200);
});
