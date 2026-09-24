<?php

namespace App\Features\Firebase\Data\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users\User;
class FcmToken extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'fcm_token',
        'device_type',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}