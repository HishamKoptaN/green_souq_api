<?php

namespace App\Features\Notifications\Data\Models;

use App\Features\Users\Data\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'type',
        'data',
        'firebase_topic',
        'firebase_token',
        'firebase_message_id',
        'is_read',
        'read_at',
        'target_type',
        'target_value',
        'priority',
        'category',
        'image_url',
        'action_url',
        'action_type',
        'sender_type',
        'sender_id',
        'ip_address',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'notification_user')
            ->withPivot('is_read', 'read_at', 'clicked_at');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->morphTo('sender');
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByTarget($query, $targetType, $targetValue = null)
    {
        $query->where('target_type', $targetType);
        if ($targetValue) {
            $query->where('target_value', $targetValue);
        }

        return $query;
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => Carbon::parse($value)->format('Y-m-d H:i'),
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => Carbon::parse($value)->format('Y-m-d H:i'),
        );
    }
}
