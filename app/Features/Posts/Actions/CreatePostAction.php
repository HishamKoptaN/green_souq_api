<?php

namespace App\Features\Posts\Actions;

use App\Features\Posts\Data\Models\Post;
use App\Features\Posts\DTOs\CreatePostDTO;
use App\Features\Posts\Factories\PostStrategyFactory;
use Illuminate\Support\Facades\DB;

class CreatePostAction
{
    public function execute(CreatePostDTO $dto): Post
    {
        return DB::transaction(function () use ($dto) {
            $post = Post::create([
                'user_id' => $dto->userId,
            ]);
            $strategy = PostStrategyFactory::resolve($dto->type);
            $strategy->handle($post, array_merge($dto->typeData, ['content' => $dto->content]));

            return $post->load(['user', 'postable']);
        });
    }
}
