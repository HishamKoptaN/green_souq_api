<?php

namespace App\Features\Notifications\Domain\Notifications;

use App\Features\Users\Data\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;

class NewUserRegisteredNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected User $user,
        protected int $notificationId,
    ) {}

    public function via($notifiable)
    {
        return ['firebase', 'database'];
    }

    public function toFirebase()
    {
        return CloudMessage::new()
            ->withNotification(
                [
                    'title' => 'مستخدم جديد سجل الآن! 👤',
                    'body' => $this->user->name,
                ],
            )
            ->withData(
                [
                    'user_id' => (string) $this->user->id,
                    'title' => 'مستخدم جديد سجل الآن! 👤',
                    'body' => $this->user->name,
                    'type' => 'new_user_registered',
                    'notification_id' => (string) ($this->notificationId ?? ''),
                ],
            );
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'مستخدم جديد سجل الآن! 👤',
            'body' => $this->user->name,
            'type' => 'new_user_registered',
            'user_id' => (string) $this->user->id,
        ];
    }

    public function toArray($notifiable)
    {
        return [];
    }
}
