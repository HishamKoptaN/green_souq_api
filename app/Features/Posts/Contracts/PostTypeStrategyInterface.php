<?php

namespace App\Features\Posts\Contracts;

use App\Features\Posts\Data\Models\Post;

interface PostTypeStrategyInterface
{
    public function handle(Post $post, array $typeData): void;
}
