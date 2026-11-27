<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class FirebaseCredentialsMissingTest extends TestCase
{
    public function test_missing_firebase_credentials_returns_clear_json_error(): void
    {
        config(['services.firebase.credentials' => '/tmp/no-such-firebase-credentials.json']);

        $response = $this->postJson('/api/auth/check');

        $response->assertStatus(503)
            ->assertJsonPath('message', 'خدمة الدخول غير مهيأة حالياً، الرجاء المحاولة لاحقاً')
            ->assertJsonPath('error_code', 'FIREBASE_CONFIG_MISSING');
        $this->assertStringNotContainsString('Server Error', $response->getContent());
    }
}
