<?php

namespace App\Features\Profile\Present\Controllers;

use App\Features\Firebase\Domain\UseCases\FirebaseUseCases;
use App\Features\Profile\Requests\UpdateProfileRequest;
use App\Features\Storage\Data\Contracts\DirectUploadSignerInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            $userData = $user->toArray();
            $userData['average_rating'] = $user->averageRating();
            $userData['ratings_count'] = $user->received_ratings_count;

            return successRes($userData);
        } catch (\Exception $e) {
            return failureRes($e->getMessage());
        }
    }

    public function presigned(Request $request, DirectUploadSignerInterface $signer)
    {
        $request->validate([
            'extension' => 'required|string|max:10',
        ]);

        return response()->json(
            $signer->generateSignature(
                'users/profiles/avatar',
                options: [
                    'public_id' => Auth::guard('sanctum')->user()->id . '_' . time(),
                    'extension' => $request->input('extension'),
                ]
            ),
        );
    }

    public function store(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if ($request->exists('avatar')) {
                $newAvatar = $request->input('avatar');
                if ($user->avatar && ($newAvatar === null || $newAvatar !== $user->avatar)) {
                    $this->deleteOldAvatarFile($user->avatar);
                }
                $user->avatar = $newAvatar;
            }
            $user = tap($user)->update($request->validated());

            return successRes($user);
        } catch (\Exception $e) {
            return failureRes($e->getMessage());
        }
    }

    private function deleteOldAvatarFile(?string $avatarUrl): void
    {
        if (!$avatarUrl) {
            return;
        }
        $path = parse_url($avatarUrl, PHP_URL_PATH) ?? $avatarUrl;
        $relativeOldPath = ltrim(str_replace('/storage/', '', $path), '/');
        if (Storage::disk('public')->exists($relativeOldPath)) {
            Storage::disk('public')->delete($relativeOldPath);
        }
    }
}
