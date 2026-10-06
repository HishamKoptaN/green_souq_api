<?php

namespace App\Providers;

use App\Features\Posts\Data\Models\Comment;
use App\Features\Posts\Data\Models\Post;
use App\Features\Posts\Data\Models\SocialPost;
use App\Features\Shops\Data\Models\Shop;
use App\Features\Storage\Data\Contracts\DirectUploadSignerInterface;
use App\Features\Storage\Data\Strategies\AwsS3UploadSigner;
use App\Features\Storage\Data\Strategies\CloudinaryUploadSigner;
use App\Features\Storage\Data\Strategies\LocalUploadSigner;
use App\Features\Users\Data\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DirectUploadSignerInterface::class, function ($app) {
            $driver = env('FILESYSTEM_DIRECT_UPLOAD_DRIVER', 'local');

            return match ($driver) {
                's3' => $app->make(AwsS3UploadSigner::class),
                'cloudinary' => $app->make(CloudinaryUploadSigner::class),
                'local' => $app->make(LocalUploadSigner::class),
                default => throw new \InvalidArgumentException("مزود الرفع غير مدعوم: {$driver}"),
            };
        });
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'post' => Post::class,
            'social' => SocialPost::class,
            'user' => User::class,
            'comment' => Comment::class,
            'shop' => Shop::class,
        ]);
    }
}
