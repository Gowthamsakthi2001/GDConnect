<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeocodingService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = \App\Models\BusinessSetting::where('key_name', 'google_map_api_key')->value('value');
    }

    public function reverse($lat, $lng)
    {
        if (!$lat || !$lng) return '-';

        $cacheKey = "geo_{$lat}_{$lng}";

        // 1. FAST: Check Redis cache
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // 2. Google API (only if not cached)
        $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
            'latlng' => "$lat,$lng",
            'key'    => $this->apiKey
        ]);

        $json = $response->json();

        if (!empty($json['results'][0]['formatted_address'])) {
            $address = $json['results'][0]['formatted_address'];

            // Cache for 1 year
            Cache::put($cacheKey, $address, 365*24*60*60);

            return $address;
        }

        return '-';
    }
}
