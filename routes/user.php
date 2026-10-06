<?php

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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
Route::get('/get-apk-link', function () {
    $token = env('GITHUB_TOKEN');
    $repo = "HishamKoptaN/green-souq-flutter";
    try {
        $releaseResponse = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/vnd.github+json',
            'User-Agent' => 'Laravel-Backend',
        ])->get("https://api.github.com/repos/{$repo}/releases/latest");

        if ($releaseResponse->failed()) {
            return response()->json(['error' => 'فشل الجلب من GitHub'], $releaseResponse->status());
        }
        $assets = $releaseResponse->json('assets', []);
        $apkAsset = collect($assets)->first(fn($a) => str_ends_with($a['name'], '.apk'));

        if (!$apkAsset) {
            return response()->json(['error' => 'لم يتم العثور على ملف APK'], 404);
        }
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/octet-stream',
            'User-Agent' => 'Laravel-Backend',
        ])->withoutRedirecting()->get("https://api.github.com/repos/{$repo}/releases/assets/{$apkAsset['id']}");
        $directDownloadUrl = $response->header('Location');
        if ($directDownloadUrl) {
            return response()->json(['download_url' => $directDownloadUrl]);
        }
        return response()->json(['error' => 'تعذر استخراج رابط التنزيل'], 500);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/posts', PostsController::class);
    Route::post('/posts/presigned', [PostsController::class, 'presigned']);
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
