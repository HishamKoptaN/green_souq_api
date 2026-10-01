<?php

namespace App\Features\Posts\Data\Models;

use App\Features\Users\Data\Models\User;
use Database\Factories\social\post\CommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'comment',
        'commentable_id',
        'commentable_type',
    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
        );
    }

    protected static function newFactory()
    {
        return CommentFactory::new();
    }
}
