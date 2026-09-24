<?php

namespace App\Features\Users\Data\Models;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['firebase_uid', 'name', 'user_type', 'phone', 'address', 'latitude', 'longitude'])]
#[Hidden(['remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, \Laravel\Sanctum\HasApiTokens;
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
