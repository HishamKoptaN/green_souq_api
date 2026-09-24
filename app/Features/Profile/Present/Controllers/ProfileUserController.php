<?php

namespace App\Features\Profile\Present\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Features\Users\Data\Models\User;
use App\Features\Storage\Services\S3Service;
use App\Features\Firebase\Domain\UseCases\FirebaseUseCases;

class ProfileUserController
{
    private $s3Service;
    private $firebaseUseCases;
    public function __construct(
        S3Service $s3Service,
        FirebaseUseCases $firebaseUseCases
    ) {
        $this->s3Service = $s3Service;
        $this->firebaseUseCases = $firebaseUseCases;
    }
    public function index(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $profileData = [
                'id' => $user->id,
                'avatar' => $user->avatar ?? null,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->getRoleNames()->first(),
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            ];
            return successRes($profileData);
        } catch (\Exception $e) {
            return failureRes($e->getMessage());
        }
    }
    public function presigned(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }
        $request->validate(['extension' => 'required|string|max:5']);
        $s3Data = $this->s3Service->signUpload(
            path: "users/profiles/avatar/{$user->id}.{$request->extension}",
            contentType: "image/{$request->extension}"
        );
        return response()->json($s3Data);
    }
    public function store(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $request->validate([
                'avatar' => 'sometimes|string|max:255',
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:255',
            ]);
            if ($request->has('avatar')) {
                $user->avatar = $request->avatar;
            }
            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }
            $user->save();
            return successRes([
                'id' => $user->id,
                'avatar' => $user->avatar ?? null,
                'name' => $user->name,
                'phone' => $user->phone,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'role' => $user->getRoleNames()->first(),
            ]);
        } catch (\ValidationException $e) {
            return failureRes($e->errors());
        } catch (\Exception $e) {
            return failureRes($e->getMessage());
        }
    }
}
