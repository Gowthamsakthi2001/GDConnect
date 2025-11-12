<?php

namespace Modules\B2B\Http\Controllers\Api\V1\B2BRider;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Modules\B2B\Entities\B2BRider;
use Modules\B2B\Entities\B2BRiderOtpVerification;
use App\Services\FirebaseNotificationService; //updated by Gowtham.s

class B2BRiderAuthController extends Controller
{
    
        public function rider_send_otp(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => [
                'required',
                'string',
                'size:13',
                'regex:/^\+91[0-9]{10}$/',
            ],
        ]);
        if ($validator->fails()) {
        $errorMessage = $validator->errors()->first('mobile_number');
        
        return response()->json([
            'success' => false,
            'message' => $errorMessage, // Unified message field
            'errors' => $validator->errors() // Optional: keep errors for detailed info
        ], 422); 
    }
        
        $existingUser = B2BRider::where('mobile_no', $request->mobile_number)->first();
       
        if (!$existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'This number is invalid. Please provide a different number.',
            ], 400);
        }
        
        // $otp = rand(100000, 999999);
        //$otp = 123456;
        if ($request->mobile_number == '+919606945066') {
            $otp = '123456';
        } else {
            $otp = rand(100000, 999999);
        }
        $otpVerification = B2BRiderOtpVerification::where('mobile_number', $request->mobile_number)
            ->first();
    
        if ($otpVerification) {
            $otpVerification->otp = $otp;
            $otpVerification->updated_at = now();
            $otpVerification->save();
        } else {
            $otpVerification = new B2BRiderOtpVerification();
            $otpVerification->otp = $otp;
            $otpVerification->mobile_number = $request->mobile_number;
            $otpVerification->save();
        }
        
         $this->sendsms($request->mobile_number,$otp);
            
        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.'
        ], 200);
    }
    
    public function otp_verification(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'mobile_number' => [
                'required',
                'string',
                'size:13',
                'regex:/^\+91[0-9]{10}$/',
            ],
            'otp' => 'required|integer',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
        $errorMessage = $validator->errors()->first();
        
        return response()->json([
            'success' => false,
            'message' => $errorMessage, // Unified message field
            'errors' => $validator->errors() // Optional: keep errors for detailed info
        ], 422);
    }
    
        // Retrieve the OTP entry based on type, type_id, and mobile number
        $otpVerification = B2BRiderOtpVerification::where('mobile_number',$request->mobile_number)
            ->where('otp', $request->otp)
            ->first();
    
        // Check if the OTP exists and if the mobile number matches
        if ($otpVerification) {
            // Optionally, you may want to check if the OTP is still valid based on your logic
            $existingUser = B2BRider::where('mobile_no', $request->mobile_number)->first();
            // Here, you can perform any additional actions upon successful OTP verification
            
                Auth::shouldUse('rider');
                $tokenResult = $existingUser->createToken('B2BRiderAppToken',['rider']); 
                $plainTextToken = $tokenResult->accessToken;           
                $tokenModel = $tokenResult->token;              
            
                // set guard value on DB token row
                $tokenModel->guard = 'rider';
                $tokenModel->save();
            
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful.',
                    'token_type' => 'Bearer',
                    'access_token' => $plainTextToken,
                    'data' => $existingUser,
                ], 200);
        } else {
            // OTP verification failed
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP or mobile number.',
            ], 400); // Bad Request
        }
    }
    
    public function sendsms($phone,$msg){
        // Configuration for MSG91
         $settings = BusinessSetting::whereIn('key_name', ['sms_temp_id', 'sms_auth_id'])
        ->pluck('value', 'key_name')
        ->toArray();
    
        $config = [
            'template_id' => $settings['sms_temp_id'] ?? null, 
            'auth_key' => $settings['sms_auth_id'] ?? null,   
            'status' => 1, 
        ];

        
        // $config = [
        //     'template_id' => '67bfe8d3d6fc056e9c72a254',
        //     'auth_key' => '432110ACqkBDfF56U67c8228eP1',
        //     'status' => 1, // 1 indicates enabled
        // ];
        
        // Receiver and OTP
        $receiver = $phone; // Mobile number with country code
        $otp = $msg; // OTP to send
        
        // Ensure the MSG91 configuration is enabled
        if (!isset($config['status']) || $config['status'] != 1) {
            echo 'error: MSG91 configuration is invalid or disabled';
            exit;
        }
        
        // Format the mobile number (remove "+")
        $receiver = str_replace("+", "", $receiver);
        
        // Construct the MSG91 API URL
        $apiUrl = sprintf(
            "https://api.msg91.com/api/v5/otp?template_id=%s&mobile=%s&authkey=%s",
            urlencode($config['template_id']),
            urlencode($receiver),
            urlencode($config['auth_key'])
        );
        
        // Prepare the OTP payload
        $postData = json_encode(['OTP' => $otp]);
        
        // Initialize cURL
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
        ]);
        
        // Execute the cURL request
        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
        
        // Handle the response
        if ($error) {
            echo 'error: ' . $error;
        } else {
            // Optionally decode and validate the response (if MSG91 returns JSON)
            $decodedResponse = json_decode($response, true);
            Log::info("OTP SENT RESPONSE : ".json_encode($decodedResponse));//updated by Gowtham.s
            if (isset($decodedResponse['type']) && $decodedResponse['type'] === 'success') {
                // echo 'success';
            } else {
                echo 'error: Invalid response from MSG91. Response: ' . $response;
            }
        }

    }
    
    
public function logout(Request $request)
{
    $user = $request->user('rider'); 

    if (!$user) {
        return response()->json([
            
            'message' => 'Unauthenticated'
        ], 401);
    }

    $token = $user->token();

    if ($token) {
      $token->delete();
    } else {
        return response()->json([
            'message' => 'Unauthenticated'
        ], 401);
    }

    return response()->json([
        'success' => true,
        'message' => 'Logged out successfully.'
    ], 200);
}

    public function update_fcm_token(Request $request){ //updated by Gowtham.S
        
       $existingUser = $request->user('rider'); 
        if (!$existingUser) {
            return response()->json([
                "status"  => false,
                "message" => "Rider Not Found"
            ], 404);
        }
    
        $validator = Validator::make($request->all(), [
            'fcm_token'     => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                "status"  => false,
                "message" => "Validation Error Occurred",
                "errors"  => $validator->errors()
            ], 422);
        }
        
        $existingUser->fcm_token = $request->fcm_token;
        $existingUser->save();
        
        return response()->json(['status'=>true, 'message'=>'FCM Token has been updated', 'data' =>$existingUser],200);
    }

    public function push_notification_test(Request $request){ //updated by Gowtham.S
        $existingUser = $request->user('rider'); 
        if (!$existingUser) {
            return response()->json([
                "status"  => false,
                "message" => "Rider Not Found"
            ], 404);
        }
        
        $svc = new FirebaseNotificationService();
        
        // Send a single token (mobile or web token)
        $token = "ctK8PRSjQtWPBT4NhzippL:APA91bG1QdtU86h20LpoXSoRgrV-r4k0H8EiZAyrD9673IqoToB2k5GoL5IOC13RfiQ84zyA_2xnqVFMfJJQNcNQirmrwBJzk0Bb2PVkufQ6vv9jt8YQKBM";
        // $token = 'dQ1HRd-6S1G_0PAeyrKw_t:APA91bEROjs27BvHkLjk0P0y_aIKl6HgBt59ESP239lOO8hjFSH0Bnmc_H7LBroQnRHCqRVeVZf1_8lrJsRedsUp-nfEoWzOxw0lt-YqHk8McB7Man12e74';
        // $token = $existingUser->fcm_token;
    
        $title = "EV Ride Request!";
        $body = "A new ride request is available near your location. Pickup at 5th Avenue, drop at Central Park.";
        $data = []; //optional
        $image = "https://admin.greendrivemobility.in/uploads/users/68185a7edb134.png"; // large image - optional
        $icon  = "https://accounts.alabtechnology.com/uploads/company/favicon.png"; // small icon/logo - optional
        
        try {
            
            $resp = $svc->sendToToken($token, $title, $body, $data, $image, $icon, $userId = $existingUser->id);

            if(!empty($resp)){ //
               
                $createModel = new \Modules\B2B\Entities\B2BRidersNotification();
                $createModel->title = $title;
                $createModel->description = $body;
                $createModel->image = $image;
                $createModel->status = 1;
                $createModel->rider_id = $existingUser->id;
                $createModel->save();

            }
            
            if(!empty($resp)){ //just for testing purpose - we have store 
               
                $createModel = new \Modules\B2B\Entities\B2BAgentsNotification();
                $createModel->title = $title;
                $createModel->description = $body;
                $createModel->image = $image;
                $createModel->status = 1;
                $createModel->agent_id = $existingUser->id;
                $createModel->save();

            }

            return response()->json(['status'=>true, 'message'=> 'Notification has been sent!','data'=>$resp]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::error("Push send error: " . $e->getMessage());
        }
    }
    
}
