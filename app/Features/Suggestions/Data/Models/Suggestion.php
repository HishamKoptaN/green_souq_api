<?php

namespace App\Features\Suggestions\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Features\Users\Data\Models\User;

class Suggestion extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
