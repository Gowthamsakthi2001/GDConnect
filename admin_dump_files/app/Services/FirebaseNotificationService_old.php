<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Notification as NotificationModel;
use Carbon\Carbon;

class FirebaseNotificationService
{
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function createJwt(array $payload, string $privateKey)
    {
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $segments = [
            $this->base64UrlEncode(json_encode($header)),
            $this->base64UrlEncode(json_encode($payload)),
        ];
        $signingInput = implode('.', $segments);
        openssl_sign($signingInput, $signature, $privateKey, 'sha256WithRSAEncryption');
        $segments[] = $this->base64UrlEncode($signature);
        return implode('.', $segments);
    }

    private function getFirebaseAccessToken()
    {
        if ($cachedToken = Cache::get('fcm_access_token')) {
            return $cachedToken;
        }

        $serviceAccountPath = storage_path('app/firebase/green-drive-onboarding-firebase-adminsdk-jyyfl-7c410c8ecf.json');

        if (!file_exists($serviceAccountPath)) {
            throw new \Exception("Firebase service account file not found.");
        }

        $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);

        $now = Carbon::now()->timestamp;
        $payload = [
            "iss" => $serviceAccount['client_email'],
            "scope" => "https://www.googleapis.com/auth/firebase.messaging",
            "aud" => $serviceAccount['token_uri'],
            "iat" => $now,
            "exp" => $now + 3600,
        ];

        $jwt = $this->createJwt($payload, $serviceAccount['private_key']);

        $response = Http::asForm()->post($serviceAccount['token_uri'], [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if (!$response->successful()) {
            Log::error('Failed to fetch Firebase access token', ['response' => $response->body()]);
            throw new \Exception("Access token fetch failed: " . $response->body());
        }

        $accessToken = $response->json('access_token');
        Cache::put('fcm_access_token', $accessToken, now()->addMinutes(59));

        return $accessToken;
    }

    public function sendToDeliveryMan(
        int $dmId,
        string $deviceToken,
        string $title,
        string $body,
        array $data = []
    ) {
        return $this->sendNotification(
            deviceToken: $deviceToken,
            title: $title,
            body: $body,
            data: array_merge($data, ['dm_id' => (string)$dmId])
        );
    }

    public function sendToUser(
        int $userId,
        string $deviceToken,
        string $title,
        string $body,
        array $data = []
    ) {
        return $this->sendNotification(
            deviceToken: $deviceToken,
            title: $title,
            body: $body,
            data: array_merge($data, ['user_id' => (string)$userId])
        );
    }

    public function sendNotification(
        string $deviceToken,
        string $title,
        string $body,
        array $data = []
    ) {
        $accessToken = $this->getFirebaseAccessToken();
    
        $serviceAccountPath = storage_path('app/firebase/green-drive-onboarding-firebase-adminsdk-jyyfl-7c410c8ecf.json');
        $keyData = json_decode(file_get_contents($serviceAccountPath), true);
        $projectId = $keyData['project_id'];
        
   
            $dmId = $data['dm_id'] ?? null;
    $userId = $data['user_id'] ?? null;
    
        NotificationModel::create([
        'dm_id' => $dmId,
        'user_id' => $userId,
        'data' => array_merge([
            'title' => $title,
            'body' => $body,
            'sent_at' => now()->toDateTimeString()
        ], $data),
        'status' => 1
    ]);
    
        $payload = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => collect(array_merge(['click_action' => 'FLUTTER_NOTIFICATION_CLICK'], $data))
                            ->mapWithKeys(fn($value, $key) => [$key => (string)$value])
                            ->toArray(),
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                    ]
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ]
                    ]
                ]
            ]
        ];
    
        $response = Http::withToken($accessToken)->post(
            "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
            $payload
        );

    
        if (!$response->successful()) {
            Log::error('Firebase push failed', ['response' => $response->body()]);
            throw new \Exception("Push failed: " . $response->body());
        }
    
                $notification->update([
                'data->fcm_message_id' => $response
            ]);
    
        return $response->json();
    }

}
