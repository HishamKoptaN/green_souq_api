<?php
use Illuminate\Support\Facades\Route;
use App\Features\Users\Present\Http\Controllers\AuthController;

Route::post('/check', [AuthController::class, 'check']);
Route::post('/auth-token', [AuthController::class, 'authToken']);
Route::post('/sign-up', [AuthController::class, 'signUp']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Auth routes are working ✅',
        'timestamp' => now(),
        'route' => 'auth.test'
    ], 200);
});