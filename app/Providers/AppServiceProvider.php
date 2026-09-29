<?php

namespace App\Providers;

use App\Core\Contracts\DirectUploadSignerInterface;
use App\Core\Services\Storage\AwsS3UploadSigner;
use App\Core\Services\Storage\CloudinaryUploadSigner;
use App\Features\Posts\Data\Models\SocialPost;
use App\Features\Users\Data\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DirectUploadSignerInterface::class, function ($app) {
            $driver = config('filesystems.direct_upload_driver', 'cloudinary');

            return match ($driver) {
                's3' => $app->make(AwsS3UploadSigner::class),
                'cloudinary' => $app->make(CloudinaryUploadSigner::class),
                default => throw new \InvalidArgumentException("مزود الرفع غير مدعوم: {$driver}"),
            };
        });
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'social' => SocialPost::class,
            'user' => User::class,
        ]);
    }
}
