<?php

namespace App\Features\Notifications\Domain\Listeners;

use App\Features\Users\Domain\Events\UserRegisteredEvent;
use App\Features\Firebase\FirebaseAuthService;
use App\Features\Notifications\Data\Models\Notification;
use App\Features\Notifications\Domain\Notifications\NewUserRegisteredNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendAdminNotificationListener
{
    public function handle(UserRegisteredEvent $event)
    {
        try {
            $adminIds = DB::table('user_has_roles')
                ->where('model_type', 'user')
                ->whereIn('role_id', [1, 2])
                ->pluck('model_id')
                ->toArray();
            if (empty($adminIds))
                return;
            $notification = Notification::create([
                'title' => 'مستخدم جديد سجل الآن!',
                'body' => $event->user->name,
                'type' => 'new_user_registered',
                'data' => [
                    'user_id' => $event->user->id
                ],
            ]);
            $validAdminIds = DB::table('users')
                ->whereIn('id', $adminIds)
                ->pluck('id')
                ->toArray();
            $pivotData = collect($validAdminIds)->map(fn($id) => [
                'notification_id' => $notification->id,
                'user_id' => $id,
                'created_at' => now(),
            ])->toArray();
            DB::table('notification_user')->insert($pivotData);
            $tokens = DB::table('fcm_tokens')
                ->join('devices', 'fcm_tokens.device_id', '=', 'devices.id')
                ->whereIn('devices.user_id', $validAdminIds)
                ->pluck('fcm_tokens.fcm_token')
                ->filter()
                ->toArray();
            $notificationObject = new NewUserRegisteredNotification($event->user, $notification->id);
            $firebaseService = new FirebaseAuthService();
            $firebaseService->getMessaging()->sendMulticast($notificationObject->toFirebase(), $tokens);
        } catch (\Throwable $e) {
            Log::error('FCM Error: ' . $e->getMessage());
        }
    }
}
