<?php

namespace App\Features\Complaints\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Features\Users\Data\Models\User;

class Complaint extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
