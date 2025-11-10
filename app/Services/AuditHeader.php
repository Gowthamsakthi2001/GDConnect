<?php

namespace App\Services;

use Illuminate\Encryption\Encrypter;

class AuditHeader
{
    public static function make(): string
    {
        // Get the base64 shared key from .env
        $key = config('services.audit.shared_key'); // base64:...
        $rawKey = base64_decode(substr($key, 7));   // decode it

        // Create encrypter with AES-256-CBC
        $encrypter = new Encrypter($rawKey, 'AES-256-CBC');

        // Claims to embed inside the encrypted token
        $claims = [
            'iss'   => config('services.audit.issuer', 'my-main-app'),
            'ts'    => time(),                           // timestamp
            'nonce' => bin2hex(random_bytes(16)),        // random anti-replay token
        ];

        // Return encrypted string
        return $encrypter->encrypt(json_encode($claims, JSON_UNESCAPED_SLASHES));
    }
}
