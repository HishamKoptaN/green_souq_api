<?php

namespace App\Features\Admin\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Features\Users\Data\Models\User;

class Role extends Model
{
    protected $fillable = ['name', 'guard_name'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_has_roles');
    }
}
