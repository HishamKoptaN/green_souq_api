<?php

namespace App\Models\Social\Post;

use App\Features\Users\Data\Models\User;
use Database\Factories\LikeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type',
    ];

    public function likeable()
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
        return LikeFactory::new();
    }
}
