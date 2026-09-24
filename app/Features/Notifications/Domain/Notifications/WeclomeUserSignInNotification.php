<?php

namespace App\Features\Notifications\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Notifications\Notification;
use App\Features\Users\Data\Models\User;

class WeclomeUserSignInNotification extends Notification
{
    use Queueable;
    protected $user;
    protected $body;
    public function __construct(
        User $user,
    ) {
        $this->user = $user;
        $this->body = "أهلاً بك في أضحية {$user->name}";
    }
    public function via($notifiable)
    {
        return ['firebase',];
    }
    public function toFirebase()
    {
        return CloudMessage::new()
            ->withNotification([
                'title' => 'ترحيب',
                'body' => $this->body,
            ])
            ->withData([
                'id' => (string) $this->user->id,
                'url' => env('APP_URL') . '/dashboard',
            ]);
    }
    public function toArray($notifiable)
    {
        return [];
    }
}
