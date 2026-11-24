<?php

namespace App\Features\Users\Present\Http\Controllers;

use App\Events\Users\UserRegistered;
use App\Features\Firebase\Domain\UseCases\FirebaseUseCases;
use App\Features\Users\Data\Models\User;
use App\Features\Users\Domain\Enums\UserType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class AuthController
{
    public function __construct(private FirebaseUseCases $firebaseUseCases) {}

    public function check()
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (! $user) {
                return failureRes(null, 401, 'غير مصرح بالدخول');
            }

            return successRes(
                $user,
                200,
            );
        } catch (Exception $e) {
            return failureRes($e->getMessage(), 500, 'حدث خطأ أثناء التحقق من المصادقة');
        }
    }

    public function authToken(Request $request)
    {
        try {
            $firebaseUid = $this->firebaseUseCases->verifiedIdToken($request);
            $user = User::firstOrCreate(
                ['firebase_uid' => $firebaseUid],
                []
            );

            return successRes([
                'token' => $user->createToken('auth', ['*'], now()->addWeek())->plainTextToken,
            ]);
        } catch (\Throwable $e) {
            $status = ($e instanceof FailedToVerifyToken) ? 401 : 500;

            return failureRes(
                'فشلت عملية تسجيل الدخول',
                $status,
                $e->getMessage(),
                'AUTH_ERROR'
            );
        }
    }

    public function signUp(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_type' => ['required', Rule::enum(UserType::class)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'avatar' => 'nullable|string|max:255',
        ]);

        // Verifying first keeps retries safe: no user row is touched when the
        // Firebase token is invalid.
        try {
            $firebaseUid = $this->firebaseUseCases->verifiedIdToken($request);
        } catch (FailedToVerifyToken) {
            return failureRes(
                'رمز الدخول غير صالح أو منتهي الصلاحية، الرجاء تسجيل الدخول مرة أخرى',
                401,
                null,
                'INVALID_ID_TOKEN'
            );
        } catch (\Throwable $e) {
            report($e);

            return failureRes(
                'تعذّر التحقق من حسابك، الرجاء المحاولة مرة أخرى',
                500,
                null,
                'TOKEN_VERIFICATION_FAILED'
            );
        }
        $user = User::updateOrCreate(
            ['firebase_uid' => $firebaseUid],
            [
                'name' => $request->name,
                'user_type' => $request->user_type,
                'phone' => $request->phone,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'avatar' => $request->avatar,
            ]
        );

        // event(new UserRegistered($user));
        return response()->json([
            'message' => 'تم تسجيل المستخدم بنجاح',
            'user' => $user,
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if ($user) {
                $user->tokens()->delete();
                Auth::logout();

                return successRes(
                    null,
                    200,
                );
            }

            return failureRes(
                'غير مصرح لك بالخروج',
                401
            );
        } catch (Exception $e) {
            return failureRes('حدث خطأ أثناء تسجيل الخروج', 500);
        }
    }
}
