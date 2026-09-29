<?php

namespace App\Features\Posts\Factories;

use App\Features\Posts\Contracts\PostTypeStrategyInterface;
use App\Features\Posts\Strategies\EventPostStrategy;
use App\Features\Posts\Strategies\SocialPostStrategy;

class PostStrategyFactory
{
    public static function resolve(string $type): PostTypeStrategyInterface
    {
        return match ($type) {
            'event' => app(EventPostStrategy::class),
            'social' => app(SocialPostStrategy::class),
            default => throw new \InvalidArgumentException("نوع المنشور غير مدعوم: {$type}"),
        };
    }
}
