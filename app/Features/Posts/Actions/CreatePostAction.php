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
            PostStrategyFactory::resolve($dto->type)->handle(
                $post,
                array_merge(
                    $dto->typeData,
                    ['content' => $dto->content],
                ),
            );
            foreach ($dto->mediaFiles as $mediaItem) {
                if (! $mediaItem) {
                    continue;
                }

                $filePath = is_array($mediaItem) ? ($mediaItem['file_path'] ?? null) : $mediaItem;
                $disk = is_array($mediaItem) ? ($mediaItem['disk'] ?? 'public') : 'public';

                if ($filePath) {
                    $post->media()->create([
                        'file_path' => $filePath,
                        'disk' => $disk,
                        'file_type' => preg_match('/\.(mp4|mov|avi|webm|mkv)$/i', $filePath) ? 'video' : 'image',
                    ]);
                }
            }

            return $post->load(['user', 'postable', 'media']);
        });
    }
}
