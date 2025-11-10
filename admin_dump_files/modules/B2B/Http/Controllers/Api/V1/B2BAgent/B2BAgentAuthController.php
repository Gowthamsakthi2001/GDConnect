<?php

namespace Modules\B2B\Http\Controllers\Api\V1\B2BAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Passport\Client as PassportClient;
use Modules\B2B\Entities\B2BAgent;
use Modules\B2B\Entities\B2BAgentOtpVerification;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;

class B2BAgentAuthController extends Controller
{
    
    
     public function agent_login(Request $request)
{
    \Log::info("Agent Login Api Called with Input".json_encode($request->all()));
    // ✅ Validation rules
    $validator = Validator::make($request->all(), [
        'username' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    // ✅ Find the agent user by email & role
    $existingUser = B2BAgent::where('role', 17)->where('status','Active')
        ->where('email', $request->username)
        ->first();

    if (!$existingUser) {
        return response()->json([
            'success' => false,
            'message' => 'Email not found.',
        ], 404);
    }

    if (!Hash::check($request->password, $existingUser->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials.',
        ], 401);
    }
    
    Auth::shouldUse('agent');
    $tokenResult = $existingUser->createToken('B2BAgentAppToken',['agent']); // PersonalAccessTokenResult
    $plainTextToken = $tokenResult->accessToken;            // string to return to client
    $tokenModel = $tokenResult->token;                      // the DB token model

    // set guard value on DB token row
    $tokenModel->guard = 'agent';
    $tokenModel->save();
    
    $imageUrl = "";
    if($existingUser->profile_photo_path){
        $imageUrl = 'uploads/users/' . $existingUser->profile_photo_path;
    }

    $existingUser->profile_photo_url = $imageUrl;
    $results=[
        'success' => true,
        'message' => 'Login successful.',
        'token_type' => 'Bearer',
        'access_token' => $plainTextToken,
        'data' => $existingUser,
        ];
        
     \Log::info("Agent Login Api Called with Input".json_encode($results));    
    return response()->json([
        'success' => true,
        'message' => 'Login successful.',
        'token_type' => 'Bearer',
        'access_token' => $plainTextToken,
        'data' => $existingUser,
    ], 200);
}
    
//     public function forgotPassword(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'email' => [
//             'required',
//             'email',
//             Rule::exists('users', 'email')->where(function ($query) {
//                 $query->where('role', 17);
//             }),
//         ],
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'success' => false,
//             'errors' => $validator->errors(),
//         ], 422);
//     }

//     // Send reset link
//     $status = Password::sendResetLink(
//         $request->only('email')
//     );
//     if ($status === Password::RESET_LINK_SENT) {
//         return response()->json([
//             'success' => true,
//             'message' => 'Password reset link sent to your email.',
//         ], 200);
//     }

//     return response()->json([
//         'success' => false,
//         'message' => 'Unable to send reset link.',
//     ], 500);
// }


    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                Rule::exists('users', 'email')->where(function ($query) {
                    $query->where('role', 17);
                }),
            ],
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
       
        $user = B2BAgent::where('email', $request->email)->first();
        
        $token = Password::broker('users')->createToken($user);
    
    
        $resetUrl = url("/b2b-agent/password/reset/{$token}?email=" . urlencode($user->email));
        
        try {
            \Mail::to($user->email)->send(new \App\Mail\B2BAgentPasswordReset(
                $resetUrl, 
                $user->name ?? $user->email
            ));
            
            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email.',
            ], 200);
        } 
        catch (\Exception $e) {
            \Log::error('Password reset email failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to send reset link. Please try again later.',
            ], 500);
        }
    }
    
    public function resetPassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
        'token' => 'required',
        'password' => 'required|min:6|confirmed', // must send password + password_confirmation
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $status = Password::broker('users')->reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        return response()->json([
            'success' => true,
            'message' => 'Password reset successful. You can now log in with your new password.',
        ], 200);
    }

    return response()->json([
        'success' => false,
        'message' => 'Invalid token or email.',
    ], 400);
}

public function logout(Request $request)
{
    \Log::info("Agent Logout api with body testing purpose".json_encode($request->all()));
    \Log::info("Agent Logout api with header".json_encode($request->header()));
    // Try to get the authenticated user from the correct guard
    $user = $request->user('agent'); // or pass 'rider' depending on guard in route
   \Log::info("Agent Logout api called with email".$user->email);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated'
        ], 401);
    }

    // Get the token instance attached in middleware
    $token = $user->token();
    
    \Log::info("Agent Logout api called with token".$token);
 
    if ($token) {
      $token->delete();
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated'
        ], 401);
    }

    return response()->json([
        'success' => true,
        'message' => 'Logged out successfully.'
    ], 200);
}


public function user_app_validation(Request $request,$user_type,$app_mode,$app_version){
        $req_user_type = $user_type;
        $req_app_mode = $app_mode;
        $req_app_version = $app_version;
        
        if (!in_array($req_user_type, ['rider', 'agent'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user type',
            ], 400);
        }
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
        if (!$req_user_type) {
            return response()->json([
                'success' => false,
                'message' => 'User Type is required',
            ], 404); 
        }
        
        
        $rider_app_live_version = BusinessSetting::where('key_name', 'b2b_rider_app_live_version')->value('value');
        $rider_app_test_version = BusinessSetting::where('key_name', 'b2b_rider_app_test_version')->value('value');
        $rider_live_latest_apk_url = BusinessSetting::where('key_name', 'b2b_rider_live_latest_apk_url')->value('value');
        $rider_test_latest_apk_url = BusinessSetting::where('key_name', 'b2b_rider_test_latest_apk_url')->value('value');
        
        
        $agent_app_live_version = BusinessSetting::where('key_name', 'b2b_agent_app_live_version')->value('value');
        $agent_app_test_version = BusinessSetting::where('key_name', 'b2b_agent_app_test_version')->value('value');
        $agent_live_latest_apk_url = BusinessSetting::where('key_name', 'b2b_agent_live_latest_apk_url')->value('value');
        $agent_test_latest_apk_url = BusinessSetting::where('key_name', 'b2b_agent_test_latest_apk_url')->value('value');
        
        $app_ArrResponse = [];
        if($user_type == 'rider'){
         if ($req_app_mode == 'test') {
            if ($req_app_version != $rider_app_test_version) {
                $app_ArrResponse['app_mode'] = $req_app_mode;
                $app_ArrResponse['app_status'] = false;
                $app_ArrResponse['message'] = 'You are using an outdated version.';
                $app_ArrResponse['app_test_latest_version'] = $rider_app_test_version;
                $app_ArrResponse['app_test_latest_download_url'] = $rider_test_latest_apk_url;
            } else {
                $app_ArrResponse['app_mode'] = $req_app_mode;
                $app_ArrResponse['app_status'] = true;
                $app_ArrResponse['message'] = 'You are using the latest version.';
            }
        }
        
        if ($req_app_mode == 'live') {
            if ($req_app_version != $rider_app_live_version) {
                $app_ArrResponse['app_mode'] = $req_app_mode;
                $app_ArrResponse['app_status'] = false;
                $app_ArrResponse['message'] = 'You are using an outdated version.';
                $app_ArrResponse['app_live_latest_version'] = $rider_app_live_version;
                $app_ArrResponse['app_live_latest_download_url'] = $rider_live_latest_apk_url;
            } else {
                $app_ArrResponse['app_mode'] = $req_app_mode;
                $app_ArrResponse['app_status'] = true;
                $app_ArrResponse['message'] = 'You are using the latest version.';
            }
        }   
        }
        
        if($user_type == 'agent'){
         if ($req_app_mode == 'test') {
            if ($req_app_version != $agent_app_test_version) {
                $app_ArrResponse['app_mode'] = $req_app_mode;
                $app_ArrResponse['app_status'] = false;
                $app_ArrResponse['message'] = 'You are using an outdated version.';
                $app_ArrResponse['app_test_latest_version'] = $agent_app_test_version;
                $app_ArrResponse['app_test_latest_download_url'] = $agent_test_latest_apk_url;
            } else {
                $app_ArrResponse['app_mode'] = $req_app_mode;
                $app_ArrResponse['app_status'] = true;
                $app_ArrResponse['message'] = 'You are using the latest version.';
            }
        }
        
        if ($req_app_mode == 'live') {
            if ($req_app_version != $agent_app_live_version) {
                $app_ArrResponse['app_mode'] = $req_app_mode;
                $app_ArrResponse['app_status'] = false;
                $app_ArrResponse['message'] = 'You are using an outdated version.';
                $app_ArrResponse['app_live_latest_version'] = $agent_app_live_version;
                $app_ArrResponse['app_live_latest_download_url'] = $agent_live_latest_apk_url;
            } else {
                $app_ArrResponse['app_mode'] = $req_app_mode;
                $app_ArrResponse['app_status'] = true;
                $app_ArrResponse['message'] = 'You are using the latest version.';
            }
        }   
        }
        
        return response()->json([
            'success' => $app_ArrResponse['app_status'] ?? false,
            'app_validation'=>$app_ArrResponse
        ], 200);
    }
    
        public function agent_send_otp(Request $request) 
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
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422); 
        }
        
        $existingUser = B2BAgent::where('role', 17)->where('phone', $request->mobile_number)->first();
       
        if (!$existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'This number is invalid. Please provide a different number.',
            ], 400);
        }
        
        $otp = rand(1000, 9999);
        $otpVerification = B2BAgentOtpVerification::where('mobile_number', $request->mobile_number)
            ->first();
    
        if ($otpVerification) {
            $otpVerification->otp = $otp;
            $otpVerification->updated_at = now();
            $otpVerification->save();
        } else {
            $otpVerification = new B2BAgentOtpVerification();
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
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422); // Unprocessable Entity
        }
    
        // Retrieve the OTP entry based on type, type_id, and mobile number
        $otpVerification = B2BAgentOtpVerification::where('mobile_number',$request->mobile_number)
            ->where('otp', $request->otp)
            ->first();
    
        // Check if the OTP exists and if the mobile number matches
        if ($otpVerification) {
            // Optionally, you may want to check if the OTP is still valid based on your logic
            $existingUser = B2BAgent::where('role', 17)->where('phone', $request->mobile_number)->first();
            // Here, you can perform any additional actions upon successful OTP verification
            
            $token = $existingUser->createToken('B2BAgentAppToken')->accessToken;
            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.',
                'token_type' => 'Bearer',
                'access_token' => $token,
                'data' =>$existingUser
            ], 200); // OK
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
}