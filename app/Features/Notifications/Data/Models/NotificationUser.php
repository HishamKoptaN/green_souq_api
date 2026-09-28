<?php

namespace App\Features\Notifications\Data\Models;

use App\Features\Users\Data\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'public',
        'message',
        'read_at',
    ];

    public function getPublicAttribute($value)
    {
        return (bool) $value;
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'notification_user'
        )->withPivot('read_at')
            ->withTimestamps();
    }
    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'notification_user', 'notification_id', 'user_id');
    // }
}
