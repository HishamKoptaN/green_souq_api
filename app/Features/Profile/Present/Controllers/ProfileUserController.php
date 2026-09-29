<?php

namespace App\Features\Profile\Present\Controllers;

use App\Core\Contracts\DirectUploadSignerInterface;
use App\Features\Firebase\Domain\UseCases\FirebaseUseCases;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileUserController
{
    private $firebaseUseCases;

    public function __construct(
        FirebaseUseCases $firebaseUseCases
    ) {
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
                // 'role' => $user->getRoleNames()->first(),
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            ];

            return successRes($profileData);
        } catch (\Exception $e) {
            return failureRes($e->getMessage());
        }
    }

    public function presigned(Request $request, DirectUploadSignerInterface $signer)
    {
        return response()->json(
            $signer->generateSignature(
                'users/profiles/avatar',
                options: [
                    'public_id' => (string) Auth::guard('sanctum')->user()->id,
                    'overwrite' => true,
                ]
            ),
        );
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $request->validate([
                'avatar' => 'sometimes|string|max:255',
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes||max:255',
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
                // 'role' => $user->getRoleNames()->first(),
            ]);
        } catch (\ValidationException $e) {
            return failureRes($e->errors());
        } catch (\Exception $e) {
            return failureRes($e->getMessage());
        }
    }
}
