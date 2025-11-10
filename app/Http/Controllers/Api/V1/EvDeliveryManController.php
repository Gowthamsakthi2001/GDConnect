<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\City\Entities\City;
use Modules\LeadSource\Entities\LeadSource;
use Modules\RiderType\Entities\RiderType;
use Modules\Zones\Entities\Zones;
use Modules\City\Entities\Area;
use Illuminate\Support\Facades\Validator;
use Modules\Clients\Entities\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\EvAdhaarOtpLog;
use App\Models\EvAdhaarOtpVerifyLog;
use App\Models\EvLicenseVerifyLog;
use App\Models\EvBankVerifyLog;
use App\Models\EvPancardVerifyLog;
use App\Models\BusinessSetting;
use Carbon\Carbon;
class EvDeliveryManController extends Controller
{
    
    public function user_app_validation(Request $request,$app_mode,$app_version){
 
        $req_app_mode = $app_mode;
        $req_app_version = $app_version;
        // dd($app_mode,$app_version,$req_app_mode,$req_app_version);
        if (!$req_app_mode) {
            return response()->json([
                'success' => false,
                'message' => 'App Mode is required',
            ], 404); 
        }
        if (!$req_app_version) {
            return response()->json([
                'success' => false,
                'message' => 'App Version is required',
            ], 404); 
        }
        
        $app_live_version = BusinessSetting::where('key_name', 'app_live_version')->value('value');
        $app_test_version = BusinessSetting::where('key_name', 'app_test_version')->value('value');
        $live_latest_apk_url = BusinessSetting::where('key_name', 'live_latest_apk_url')->value('value');
        $test_latest_apk_url = BusinessSetting::where('key_name', 'test_latest_apk_url')->value('value');
        
        
        $app_ArrResponse = [];

        if ($req_app_mode == 'test') {
            if ($req_app_version != $app_test_version) {
                $app_ArrResponse['app_mode'] = $req_app_mode;
                $app_ArrResponse['app_status'] = false;
                $app_ArrResponse['message'] = 'You are using an outdated version.';
                $app_ArrResponse['app_test_latest_version'] = $app_test_version;
                $app_ArrResponse['app_test_latest_download_url'] = $test_latest_apk_url;
            } else {
                $app_ArrResponse['app_mode'] = $req_app_mode;
                $app_ArrResponse['app_status'] = true;
                $app_ArrResponse['message'] = 'You are using the latest version.';
            }
        }
        
        if ($req_app_mode == 'live') {
            if ($req_app_version != $app_live_version) {
                $app_ArrResponse['app_mode'] = $req_app_mode;
                $app_ArrResponse['app_status'] = false;
                $app_ArrResponse['message'] = 'You are using an outdated version.';
                $app_ArrResponse['app_live_latest_version'] = $app_live_version;
                $app_ArrResponse['app_live_latest_download_url'] = $live_latest_apk_url;
            } else {
                $app_ArrResponse['app_mode'] = $req_app_mode;
                $app_ArrResponse['app_status'] = true;
                $app_ArrResponse['message'] = 'You are using the latest version.';
            }
        }

        
        return response()->json([
            'success' => $app_ArrResponse['app_status'],
            'app_validation'=>$app_ArrResponse
        ], 200);
    }
    public function getEvLeadSources(): JsonResponse
    {
        try {
            $evLeadSources = LeadSource::where('status',1)->get(); // Fetch all EvLeadSource records
            return response()->json([
                'success' => true,
                'data' => $evLeadSources
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
             public function fcm_token_update(Request $request)
        {
            try {
                //  Validate request
                $validated = $request->validate([
                    'user_id'   => 'required|integer|exists:ev_tbl_delivery_men,id',
                    'fcm_token' => 'required|string|max:500',
                ]);
        
                // Find the deliveryman
                $rider = Deliveryman::find($validated['user_id']);
        
                //  Update FCM token
                $rider->fcm_token = $validated['fcm_token'];
                $rider->save();
        
                // Return success response
                return response()->json([
                    'status'  => true,
                    'message' => 'FCM token updated successfully.',
                    'data'    => [
                        'user_id'   => $rider->id,
                        'fcm_token' => $rider->fcm_token,
                    ]
                ], 200);
        
            } catch (\Illuminate\Validation\ValidationException $e) {
                //  Validation error
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed.',
                    'errors'  => $e->errors(),
                ], 422);
        
            } catch (\Exception $e) {
                //  Other error
                return response()->json([
                    'status'  => false,
                    'message' => 'Something went wrong while updating FCM token.',
                    'error'   => $e->getMessage(),
                ], 500);
            }
        }
        
    public function getArea(Request $request): JsonResponse
    {
        try {
            // Validate the city_id input
            $validator = Validator::make($request->all(), [
                'city_id' => 'required|integer', // Adjust the table and column name as per your schema
            ]);
    
            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            // Fetch areas based on the valid city_id
            $area = Area::where('city_id', $request->city_id)->get();
    
            return response()->json([
                'success' => true,
                'data' => $area
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get all City data
    public function getEvCities(): JsonResponse
    {
        try {
            $evCities = City::where('status',1)->get(); // Fetch all City records
            return response()->json([
                'success' => true,
                'data' => $evCities
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function list(): JsonResponse
    {
        try {
            $EvDeliveryMan = Deliveryman::all(); // Fetch all EvLeadSource records
            return response()->json([
                'success' => true,
                'data' => $EvDeliveryMan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function RiderType(): JsonResponse
    {
        try {
            $RiderType = RiderType::where('status',1)->get(); // Fetch all EvLeadSource records
            return response()->json([
                'success' => true,
                'data' => $RiderType
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function profile(Request $request)
    { 
        try {
            $dm = Deliveryman::leftJoin('ev_tbl_city as current_city', 'ev_tbl_delivery_men.current_city_id', '=', 'current_city.id')
                ->leftJoin('ev_tbl_city as interested_city', 'ev_tbl_delivery_men.interested_city_id', '=', 'interested_city.id')
                ->leftJoin('ev_tbl_lead_source as ls', 'ev_tbl_delivery_men.lead_source_id', '=', 'ls.id')
                ->select(
                    'ev_tbl_delivery_men.*',
                    'current_city.id as city_id',
                    'current_city.city_name',
                    'interested_city.id as interested_id',
                    'interested_city.city_name as interested_city_name',
                    'ls.id as leads_id',
                    'ls.source_name'
                )
                ->where('ev_tbl_delivery_men.id', $request->id)
                ->first();

            // Check if the Rider was found
            if (!$dm) {
                return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage()
            ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $dm
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function zones(): JsonResponse
    {
        try {
             $Zones = Zones::where('status', 1)->get();
            return response()->json([
                'success' => true,
                'data' => $Zones
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function get_area(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer',
            'i_id' => 'required',// Ensure the city ID is provided and is an integer
        ]);
    
        // Fetch areas based on the city ID
        $areas = Area::where('city_id', $request->id)->get();
    
        // Generate the HTML for the dropdown options
        $options = ""; // Initialize an empty string for options
        foreach ($areas as $area) {
            // Check if the current area should be selected (only if i_id is provided)
            $selected = ($request->has('i_id') && $request->i_id == $area->id) ? 'selected' : '';
            $options .= "<option value='{$area->id}' {$selected}>{$area->Area_name}</option>";
        }
    
        // Return the options as JSON response
        return response()->json([
            'status' => true,
            'data' => $options, // Include the HTML for the dropdown options
        ],200);
    }
    
    // public function downloadImage(Request $request)
    // {
    //     $filePath = $request->path;
    //     $name = $request->name;
    //     $storagePath = public_path($filePath); 
        
    //     if (!file_exists($storagePath)) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'File not found!',
    //         ], 404);
    //     }
    
    //     return view('pages.download',compact('storagePath','name'));
    // }
    
    
    public function client(Request $request)
    { 
        try {
            //  $areas = client::where('id', $request->id)->get();
            
          $areas = Client::select('ev_tbl_clients.*', 'zones.name as zone_name')
            ->leftJoin('zones', 'ev_tbl_clients.client_zone', '=', 'zones.id')
            ->where('ev_tbl_clients.id', $request->id)
            ->get();

             
            if (!$areas) {
                return response()->json([
                'success' => false,
                'message' => 'Client Not Found',
                'error' => $e->getMessage()
            ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $areas
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
    public function fetchChallanInfo(Request $request) 
    {
        // Get the API endpoint from the frontend request
        $url = $request->header('api_url');
        if (!$url) {
            return response()->json(['error' => "The 'api_url' parameter is required."], 400);
        }

        // Get the payload data
        $data = $request->all(); // Exclude 'api_url' from the payload

        // Access the 'x-api-key' from the request headers
        $apiKey = $request->header('x-api-key');
        if (!$apiKey) {
            return response()->json(['error' => "'x-api-key' header is not set."], 400);
        }

        // Send the API request using Laravel's HTTP facade
        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $data);

        Log::info('Json Response by Document '.json_encode($response->json()));
        // Check the response and return it
        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json([
                'error' => 'Failed to fetch data',
                'details' => $response->json(),
            ], $response->status());
        }
    }
    
    // public function fetchChallanInfo(Request $request)
    // {
    //     // Get the API endpoint from the frontend request
    //     $url = $request->header('api_url');
    //     if (!$url) {
    //         return response()->json(['error' => "The 'api_url' parameter is required."], 400);
    //     }

    //     // Get the payload data
    //     $data = $request->all(); // Exclude 'api_url' from the payload

    //     // Access the 'x-api-key' from the request headers
    //     $apiKey = $request->header('x-api-key');
    //     if (!$apiKey) {
    //         return response()->json(['error' => "'x-api-key' header is not set."], 400);
    //     }

    //     // Send the API request using Laravel's HTTP facade
    //     $response = Http::withHeaders([
    //         'x-api-key' => $apiKey,
    //         'Content-Type' => 'application/json',
    //     ])->post($url, $data);

    //     // Check the response and return it
    //     if ($response->successful()) {
    //         return response()->json($response->json());
    //     } else {
    //         return response()->json([
    //             'error' => 'Failed to fetch data',
    //             'details' => $response->json(),
    //         ], $response->status());
    //     }
    // }
    
    public function api_club_document_verify(Request $request): JsonResponse
    {
        // Define the validation rules
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:ADHAAR_SEND_OTP_ENDPOINT,ADHAAR_VERIFY_OTP_ENDPOINT,LICENSE_VERIFY_ENDPOINT,BANK_VERIFY_ENDPOINT,PAN_VERIFY_ENDPOINT',
        ]);
    
        $type = $request->document_type; // Ensure $type is defined before using it
    
        if ($type == 'ADHAAR_SEND_OTP_ENDPOINT') {
            $validator->addRules([
                'aadhaar_no' => 'required|digits:12',
            ]);
        }
        
        if ($type == 'ADHAAR_VERIFY_OTP_ENDPOINT') {
            $validator->addRules([
                'request_id' => 'required',
                'otp'=>'required'
            ]);
        }
        if ($type == 'LICENSE_VERIFY_ENDPOINT') {
            $validator->addRules([
                'license_number' => 'required|string|max:16',
                'date_of_birth'=>'required|date|before:today'
            ]);
        }
        if ($type == 'BANK_VERIFY_ENDPOINT') {
            $validator->addRules([
               'ifsc_code' => 'required|string',
                'account_number' => 'required|string|max:20',
                'account_holder_name' => 'required|string|max:255',
            ]);
        }
        // if ($type == 'PAN_VERIFY_ENDPOINT') {
        //     $validator->addRules([
        //       'pan_number' => 'required|string|max:10',
        //     ]);
        // }
        
         if ($type == 'PAN_VERIFY_ENDPOINT') {
            $request->merge([
                'pan_number' => strtoupper($request->input('pan_number'))
            ]);
        
           $validator->addRules([
                'pan_number' => [
                    'required',
                    'string',
                    'size:10',
                    'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'
                ],
            ]);
        }


    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        Log::info('Json Response API CLUB REQUEST'.json_encode($request->all()));
        $settings = \App\Models\EvApiClubSetting::pluck('value', 'key_name')->toArray();
        $api_club_prod_url = $settings['API_CLUB_PRODUCTION'];
        $api_club_test_url = $settings['API_CLUB_TEST'];
        $api_club_mode = $settings['API_CLUB_MODE'];
        $apiKey = $settings['X_API_KEY'];
         
        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => "'x-api-key' header is missing"], 400);
        }
    
        if ($type == 'ADHAAR_SEND_OTP_ENDPOINT') {
            $verify = $settings['ADHAAR_CARD_VERIFY'];
            $endpoint = $settings['ADHAAR_SEND_OTP_ENDPOINT'];
            if ($api_club_mode == 1 && $verify == 1) { // production
                $url = $api_club_prod_url . $endpoint;
            } else {
                $url = $api_club_test_url . $endpoint;
            }
            return $this->adhaar_car_send_otp($url, $apiKey, $request->aadhaar_no , $request->dm_id);
        }
        
        if ($type == 'ADHAAR_VERIFY_OTP_ENDPOINT') {
            $verify = $settings['ADHAAR_CARD_VERIFY'];
            $endpoint = $settings['ADHAAR_VERIFY_OTP_ENDPOINT'];
            if ($api_club_mode == 1 && $verify == 1) { // production
                $url = $api_club_prod_url . $endpoint;
            } else {
                $url = $api_club_test_url . $endpoint;
            }
            return $this->adhaar_car_verify_otp($url, $apiKey, $request->request_id,$request->otp , $request->aadhaar_no , $request->dm_id);
        }
        
         if ($type == 'LICENSE_VERIFY_ENDPOINT') {
            $verify = $settings['LICENSE_VERIFY'];
            $endpoint = $settings['LICENSE_VERIFY_ENDPOINT'];
            if ($api_club_mode == 1 && $verify == 1) { // production
                $url = $api_club_prod_url . $endpoint;
            } else {
                $url = $api_club_test_url . $endpoint;
            }
            return $this->license_verify_new($url, $apiKey, $request->license_number, $request->date_of_birth , $request->dm_id);
        }
        
        if ($type == 'BANK_VERIFY_ENDPOINT') {
            $verify = $settings['BANK_VERIFY'];
            $endpoint = $settings['BANK_VERIFY_ENDPOINT'];
            if ($api_club_mode == 1 && $verify == 1) { // production
                $url = $api_club_prod_url . $endpoint;
            } else {
                $url = $api_club_test_url . $endpoint;
            }
            return $this->bank_verify_new($url, $apiKey, $request->account_number, $request->ifsc_code,$request->account_holder_name , $request->dm_id);
        }
        
        if ($type == 'PAN_VERIFY_ENDPOINT') {
            $verify = $settings['PAN_VERIFY'];
            $endpoint = $settings['PAN_VERIFY_ENDPOINT'];
            if ($api_club_mode == 1 && $verify == 1) { // production
                $url = $api_club_prod_url . $endpoint;
            } else {
                $url = $api_club_test_url . $endpoint;
            }
            return $this->pancard_verify_new($url, $apiKey, $request->pan_number);
        }
    
        // Default response if no conditions match
        return response()->json(['error' => 'Invalid document type'], 400);
    }

    
    private function adhaar_car_send_otp($url, $apiKey, $adhaar_no , $dm_id):JsonResponse
    {
        
        $data = ['aadhaar_no' => $adhaar_no];
        $response = Http::withHeaders([
            'Referer'=>'docs.apiclub.in',
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
            'accept'=>'application/json'
        ])->post($url, $data);
    
        $responseData = $response->json();

        Log::info('Json Response Adhaar Send '.json_encode($responseData));
        if ($response->successful()) {
            EvAdhaarOtpLog::create([
                'adhaar_no'=>$adhaar_no,
                'dm_id' =>$dm_id ?? null,
                'request_id' => $responseData['request_id'] ?? null,
                'ref_id' => $responseData['response']['ref_id'] ?? null,
                'message' => $responseData['response']['message'] ?? null,
                'json_data' => json_encode($responseData),
            ]);
            // return response()->json($responseData);
            return response()->json(['success'=>true,'message'=>'Adhaar card OTP sent successfully','data'=>$responseData],200);
        } else {
   
            if($response->status() == 422){
                return response()->json(['success'=>false,'message'=>$responseData['message'],'error'=>$responseData],400);
            }else{
                return response()->json(['success'=>false,'message'=>'Adhaar card OTP sent failed','error'=>$responseData],400);
            }
            
        }
    }
    
    private function adhaar_car_verify_otp($url, $apiKey, $request_id,$otp , $adhaar_no , $dm_id):JsonResponse
    {
        $data = ['ref_id' => $request_id,'otp'=>$otp];
        $response = Http::withHeaders([
            'Referer'=>'docs.apiclub.in',
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $data);
    
        $responseData = $response->json();
         Log::info('Json Response Adhaar Verify '.json_encode($responseData));
        if ($response->successful()) {
            EvAdhaarOtpVerifyLog::create([
                'request_id' => $responseData['request_id'] ?? null,
                'adhaar_no' => $adhaar_no ?? null,
                'dm_id' => $dm_id ?? null,
                'response' => json_encode($responseData),
            ]);
                $name = $responseData['response']['name'] ?? null;
                $address = $responseData['response']['address'] ?? null;
            
                return response()->json([
                    'success' => true,
                    'message' => 'Aadhaar card verified successfully',
                    'name' => $name,
                    'address' => $address,
                    'data' => $responseData,
                ], 200);
        } else {
            EvAdhaarOtpVerifyLog::create([
                'request_id' => $responseData['request_id'] ?? null,
                'response' => json_encode($responseData),
            ]);
          if($response->status() == 422){
             return response()->json(['success'=>false,'message'=>$responseData['message'],'error'=>$responseData],400);
          }else{
              return response()->json(['success'=>false,'message'=>'Adhaar card verify failed','error'=>$responseData],400);
          }
        }
    }
    
    private function license_verify_new($url, $apiKey, $license, $dob , $dm_id): JsonResponse
    {
        $formattedDob = \Carbon\Carbon::parse($dob)->format('d-m-Y');
        
        $data = [
            'dl_no' => $license,
            'dob'   => $formattedDob
        ];
    
        $response = Http::withHeaders([
            'Referer'      => 'docs.apiclub.in',
            'x-api-key'    => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $data);
    
       $responseData = $response->json();
        Log::info('Json Response License Verify: ' . json_encode($responseData));
        
        // Ensure `response` key exists in $responseData
        $responseDetails = $responseData['response'] ?? [];

        if ($response->successful()) {
        
            EvLicenseVerifyLog::create([
                'request_id'             => $responseData['request_id'] ?? null,
                'license_number'         => $responseDetails['license_number'] ?? null,
                'dob'                    => isset($responseDetails['dob']) ? Carbon::parse($responseDetails['dob'])->format('Y-m-d') : null,
                'holder_name'            => $responseDetails['holder_name'] ?? null,
                'father_or_husband_name' => $responseDetails['father_or_husband_name'] ?? null,
                'permanent_address'      => $responseDetails['permanent_address'] ?? null,
                'permanent_zip'      => $responseDetails['permanent_zip'] ?? null,
                'temporary_address'      => $responseDetails['temporary_address'] ?? null,
                'temporary_zip'      => $responseDetails['temporary_zip'] ?? null,
                'gender'                 => $responseDetails['gender'] ?? null,
                'issue_date'             => isset($responseDetails['issue_date']) ? Carbon::parse($responseDetails['issue_date'])->format('Y-m-d') : null,
                'rto_code'               => $responseDetails['rto_code'] ?? null,
                'rto'                    => $responseDetails['rto'] ?? null,
                'state'                  => $responseDetails['state'] ?? null,
                'valid_from'             => isset($responseDetails['valid_from']) ? Carbon::parse($responseDetails['valid_from'])->format('Y-m-d') : null,
                'valid_upto'             => isset($responseDetails['valid_upto']) ? Carbon::parse($responseDetails['valid_upto'])->format('Y-m-d') : null,
                'transport_validity'     => $responseDetails['transport_validity'] ?? null,
                'non_transport_validity'=> $responseDetails['non_transport_validity'] ?? null,
                'blood_group'            => $responseDetails['blood_group'] ?? null,
                'vehicle_class'          => $responseDetails['vehicle_class'] ?? null,
                'image'                  => !empty($responseDetails['image']) ? (is_array($responseDetails['image']) ? json_encode($responseDetails['image']) : $responseDetails['image']) : null,
                'message'                => $responseData['message'] ?? null,
                'dm_id'                  => $dm_id ?? null,
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);
        
            return response()->json([
                'success' => true,
                'message' => 'License verified successfully',
                'data'    => $responseData
            ], 200);
        }
         else {
                    
                  if($response->status() == 422){
                     return response()->json(['success'=>false,'message'=>$responseData['message'],'error'=>$responseData],400);
                  }else{
                      return response()->json(['success'=>false,'message'=>'License verify failed','error'=>$responseData],400);
                  }
                  
                }
            }
    
    private function bank_verify_new($url, $apiKey, $account_number, $ifsc_code,$account_holder_name , $dm_id): JsonResponse
    {

        $data = [
            'name' => $account_holder_name,
            'accno'   => $account_number,
            'ifsc'=> $ifsc_code
        ];
    
        $response = Http::withHeaders([
            'Referer'      => 'docs.apiclub.in',
            'x-api-key'    => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $data);
    
        $responseData = $response->json();
        Log::info('Json Response Bank Verify: ' . json_encode($responseData));
    
        // Extract response details safely
        $responseDetails = $responseData['response'] ?? [];
        $accountDetails  = $responseDetails['account_details'] ?? [];
            if ($response->successful()) {
                EvBankVerifyLog::create([
                    'request_id'           => $responseData['request_id'] ?? null,
                    'account_status'       => $accountDetails['account_status'] ?? null,
                    'beneficiary_name'     => $accountDetails['beneficiary_name'] ?? null,
                    'beneficiary_account'  => $accountDetails['beneficiary_account'] ?? null,
                    'beneficiary_ifsc'     => $accountDetails['beneficiary_ifsc'] ?? null,
                    'bank_name'            => $accountDetails['bank_name'] ?? null,
                    'branch_name'          => $accountDetails['branch_name'] ?? null,
                    'message'              => $responseDetails['message'] ?? null,
                    'res_created_at'       => $responseDetails['created_at'] ?? null, 
                    'dm_id'                => $dm_id ?? null ,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);
            if($accountDetails['account_status'] == 'ACTIVE'){
                
                return response()->json(['success'=>true,'message'=>'This account status is '.$accountDetails['account_status'],'data'=>$responseData],200);
            }else{
                return response()->json(['success'=>true,'message'=>'This account status is '.$accountDetails['account_status'],'data'=>$responseData],400);
            }
            
        } else {
            
          if($response->status() == 400){
              Log::info('Json Response Bank Verify Err If: ' . $responseData['message']);
             return response()->json(['success'=>false,'message'=>$responseData['message'],'error'=>$responseData],400);
          }else{
              return response()->json(['success'=>false,'message'=>'Bank verify failed','error'=>$responseData],400);
          }
          
        }
    }
    
    private function pancard_verify_new($url, $apiKey, $pancard_number): JsonResponse
    {

        $data = [
            'pan_no' => $pancard_number,
        ];
    
        $response = Http::withHeaders([
            'Referer'      => 'docs.apiclub.in',
            'x-api-key'    => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $data);
    
        $responseData = $response->json();
        Log::info('Json Response Pancard Verify: ' . json_encode($responseData));
    
        // Extract response details safely
        $responseDetails = $responseData['response'] ?? [];
            if ($response->successful()) {
                EvPancardVerifyLog::create([
                    'request_id'           => $responseData['request_id'] ?? null,
                    'pan_no'               => $responseDetails['pan_no'] ?? null,
                    'registered_name'      => $responseDetails['registered_name'] ?? null,
                    'message'              => $responseData['message'] ?? null,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);
           
            return response()->json(['success'=>true,'message'=>'Pan card verify successfully.','data'=>$responseData],200);
            
            
        } else {
            
          if($response->status() == 422){
             return response()->json(['success'=>false,'message'=>$responseData['message'],'error'=>$responseData],400);
          }else{
              return response()->json(['success'=>false,'message'=>'Pan verify failed','error'=>$responseData],400);
          }
          
        }
    }
    
    //  public function log_info(Request $request, $dm_id,$fliter_date)
    // {
    //     // Get filter type (daily, weekly, monthly, yearly)
    //     $filter_type = $request->get('filter_type', 'daily');
    //     $query = "
    //         SELECT
    //             ev_delivery_man_logs.id,
    //             ev_delivery_man_logs.user_id,
    //             ev_delivery_man_logs.punched_in,
    //             ev_delivery_man_logs.punched_out,
    //             DATE(ev_delivery_man_logs.punched_in) AS date,
    //             TIME(ev_delivery_man_logs.punched_in) AS in_time,
    //             TIME(ev_delivery_man_logs.punched_out) AS out_time,
    //             CONCAT(
    //                 TIMESTAMPDIFF(HOUR, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out), ' hours ',
    //                 MOD(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out), 60), ' minutes'
    //             ) AS total_time
    //         FROM ev_delivery_man_logs
    //         WHERE ev_delivery_man_logs.user_id = ? ";
    //     // Execute the query
    //     $reports = DB::select($query, [$dm_id]);

    //     return response()->json(['status' => true, 'data' => $reports]);
    // }
    
    public function log_info(Request $request, $dm_id, $filter_date)
    {
        $filter_type = $request->get('filter_type', 'daily');
        $query = "
            SELECT
                ev_delivery_man_logs.id,
                ev_delivery_man_logs.user_id,
                ev_delivery_man_logs.punched_in,
                ev_delivery_man_logs.punched_out,
                DATE(ev_delivery_man_logs.punched_in) AS date,
                TIME(ev_delivery_man_logs.punched_in) AS in_time,
                TIME(ev_delivery_man_logs.punched_out) AS out_time,
                CONCAT(
                    TIMESTAMPDIFF(HOUR, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out), ' hours ',
                    MOD(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out), 60), ' minutes'
                ) AS total_time
            FROM ev_delivery_man_logs
            WHERE ev_delivery_man_logs.user_id = ? 
            AND DATE(ev_delivery_man_logs.punched_in) = ?";
        $reports = DB::select($query, [$dm_id, $filter_date]);
    
        return response()->json(['status' => true, 'data' => $reports]);
    }

    
 

    

}
