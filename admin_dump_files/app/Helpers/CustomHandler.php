<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Http;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\City\Entities\City;
use Modules\VehicleServiceTicket\Entities\VehicleTicket;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Modules\B2B\Entities\B2BVehicleRequests;
use Modules\Zones\Entities\Zones;
use Modules\B2B\Entities\B2BReturnRequest;
use Modules\B2B\Entities\B2BAgent;
use Modules\B2B\Entities\B2BVehicleAssignment;
use Modules\B2B\Entities\B2BServiceRequest;
use Modules\AssetMaster\Entities\AssetVehicleInventory;
use Modules\B2B\Entities\B2BRider;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\B2B\Entities\B2BAgentsNotification;
use App\Services\FirebaseNotificationService;

use Modules\B2B\Entities\B2BRidersNotification;

class CustomHandler
{
    public static function sendSms($phone, $message, $type = 'general')
    {
        $settings = BusinessSetting::whereIn('key_name', ['sms_temp_id', 'sms_auth_id'])
            ->pluck('value', 'key_name')
            ->toArray();

        if (empty($settings['sms_temp_id']) || empty($settings['sms_auth_id'])) {
            Log::error('MSG91 configuration is missing.');
            return ['status' => false, 'message' => 'MSG91 configuration is invalid.'];
        }
        $receiver = str_replace("+", "", $phone);
        $apiUrl = "https://api.msg91.com/api/v5/";
        if ($type === 'otp') {
            $apiUrl .= "otp?template_id={$settings['sms_temp_id']}&mobile={$receiver}&authkey={$settings['sms_auth_id']}";
            $postData = ['OTP' => $message];
        } else { 
            $apiUrl .= "flow/";
            $postData = [
                'flow_id' => $settings['sms_temp_id'],
                'recipients' => [
                    [
                        'mobiles' => $receiver,
                        'message' => $message
                    ]
                ]
            ];
        }
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($apiUrl, $postData);

            $responseData = $response->json();
            Log::info("SMS SENT RESPONSE: " . json_encode($responseData));

            if (isset($responseData['type']) && $responseData['type'] === 'success') {
                return ['status' => true, 'message' => 'SMS sent successfully.'];
            } else {
                return ['status' => false, 'message' => 'Failed to send SMS.', 'response' => $responseData];
            }
        } catch (\Exception $e) {
            Log::error("MSG91 API error: " . $e->getMessage());
            return ['status' => false, 'message' => 'Exception occurred while sending SMS.'];
        }
    }
    
    public static function admin_whatsapp_message($message)
    {
        
        $adminPhone = BusinessSetting::where('key_name', 'admin_whatsapp_no')->value('value');
        $phone = str_replace('+', '', $adminPhone);
        // WhatsApp API
        $api_key = BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
        $url = 'https://whatshub.in/api/whatsapp/send';
        $postdata = [
            "contact" => [
                [
                    "number" => $phone,
                    "message" => $message,
                ],
            ],
        ];
    
      $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postdata),
            CURLOPT_HTTPHEADER => [
                'Api-key: ' . $api_key,
                'Content-Type: application/json',
            ],
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl); 
        
        curl_close($curl);
        if ($response === false) {
            Log::error("WhatsApp API request failed: " . $curlError);
        } else {
            $response_data = json_decode($response, true);
            if ($response_data === null) {
                Log::error("WhatsApp API request returned invalid JSON: " . $response);
            } else {
                Log::info("WhatsApp Message Sent: " . json_encode($response_data));
                return true;
            }
        }
    
    }
    
    public static function user_whatsapp_message($mobile_number,$message)
    {
        $phone = str_replace('+', '', $mobile_number);
        // WhatsApp API
        $api_key = BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
        $url = 'https://whatshub.in/api/whatsapp/send';
        
        $postdata = [
            "contact" => [
                [
                    "number" => $phone,
                    "message" => $message,
                ],
            ],
        ];
    
      $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postdata),
            CURLOPT_HTTPHEADER => [
                'Api-key: ' . $api_key,
                'Content-Type: application/json',
            ],
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl); 
        
        curl_close($curl);
        if ($response === false) {
            Log::error("WhatsApp API request failed: " . $curlError);
        } else {
            $response_data = json_decode($response, true);
            if ($response_data === null) {
                Log::error("WhatsApp API request returned invalid JSON: " . $response);
            } else {
                Log::info("WhatsApp Message Sent: " . json_encode($response_data));
                return true;
            }
        }
    
    }
    
   public static function get_punchin_city($lat, $long)
    {
        $api_key = BusinessSetting::where('key_name', 'google_map_api_key')->value('value');

        if (!$api_key) {
            return 'API Key Missing';
        }

        $geocodeResponse = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'latlng' => "{$lat},{$long}",
            'key' => $api_key,
        ]);

        if (!$geocodeResponse->ok()) {
            return 'API Error';
        }

        $geocodeData = $geocodeResponse->json();

        if (!isset($geocodeData['status']) || $geocodeData['status'] !== 'OK' || empty($geocodeData['results'])) {
            return 'Unknown Address';
        }

        $components = $geocodeData['results'][0]['address_components'];
        $street_number = '';
        $route = '';
        $neighborhood = '';
        $city = '';
        $state = '';

        foreach ($components as $component) {
            if (in_array('street_number', $component['types'])) {
                $street_number = $component['long_name'];
            }
            if (in_array('route', $component['types'])) {
                $route = $component['long_name'];
            }
            if (in_array('sublocality_level_1', $component['types']) || in_array('neighborhood', $component['types'])) {
                $neighborhood = $component['long_name'];
            }
            if (in_array('locality', $component['types'])) {
                $city = $component['long_name'];
            }
            if (in_array('administrative_area_level_1', $component['types'])) {
                $state = $component['long_name'];
            }
        }

        $formatted_address = trim("{$neighborhood}, {$street_number}, {$route}, {$city}, {$state}", ', ');

        return $formatted_address ?: 'Unknown Address';
    }
    
    public static function uploadFileImage($file, $directory)
    {
        $imageName = Str::uuid(). '.' . $file->getClientOriginalExtension();
    
        $path = public_path($directory);
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    
        $file->move($path, $imageName);
        return $imageName; 
    }
    
    public static function GlobalFileDelete($fileName, $directory)
    {
        $file_path = public_path($directory . $fileName);
        if (File::exists($file_path)) {
            File::delete($file_path);
        }
    }

    
    public static function GenerateTicketId($city_id)
    {
        $city = City::where('id', $city_id)->first();
        if (!$city || empty($city->short_code)) {
            return null;
        }
        
        $prefix = 'GD' . strtoupper($city->short_code);
        $get_ticket_count = VehicleTicket::where('city_id', $city_id)->count();
        $running_number = $get_ticket_count + 1;
    
        $running_number_padded = str_pad($running_number, 5, '0', STR_PAD_LEFT);
    
        $ticket_id = $prefix . $running_number_padded;
    
        return $ticket_id;
    }
    
       /**
     * Send email with given parameters
     *
     * @param array|string $toEmails
     * @param string $subject
     * @param string $body
     * @param array|string|null $ccEmails
     * @return bool
     */
    public static function sendEmail($toEmails, $subject, $body, $ccEmails = null)
    {
        try {
            Mail::send([], [], function ($message) use ($toEmails, $subject, $body, $ccEmails) {
                $message->to((array) $toEmails)
                    ->subject($subject)
                    ->html($body);
    
                if (!empty($ccEmails)) {
                    $message->cc((array) $ccEmails);
                }
            });
    
    
            // If no exception, assume success
            \Log::info("Mail sent successfully", [
                'to'   => (array) $toEmails,
                'cc'   => (array) $ccEmails,
                'time' => now()->toDateTimeString(),
            ]);
    
            return true;
    
        } catch (\Throwable $e) {
            
            \Log::error("Email sending failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'to'    => (array) $toEmails,
                'cc'    => (array) $ccEmails,
            ]);
            return false;
        }
    }
    


    public static function RiderstoreNotification($title, $body, $rider_id){
        
        $createModel = new \Modules\B2B\Entities\B2BRidersNotification();
        $createModel->title = $title;
        $createModel->description = $body;
        $createModel->image = null; // optional image
        $createModel->status = 1;
        $createModel->rider_id = $rider_id;
        $createModel->save();
    }
    
    public static function saveAgentNotification($agentId, $title, $body, $image = null){
        
        $createModel = new \Modules\B2B\Entities\B2BAgentsNotification();
        $createModel->title = $title;
        $createModel->description = $body;
        $createModel->image = $image;
        $createModel->status = 1;
        $createModel->agent_id = $agentId;
        $createModel->save();
    }

}