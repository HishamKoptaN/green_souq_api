<?php

namespace App\Features\Posts\Data\Models;

use App\Features\Storage\Data\Models\Media;
use App\Features\Users\Data\Models\User;
use App\Models\Global\Hide;
use App\Models\Global\Report;
use Carbon\Carbon;
use Database\Factories\social\post\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'postable_type',
        'postable_id',
        'created_at',
    ];

    protected $with = [
        'postable',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function parent()
    {
        return $this->belongsTo(Post::class, 'parent_id');
    }

    public function shares()
    {
        return $this->hasMany(Post::class, 'parent_id')->whereIn('type', ['share', 'quote']);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function hides()
    {
        return $this->morphMany(Hide::class, 'hideable');
    }

    public function postable()
    {
        return $this->morphTo();
    }

    public function sharedPosts()
    {
        return $this->hasMany(
            SharedPost::class,
            'post_id',
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
        );
    }

    public function comments()
    {
        return $this->morphMany(
            Comment::class,
            'commentable',
        );
    }

    public function likes()
    {
        return $this->morphMany(
            Like::class,
            'likeable',
        );
    }

    protected static function newFactory()
    {
        return PostFactory::new();
    }

    public function sharedUsers()
    {
        return $this->belongsToMany(User::class, 'post_shares')->withTimestamps();
    }

    public function getIsFollowingAttribute()
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return false;
        }

        return $authUser->following()->where('followed_id', $this->user_id)->exists();
    }

    public function scopeRegular(
        $query,
    ) {
        return $query->where(
            'type',
            'social',
        );
    }

    public function getPostTypeAttribute()
    {
        return class_basename(
            $this->postable_type,
        );
    }
}
