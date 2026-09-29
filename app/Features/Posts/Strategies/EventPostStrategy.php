<?php

namespace App\Features\Posts\Strategies;

use App\Features\Posts\Contracts\PostTypeStrategyInterface;
use App\Features\Posts\Data\Models\Post;

class EventPostStrategy implements PostTypeStrategyInterface
{
    public function handle(Post $post, array $typeData): void
    {
        $post->eventDetail()->create([
            'event_date' => $typeData['event_date'],
            'location_name' => $typeData['location_name'],
            'latitude' => $typeData['latitude'] ?? null,
            'longitude' => $typeData['longitude'] ?? null,
        ]);
    }
}
