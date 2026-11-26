<?php

namespace App\Features\Admin\Present\Http\Controllers;

use App\Features\Firebase\Domain\UseCases\FirebaseUseCases;
use App\Features\Users\Data\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class AdminAuthController
{
    public function login(Request $request, FirebaseUseCases $firebase)
    {
        $request->validate(['id_token' => 'required|string']);

        try {
            $firebaseUid = $firebase->verifiedIdToken($request);
        } catch (FailedToVerifyToken) {
            return failureRes('رمز الدخول غير صالح أو منتهي الصلاحية', 401, null, 'ADMIN_INVALID_TOKEN');
        } catch (\Throwable $e) {
            report($e);

            return failureRes('تعذّر التحقق من هوية المشرف', 500, null, 'ADMIN_TOKEN_VERIFICATION_FAILED');
        }

        $user = User::where('firebase_uid', $firebaseUid)->first();

        if (! $user || ! $user->isAdmin()) {
            return failureRes('هذا الحساب غير مخول له دخول لوحة التحكم', 403, null, 'ADMIN_FORBIDDEN');
        }

        return successRes([
            'token' => $user->createToken('admin', ['admin'], now()->addWeek())->plainTextToken,
            'user' => $this->adminUser($user),
        ]);
    }

    public function me(Request $request)
    {
        return successRes($this->adminUser($request->user()));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return successRes(null);
    }

    private function adminUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'user_type' => $user->user_type,
            'roles' => $user->roles()->pluck('name')->all(),
        ];
    }
}
