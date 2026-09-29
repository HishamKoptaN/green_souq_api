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
}
