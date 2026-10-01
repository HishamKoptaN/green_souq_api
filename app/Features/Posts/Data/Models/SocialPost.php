<?php

namespace App\Features\Posts\Data\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
    ];

    public function posts()
    {
        return $this->morphMany(
            Post::class,
            'postable',
        );
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }

    public function isLikedBy(?User $user = null): bool
    {
        $user = $user ?? auth('sanctum')->user();
        if (! $user) {
            return false;
        }
        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('user_id', $user->id);
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
