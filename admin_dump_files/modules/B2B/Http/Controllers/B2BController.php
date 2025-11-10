<?php

namespace Modules\B2B\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use App\Models\BusinessSetting;
use Modules\B2B\Entities\B2BRider;
use Modules\Zones\Entities\Zones;
use Modules\MasterManagement\Entities\CustomerLogin;
use App\Mail\B2BAgentPasswordReset;
use App\Mail\B2BResetMail;


class B2BController extends Controller
{



    public function login()
    {
        return view('b2b::auth.login');
    }
    
    public function forgot_password()
    {
        return view('b2b::auth.forgot-password');
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('b2b::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('b2b::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('b2b::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
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
    
     public function successPage() {
        return view('b2b::auth.password-reset-success');
     }
    
  


    public function sendQrCodeWhatsApp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rider_id' => 'required|exists:b2b_tbl_riders,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $rider = B2BRider::with('vehicleRequest')->where('id',$request->rider_id)->first();
        // print_r($rider);exit;
       
    
        if (!$rider || !$rider->mobile_no) {
            return response()->json([
                'status' => 'error',
                'message' => 'Rider or mobile number not found'
            ], 400);
        }
    
        // Phone formatting
        $cleanedPhone = preg_replace('/\D+/', '', $rider->mobile_no);
        if (substr($cleanedPhone, 0, 1) === '0') {
            $cleanedPhone = substr($cleanedPhone, 1);
        }
        if (substr($cleanedPhone, 0, 2) === '91' && strlen($cleanedPhone) === 12) {
            $phone = $cleanedPhone;
        } elseif (strlen($cleanedPhone) === 10) {
            $phone = '91' . $cleanedPhone;
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Provided number is not valid'
            ], 400);
        }
    
        $vehicleRequest = $rider->vehicleRequest->last();
            if (!$vehicleRequest) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No vehicle request found for this rider'
                ], 404);
            }

        $filename = $vehicleRequest->qrcode_image;
    
         $protocol = request()->getScheme(); // http or https
         $host = request()->getHost();
         $fileUrl = "{$protocol}://{$host}/b2b/qr/{$filename}";
         Log::info('QR File URL Generated: ' . $fileUrl);
    
        // WhatsApp API
        $api_key =BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
        $url = 'https://whatshub.in/api/whatsapp/send';
        
        $riderName = $rider->name ?? 'Rider';
        $requestId = $vehicleRequest->req_id ?? '';
        
        $postdata = [
            "contact" => [
                [
                    "number"    => $phone,
                    "message"   => "Dear {$riderName},\n\nYour Vehicle Request ID is: {$requestId}.\nHere is your QR Code.",
                    "media"     => "image",
                    "url"       => $fileUrl,
                    "file_name" => $filename
                ]
            ]
        ];
    
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postdata),
            CURLOPT_HTTPHEADER => [
                'Api-key: ' . $api_key,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);
    
        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
    
        if ($error) {
            return response()->json([
                'status' => 'error',
                'message' => 'cURL Error: ' . $error
            ], 500);
        }
    
        $responseData = json_decode($response, true);
    
        if (!isset($responseData['success']) || $responseData['success'] != true) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send WhatsApp message',
                'response' => $responseData
            ], 500);
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'QR Code sent via WhatsApp successfully',
            'response' => $responseData
        ]);
    }
    
    
    public function forgetPasswordView(Request $request){
        return view('b2b::auth.forgot-password');
    }
    
    public function forgotPasswordWeb(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:ev_tbl_customer_logins,email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $customer = CustomerLogin::where('email', $request->email)->first();

        if (!$customer) {
            return back()->withErrors(['email' => 'Email not found in our records.'])->withInput();
        }


        $token = Password::broker('customer_logins')->createToken($customer);

       $resetUrl = route('b2b.password.reset.form', ['token' => $token, 'email' => $customer->email]);

        try {
            Mail::to($customer->email)->send(new B2BResetMail(
                $resetUrl,
                $customer->name ?? $customer->email
            ));

            return back()->with('success', 'Password reset link sent to your email.');
        } catch (\Exception $e) {
            Log::error('Password reset email failed: ' . $e->getMessage());

            return back()->withErrors(['email' => 'Unable to send reset link. Please try again later.']);
        }
    }
    
    public function resetPasswordWeb(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:ev_tbl_customer_logins,email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed', // expects password_confirmation
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
        $status = Password::broker('customer_logins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );
    
        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully! You can now log in with your new password.',
            ], 200);
        }
    
        return response()->json([
            'success' => false,
            'message' => 'Invalid token or email.',
        ], 400);
    }
    
    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->email;
        return view('b2b::auth.Customer-password-reset', compact('token', 'email'));
    }
    
    

    public function app_version_manage_view(){
        $rider_app_live_version = BusinessSetting::where('key_name', 'b2b_rider_app_live_version')->value('value');
        $rider_app_test_version = BusinessSetting::where('key_name', 'b2b_rider_app_test_version')->value('value');
        $rider_live_latest_apk_url = BusinessSetting::where('key_name', 'b2b_rider_live_latest_apk_url')->value('value');
        $rider_test_latest_apk_url = BusinessSetting::where('key_name', 'b2b_rider_test_latest_apk_url')->value('value');
        
        
        $agent_app_live_version = BusinessSetting::where('key_name', 'b2b_agent_app_live_version')->value('value');
        $agent_app_test_version = BusinessSetting::where('key_name', 'b2b_agent_app_test_version')->value('value');
        $agent_live_latest_apk_url = BusinessSetting::where('key_name', 'b2b_agent_live_latest_apk_url')->value('value');
        $agent_test_latest_apk_url = BusinessSetting::where('key_name', 'b2b_agent_test_latest_apk_url')->value('value');
        $b2b_app_password = BusinessSetting::where('key_name', 'b2b_app_version_password')->value('value');
       
         return view('b2b::settings.app_version',compact('rider_app_live_version','rider_app_test_version','rider_live_latest_apk_url','rider_test_latest_apk_url',
                                                        'agent_app_live_version','agent_app_test_version','agent_live_latest_apk_url','agent_test_latest_apk_url','b2b_app_password'));
    }
    
    public function updateRiderAppSettings(Request $request)
{
    return $this->updateAppVersionSettings($request, 'b2b_rider');
}

public function updateAgentAppSettings(Request $request)
{
    return $this->updateAppVersionSettings($request, 'b2b_agent');
}

private function updateAppVersionSettings(Request $request, $prefix)
{
    $request->validate([
        "{$prefix}_app_live_version" => 'required|string|max:255',
        "{$prefix}_app_test_version" => 'required|string|max:255',
        "{$prefix}_live_latest_apk_url" => 'required|string',
        "{$prefix}_test_latest_apk_url" => 'required|string',
    ]);

    BusinessSetting::updateOrCreate(
        ['key_name' => "{$prefix}_app_live_version"],
        ['value' => $request->input("{$prefix}_app_live_version")]
    );

    BusinessSetting::updateOrCreate(
        ['key_name' => "{$prefix}_app_test_version"],
        ['value' => $request->input("{$prefix}_app_test_version")]
    );

    BusinessSetting::updateOrCreate(
        ['key_name' => "{$prefix}_live_latest_apk_url"],
        ['value' => $request->input("{$prefix}_live_latest_apk_url")]
    );

    BusinessSetting::updateOrCreate(
        ['key_name' => "{$prefix}_test_latest_apk_url"],
        ['value' => $request->input("{$prefix}_test_latest_apk_url")]
    );

    return response()->json([
        'status' => true,
        'message' => ucfirst(str_replace('_',' ',$prefix)) . ' App Version settings updated successfully!',
    ]);
}


    public function help(Request $request)
    {
         return view('b2b::help.help');
    }
    
    
    
    public function get_zones(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer', // Ensure the city ID is provided and is an integer
        ]);
    
        // Fetch areas based on the city ID
        $zones = Zones::where('city_id', $request->id)->get();
    
    
        // Generate the HTML for the dropdown options
        $options = ""; // Initialize an empty string for options
        foreach ($zones as $zone) {
            // Check if the current area should be selected (only if i_id is provided)
            $selected = ($request->has('i_id') && $request->i_id == $zone->id) ? 'selected' : '';
            $options .= "<option value='{$zone->id}' {$selected}>{$zone->name}</option>";
        }
    
        // Return the options as JSON response
        return response()->json([
            'status' => true,
            'data' => $options, // Include the HTML for the dropdown options
            'zones'=>$zones,
        ]);
    }
    
}
