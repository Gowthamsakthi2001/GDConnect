<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\EvMobitraApiSetting;
use App\Models\MobitraApiLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\AssetMaster\Entities\AssetMasterVehicle;


class MobitraApiController extends Controller
{
    public function mobitra_api_setting()
    {
        return view('vehiclemanagement::mobitra_api.mobitra_api_settings');
    }
    
        public function mobitra_api_settings_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'base_url' => 'required|url',
            'production_url' => 'required|url',
            'test_url' => 'required|url',
            'authenticate_endpoint' => 'required',
            'get_user_by_id_endpoint' => 'required',
            'get_user_list_endpoint' => 'required',
            'vw_role_based_imei_endpoint' => 'required',
            'fleet_tracking_endpoint' => 'required',
            'fleet_notification_endpoint' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except('_token');

           $settings =
           [
            'USER_NAME'=> $request->user_name,
            'PASSWORD'=> $request->password,
            'API_CLUB_MODE' => $request->api_club_mode,
            'BASE_URL' => $request->base_url,
            'API_MOBITRA_PRODUCTION' => $request->production_url,
            'API_MOBITRA_TEST' => $request->test_url,
            'AUTHENTICATE_ENDPOINT' => $request->authenticate_endpoint,
            'GET_USER_BY_ID_ENDPOINT' => $request->get_user_by_id_endpoint,
            'GET_USER_LIST_ENDPOINT' => $request->get_user_list_endpoint,
            'VW_ROLE_BASED_IMEI_ENDPOINT' => $request->vw_role_based_imei_endpoint,
            'FLEET_TRACKING_ENDPOINT' => $request->fleet_tracking_endpoint,
            'FLEET_NOTIFICATION_ENDPOINT' => $request->fleet_notification_endpoint,
        ];

    // Step 1: Fetch old settings
    $oldSettingsRaw = EvMobitraApiSetting::whereIn('key_name', array_keys($settings))->get();
    $oldSettings = $oldSettingsRaw->pluck('value', 'key_name')->toArray();

    // Step 2: Update each setting
    foreach ($settings as $key => $value) {
        EvMobitraApiSetting::updateOrInsert(
            ['key_name' => $key],
            ['value' => $value]
        );
    }

    // Step 3: Compute changed fields
    $changes = [];
    foreach ($settings as $key => $newValue) {
        $oldValue = $oldSettings[$key] ?? null;

        if ((string)$oldValue !== (string)$newValue) {
            $oldText = ($oldValue === null || $oldValue === '') ? '-' : $oldValue;
            $newText = ($newValue === null || $newValue === '') ? '-' : $newValue;
            $changes[] = "{$key}: {$oldText} → {$newText}";
        }
    }

    // Prepare log message
    $longDescription = !empty($changes)
        ? "Mobitra API Settings updated. Changes: " . implode("; ", $changes)
        : "Mobitra API Settings updated. No changes detected.";
        
    $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find(optional($user)->role))->name ?? 'Unknown';

        audit_log_after_commit([
            'module_id'         => 8,
            'short_description' => 'Mobitra API Settings Updated',
            'long_description'  => $longDescription,
            'role'              => $roleName,
            'user_id'           => optional($user)->id,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'mobitra_api_settings.update',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
        
        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
    
    public function mobitra_api_mode_update(Request $request)
    {
        // if($request->api_log_mode == false){
        //     return response()->json(['success'=>false,'message'=>'Api Club Mode field is required'],200);
        // }
        $old = EvMobitraApiSetting::where('key_name', 'API_CLUB_MODE')->value('value');
        $new = $request->api_log_mode;
        $updated = EvMobitraApiSetting::where('key_name', 'API_CLUB_MODE')->update([
            'value' => $request->api_log_mode
        ]);
        if ($updated) {
            $oldText = ($old === null || $old === '') ? '-' : $old;
            $newText = ($new === null || $new === '') ? '-' : $new;
            
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find(optional($user)->role))->name ?? 'Unknown';

            audit_log_after_commit([
                'module_id'         => 8,
                'short_description' => 'Mobitra API Mode Updated',
                'long_description'  => "API_CLUB_MODE changed: {$oldText} → {$newText}.",
                'role'              => $roleName,
                'user_id'           => optional($user)->id,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'mobitra_api_settings.mode_update',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Settings updated successfully'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to update settings'], 200);
        }
        
    }
    
 
public function authenticate(Request $request)
{
    // Initialize log data with default values
    $logData = [
        'user_id' => auth()->id(),
        'api_username' => $request->user_name ?? EvMobitraApiSetting::where('key_name', 'USER_NAME')->value('value'),
        'api_user_id' => null, // Will be populated from API response
        'api_endpoint' => null,
        'status_code' => null,
        'status_type' => null,
    ];

    try {
        // Get API settings
        $settings = EvMobitraApiSetting::pluck('value', 'key_name')->toArray();
        
        // Validate API mode
        // if (!isset($settings['API_CLUB_MODE']) || $settings['API_CLUB_MODE'] != 1) {
        //     throw new \Exception('API is not in production mode');
        // }

        // Validate required settings
        $requiredSettings = ['BASE_URL', 'AUTHENTICATE_ENDPOINT', 'USER_NAME', 'PASSWORD'];
        foreach ($requiredSettings as $setting) {
            if (empty($settings[$setting])) {
                throw new \Exception("Missing required API setting: $setting");
            }
        }

        // Build request URL
        $url = rtrim($settings['BASE_URL'], '/') . '/' . ltrim($settings['AUTHENTICATE_ENDPOINT'], '/');
        $logData['api_endpoint'] = $url;

        // Prepare request data
        $data = ($request->user_name && $request->password)
            ? ['username' => $request->user_name, 'password' => $request->password]
            : ['username' => $settings['USER_NAME'], 'password' => $settings['PASSWORD']];

        // Make API request
        $response = Http::timeout(30)
            ->retry(3, 100)
            ->withHeaders([
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post($url, $data);

        // Set response status info
        $logData['status_code'] = $response->status();
        $logData['status_type'] = $response->successful() ? 'Success' : 'Error';

        // Process response
        $responseData = $response->json();
        
        if ($response->failed()) {
            Log::warning('API Authentication Failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            throw new \Exception('API request failed: ' . ($responseData['message'] ?? $response->body()));
        }

        // Validate response structure
        if (!isset($responseData['token']) || !isset($responseData['userId'])) {
            throw new \Exception('Token or user_id missing in API response');
        }

        // Update log with API user ID from response
        $logData['api_user_id'] = $responseData['userId'];
        // MobitraApiLog::create($logData);

        return [
            'token' => $responseData['token'],
            'user_id' => $responseData['userId']
        ];

    } catch (\Exception $e) {
        // Ensure error log contains all available information
        $logData['status_code'] = $logData['status_code'] ?? 500;
        $logData['status_type'] = 'Error';
        $logData['error_message'] = $e->getMessage(); // Additional error info
        
        // Save error log
        // MobitraApiLog::create($logData);

        Log::error('API Connection Error', [
            'error' => $e->getMessage(),
            'url' => $logData['api_endpoint'] ?? null,
            'trace' => $e->getTraceAsString()
        ]);

        throw new \Exception('Authentication failed: ' . $e->getMessage());
    }
}
    
public function getUserData(Request $request)
{
    // Initialize log data with default values
    $logData = [
        'user_id' => auth()->id(),
        'api_username' => $request->user_name ?? EvMobitraApiSetting::where('key_name', 'USER_NAME')->value('value'),
        'api_user_id' => null, // Will be populated from API response
        'api_endpoint' => null,
        'status_code' => null,
        'status_type' => null
    ];

    try {
        // Get API settings
        $settings = EvMobitraApiSetting::pluck('value', 'key_name')->toArray();
        
        // Validate API mode
        // if (!isset($settings['API_CLUB_MODE']) || $settings['API_CLUB_MODE'] != 1) {
        //     throw new \Exception('API is not in production mode');
        // }

        // Validate required settings
        $requiredSettings = ['BASE_URL', 'API_TOKEN', 'GET_USER_BY_ID_ENDPOINT'];
        foreach ($requiredSettings as $setting) {
            if (empty($settings[$setting])) {
                throw new \Exception("Missing required API setting: $setting");
            }
        }
        
        $data = $this->authenticate($request);
        $user_id = $data['user_id'];
        $logData['api_user_id'] = $user_id;
        
        $endpoint = preg_replace('/\{(\$)?user_id\}/', $user_id, $settings['GET_USER_BY_ID_ENDPOINT']);
        $url = rtrim($settings['BASE_URL'], '/') . '/' . ltrim($endpoint, '/');
        $logData['api_endpoint'] = $url;

        $token = $data['token'];

        // Make API request with timeout
        $response = Http::timeout(30)
            ->retry(3, 100) // Retry 3 times with 100ms delay
            ->withHeaders([
                'accept' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ])
            ->get($url);

        // Set response status info
        $logData['status_code'] = $response->status();
        $logData['status_type'] = $response->successful() ? 'Success' : 'Error';

        // Handle non-successful responses
        if ($response->failed()) {
            $errorResponse = $response->json() ?? $response->body();
            // $logData['error_message'] = 'API request failed: ' . ($errorResponse['message'] ?? $response->body());
            // MobitraApiLog::create($logData);

            Log::warning('User Data API Request Failed', [
                'status' => $response->status(),
                'response' => $errorResponse,
                'user_id' => $user_id
            ]);
            
            return response()->json([
                'status' => $response->status(),
                'message' => 'API request failed',
                'errors' => $errorResponse
            ], $response->status());
        }

        $responseData = $response->json();
        // MobitraApiLog::create($logData);

        return response()->json([
            'status' => $response->status(),
            'data' => $responseData
        ]);

    } catch (\Illuminate\Http\Client\RequestException $e) {
        $logData['status_code'] = 503;
        $logData['status_type'] = 'Error';
        // MobitraApiLog::create($logData);

        Log::error('User Data API Connection Error', [
            'error' => $e->getMessage(),
            'url' => $logData['api_endpoint'] ?? null,
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 503,
            'message' => 'Service unavailable',
            'error' => 'Could not connect to API service'
        ], 503);

    } catch (\Exception $e) {
        $logData['status_code'] = 500;
        $logData['status_type'] = 'Error';
        // MobitraApiLog::create($logData);

        Log::error('User Data API Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 500,
            'message' => 'Internal server error',
            'error' => $e->getMessage()
        ], 500);
    }
} 

public function getUserDevicesJson(Request $request)
{
    // Initialize log data with default values
    $logData = [
        'user_id' => auth()->id(),
        'api_username' => $request->user_name ?? EvMobitraApiSetting::where('key_name', 'USER_NAME')->value('value'),
        'api_user_id' => null, // Will be populated from API response
        'api_endpoint' => null,
        'status_code' => null,
        'status_type' => null,
    ];

    try {
        // Get API settings
        $settings = EvMobitraApiSetting::pluck('value', 'key_name')->toArray();
        
        // Validate API mode
        // if (!isset($settings['API_CLUB_MODE']) || $settings['API_CLUB_MODE'] != 1) {
        //     throw new \Exception('API is not in production mode');
        // }

        // Validate required settings
        $requiredSettings = ['BASE_URL', 'API_TOKEN', 'GET_USER_LIST_ENDPOINT'];
        foreach ($requiredSettings as $setting) {
            if (empty($settings[$setting])) {
                throw new \Exception("Missing required API setting: $setting");
            }
        }

        // Get pagination parameters from request
        $page = $request->input('page', 0);
        $pageSize = $request->input('pageSize', 10);
        
        $data = $this->authenticate($request);
        $userId = $data['user_id'];
        $logData['api_user_id'] = $userId;

        if (empty($userId)) {
            throw new \Exception("User ID is required");
        }

        // Build endpoint URL with parameters
        $endpoint = preg_replace([
            '/\{(\$)?userId\}/',
            '/\{(\$)?page\}/',
            '/\{(\$)?pageSize\}/'
        ], [
            $userId,
            $page,
            $pageSize
        ], $settings['GET_USER_LIST_ENDPOINT']);
        
        $vehicle_data = '';
        if($request->input('vehicle_number')){
            $vehicle_data = '&vehicleNumber=' . $request->input('vehicle_number');
        }
        $url = rtrim($settings['BASE_URL'], '/') . '/' . ltrim($endpoint, '/') . $vehicle_data;
        $logData['api_endpoint'] = $url;
        // print_r($url);exit;
        // Make API request
        $response = Http::timeout(30)
            ->retry(3, 100)
            ->withHeaders([
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . $settings['API_TOKEN']
            ])
            ->get($url);

        // Set response status info
        $logData['status_code'] = $response->status();
        $logData['status_type'] = $response->successful() ? 'Success' : 'Error';

        // Handle non-successful responses
        if ($response->failed()) {
            $errorResponse = $response->json() ?? $response->body();
            $logData['error_message'] = 'API request failed: ' . ($errorResponse['message'] ?? $response->body());
            // MobitraApiLog::create($logData);

            return response()->json([
                'status' => $response->status(),
                'message' => 'API request failed',
                'errors' => $errorResponse
            ], $response->status());
        }

        $responseData = $response->json();
        // print_r($responseData);exit;
        // MobitraApiLog::create($logData);

        return response()->json([
            'status' => $response->status(),
            'page' => $page,
            'pageSize' => $pageSize,
            'data' => $responseData
        ]);

    } catch (\Illuminate\Http\Client\RequestException $e) {
        $logData['status_code'] = 503;
        $logData['status_type'] = 'Error';
        $logData['error_message'] = $e->getMessage();
        // MobitraApiLog::create($logData);

        Log::error('User Devices API Connection Error', [
            'error' => $e->getMessage(),
            'url' => $logData['api_endpoint'] ?? null,
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 503,
            'message' => 'Service unavailable',
            'error' => 'Could not connect to API service'
        ], 503);

    } catch (\Exception $e) {
        $logData['status_code'] = 500;
        $logData['status_type'] = 'Error';
        $logData['error_message'] = $e->getMessage();
        // MobitraApiLog::create($logData);

        Log::error('User Devices API Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 500,
            'message' => 'Internal server error',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function getUserDevices(Request $request)
{
    // Initialize log data with default values
    $logData = [
        'user_id' => auth()->id(),
        'api_username' => $request->user_name ?? EvMobitraApiSetting::where('key_name', 'USER_NAME')->value('value'),
        'api_user_id' => null, // Will be populated from API response
        'api_endpoint' => null,
        'status_code' => null,
        'status_type' => null,
    ];

    try {
        // Get API settings
        $settings = EvMobitraApiSetting::pluck('value', 'key_name')->toArray();
        
        // Validate API mode
        // if (!isset($settings['API_CLUB_MODE']) || $settings['API_CLUB_MODE'] != 1) {
        //     throw new \Exception('API is not in production mode');
        // }

        // Validate required settings
        $requiredSettings = ['BASE_URL', 'API_TOKEN', 'GET_USER_LIST_ENDPOINT'];
        foreach ($requiredSettings as $setting) {
            if (empty($settings[$setting])) {
                throw new \Exception("Missing required API setting: $setting");
            }
        }

        // Get pagination parameters from request
        $page = $request->input('page', 0);
        $pageSize = $request->input('pageSize', 10);
        
        $data = $this->authenticate($request);
        
        $userId = $data['user_id'];
        $logData['api_user_id'] = $userId;

        if (empty($userId)) {
            throw new \Exception("User ID is required");
        }

        // Build endpoint URL with parameters
        $endpoint = preg_replace([
            '/\{(\$)?userId\}/',
            '/\{(\$)?page\}/',
            '/\{(\$)?pageSize\}/'
        ], [
            $userId,
            $page,
            $pageSize
        ], $settings['GET_USER_LIST_ENDPOINT']);

        $url = rtrim($settings['BASE_URL'], '/') . '/' . ltrim($endpoint, '/');
        $logData['api_endpoint'] = $url;

        // Make API request
        $response = Http::timeout(30)
            ->retry(3, 100)
            ->withHeaders([
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . $settings['API_TOKEN']
            ])
            ->get($url);

        // Set response status info
        $logData['status_code'] = $response->status();
        $logData['status_type'] = $response->successful() ? 'Success' : 'Error';

        // Handle non-successful responses
        if ($response->failed()) {
            $errorResponse = $response->json() ?? $response->body();
            $logData['error_message'] = 'API request failed: ' . ($errorResponse['message'] ?? $response->body());
            // MobitraApiLog::create($logData);

            return [
                'status' => $response->status(),
                'message' => 'API request failed',
                'errors' => $errorResponse
            ];
        }

        $responseData = $response->json();
        
        // print_r($responseData);exit;
        // MobitraApiLog::create($logData);

        return [
            'status' => $response->status(),
            'page' => $page,
            'pageSize' => $pageSize,
            'data' => $responseData
        ];

    } catch (\Illuminate\Http\Client\RequestException $e) {
        $logData['status_code'] = 503;
        $logData['status_type'] = 'Error';
        $logData['error_message'] = $e->getMessage();
        // MobitraApiLog::create($logData);

        Log::error('User Devices API Connection Error', [
            'error' => $e->getMessage(),
            'url' => $logData['api_endpoint'] ?? null,
            'trace' => $e->getTraceAsString()
        ]);

        return [
            'status' => 503,
            'message' => 'Service unavailable',
            'error' => 'Could not connect to API service'
        ];

    } catch (\Exception $e) {
        $logData['status_code'] = 500;
        $logData['status_type'] = 'Error';
        $logData['error_message'] = $e->getMessage();
        // MobitraApiLog::create($logData);

        Log::error('User Devices API Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return[
            'status' => 500,
            'message' => 'Internal server error',
            'error' => $e->getMessage()
        ];
    }
}

public function getRoleBasedImeiData(Request $request)
{
    // Initialize log data with default values
    $logData = [
        'user_id' => auth()->id(),
        'api_username' => $request->user_name ?? EvMobitraApiSetting::where('key_name', 'USER_NAME')->value('value'),
        'api_user_id' => null,
        'api_endpoint' => null, // Will be set to actual endpoint URL
        'status_code' => null,
        'status_type' => null
    ];

    try {
        // Get API settings
        $settings = EvMobitraApiSetting::pluck('value', 'key_name')->toArray();

        // Validate required settings
        $requiredSettings = ['BASE_URL', 'API_TOKEN', 'VW_ROLE_BASED_IMEI_ENDPOINT', 'GET_USER_LIST_ENDPOINT'];
        foreach ($requiredSettings as $setting) {
            if (empty($settings[$setting])) {
                throw new \Exception("Missing required API setting: $setting");
            }
        }

        // First get the device list
        $deviceResponse = $this->getUserDevices($request);
        // print_r($deviceResponse);exit;
        if (!($deviceResponse['status'] == 200)) {
            $logData['status_code'] = $deviceResponse['status'];
            $logData['status_type'] = 'Error';
            $logData['error_message'] = 'Failed to get device list';
            // MobitraApiLog::create($logData);

            return [
                'status' => $deviceResponse['status'],
                'message' => 'Failed to get device list',
                'errors' => $deviceResponse['errors'] ?? null
            ];
        }

        $devices = $deviceResponse['data']['payload']['deviceList'] ?? [];
        
        // Extract unique accountId and roleId combinations
        $accountRoleMap = [];
        foreach ($devices as $device) {
            $accountId = $device['accountId'] ?? null;
            $roleId = $device['role']['id'] ?? null;
            
            if ($accountId && $roleId) {
                if (!isset($accountRoleMap[$accountId])) {
                    $accountRoleMap[$accountId] = [];
                }
                if (!in_array($roleId, $accountRoleMap[$accountId])) {
                    $accountRoleMap[$accountId][] = $roleId;
                }
            }
        }

        $baseUrl = rtrim($settings['BASE_URL'], '/');
        $token = $settings['API_TOKEN'];
        $results = [];
        $successCount = 0;
        $failureCount = 0;
        
        // Process each accountId and roleId combination
        foreach ($accountRoleMap as $accountId => $roleIds) {
            foreach ($roleIds as $roleId) {
                // Construct endpoint URL with parameters
                $endpoint = preg_replace([
                    '/\{(\$)?accountId\}/',
                    '/\{(\$)?roleIds\}/'
                ], [
                    $accountId,
                    $roleId
                ], $settings['VW_ROLE_BASED_IMEI_ENDPOINT']);

                $url = $baseUrl . '/' . ltrim($endpoint, '/');
                
                // Create log entry for this specific API call
                $callLogData = [
                    'user_id' => $logData['user_id'],
                    'api_username' => $logData['api_username'],
                    'api_user_id' => $accountId,
                    'api_endpoint' => $url, // Actual endpoint URL being called
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $response = Http::timeout(30)
                    ->retry(2, 100)
                    ->withHeaders([
                        'accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $token
                    ])
                    ->get($url);

                $callLogData['status_code'] = $response->status();
                $callLogData['status_type'] = $response->successful() ? 'Success' : 'Error';

                if ($response->successful()) {
                    $responseData = $response->json();
                    $successCount++;
                    
                    $results[] = [
                        'accountId' => $accountId,
                        'roleId' => $roleId,
                        'data' => $responseData,
                        'status' => 'success'
                    ];
                } else {
                    $failureCount++;
                    $callLogData['error_message'] = $response->body();
                    
                    $results[] = [
                        'accountId' => $accountId,
                        'roleId' => $roleId,
                        'error' => $response->json() ?? $response->body(),
                        'status' => 'failed',
                        'statusCode' => $response->status()
                    ];
                }

                // MobitraApiLog::create($callLogData);
            }
        }

        // Create summary log entry with the main endpoint pattern
        $logData['api_endpoint'] = $settings['VW_ROLE_BASED_IMEI_ENDPOINT']; // The endpoint pattern
        $logData['status_code'] = 200;
        $logData['status_type'] = 'Success';
        $logData['additional_info'] = json_encode([
            'total_devices_processed' => count($devices),
            'unique_account_role_pairs' => count($results),
            'successful_calls' => $successCount,
            'failed_calls' => $failureCount
        ]);
        // MobitraApiLog::create($logData);

        return [
            'status' => 200,
            'message' => 'Process completed',
            'totalDevicesProcessed' => count($devices),
            'uniqueAccountRolePairs' => count($results),
            'successfulCalls' => $successCount,
            'failedCalls' => $failureCount,
            'results' => $results
        ];

    } catch (\Exception $e) {
        $logData['status_code'] = 500;
        $logData['status_type'] = 'Error';
        $logData['error_message'] = $e->getMessage();
        // MobitraApiLog::create($logData);

        Log::error('Role Based IMEI Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return [
            'status' => 500,
            'message' => 'Internal server error',
            'error' => $e->getMessage()
        ];
    }
}


public function getVehicleStatusData(Request $request)
{
    // Initialize log data with default values
    $logData = [
        'user_id' => auth()->id(),
        'api_username' => $request->user_name ?? EvMobitraApiSetting::where('key_name', 'USER_NAME')->value('value'),
        'api_user_id' => null,
        'api_endpoint' => null,
        'status_code' => null,
        'status_type' => null,
        'created_at' => now(),
        'updated_at' => now()
    ];

    try {
        // Get API settings
        $settings = EvMobitraApiSetting::pluck('value', 'key_name')->toArray();

        // Validate required settings
        $requiredSettings = ['BASE_URL', 'API_TOKEN', 'FLEET_TRACKING_ENDPOINT'];
        foreach ($requiredSettings as $setting) {
            if (empty($settings[$setting])) {
                throw new \Exception("Missing required API setting: $setting");
            }
        }

        // First get the IMEI and role data from role-based endpoint
        $deviceResponse = $this->getRoleBasedImeiData($request);
        
        if (!isset($deviceResponse['status']) || $deviceResponse['status'] != 200) {
            $logData['status_code'] = $deviceResponse['status'] ?? 500;
            $logData['status_type'] = 'Error';
            $logData['error_message'] = 'Failed to get IMEI data';
            MobitraApiLog::create($logData);

            return [
                'status' => $deviceResponse['status'] ?? 500,
                'message' => 'Failed to get IMEI data',
                'errors' => $deviceResponse['errors'] ?? null
            ];
        }

        // Extract all IMEI numbers and roleIds from the response
        $imeiNumbers = [];
        $roleIds = [];
        foreach ($deviceResponse['results'] as $result) {
            if (isset($result['data']['payload'])) {
                foreach ($result['data']['payload'] as $device) {
                    if (!empty($device['imei'])) {
                        $imeiNumbers[] = $device['imei'];
                    }
                    if (!empty($device['roleId']) && !in_array($device['roleId'], $roleIds)) {
                        $roleIds[] = $device['roleId'];
                    }
                }
            }
        }

        // Get parameters from request or use defaults
        $params = [
            'accountId' => $request->input('accountId', 11),
            'limit' => $request->input('limit', 1000),
            'offset' => $request->input('offset', 1),
            'startDate' => $request->input('startDate', strtotime('-1 day')),
            'endDate' => $request->input('endDate', time()),
            'status' => $request->input('status', '')
        ];

        // GraphQL query payload
        $payload = [
            'operationName' => 'VehicleStatusAndSinceUpdated',
            'variables' => array_merge($params, [
                'roleIds' => $roleIds,
                'IMEINumbers' => $imeiNumbers
            ]),
            'query' => 'query VehicleStatusAndSinceUpdated(
                $accountId: Int!, 
                $roleIds: [Int!]!, 
                $status: String, 
                $limit: Int!, 
                $offset: Int!, 
                $startDate: Int!, 
                $endDate: Int!, 
                $IMEINumbers: [String]
            ) {
                vehicleStatusAndSinceUpdated(
                    accountId: $accountId
                    roleIds: $roleIds
                    IMEINumbers: $IMEINumbers
                    status: $status
                    limit: $limit
                    offset: $offset
                    startDate: $startDate
                    endDate: $endDate
                ) {
                    totalCount
                    count {
                        running
                        stopped
                        offline
                    }
                    nodes {
                        vehicleNumber
                        distanceTravelled
                        lastIgnition
                        lastSpeed
                        latitude
                        longitude
                        lastDbTime
                        lastContactedTime
                        gsmNetwork
                        gpsNetwork
                        battery
                        charging
                        vehicleType
                        deviceType
                        IMEINumber
                        vehicleStatus
                        vehicleSince
                        favourite
                        roleId
                        address
                        redDotFlag
                        deviceSubscriptionExpiryDate
                        deviceEnableStatus
                        roleName
                        prRoleName
                        driverName
                        userId
                        deviceId
                        displayNumber
                    }
                }
            }'
        ];

        // Make GraphQL request
        $url = rtrim($settings['BASE_URL'], '/') . '/' . ltrim($settings['FLEET_TRACKING_ENDPOINT'], '/');
        $logData['api_endpoint'] = $url;

        $response = Http::timeout(120)
            ->retry(3, 100)
            ->withHeaders([
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . $settings['API_TOKEN'],
                'Content-Type' => 'application/json'
            ])
            ->post($url, $payload);

        // Set response status info
        $logData['status_code'] = $response->status();
        $logData['status_type'] = $response->successful() ? 'Success' : 'Error';

        // Handle response
        if ($response->failed()) {
            $logData['error_message'] = $response->body();
            MobitraApiLog::create($logData);

            Log::warning('Vehicle Status API Request Failed', [
                'status' => $response->status(),
                'response' => $response->body(),
                'payload' => $payload
            ]);
            
            return [
                'status' => $response->status(),
                'message' => 'GraphQL request failed',
                'errors' => $response->json() ?? $response->body(),
                'imei_count' => count($imeiNumbers),
                'roleIds_count' => count($roleIds)
            ];
        }

        $responseData = $response->json();
        // print_r($responseData);exit;
        $vehicleData = $responseData['data']['vehicleStatusAndSinceUpdated'] ?? null;

        // Create IMEI mapping
        $imeiMapping = [];
        foreach ($deviceResponse['results'] as $result) {
            if (isset($result['data']['payload'])) {
                foreach ($result['data']['payload'] as $device) {
                    if (!empty($device['imei'])) {
                        $imeiMapping[$device['imei']] = [
                            'roleId' => $device['roleId'],
                            'accountId' => $device['accountId'],
                            'vehicleNumber' => $device['vehicleNumber']
                        ];
                    }
                }
            }
        }

        // Enhance vehicle data
        $enhancedVehicles = [];
        if (isset($vehicleData['nodes'])) {
            foreach ($vehicleData['nodes'] as $vehicle) {
                $imei = $vehicle['IMEINumber'] ?? null;
                if ($imei && isset($imeiMapping[$imei])) {
                    $enhancedVehicles[] = array_merge($vehicle, [
                        'originalRoleId' => $imeiMapping[$imei]['roleId'],
                        'originalAccountId' => $imeiMapping[$imei]['accountId'],
                        'registeredVehicleNumber' => $imeiMapping[$imei]['vehicleNumber']
                    ]);
                } else {
                    $enhancedVehicles[] = $vehicle;
                }
            }
        }

        // Prepare response data
        $transformedData = [
            'summary' => [
                'total' => $vehicleData['totalCount'] ?? 0,
                'running' => $vehicleData['count']['running'] ?? 0,
                'stopped' => $vehicleData['count']['stopped'] ?? 0,
                'offline' => $vehicleData['count']['offline'] ?? 0,
                'imeiCount' => count($imeiNumbers),
                'roleIdsCount' => count($roleIds),
                'matchedVehicles' => count($enhancedVehicles)
            ],
            'vehicles' => $enhancedVehicles
        ];

        MobitraApiLog::create($logData);
        
        return [
            'status' => 200,
            'message' => 'Vehicle status data retrieved',
            'data' => $transformedData,
            'requestDetails' => [
                'roleIdsUsed' => $roleIds,
                'imeiNumbersUsed' => $imeiNumbers
            ]
        ];
        
        // return response()->json([
        //     'status' => 200,
        //     'message' => 'Vehicle status data retrieved',
        //     'data' => $transformedData,
        //     'requestDetails' => [
        //         'roleIdsUsed' => $roleIds,
        //         'imeiNumbersUsed' => $imeiNumbers
        //     ]
        // ]);

    } catch (\Exception $e) {
        $logData['status_code'] = 500;
        $logData['status_type'] = 'Error';
        $logData['error_message'] = $e->getMessage();
        MobitraApiLog::create($logData);

        Log::error('Vehicle Status API Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return [
            'status' => 500,
            'message' => 'Internal server error',
            'error' => $e->getMessage()
        ];
    }
}





public function getNotifications(Request $request)
{
    // Initialize log data with default values
    $logData = [
        'user_id' => auth()->id(),
        'api_username' => $request->user_name ?? EvMobitraApiSetting::where('key_name', 'USER_NAME')->value('value'),
        'api_user_id' => null,
        'api_endpoint' => null,
        'status_code' => null,
        'status_type' => null,
        'created_at' => now(),
        'updated_at' => now()
    ];

    try {
        // Get API settings
        $settings = EvMobitraApiSetting::pluck('value', 'key_name')->toArray();

        // Validate required settings
        $requiredSettings = ['BASE_URL', 'API_TOKEN', 'FLEET_NOTIFICATION_ENDPOINT'];
        foreach ($requiredSettings as $setting) {
            if (empty($settings[$setting])) {
                throw new \Exception("Missing required API setting: $setting");
            }
        }

        // First get the IMEI data from role-based endpoint
        $deviceResponse = $this->getRoleBasedImeiData($request);
        
        if (!isset($deviceResponse['status']) || $deviceResponse['status'] != 200) {
            $logData['status_code'] = $deviceResponse['status'] ?? 500;
            $logData['status_type'] = 'Error';
            $logData['error_message'] = 'Failed to get IMEI data';
            // MobitraApiLog::create($logData);

            return [
                'status' => $deviceResponse['status'] ?? 500,
                'message' => 'Failed to get IMEI data',
                'errors' => $deviceResponse['errors'] ?? null
            ];
        }

        // Extract all IMEI numbers from the response
        $imeiNumbers = [];
        foreach ($deviceResponse['results'] as $result) {
            if (isset($result['data']['payload'])) {
                foreach ($result['data']['payload'] as $device) {
                    if (!empty($device['imei'])) {
                        $imeiNumbers[] = $device['imei'];
                    }
                }
            }
        }

        // Get parameters from request with validation
        $params = [
            'IMEINumber' => $imeiNumbers,
            'NotificationType' => $request->input('NotificationType', 'All'),
            'DateTime' => $request->input('DateTime', date('Y-m-d H:i:s', strtotime('-1 day'))),
            'limit' => max(1, (int)$request->input('limit', 50)),
            'offset' => max(0, (int)$request->input('offset', 0)),
        ];

        // GraphQL query payload
        $payload = [
            'operationName' => 'getNotifications',
            'variables' => [
                'inputs' => $params
            ],
            'query' => 'query getNotifications($inputs: NotificationInput!) {
                notification(inputs: $inputs) {
                    totalCount
                    notifications {
                        IMEINumber
                        alertTime
                        alertMsg
                        userId
                        roleId
                        geofenceId
                        vehicleNumber
                        vehicleIn
                        vehicleOut
                        deviceId
                        type
                    }
                }
            }'
        ];

        // Make GraphQL request
        $url = rtrim($settings['BASE_URL'], '/') . '/' . ltrim($settings['FLEET_NOTIFICATION_ENDPOINT'], '/');
        $logData['api_endpoint'] = $url;

        $response = Http::timeout(60)
            ->retry(3, 100)
            ->withHeaders([
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . $settings['API_TOKEN'],
                'Content-Type' => 'application/json'
            ])
            ->post($url, $payload);

        // Set response status info
        $logData['status_code'] = $response->status();
        $logData['status_type'] = $response->successful() ? 'Success' : 'Error';

        // Handle response
        if ($response->failed()) {
            $errorData = $response->json();
            $errorMessage = $errorData['errors'][0]['message'] ?? 'Unknown GraphQL error';
            $logData['error_message'] = $errorMessage;
            // MobitraApiLog::create($logData);

            return [
                'status' => $response->status(),
                'message' => 'Notification request failed',
                'error' => $errorMessage,
                'imei_numbers_used' => $imeiNumbers,
                'request_params' => $params
            ];
        }

        $responseData = $response->json();
        $notificationData = $responseData['data']['notification'] ?? null;

        // Create IMEI mapping
        $imeiMapping = [];
        foreach ($deviceResponse['results'] as $result) {
            if (isset($result['data']['payload'])) {
                foreach ($result['data']['payload'] as $device) {
                    if (!empty($device['imei'])) {
                        $imeiMapping[$device['imei']] = [
                            'vehicleNumber' => $device['vehicleNumber'] ?? null,
                            'roleId' => $device['roleId'] ?? null
                        ];
                    }
                }
            }
        }

        // Enhance notifications with vehicle data
        $enhancedNotifications = [];
        if (isset($notificationData['notifications'])) {
            foreach ($notificationData['notifications'] as $notification) {
                $imei = $notification['IMEINumber'] ?? null;
                if ($imei && isset($imeiMapping[$imei])) {
                    $enhancedNotifications[] = array_merge($notification, [
                        'registeredVehicleNumber' => $imeiMapping[$imei]['vehicleNumber'],
                        'originalRoleId' => $imeiMapping[$imei]['roleId']
                    ]);
                } else {
                    $enhancedNotifications[] = $notification;
                }
            }
        }

        // Add additional info to log
        $logData['additional_info'] = json_encode([
            'imei_count' => count($imeiNumbers),
            'notification_count' => count($enhancedNotifications),
            'total_count' => $notificationData['totalCount'] ?? 0
        ]);
        // MobitraApiLog::create($logData);

        return [
            'status' => 200,
            'message' => 'Notifications retrieved successfully',
            'data' => [
                'totalCount' => $notificationData['totalCount'] ?? 0,
                'notifications' => $enhancedNotifications,
            ],
            'pagination' => [
                'current_limit' => $params['limit'],
                'current_offset' => $params['offset'],
                'next_offset' => $params['offset'] + $params['limit']
            ],
            'imei_numbers_used' => $imeiNumbers
        ];

    } catch (\Exception $e) {
        $logData['status_code'] = 500;
        $logData['status_type'] = 'Error';
        $logData['error_message'] = $e->getMessage();
        // MobitraApiLog::create($logData);

        Log::error('Notification API Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return [
            'status' => 500,
            'message' => 'Internal server error',
            'error' => $e->getMessage()
        ];
    }
}

// public function mobitra_tracking(Request $request){
//   $status = EvMobitraApiSetting::where('key_name', 'API_CLUB_MODE')->value('value');
//   $api_mode = false;
//   if($status){
//      $api_mode = true;  
//   }
//     $results = $this->getVehicleStatusData($request);
//     if(!empty($results['data']['vehicles']) || !empty($results['data']['vehicles'])){
//         $api_mode = false;
//     }
//   return view('vehiclemanagement::mobitra_api.tracking',compact('results','api_mode'));
// }


public function getVehicleStatusDataJson(Request $request)
{

    try {
        $imei = $request->input('imei');
        $settings = EvMobitraApiSetting::pluck('value', 'key_name')->toArray();
        $requiredSettings = ['BASE_URL', 'API_TOKEN', 'FLEET_TRACKING_ENDPOINT'];
        foreach ($requiredSettings as $setting) {
            if (empty($settings[$setting])) {
                throw new \Exception("Missing required API setting: $setting");
            }
        }

        $deviceResponse = $this->getRoleBasedImeiData($request);
        if (!isset($deviceResponse['status']) || $deviceResponse['status'] != 200) {
            
            return response()->json([
                'status' => $deviceResponse['status'] ?? 500,
                'message' => 'Failed to get IMEI data',
                'errors' => $deviceResponse['errors'] ?? null
            ]);
        }

        $imeiNumbers = [];
        $roleIds = [];
        foreach ($deviceResponse['results'] as $result) {
            if (isset($result['data']['payload'])) {
                foreach ($result['data']['payload'] as $device) {
                    // if (!empty($device['imei'])) $imeiNumbers[] = $device['imei'];
                    if (!empty($device['roleId']) && !in_array($device['roleId'], $roleIds)) {
                        $roleIds[] = $device['roleId'];
                    }
                }
            }
        }
        $imeiNumbers[] = $imei;
        $params = [
            'accountId' => $request->input('accountId', 11),
            'limit' => $request->input('limit', 50),
            'offset' => $request->input('offset', 1),
            'startDate' => $request->input('startDate', strtotime('-1 day')),
            'endDate' => $request->input('endDate', time()),
            'status' => $request->input('status', '')
        ];

        $payload = [
            'operationName' => 'VehicleStatusAndSinceUpdated',
            'variables' => array_merge($params, [
                'roleIds' => $roleIds,
                'IMEINumbers' => $imeiNumbers
            ]),
            'query' => 'query VehicleStatusAndSinceUpdated(
                $accountId: Int!, 
                $roleIds: [Int!]!, 
                $status: String, 
                $limit: Int!, 
                $offset: Int!, 
                $startDate: Int!, 
                $endDate: Int!, 
                $IMEINumbers: [String]
            ) {
                vehicleStatusAndSinceUpdated(
                    accountId: $accountId
                    roleIds: $roleIds
                    IMEINumbers: $IMEINumbers
                    status: $status
                    limit: $limit
                    offset: $offset
                    startDate: $startDate
                    endDate: $endDate
                ) {
                    totalCount
                    count {
                        running
                        stopped
                        offline
                    }
                    nodes {
                        vehicleNumber
                        distanceTravelled
                        lastIgnition
                        lastSpeed
                        latitude
                        longitude
                        lastDbTime
                        lastContactedTime
                        gsmNetwork
                        gpsNetwork
                        battery
                        charging
                        vehicleType
                        deviceType
                        IMEINumber
                        vehicleStatus
                        vehicleSince
                        favourite
                        roleId
                        address
                        redDotFlag
                        deviceSubscriptionExpiryDate
                        deviceEnableStatus
                        roleName
                        prRoleName
                        driverName
                        userId
                        deviceId
                        displayNumber
                    }
                }
            }'
        ];

        $url = rtrim($settings['BASE_URL'], '/') . '/' . ltrim($settings['FLEET_TRACKING_ENDPOINT'], '/');
       

        $response = Http::timeout(120)
            ->retry(3, 100)
            ->withHeaders([
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . $settings['API_TOKEN'],
                'Content-Type' => 'application/json'
            ])
            ->post($url, $payload);

    

        if ($response->failed()) {
            $logData['error_message'] = $response->body();
           
            return response()->json([
                'status' => $response->status(),
                'message' => 'GraphQL request failed',
                'errors' => $response->json() ?? $response->body(),
            ]);
        }

        $responseData = $response->json();
        $vehicleData = $responseData['data']['vehicleStatusAndSinceUpdated'] ?? null;

        $imeiMapping = [];
        foreach ($deviceResponse['results'] as $result) {
            if (isset($result['data']['payload'])) {
                foreach ($result['data']['payload'] as $device) {
                    if (!empty($device['imei'])) {
                        $imeiMapping[$device['imei']] = [
                            'roleId' => $device['roleId'],
                            'accountId' => $device['accountId'],
                            'vehicleNumber' => $device['vehicleNumber']
                        ];
                    }
                }
            }
        }

        $enhancedVehicles = [];
        if (isset($vehicleData['nodes'])) {
            foreach ($vehicleData['nodes'] as $vehicle) {
                $imei = $vehicle['IMEINumber'] ?? null;
                if ($imei && isset($imeiMapping[$imei])) {
                    $enhancedVehicles[] = array_merge($vehicle, [
                        'originalRoleId' => $imeiMapping[$imei]['roleId'],
                        'originalAccountId' => $imeiMapping[$imei]['accountId'],
                        'registeredVehicleNumber' => $imeiMapping[$imei]['vehicleNumber']
                    ]);
                } else {
                    $enhancedVehicles[] = $vehicle;
                }
            }
        }

        $transformedData = [
            'summary' => [
                'total' => $vehicleData['totalCount'] ?? 0,
                'running' => $vehicleData['count']['running'] ?? 0,
                'stopped' => $vehicleData['count']['stopped'] ?? 0,
                'offline' => $vehicleData['count']['offline'] ?? 0,
                'imeiCount' => count($imeiNumbers),
                'roleIdsCount' => count($roleIds),
                'matchedVehicles' => count($enhancedVehicles)
            ],
            'vehicles' => $enhancedVehicles
        ];

        // $filePath = public_path('vehicles.json');
        //     file_put_contents($filePath, json_encode($transformedData));
            
            return response()->json([
                'status' => 200,
                'message' => 'Vehicle status data retrieved and file updated',
                'data' =>$transformedData,
                // 'file_path' => asset('vehicles.json') // public URL
            ]);

    } catch (\Exception $e) {
       

        Log::error('Vehicle Status API Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 500,
            'message' => 'Internal server error',
            'error' => $e->getMessage()
        ]);
    }
}

public function mobitra_tracking(Request $request)
{
    // API mode status
    $status = EvMobitraApiSetting::where('key_name', 'API_CLUB_MODE')->value('value');
    $api_mode = $status ? true : false;
    $api_mode = true;
    // Query vehicles
    $query = AssetMasterVehicle::from('ev_tbl_asset_master_vehicles as amv')
        ->join('asset_vehicle_inventories','amv.id', '=', 'asset_vehicle_inventories.asset_vehicle_id')
        ->leftJoin('vehicle_types as vt', 'amv.vehicle_type', '=', 'vt.id')
        ->leftJoin('ev_tbl_vehicle_models as vm', 'amv.model', '=', 'vm.id')
        ->select(
            'amv.telematics_imei_number',
            'amv.permanent_reg_number',
            'vt.name as vehicle_type_name',
            'vm.vehicle_model'
        )
        ->where('asset_vehicle_inventories.transfer_status',1);

    // Apply search filter
    if ($request->filled('search')) {
        $s = mb_strtolower(trim($request->search), 'UTF-8');
        $query->whereRaw("LOWER(COALESCE(amv.permanent_reg_number, '')) LIKE ?", ["%{$s}%"]);
    }

    // Paginate
    $vehicles = $query->paginate(50)->appends($request->only('search', 'page'));

    return view('vehiclemanagement::mobitra_api.tracking', compact('vehicles', 'api_mode'));
}

public function mobitra_tracking_json(Request $request)
{
    $status = EvMobitraApiSetting::where('key_name', 'API_CLUB_MODE')->value('value');
    $api_mode = $status ? true : false;

    $query = AssetMasterVehicle::from('ev_tbl_asset_master_vehicles as amv')
        ->join('asset_vehicle_inventories','amv.id', '=', 'asset_vehicle_inventories.asset_vehicle_id')
        ->leftJoin('vehicle_types as vt', 'amv.vehicle_type', '=', 'vt.id')
        ->leftJoin('ev_tbl_vehicle_models as vm', 'amv.model', '=', 'vm.id')
        ->select(
            'amv.telematics_imei_number as IMEINumber',
            'amv.permanent_reg_number as vehicleNumber',
            'vt.name as vehicleType',
            'vm.vehicle_model as vehicleModel'
        )
        ->where('asset_vehicle_inventories.transfer_status',1);

    if ($request->filled('search')) {
        $s = mb_strtolower(trim($request->search), 'UTF-8');
        $query->whereRaw("LOWER(COALESCE(amv.permanent_reg_number, '')) LIKE ?", ["%{$s}%"]);
    }

    $vehicles = $query->paginate(50)->appends($request->only('search', 'page'));

    return response()->json($vehicles);
}

}
