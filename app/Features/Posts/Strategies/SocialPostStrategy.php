<?php

namespace App\Features\Posts\Strategies;

use App\Features\Posts\Contracts\PostTypeStrategyInterface;
use App\Features\Posts\Data\Models\Post;
use App\Features\Posts\Data\Models\SocialPost;

class SocialPostStrategy implements PostTypeStrategyInterface
{
    public function handle(
        Post $post,
        array $typeData,
    ): void {
        $socialPost = SocialPost::create([
            'content' => $typeData['content'] ?? null,
        ]);
        $post->postable()->associate($socialPost);
        $post->save();
    }
}
