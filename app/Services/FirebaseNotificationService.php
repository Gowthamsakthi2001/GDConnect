<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Notification as NotificationModel;
use Carbon\Carbon;
use App\Models\BusinessSetting;
use Exception;

class FirebaseNotificationService
{
    protected $serviceAccount;
    protected $tokenCacheKey = 'firebase_access_token';

    public function __construct()
    {
        $this->serviceAccount = $this->loadServiceAccount();
    }

    /**
     * Load service account JSON content from BusinessSetting.
     * The BusinessSetting value should be either:
     *  - a path to a json file on disk, OR
     *  - a JSON string containing the service account content.
     *
     * @return array
     * @throws Exception
     */
    protected function loadServiceAccount(): array
    {
        $setting = BusinessSetting::where('key_name', 'push_notification_service_content')->first();
    
        if (!$setting) {
            throw new Exception('Firebase service account setting not found (push_notification_service_content).');
        }
    
        $value = $setting->value ?? null;
    
        if (!$value) {
            throw new Exception('Firebase service account value is empty.');
        }

        if (file_exists($value) && is_readable($value)) {
            $json = file_get_contents($value);
            $data = json_decode($json, true);
        } else {

            $data = json_decode($value, true);
            if ($data === null) {
                throw new Exception('Firebase service account content is not valid JSON and file not found.');
            }
        }
    
        if (!isset($data['client_email'], $data['private_key'], $data['token_uri'])) {
            throw new Exception('Firebase service account JSON missing required fields (client_email, private_key, token_uri).');
        }
    
        return $data;
    }


    /**
     * URL-safe base64 encode
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Create JWT signed with private key (RS256)
     */
    private function createJwt(array $payload, string $privateKey)
    {
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $segments = [
            $this->base64UrlEncode(json_encode($header)),
            $this->base64UrlEncode(json_encode($payload)),
        ];
        $signingInput = implode('.', $segments);
        $ok = openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        if (!$ok) {
            throw new \Exception('Failed to sign JWT using OpenSSL.');
        }

        $segments[] = $this->base64UrlEncode($signature);
        return implode('.', $segments);
    }

    /**
     * Get OAuth access token from Google (cached)
     *
     * @return string
     * @throws Exception
     */
    public function getFirebaseAccessToken(): string
    {
        // Use cache to avoid fetching token repeatedly
        $cached = Cache::get($this->tokenCacheKey);
        if ($cached && isset($cached['token'], $cached['expires_at']) && Carbon::now()->lt(Carbon::parse($cached['expires_at']))) {
            return $cached['token'];
        }

        $now = Carbon::now()->timestamp;
        $payload = [
            "iss" => $this->serviceAccount['client_email'],
            "scope" => "https://www.googleapis.com/auth/firebase.messaging",
            "aud" => $this->serviceAccount['token_uri'],
            "iat" => $now,
            "exp" => $now + 3600, // 1 hour
        ];

        $jwt = $this->createJwt($payload, $this->serviceAccount['private_key']);

        $response = Http::asForm()->post($this->serviceAccount['token_uri'], [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if (!$response->successful()) {
            Log::error('Failed to fetch Firebase access token', ['response' => $response->body(), 'status' => $response->status()]);
            throw new Exception("Access token fetch failed: " . $response->body());
        }

        $accessToken = $response->json('access_token');
        $expiresIn = $response->json('expires_in', 3600);
        $expiresAt = Carbon::now()->addSeconds($expiresIn - 30); // small safety margin

        Cache::put($this->tokenCacheKey, ['token' => $accessToken, 'expires_at' => $expiresAt->toDateTimeString()], $expiresIn - 30);

        return $accessToken;
    }

    /**
     * Send a message to FCM HTTP v1 API
     *
     * @param array $messagePayload - payload under "message" key (see FCM HTTP v1)
     * @return array response
     * @throws Exception
     */
     
    protected function sendRawMessage(array $messagePayload): array
    {
        $accessToken = $this->getFirebaseAccessToken();

        $projectId = $this->serviceAccount['project_id'] ?? null;
        if (!$projectId) {
            throw new Exception('project_id not found in service account JSON.');
        }
        
         

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $response = Http::withToken($accessToken)
            ->withHeaders(['Accept' => 'application/json'])
            ->post($url, ['message' => $messagePayload]);

        if (!$response->successful()) {
            Log::error('FCM send failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $messagePayload,
            ]);
            throw new Exception("FCM send failed: " . $response->body());
        }
       
        return $response->json();
    }

    /**
     * Build a cross-platform FCM message payload supporting:
     *  - notification.title, notification.body, notification.image
     *  - android.notification (icon, image)
     *  - webpush.notification (icon, image)
     *  - apns.payload (aps.alert + fcm_options.image)
     *
     * $target should be: ['token' => '...'] or ['topic' => 'news'] or ['condition' => "..."]
     *
     * @param array $target
     * @param string $title
     * @param string $body
     * @param array $data optional data payload (string values)
     * @param string|null $imageUrl optional image to show in notification
     * @param string|null $iconUrl optional small icon/logo
     * @return array
     */
    protected function buildMessagePayload(array $target, string $title, string $body, array $data = [], ?string $imageUrl = null, ?string $iconUrl = null): array
    {
        $notification = [
            'title' => $title,
            'body' => $body,
        ];

        if ($imageUrl) {
            // Standard FCM notification top-level image (supported where platform supports it)
            $notification['image'] = $imageUrl;
        }

        // Ensure data values are strings
        $stringifiedData = [];
        if(!empty($data)){
           foreach ($data as $k => $v) {
                $stringifiedData[$k] = is_string($v) ? $v : json_encode($v);
            } 
        }
       
        $message = array_merge($target, [
            'notification' => $notification,

            // Android specific
            'android' => [
                'priority' => 'HIGH',
                'notification' => array_filter([
                    'title' => $title,
                    'body' => $body,
                    // small icon resource name (for apps); on web/android you can use iconUrl via webpush
                    'image' => $imageUrl ?: null,
                    // 'icon' can be a resource name for Android native apps; keep null if not set
                ]),
            ],
            // APNs (iOS)
            'apns' => [
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => array_filter([
                        'alert' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        // allow mutable content for rich notifications
                        'mutable-content' => 1,
                        'sound' => 'default'
                    ]),
                    // Custom keys can go here
                ],
                // fcm_options for iOS image
                'fcm_options' => $imageUrl ? ['image' => $imageUrl] : new \stdClass(),
            ],
            // Web Push
            'webpush' => [
                'headers' => [
                    // optional TTL
                    'TTL' => '4500',
                ],
                'notification' => array_filter([
                    'title' => $title,
                    'body' => $body,
                    'icon' => $iconUrl ?: $imageUrl ?: null,
                    'image' => $imageUrl ?: null,
                    // You may include other web notification fields like badge, tag, vibrate, etc.
                ]),
            ],
            // Include fcm_options top-level for some behaviors
            // 'fcm_options' => $imageUrl ? ['image' => $imageUrl] : new \stdClass(),
            
            
        ]);
        
        // only add data if not empty
        if (!empty($stringifiedData)) {
            $message['data'] = $stringifiedData;
        }
 
        return $message;
    }

    /**
     * Send to a single device token
     *
     * @param string $token
     * @param string $title
     * @param string $body
     * @param array $data
     * @param string|null $imageUrl
     * @param string|null $iconUrl
     * @param int|null $userId optional to save notification
     * @return array
     */
    public function sendToToken(string $token, string $title, string $body, array $data = [], ?string $imageUrl = null, ?string $iconUrl = null, ?int $userId = null): array
    {
        
        $messagePayload = $this->buildMessagePayload(['token' => $token], $title, $body, $data, $imageUrl, $iconUrl);
        $response = $this->sendRawMessage($messagePayload);

        return $response;
    }

    /**
     * Send to a topic
     *
     * @param string $topic (no /topics/ prefix)
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = [], ?string $imageUrl = null, ?string $iconUrl = null)
    {
        $target = ['topic' => $topic];
        $payload = $this->buildMessagePayload($target, $title, $body, $data, $imageUrl, $iconUrl);
        return $this->sendRawMessage($payload);
    }

    /**
     * Send to multiple tokens (simple loop).
     * For high volume, use Firebase Admin SDK or batching logic.
     *
     * @param array $tokens
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = [], ?string $imageUrl = null, ?string $iconUrl = null)
    {
        $results = [];
        foreach ($tokens as $token) {
            try {
                $results[$token] = $this->sendToToken($token, $title, $body, $data, $imageUrl, $iconUrl);
            } catch (Exception $ex) {
                Log::error('Error sending to token', ['token' => $token, 'error' => $ex->getMessage()]);
                $results[$token] = ['error' => $ex->getMessage()];
            }
        }
        return $results;
    }

    /**
     * Save Notification model (customize to your schema)
     */
    protected function saveNotification(string $title, string $body, array $data = [], ?string $imageUrl = null, ?int $userId = null, ?string $token = null)
    {
        try {
            $n = new NotificationModel();
            // Adjust attribute names to match your model / migration
            if (property_exists($n, 'user_id')) $n->user_id = $userId;
            if (property_exists($n, 'device_token')) $n->device_token = $token;
            if (property_exists($n, 'title')) $n->title = $title;
            if (property_exists($n, 'body')) $n->body = $body;
            if (property_exists($n, 'image')) $n->image = $imageUrl;
            if (property_exists($n, 'data')) $n->data = json_encode($data);
            $n->save();
        } catch (\Throwable $ex) {
            Log::error('Failed saving notification record', ['error' => $ex->getMessage()]);
            // don't rethrow — sending should not break because of DB
        }
    }
}
