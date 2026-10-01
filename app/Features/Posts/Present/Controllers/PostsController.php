<?php

namespace App\Features\Posts\Present\Controllers;

use App\Features\Posts\Actions\CreatePostAction;
use App\Features\Posts\Data\Models\Post;
use App\Features\Posts\DTOs\CreatePostDTO;
use App\Features\Posts\Present\Resources\PostResource;
use App\Features\Posts\Requests\StorePostRequest;
use App\Features\Users\Data\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostsController
{
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $query = Post::with(['author', 'postable'])->latest();
        if ($request->has('user_id')) {
            $targetUserId = $request->query('user_id');
            if ((int) $targetUserId === $user->id) {
                $query->where('user_id', $user->id);
            } else {
                $query->where('user_id', $targetUserId);
            }
        } elseif ($request->query('type') === 'me') {
            $query->where('user_id', $user->id);
        }

        return paginateRes($query->paginate(5), PostResource::class);
    }

    public function show(User $user)
    {
        $authEvaluator = Auth::guard('sanctum')->user();
        if ($authEvaluator) {
            $user->load([
                'receivedRatings' => function ($q) use ($authEvaluator) {
                    $q->where('evaluator_id', $authEvaluator->id);
                },
            ]);
        }

        return new PostResource($user);
    }

    public function store(StorePostRequest $request, CreatePostAction $action): JsonResponse
    {
        return response()->json(
            new PostResource(
                $action->execute(
                    CreatePostDTO::fromRequest(
                        $request->validated(),
                        Auth::guard('sanctum')->user()->id,
                    ),
                ),
            ),
            201,
        );
    }
}
