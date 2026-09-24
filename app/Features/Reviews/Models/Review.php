<?php

namespace App\Features\Reviews\Models;

use Illuminate\Database\Eloquent\Model;
use App\Features\Users\Models\User;

class Review extends Model
{
    protected $fillable = ['reviewer_id', 'reviewee_id', 'rating', 'comment'];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}
