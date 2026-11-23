<?php

namespace App\Features\Firebase\Domain\UseCases;

use App\Features\Firebase\Data\Models\FcmToken;
use App\Models\Users\Device;
use Exception;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;

class FirebaseUseCases
{
    protected $factory;

    protected Auth $auth;

    protected Messaging $messaging;

    public function __construct()
    {
        $credentialsPath = config('services.firebase.credentials');
        if (! $credentialsPath || ! file_exists($credentialsPath)) {
            throw new Exception("Firebase credentials file missing at: {$credentialsPath}");
        }
        $this->factory = (new Factory)->withServiceAccount($credentialsPath);
        $this->auth = $this->factory->createAuth();
        $this->messaging = $this->factory->createMessaging();
    }

    public function getAuth()
    {
        return $this->auth;
    }

    public function getMessaging(): Messaging
    {
        return $this->messaging;
    }

    public function findUserByUID(string $uid)
    {
        try {
            return $this->auth->getUser($uid);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function verify(string $idToken): Token
    {
        return $this->auth->verifyIdToken($idToken);
    }

    public function getUidFromIdToken(string $idToken): string
    {
        $verifiedToken = $this->auth->verifyIdToken($idToken);

        return $verifiedToken->claims()->get('sub');
    }

    public function verifiedIdToken($request)
    {
        $id_token = $request->input('id_token') ?? $request->input('idToken');
        $verifiedIdToken = $this->auth->verifyIdToken(
            $id_token,
        );
        $firebaseUid = $verifiedIdToken->claims()->get('sub');

        return $firebaseUid;
    }

    public function upsertFcm(
        $request,
        $userId,
    ): array {
        try {
            FcmToken::table('fcm_tokens')->where('fcm_token', $request->fcmToken)->delete();
            $device = Device::updateOrCreate(
                ['device_id' => $request->input('device.device_id')],
                [
                    'user_id' => $userId,
                    'model_name' => $request->input('device.model_name', 'Unknown'),
                    'os_version' => $request->input('device.os_version', 'unknown'),
                ]
            );
            $device->fcmTokens()->updateOrCreate(
                ['fcm_token' => $request->fcm_token],
                ['updated_at' => now()]
            );
            $this->subscribeTokenToTopic($request->fcm_token, 'all');
        } catch (\Throwable $fcmEx) {
            Log::error('FCM & Device Sync Error: '.$fcmEx->getMessage());
        }
    }

    public function subscribeTokenToTopic(string $fcmToken, string $topicName)
    {
        try {
            $messaging = $this->getMessaging();
            $messaging->subscribeToTopic($topicName, $fcmToken);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
