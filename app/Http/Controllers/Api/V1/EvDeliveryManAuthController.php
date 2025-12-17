<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\Deliveryman\Entities\DeliveryManLogs;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Modules\Deliveryman\Entities\OtpVerification;
use Illuminate\Support\Str;
use Modules\Leads\Entities\leads;
use App\Models\EvGlobalAadhaarNo; //updated by gowtham.s
use App\Models\EvDeliveryMan; //updated by siva.m
use App\Models\EvGlobalAadhaarResponse;
use Illuminate\Support\Facades\Http;
use App\Models\EvGenerateId;
use App\Models\BusinessSetting;
use App\Models\EvAdhaarOtpVerifyLog;
use App\Models\EvLicenseVerifyLog;
use App\Mail\RiderRegisterationMail;
use App\Mail\RiderAdminNotificationMail;
use Illuminate\Support\Facades\Mail;
class EvDeliveryManAuthController extends Controller
{
    
    public function store(Request $request)
    {
        // Define the validation rules
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => [
                    'required',
                    'string',
                    'size:13', 
                    'regex:/^\+91[0-9]{10}$/',
                    // 'unique:ev_tbl_delivery_men,mobile_number',
                ],
            'gender' => 'required|in:male,female',
            'house_no' => 'required|string|max:255',
            'street_name' => 'required|string|max:255',
            'pincode' => ['required', 'digits:6', 'regex:/^[1-9][0-9]{5}$/'],
            'alternative_number' => [
                'required',
                'string',
                'size:13',
                'regex:/^\+91[0-9]{10}$/',
                'unique:ev_tbl_delivery_men,alternative_number',
            ],
            'email' => 'required|email|unique:ev_tbl_delivery_men,email',
            'current_city_id' => 'required|integer',
            'interested_city_id' => 'required|integer',
            'remarks' => 'nullable|string|max:500',
            'role_request'=>'required|in:deliveryman,in-house,adhoc,helper'
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422); // Unprocessable Entity
        }

        // Check if the Rider already exists
        $existingDeliveryMan = Deliveryman::where('mobile_number', $request->mobile_number)->first();
    
        // If the Rider exists
        if ($existingDeliveryMan) {
            // Check if the Rider is denied
            if ($existingDeliveryMan->approved_status == 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your registration is denied. Please contact the admin to approve your registration.',
                    'data' => $existingDeliveryMan,
                ], 403); // Forbidden
            }
    
            // Mobile number already exists, generate a new OTP
            // $otp = rand(1000, 9999);
            
            // // Check if an OTP entry already exists for this user
            // $otpVerification = OtpVerification::where('mobile_number', $existingDeliveryMan->mobile_number)
            //     ->first();
    
            // if ($otpVerification) {
            //     // Update existing OTP
            //     $otpVerification->otp = $otp;
            //     $otpVerification->save();
            // } else {
            //     // Create a new OTP verification record
            //     $otpVerification = new OtpVerification();
            //     $otpVerification->otp = $otp;
            //     $otpVerification->mobile_number = $request->mobile_number;
            //     $otpVerification->save();
            // }
    
            // Send the OTP to the user's mobile number here (via SMS service)
            // $this->sendsms($request->mobile_number,$otp);
            return response()->json([
                'success' => true,
                'message' => 'You are already registered. An OTP has been sent to your mobile number.',
                'data' => $existingDeliveryMan,
            ], 200); // OK
        }
    
        // If the Rider doesn't exist, create a new Rider
        try {
            $dm = new Deliveryman();
            $dm->first_name = $request->first_name;
            $dm->last_name = $request->last_name;
            $dm->mobile_number = $request->mobile_number;
            $dm->gender = $request->gender;
            $dm->house_no = $request->house_no;
            $dm->street_name = $request->street_name;
            $dm->pincode = $request->pincode;
            $dm->alternative_number = $request->alternative_number;
            $dm->email = $request->email;
            $dm->current_city_id = $request->current_city_id;
            $dm->interested_city_id = $request->interested_city_id;
            $dm->remarks = $request->remarks ?? null;
            $dm->work_type = $request->role_request;
            
            // Determine rider type code
            // $riderType = match($request->role_request) {
            //     'deliveryman' => 'R',
            //     'in-house'    => 'E',
            //     'adhoc'       => 'A',
            //     'helper'       => 'H',
            //     default       => 'N', // fallback
            // };
            
            // $id_prefix = 'GDMAPP' . $riderType;
            
            // // Get the last serial number (from all types globally)
            // $lastId = Deliveryman::where('delete_status', 0)
            //     ->orderByDesc('reg_application_id')
            //     ->value('reg_application_id');
            
            // $lastSerial = $lastId ? (int) substr($lastId, -5) : 0;
            
            // // Generate new unique ID
            // do {
            //     $lastSerial++;
            //     $newSerial = str_pad((string)$lastSerial, 5, '0', STR_PAD_LEFT);
            //     $reg_application_id = $id_prefix . $newSerial;
            
            //     // Ensure it's unique (rarely necessary due to sequential logic)
            //     $exists = Deliveryman::where('reg_application_id', $reg_application_id)->exists();
            // } while ($exists);
            
            // $dm->reg_application_id = $reg_application_id;

            // Save the new Rider
            $dm->save();
            
            $lead = new leads();
            $lead->telecaller_status = 'New';
            $lead->f_name = $request->first_name;
            $lead->l_name = $request->last_name;
            $lead->phone_number = $request->mobile_number;
            $lead->current_city = $request->current_city_id;
            $lead->intrested_city = $request->interested_city_id;
            $lead->register_date = Carbon::now();
            
            $lead->save();
    
            // // Generate OTP for the new Rider
            // $otp = rand(1000, 9999);
    
            // // Create a new OTP verification record
            // $verify = new OtpVerification();
            // $verify->otp = $otp;
            // $verify->mobile_number = $request->mobile_number;
            // $verify->save();
    
            // // Send the OTP to the user's mobile number here (via SMS service)
            // $this->sendsms($request->mobile_number,$otp);
            return response()->json([
                'success' => true,
                'data' => $dm,
                'message' => 'Rider created successfully.',
            ], 201); // Created
        } catch (\Exception $e) {
            Log::error('Error creating Deliveryman: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while creating the Rider. Please try again.',
            ], 500); 
        }
    }

    public function check_process(Request $request) 
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
            ], 422); // Unprocessable Entity
        }
        
        
        $mobile = trim($request->mobile_number);
        $test_numbers = ["+919790860187", "+917845284066", "+917812880655"];
        
        if (in_array($mobile, $test_numbers)) {
            $otp = 1234;
        }else{
            
            $otp = rand(1000, 9999); // Generate a random OTP
            // $otp = 1234;
        }
     
        $otpVerification = OtpVerification::where('mobile_number', $request->mobile_number)
            ->first();
    
        if ($otpVerification) {
            // Update existing OTP
            $otpVerification->otp = $otp;
            // $otpVerification->updated_at = now();
            $otpVerification->save();
        } else {
            // Create a new OTP verification record
            $otpVerification = new OtpVerification();
            $otpVerification->otp = $otp;
            $otpVerification->mobile_number = $request->mobile_number;
            $otpVerification->save();
        }
        
        $settings = \App\Models\EvApiClubSetting::pluck('value', 'key_name')->toArray(); //update by Gowtham.s
         $api_culb_setting_data = [
            'API_CLUB_PRODUCTION'=>$settings['API_CLUB_MODE'],
            'ADHAAR_CARD_VERIFY'=>$settings['ADHAAR_CARD_VERIFY'],
            'LICENSE_VERIFY'=>$settings['LICENSE_VERIFY'],
            'BANK_VERIFY'=>$settings['BANK_VERIFY'],
            'PAN_VERIFY'=>$settings['PAN_VERIFY'],
        ];

        $existingUser = Deliveryman::where('mobile_number', $request->mobile_number)->first();
        // if($existingUser){
        //     if($existingUser->rider_status == 1 && $existingUser->approved_status == 1){
        //          $this->sendsms($request->mobile_number,$otp);
        //     }
        // }else{
        //     $this->sendsms($request->mobile_number,$otp);
        // }
        
        if (!in_array($mobile, $test_numbers)) {
             $this->sendsms($request->mobile_number,$otp);
        }
       
     
        if (!$existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'You are not registered. Please register to proceed.',
                'approved_status' => 0,
                'api_culb_setting_data'=>$api_culb_setting_data//updated by Gowtham.s
            ], 400); // Bad Request
        }else if ($existingUser->approved_status == 2) {
            return response()->json([
                'success' => false,
                'message' => 'Your registration is denied. Please contact the admin to approve your registration.',
                'data' => $existingUser,
            ], 403); 
        } else{
            return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
            'data' => $existingUser,
            'api_culb_setting_data'=>$api_culb_setting_data
        ], 200);
        }
    }
    
    public function alternative_send_otp(Request $request) 
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
        
        $existingUser = Deliveryman::where('alternative_number', $request->mobile_number)->first();
       
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'This number is already in use. Please provide a different alternative number.',
            ], 400);
        }
        
        $existingUser1 = Deliveryman::where('mobile_number', $request->mobile_number)->first();
         if ($existingUser1) {
            return response()->json([
                'success' => false,
                'message' => 'This number is already in use. Please provide a different alternative number.',
            ], 400);
        }


        
        $otp = rand(1000, 9999);
        $otpVerification = OtpVerification::where('mobile_number', $request->mobile_number)
            ->first();
    
        if ($otpVerification) {
            $otpVerification->otp = $otp;
            $otpVerification->updated_at = now();
            $otpVerification->save();
        } else {
            $otpVerification = new OtpVerification();
            $otpVerification->otp = $otp;
            $otpVerification->mobile_number = $request->mobile_number;
            $otpVerification->save();
        }
        
        $this->sendsms($request->mobile_number,$otp);
            
        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.'
        ], 200);
        
        
        
     
        // if (!$existingUser) {
            // return response()->json([
            //     'success' => false,
            //     'message' => 'You are not registered. Please register to proceed.',
            //     'approved_status' => 0
            // ], 400);
        // }else if ($existingUser->approved_status == 2) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Your registration is denied. Please contact the admin to approve your registration.',
        //         'data' => $existingUser,
        //     ], 403); 
        // } else{
            
            
        // }
    }
    
    public function referal_person_send_otp(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'referal_person_number' => [
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
        
        $mobile = trim($request->referal_person_number);
        $test_numbers = ["+919790860187", "+917845284066", "+917812880655"];
        
        if (in_array($mobile, $test_numbers)) {
            $otp = 1234;
        } else {
            $otp = rand(1000, 9999);
        }

        $otpVerification = OtpVerification::where('mobile_number', $request->referal_person_number)
            ->first();
    
        if ($otpVerification) {
            $otpVerification->otp = $otp;
            $otpVerification->updated_at = now();
            $otpVerification->save();
        } else {
            $otpVerification = new OtpVerification();
            $otpVerification->otp = $otp;
            $otpVerification->mobile_number = $request->referal_person_number;
            $otpVerification->save();
        }
        if (!in_array($mobile, $test_numbers)) {
           $this->sendsms($request->referal_person_number,$otp);
        }
            
        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.'
        ], 200);
    }

    // public function register(Request $request)
    // {
    //     Log::info("Deliveryman Store Json Data Test :".json_encode($request->all()));
        

    //     // Define the validation rules
    //     $validator = Validator::make($request->all(), [
    //         'id' => 'required',
    //         'apply_job_source' => 'required|string|max:255',
    //         'referral' => 'nullable|string|max:255',
    //         'job_agency' => 'nullable|string|max:255',
    //         'photo' => 'required|image|max:10240', // Example for image upload
    //         'aadhar_card_front' => 'required|image',//1MB Accept
    //         'aadhar_card_back' => 'required|image',//1MB Accept
    //         'aadhar_number' => 'required|string|max:12',
    //         'pan_card_front' => 'required|image',//1MB Accept
    //         // 'pan_card_back' => 'required|image|max:10240',
    //         'pan_number' => 'required|string|max:10',
    //         // 'license_number' => 'required|string|max:16',
    //         'driving_license_front' => 'nullable|image',//1MB Accept
    //         'driving_license_back' => 'nullable|image',//1MB Accept
    //         'bank_passbook' => 'required|image',//1MB Accept
    //         'bank_name' => 'required|string|max:255',
    //         'ifsc_code' => 'required|string',
    //         'account_number' => 'required|string|max:20',
    //         'account_holder_name' => 'required|string|max:255',
    //         // 'date_of_birth' => 'required|date',
    //         'date_of_birth' => 'required|date|before:today',
    //         'present_address' => 'required|string|max:500',
    //         'permanent_address' => 'required|string|max:500',
    //         'father_name' => 'nullable|string|max:255',
    //         'father_mobile_number' => 'nullable|string|max:13',
    //         'mother_name' => 'nullable|string|max:255',
    //         'mother_mobile_number' => 'nullable|string|max:13',
    //         'referal_person_number' => 'nullable|string|max:255', 
    //         'referal_person_name' => 'nullable|string|max:255',
    //         'referal_person_relationship' => 'nullable|string|max:255',
    //         'spouse_name' => 'nullable|string|max:255',
    //         'spouse_mobile_number' => 'nullable|string|max:13',
    //         'emergency_contact_person_1_name' => 'nullable|string|max:255',
    //         'emergency_contact_person_1_mobile' => 'nullable|string|max:13',
    //         'emergency_contact_person_2_name' => 'nullable|string|max:255',
    //         'emergency_contact_person_2_mobile' => 'nullable|string|max:13',
    //         'blood_group' => 'required|string|max:3',
    //         'emp_prev_company_id'=>'nullable',
    //         'emp_prev_experience'=>'nullable',
    //         'social_links'=>'nullable',
    //         // 'bank_statements' => 'nullable|mimes:pdf',
    //         'bank_statements' => 'nullable',//1MB Accept
    //         'fcm_token' => 'nullable',
    //         'vehicle_type' => 'nullable|string|max:50',
    //         'rider_type' => 'nullable|string|max:50',
    //         'license_number' => 'nullable|unique:ev_tbl_delivery_men,license_number',
    //         'marital_status' => 'nullable,',
    //         'work_type'=>'required|in:deliveryman,in-house,adhoc,helper',
    //     ]);
    
    //   if ($request->work_type == "deliveryman" || $request->work_type == "adhoc") {
           
    //       if(strtolower($request->llr) === "true"){
               
    //             $validator->addRules([
    //             'license_number' => 'required|unique:ev_tbl_delivery_men,llr_number',
    //             'driving_license_front' => 'required|mimes:jpg,jpeg,png,pdf'//1MB Accept
                
    //             ]);
               
    //               $validator->setCustomMessages([
    //                 'license_number.required' => 'LLR Number is required.',
    //                 'license_number.unique' => 'LLR Number must be unique.',
    //                 'driving_license_front.required' => 'LLR image is required.',
    //                 'driving_license_front.max' => 'LLR image must not be greater than 1MB.',
    //             ]);
    
    //       }
    //       else{
               
    //             $validator->addRules([
    //                 'license_number' => 'required|unique:ev_tbl_delivery_men,license_number',
    //                 'driving_license_front' => 'required|image',//1MB Accept
    //                 'driving_license_back' => 'required|image'//1MB Accept
                    
    //             ]);
                
    //             $validator->setCustomMessages([
    //             'license_number.required' => 'License Number is required.',
    //             'license_number.unique' => 'License Number must be unique.',
    //             'driving_license_front.required' => 'Driving License Front Image is required.',
    //             'driving_license_back.required' => 'Driving License Back Image is required.',
    //             'driving_license_front.max' => 'Driving License Front image must not be greater than 10MB.',
    //             'driving_license_back.max' => 'Driving License Back image must not be greater than 10MB.',
    //         ]);
            
    //       }
           
    //         $validator->addRules([
    //             'vehicle_type' => 'required|string|max:50',
    //             'rider_type' => 'required|string|max:50',
                
    //         ]);
            
            
    //     }
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'errors' => $validator->errors(),
    //         ], 422); 
    //     }
    //     // Log::info("Deliveryman Store Json Data :".json_encode($request->all()));
    //     try {
            
    //         // if ($request->work_type == "deliveryman") {
    //         //     $emp_id = $this->get_deliveryman_permanentid_count($request->work_type);
    //         // }
    //         // if ($request->work_type == 'in-house') {
    //         //     $emp_id = $this->get_employee_permanentid_count($request->work_type);
    //         // }




    //     $settings = \App\Models\EvApiClubSetting::pluck('value', 'key_name')->toArray();
        
    //     if ($settings['DL_NAME_VERIFY'] == "1") {
        
        
    //     if (!empty($request->aadhar_number) && !empty($request->license_number)) {
        
    //         $adhaar_values = EvAdhaarOtpVerifyLog::where('adhaar_no', $request->aadhar_number)
    //             ->orderByDesc('id')
    //             ->first();
        
    //         $responseData = json_decode($adhaar_values->response, true);
    //         $adhaar_fullName = $responseData['response']['name'] ?? '';
        
    //         $license_values = EvLicenseVerifyLog::where('license_number', $request->license_number)
    //             ->orderByDesc('id')
    //             ->first();
        
    //         $license_fullName = $license_values->holder_name ?? '';
        
    //         // Normalize and split names
    //         $normalize = function ($name) {
    //             $name = trim(preg_replace('/\s+/u', ' ', strtolower($name)));
    //             return array_values(array_filter(
    //                 preg_split('/[\s.]+/', $name),
    //                 fn($part) => mb_strlen($part) >= 3
    //             ));
    //         };
        
    //         $adhaarParts = $normalize($adhaar_fullName);   
    //         $licenseParts = $normalize($license_fullName); 
        
    //         $matchCount = 0;
    //         $totalParts = max(count($adhaarParts), count($licenseParts));
        
    //         foreach ($adhaarParts as $aPart) {
    //             foreach ($licenseParts as $lPart) {
    //                 if (
    //                     mb_strlen($aPart) >= 3 &&
    //                     mb_strlen($lPart) >= 3 &&
    //                     (Str::startsWith($aPart, $lPart) || Str::startsWith($lPart, $aPart))
    //                 ) {
    //                     $matchCount++;
    //                     break; // one match per Aadhaar part
    //                 }
    //             }
    //         }
        
    //         $matchPercentage = $totalParts > 0 ? ($matchCount / $totalParts) * 100 : 0;
        
        
    //         if ($matchPercentage < 50) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Names do not match',
    //             ], 422);
    //         }
    //     }

        
    //     }
        
        

    //         $dm = Deliveryman::findOrFail($request->id);
    //         $dm->apply_job_source = $request->apply_job_source;
    //         // $dm->emp_id = $emp_id ?? null;
    //         $dm->emp_id_status = 0;
    //         $dm->referral = $request->referral;
    //         $dm->job_agency = $request->job_agency;
            
    //         if(strtolower($request->llr) === "true"){
    //             $dm->llr_number = $request->license_number ?? null;
    //         }
    //         else{
    //             $dm->license_number = $request->license_number ?? null;
    //         }
            
            
            
            
    //         if(isset($request->first_name) && $request->first_name != ""){ //must be adhaar first name only update
    //             $dm->first_name = $request->first_name;
    //         }
            
    //         if(isset($request->last_name) && $request->last_name != ""){ //must be adhaar last name only update
    //             $dm->last_name = $request->last_name;
    //         }
            
    
    //         // Handle file uploads
    //         if ($request->hasFile('photo')) {
    //             $dm->photo = $this->uploadFile($request->file('photo'), 'EV/images/photos');
    //         }
    //         if ($request->hasFile('bank_statements')) {
    //             $dm->bank_statements = $this->uploadFile($request->file('bank_statements'), 'EV/images/bank_statements');
    //         }
    //         if ($request->hasFile('aadhar_card_front')) {
    //             $dm->aadhar_card_front = $this->uploadFile($request->file('aadhar_card_front'), 'EV/images/aadhar');
    //         }
    //         if ($request->hasFile('aadhar_card_back')) {
    //             $dm->aadhar_card_back = $this->uploadFile($request->file('aadhar_card_back'), 'EV/images/aadhar');
    //         }
    //         if ($request->hasFile('pan_card_front')) {
    //             $dm->pan_card_front = $this->uploadFile($request->file('pan_card_front'), 'EV/images/pan');
    //         }
    //         if ($request->hasFile('pan_card_back')) {
    //             $dm->pan_card_back = $this->uploadFile($request->file('pan_card_back'), 'EV/images/pan');
    //         }
            
    //         if(strtolower($request->llr) === "true"){
                
    //             if ($request->hasFile('driving_license_front')) {
    //             $dm->llr_image = $this->uploadFile($request->file('driving_license_front'), 'EV/images/llr_images');
    //             }else{
    //                 $dm->llr_image = null;
    //             }
    //         }
    //         else{
                
    //             if ($request->hasFile('driving_license_front')) {
    //             $dm->driving_license_front = $this->uploadFile($request->file('driving_license_front'), 'EV/images/driving_license') ?? null;
    //             }else{
    //                 $dm->driving_license_front = null;
    //             }
                
    //         }
            

            
            
            
            
    //         if ($request->hasFile('driving_license_back')) {
    //             $dm->driving_license_back = $this->uploadFile($request->file('driving_license_back'), 'EV/images/driving_license');
    //         }else{
    //             $dm->driving_license_back = null;
    //         }
    //         if ($request->hasFile('bank_passbook')) {
    //             $dm->bank_passbook = $this->uploadFile($request->file('bank_passbook'), 'EV/images/bank_passbook');
    //         }
            

    
    //         // Save additional fields
    //         $dm->aadhar_number = $request->aadhar_number;
    //         // $dm->license_number = $request->license_number;
    //         $dm->pan_number = $request->pan_number;
    //         $dm->bank_name = $request->bank_name;
    //         $dm->ifsc_code = $request->ifsc_code;
    //         $dm->account_number = $request->account_number;
    //         $dm->account_holder_name = $request->account_holder_name;
    //         $dm->date_of_birth = $request->date_of_birth;
    //         $dm->present_address = $request->present_address;
    //         $dm->permanent_address = $request->permanent_address;
    //         $dm->father_name = $request->father_name;
    //         $dm->father_mobile_number = $request->father_mobile_number;
    //         $dm->mother_name = $request->mother_name;
    //         $dm->mother_mobile_number = $request->mother_mobile_number;
    //         $dm->marital_status = $request->marital_status ?? 0;
    //         $dm->spouse_name = $request->spouse_name;
    //         $dm->spouse_mobile_number = $request->spouse_mobile_number;
    //         $dm->emergency_contact_person_1_name = $request->emergency_contact_person_1_name;
    //         $dm->emergency_contact_person_1_mobile = $request->emergency_contact_person_1_mobile;
    //         $dm->emergency_contact_person_2_name = $request->emergency_contact_person_2_name;
    //         $dm->emergency_contact_person_2_mobile = $request->emergency_contact_person_2_mobile;
    //         $dm->blood_group = $request->blood_group;
    //         $dm->emp_prev_company_id = $request->emp_prev_company_id;
    //         $dm->emp_prev_experience = $request->emp_prev_experience;
    //         $dm->social_links = $request->social_links;
    //         $dm->fcm_token = $request->fcm_token ?? null;
    //         $dm->referal_person_name = $request->referal_person_name;
    //         $dm->referal_person_number = $request->referal_person_number;
    //         $dm->referal_person_relationship = $request->referal_person_relationship;
    //         $dm->vehicle_type = $request->vehicle_type ?? null;
    //         $dm->rider_type = $request->rider_type ?? null;
    //         $dm->register_date_time = Carbon::now();
            
    //       if(isset($request->work_type) && $request->work_type == "in-house") { // Updated by Gowtham S.
    //             $dm->work_type = 'in-house';
    //         } else if(isset($request->work_type) && $request->work_type == "adhoc") { 
    //             $dm->work_type = 'adhoc';
    //         }else if(isset($request->work_type) && $request->work_type == "helper") { 
    //             $dm->work_type = 'helper';
    //         }else {
    //             $dm->work_type = $request->work_type;
    //         }

    //         // if(isset($request->work_type) && $request->work_type == "deliveryman"){//updated by Gowtham.s
    //         //   Log::info("If Api Verify deliveryman by Type :".$request->work_type);
    //         //     $admin = \DB::table('users')->where('id',1)->first();
    //         //     $dm->aadhar_verify = 1;
    //         //     $dm->pan_verify = 1;
    //         //     $dm->lisence_verify = 1;
    //         //     $dm->bank_verify = 1;
                
    //         //     $dm->aadhar_verify_date = now();
    //         //     $dm->pan_verify_date = now();
    //         //     $dm->lisence_verify_date = now();
    //         //     $dm->bank_verify_date = now();
    //         //     $dm->who_verify = $admin->name ?? null;
    //         //     $dm->who_verify_id = $admin->id ?? null;
    //         // }else{
    //         //     Log::info("Else Api deliveryman Verify by Type :".$request->work_type);
    //         // }
            
    //         //  if(isset($request->work_type) && $request->work_type == "adhoc"){//updated by Gowtham.s
    //         //   Log::info("If Api Verify Adhoc by Type :".$request->work_type);
    //         //     $admin = \DB::table('users')->where('id',1)->first();
    //         //     $dm->aadhar_verify = 1;
    //         //     $dm->pan_verify = 1;
    //         //     $dm->lisence_verify = 1;
    //         //     $dm->bank_verify = 1;
                
    //         //     $dm->aadhar_verify_date = now();
    //         //     $dm->pan_verify_date = now();
    //         //     $dm->lisence_verify_date = now();
    //         //     $dm->bank_verify_date = now();
    //         //     $dm->who_verify = $admin->name ?? null;
    //         //     $dm->who_verify_id = $admin->id ?? null;
    //         // }else{
    //         //     Log::info("Else Api Verify Adhoc by Type :".$request->work_type);
    //         // }
            
            
    //       $riderType = match($dm->work_type) {
    //             'deliveryman' => 'R',
    //             'in-house'    => 'E',
    //             'adhoc'       => 'A',
    //             'helper'       => 'H',
    //             default       => 'N/A',
    //         };
            
    //         $id_start = 'GDMAPP' . $riderType;
            
    //         // Get the last serial number for the same type
    //         $lastId = Deliveryman::where('delete_status', 0)
    //             ->where('reg_application_id', 'like', $id_start . '%')
    //             ->orderByDesc('reg_application_id')
    //             ->value('reg_application_id');
            
    //         // Start from last serial number or 1
    //         $lastSerial = $lastId ? (int)substr($lastId, -5) : 0;
            
    //         do {
    //             $lastSerial++;
    //             $newSerial = str_pad((string)$lastSerial, 5, '0', STR_PAD_LEFT);
    //             $reg_application_id = $id_start . $newSerial;
            
    //             // Check uniqueness
    //             $exists = Deliveryman::where('reg_application_id', $reg_application_id)->exists();
    //         } while ($exists);
            
    //         $dm->reg_application_id = $reg_application_id;

            
    //         // Save the model
    //         $dm->save();
            
    //         $this->admin_message($dm->mobile_number);
    //         $this->rider_message($dm->mobile_number);
            
    //         $rider_mail = $dm->email ?? '';
    //         if ($rider_mail) {
    //           Mail::to($rider_mail)->send(new RiderRegisterationMail($dm)); // rider mail
    //         }
    //         $admin_email = BusinessSetting::where('key_name', 'admin_sender_mail')->value('value');
             
    //          if ($admin_email) {
    //             Mail::to($admin_email)->send(new RiderAdminNotificationMail($dm)); // admin mail
    //         }
            
    //         return response()->json([
    //             'success' => true,
    //             'data' => $dm,
    //             'message' => 'Rider registered successfully.',
    //         ], 200); 
            
            
            
    //     } catch (\Exception $e) {
    //         // Log the error
    //         \Log::error('Error registering Rider: ' . $e->getMessage());
    
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to register Rider. Please try again.',
    //             'error' => $e->getMessage(),
    //         ], 500); // Internal Server Error
    //     }
    // }
    
    
    public function register(Request $request)
    {
        Log::info("Deliveryman Store Json Data :".json_encode($request->all()));
        

        // Define the validation rules
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'apply_job_source' => 'required|string|max:255',
            'referral' => 'nullable|string|max:255',
            'job_agency' => 'nullable|string|max:255',
            'photo' => 'required|image', // //1MB Accept
            'aadhar_card_front' => 'required|image',//1MB Accept
            'aadhar_card_back' => 'required|image',
            'aadhar_number' => 'required|string|max:12',
            'pan_card_front' => 'required|image',//1MB Accept
            // 'pan_card_back' => 'required|image|max:1024',//1MB Accept
            'pan_number' => 'required|string|max:10',
            // 'license_number' => 'required|string|max:16',
            'driving_license_front' => 'nullable|image',//1MB Accept
            'driving_license_back' => 'nullable|image',//1MB Accept
            'bank_passbook' => 'required|image',//1MB Accept
            'bank_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string',
            'account_number' => 'required|string|max:20',
            'account_holder_name' => 'required|string|max:255',
            // 'date_of_birth' => 'required|date',
            'date_of_birth' => 'required|date|before:today',
            'present_address' => 'required|string|max:500',
            'permanent_address' => 'required|string|max:500',
            'father_name' => 'nullable|string|max:255',
            'father_mobile_number' => 'nullable|string|max:13',
            'mother_name' => 'nullable|string|max:255',
            'mother_mobile_number' => 'nullable|string|max:13',
            'referal_person_number' => 'nullable|string|max:255', 
            'referal_person_name' => 'nullable|string|max:255',
            'referal_person_relationship' => 'nullable|string|max:255',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_mobile_number' => 'nullable|string|max:13',
            'emergency_contact_person_1_name' => 'nullable|string|max:255',
            'emergency_contact_person_1_mobile' => 'nullable|string|max:13',
            'emergency_contact_person_2_name' => 'nullable|string|max:255',
            'emergency_contact_person_2_mobile' => 'nullable|string|max:13',
            'blood_group' => 'required|string|max:3',
            'emp_prev_company_id'=>'nullable',
            'emp_prev_experience'=>'nullable',
            'social_links'=>'nullable',
            // 'bank_statements' => 'nullable|mimes:pdf',
            'bank_statements' => 'nullable',//1MB Accept
            'fcm_token' => 'nullable',
            'vehicle_type' => 'nullable|string|max:50',
            'rider_type' => 'nullable|string|max:50',
            'license_number' => 'nullable|unique:ev_tbl_delivery_men,license_number',
            'marital_status' => 'nullable,',
            'work_type'=>'required|in:deliveryman,in-house,adhoc,helper',
        ]);
    
       if ($request->work_type == "deliveryman" || $request->work_type == "adhoc") {
           
           if(strtolower($request->llr) === "true"){
               
                $validator->addRules([
                'license_number' => 'required|unique:ev_tbl_delivery_men,llr_number',
                'driving_license_front' => 'required|mimes:jpg,jpeg,png,pdf',//1MB Accept
                
                ]);
               
                   $validator->setCustomMessages([
                    'license_number.required' => 'LLR Number is required.',
                    'license_number.unique' => 'LLR Number must be unique.',
                    'driving_license_front.required' => 'LLR image is required.',
                    'driving_license_front.max' => 'LLR image must not be greater than 1MB.',
                ]);
    
           }
           else{
               
                $validator->addRules([
                    'license_number' => 'required|unique:ev_tbl_delivery_men,license_number',
                    'driving_license_front' => 'required|image',//1MB Accept
                    'driving_license_back' => 'required|image'//1MB Accept
                    
                ]);
                
                $validator->setCustomMessages([
                'license_number.required' => 'License Number is required.',
                'license_number.unique' => 'License Number must be unique.',
                'driving_license_front.required' => 'Driving License Front Image is required.',
                'driving_license_back.required' => 'Driving License Back Image is required.',
                'driving_license_front.max' => 'Driving License Front image must not be greater than 10MB.',
                'driving_license_back.max' => 'Driving License Back image must not be greater than 10MB.',
            ]);
            
           }
           
            $validator->addRules([
                'vehicle_type' => 'required|string|max:50',
                'rider_type' => 'required|string|max:50',
                
            ]);
            
            
        }
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422); 
        }
        // Log::info("Deliveryman Store Json Data :".json_encode($request->all()));
        try {
            
            // if ($request->work_type == "deliveryman") {
            //     $emp_id = $this->get_deliveryman_permanentid_count($request->work_type);
            // }
            // if ($request->work_type == 'in-house') {
            //     $emp_id = $this->get_employee_permanentid_count($request->work_type);
            // }




        $settings = \App\Models\EvApiClubSetting::pluck('value', 'key_name')->toArray();
        
        if ($settings['DL_NAME_VERIFY'] == "1") {
        
        
        if (!empty($request->aadhar_number) && !empty($request->license_number)) {
        
            $adhaar_values = EvAdhaarOtpVerifyLog::where('adhaar_no', $request->aadhar_number)
                ->orderByDesc('id')
                ->first();
        
            $responseData = json_decode($adhaar_values->response, true);
            $adhaar_fullName = $responseData['response']['name'] ?? '';
        
            $license_values = EvLicenseVerifyLog::where('license_number', $request->license_number)
                ->orderByDesc('id')
                ->first();
        
            $license_fullName = $license_values->holder_name ?? '';
        
            // Normalize and split names
            $normalize = function ($name) {
                $name = trim(preg_replace('/\s+/u', ' ', strtolower($name)));
                return array_values(array_filter(
                    preg_split('/[\s.]+/', $name),
                    fn($part) => mb_strlen($part) >= 3
                ));
            };
        
            $adhaarParts = $normalize($adhaar_fullName);   
            $licenseParts = $normalize($license_fullName); 
        
            $matchCount = 0;
            $totalParts = max(count($adhaarParts), count($licenseParts));
        
            foreach ($adhaarParts as $aPart) {
                foreach ($licenseParts as $lPart) {
                    if (
                        mb_strlen($aPart) >= 3 &&
                        mb_strlen($lPart) >= 3 &&
                        (Str::startsWith($aPart, $lPart) || Str::startsWith($lPart, $aPart))
                    ) {
                        $matchCount++;
                        break; // one match per Aadhaar part
                    }
                }
            }
        
            $matchPercentage = $totalParts > 0 ? ($matchCount / $totalParts) * 100 : 0;
        
        
            if ($matchPercentage < 50) {
                return response()->json([
                    'success' => false,
                    'message' => 'Names do not match',
                ], 422);
            }
        }

        
        }
        
        

            $dm = Deliveryman::findOrFail($request->id);
            $dm->apply_job_source = $request->apply_job_source;
            // $dm->emp_id = $emp_id ?? null;
            $dm->emp_id_status = 0;
            $dm->referral = $request->referral;
            $dm->job_agency = $request->job_agency;
            
            if(strtolower($request->llr) === "true"){
                $dm->llr_number = $request->license_number ?? null;
            }
            else{
                $dm->license_number = $request->license_number ?? null;
            }
            
            
            
            
            if(isset($request->first_name) && $request->first_name != ""){ //must be adhaar first name only update
                $dm->first_name = $request->first_name;
            }
            
            if(isset($request->last_name) && $request->last_name != ""){ //must be adhaar last name only update
                $dm->last_name = $request->last_name;
            }
            
    
            // Handle file uploads
            if ($request->hasFile('photo')) {
                $dm->photo = $this->uploadFile($request->file('photo'), 'EV/images/photos');
            }
            if ($request->hasFile('bank_statements')) {
                $dm->bank_statements = $this->uploadFile($request->file('bank_statements'), 'EV/images/bank_statements');
            }
            if ($request->hasFile('aadhar_card_front')) {
                $dm->aadhar_card_front = $this->uploadFile($request->file('aadhar_card_front'), 'EV/images/aadhar');
            }
            if ($request->hasFile('aadhar_card_back')) {
                $dm->aadhar_card_back = $this->uploadFile($request->file('aadhar_card_back'), 'EV/images/aadhar');
            }
            if ($request->hasFile('pan_card_front')) {
                $dm->pan_card_front = $this->uploadFile($request->file('pan_card_front'), 'EV/images/pan');
            }
            if ($request->hasFile('pan_card_back')) {
                $dm->pan_card_back = $this->uploadFile($request->file('pan_card_back'), 'EV/images/pan');
            }
            
            if(strtolower($request->llr) === "true"){
                
                if ($request->hasFile('driving_license_front')) {
                $dm->llr_image = $this->uploadFile($request->file('driving_license_front'), 'EV/images/llr_images');
                }else{
                    $dm->llr_image = null;
                }
            }
            else{
                
                if ($request->hasFile('driving_license_front')) {
                $dm->driving_license_front = $this->uploadFile($request->file('driving_license_front'), 'EV/images/driving_license') ?? null;
                }else{
                    $dm->driving_license_front = null;
                }
                
            }
            

            
            
            
            
            if ($request->hasFile('driving_license_back')) {
                $dm->driving_license_back = $this->uploadFile($request->file('driving_license_back'), 'EV/images/driving_license');
            }else{
                $dm->driving_license_back = null;
            }
            if ($request->hasFile('bank_passbook')) {
                $dm->bank_passbook = $this->uploadFile($request->file('bank_passbook'), 'EV/images/bank_passbook');
            }
            

    
            // Save additional fields
            $dm->aadhar_number = $request->aadhar_number;
            // $dm->license_number = $request->license_number;
            $dm->pan_number = $request->pan_number;
            $dm->bank_name = $request->bank_name;
            $dm->ifsc_code = $request->ifsc_code;
            $dm->account_number = $request->account_number;
            $dm->account_holder_name = $request->account_holder_name;
            $dm->date_of_birth = $request->date_of_birth;
            $dm->present_address = $request->present_address;
            $dm->permanent_address = $request->permanent_address;
            $dm->father_name = $request->father_name;
            $dm->father_mobile_number = $request->father_mobile_number;
            $dm->mother_name = $request->mother_name;
            $dm->mother_mobile_number = $request->mother_mobile_number;
            $dm->marital_status = $request->marital_status ?? 0;
            $dm->spouse_name = $request->spouse_name;
            $dm->spouse_mobile_number = $request->spouse_mobile_number;
            $dm->emergency_contact_person_1_name = $request->emergency_contact_person_1_name;
            $dm->emergency_contact_person_1_mobile = $request->emergency_contact_person_1_mobile;
            $dm->emergency_contact_person_2_name = $request->emergency_contact_person_2_name;
            $dm->emergency_contact_person_2_mobile = $request->emergency_contact_person_2_mobile;
            $dm->blood_group = $request->blood_group;
            $dm->emp_prev_company_id = $request->emp_prev_company_id;
            $dm->emp_prev_experience = $request->emp_prev_experience;
            $dm->social_links = $request->social_links;
            $dm->fcm_token = $request->fcm_token ?? null;
            $dm->referal_person_name = $request->referal_person_name;
            $dm->referal_person_number = $request->referal_person_number;
            $dm->referal_person_relationship = $request->referal_person_relationship;
            $dm->vehicle_type = $request->vehicle_type ?? null;
            $dm->rider_type = $request->rider_type ?? null;
            $dm->register_date_time = Carbon::now();
            
           if(isset($request->work_type) && $request->work_type == "in-house") { // Updated by Gowtham S.
                $dm->work_type = 'in-house';
            } else if(isset($request->work_type) && $request->work_type == "adhoc") { 
                $dm->work_type = 'adhoc';
            }else if(isset($request->work_type) && $request->work_type == "helper") { 
                $dm->work_type = 'helper';
            }else {
                $dm->work_type = $request->work_type;
            }

            // if(isset($request->work_type) && $request->work_type == "deliveryman"){//updated by Gowtham.s
            //   Log::info("If Api Verify deliveryman by Type :".$request->work_type);
            //     $admin = \DB::table('users')->where('id',1)->first();
            //     $dm->aadhar_verify = 1;
            //     $dm->pan_verify = 1;
            //     $dm->lisence_verify = 1;
            //     $dm->bank_verify = 1;
                
            //     $dm->aadhar_verify_date = now();
            //     $dm->pan_verify_date = now();
            //     $dm->lisence_verify_date = now();
            //     $dm->bank_verify_date = now();
            //     $dm->who_verify = $admin->name ?? null;
            //     $dm->who_verify_id = $admin->id ?? null;
            // }else{
            //     Log::info("Else Api deliveryman Verify by Type :".$request->work_type);
            // }
            
            //  if(isset($request->work_type) && $request->work_type == "adhoc"){//updated by Gowtham.s
            //   Log::info("If Api Verify Adhoc by Type :".$request->work_type);
            //     $admin = \DB::table('users')->where('id',1)->first();
            //     $dm->aadhar_verify = 1;
            //     $dm->pan_verify = 1;
            //     $dm->lisence_verify = 1;
            //     $dm->bank_verify = 1;
                
            //     $dm->aadhar_verify_date = now();
            //     $dm->pan_verify_date = now();
            //     $dm->lisence_verify_date = now();
            //     $dm->bank_verify_date = now();
            //     $dm->who_verify = $admin->name ?? null;
            //     $dm->who_verify_id = $admin->id ?? null;
            // }else{
            //     Log::info("Else Api Verify Adhoc by Type :".$request->work_type);
            // }
            
            
          $riderType = match($dm->work_type) {
                'deliveryman' => 'R',
                'in-house'    => 'E',
                'adhoc'       => 'A',
                'helper'       => 'H',
                default       => 'N/A',
            };
            
            $id_start = 'GDMAPP' . $riderType;
            
            // Get the last serial number for the same type
            $lastId = Deliveryman::where('delete_status', 0)
                ->where('reg_application_id', 'like', $id_start . '%')
                ->orderByDesc('reg_application_id')
                ->value('reg_application_id');
            
            // Start from last serial number or 1
            $lastSerial = $lastId ? (int)substr($lastId, -5) : 0;
            
            do {
                $lastSerial++;
                $newSerial = str_pad((string)$lastSerial, 5, '0', STR_PAD_LEFT);
                $reg_application_id = $id_start . $newSerial;
            
                // Check uniqueness
                $exists = Deliveryman::where('reg_application_id', $reg_application_id)->exists();
            } while ($exists);
            
            $dm->reg_application_id = $reg_application_id;

            
            // Save the model
            $dm->save();
            
            $this->admin_message($dm->mobile_number);
            $this->rider_message($dm->mobile_number);
            
            $rider_mail = $dm->email ?? '';
            if ($rider_mail) {
              Mail::to($rider_mail)->send(new RiderRegisterationMail($dm)); // rider mail
            }
            $admin_email = BusinessSetting::where('key_name', 'admin_sender_mail')->value('value');
             
             if ($admin_email) {
                Mail::to($admin_email)->send(new RiderAdminNotificationMail($dm)); // admin mail
            }
            
            return response()->json([
                'success' => true,
                'data' => $dm,
                'message' => 'Rider registered successfully.',
            ], 200); 
            
            
            
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error registering Rider: ' . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to register Rider. Please try again.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error
        }
    }
    
   public function profile_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:ev_tbl_delivery_men,id',
            'photo' => 'required|image|max:10240',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $dm = Deliveryman::findOrFail($request->id);
        if ($request->hasFile('photo')) {
            if ($dm->photo) {
                $oldPath = public_path('EV/images/photos/' . $dm->photo);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $dm->photo = $this->uploadFile($request->file('photo'), 'EV/images/photos');
        }
        $dm->save();
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'photo' => url('public/EV/images/photos/' . $dm->photo)
        ], 200);
    }
    
    private function get_deliveryman_permanentid_count($work_type)
    {
        $count = Deliveryman::where('work_type', $work_type)->count();
        return $count == 0 ? 'GDC-1001' : 'GDC-' . (1001 + $count);
    }

    
    private function get_employee_permanentid_count($work_type)
    {
        $count = Deliveryman::where('work_type', $work_type)->count();
        return $count == 0 ? 'EMP-1001' : 'EMP-' . (1001 + $count);
    }

    
    public function uploadFile($file, $directory)
    {
        $imageName = Str::uuid(). '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $imageName);
        return $imageName; // Return the name of the uploaded file
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
        $otpVerification = OtpVerification::where('mobile_number',$request->mobile_number)
            ->where('otp', $request->otp)
            ->first();
    
        // Check if the OTP exists and if the mobile number matches
        if ($otpVerification) {
            // Optionally, you may want to check if the OTP is still valid based on your logic
    
            // Here, you can perform any additional actions upon successful OTP verification
            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.',
            ], 200); // OK
        } else {
            // OTP verification failed
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP or mobile number.',
            ], 400); // Bad Request
        }
    }
    
    public function approved_status(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422); // Unprocessable Entity
        }
        
       try {
            $dm = Deliveryman::findOrFail($request->id);
    
            if ($dm->approved_status == 0) {
                return response()->json([
                    'message' => 'Your registration is pending.',
                    'status' => $dm->approved_status,
                ], 200);
            } elseif ($dm->approved_status == 2) {
                return response()->json([
                    'message' => 'Your registration is denied. Please contact admin.',
                    'status' => $dm->approved_status,
                ], 200);
            } elseif ($dm->approved_status == 1) {
                return response()->json([
                    'message' => 'You are approved as a deliveryman.',
                    'status' => $dm->approved_status,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Invalid status.',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Deliveryman not found.',
                'exception' => $e->getMessage(),
            ], 404);
        }
        
    }
    
   public function rider_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'rider_status' => 'required|string',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422); // Unprocessable Entity
        }
    
        $dm = Deliveryman::findOrFail($request->id);
        $dm->rider_status = $request->rider_status;
        $dm->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Rider status updated successfully!',
            'data' => $dm,
        ],200);
    }
    
    public function active_inactive(Request $request, $id)
    {
        Log::info("Active Inactive Api is Called ID:".$id);
        $dm = Deliveryman::find($id);
    
        if (!$dm) {
            return response()->json([
                'success' => false,
                'message' => 'Rider not found',
            ], 404); 
        }

        return response()->json([
            'success' => true,
            'message' => 'Rider status',
            'data' => $dm->rider_status == 1 ? 'Rider active!' : 'Rider deactive!',
        ], 200);
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

    public function admin_message($mobile){
        
        $dm = Deliveryman::where('mobile_number',$mobile)->first();
        
        $phone = 919606945066;
        $message = "Dear Admin,\n\n" .
                    "A new rider has submitted their form through GreenDriveConnect and the rider is waiting for approval.\n\n" .
                    "Details:\n\n" .
                    "Name: " . $dm->first_name . " " . $dm->last_name . "\n" .
                    "Contact: " . $dm->mobile_number . "\n" .
                    "Submission Time: " . $dm->created_at . "\n\n" .
                    "Please review and process the request at your earliest convenience.\n\n" .
                    "Thank you,\n" .
                    "GreenDriveConnect Team";
        // $message = $request->message;
        
        $api_key = env('WHATSAPP_API_KEY'); 
        $url = env('WHATSAPP_API_URL'); 
        
        $postdata = array(
            "contact" => array(
                array(
                    "number" => $phone,
                    "message" => $message,
                ),
            ),
        );
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postdata),
            CURLOPT_HTTPHEADER => array(
                'Api-key: ' . $api_key,
                'Content-Type: application/json',
            ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        
        $response_data = json_decode($response, true);
        // print_r($response_data);
        
        
        // if (isset($response_data['status']) && $response_data['status'] != 'success') {
        //     return [
        //             'status' => 'error',
        //             'message' => 'Failed to send WhatsApp message',
        //             'error_details' => $response_data
        //         ];
        // }
        // log::info('WhatsApp Message Send by Admin '.json_encode($response_data));
        curl_close($curl);
        
            // return [
            //     'status' => 'success',
            //     'message' => 'WhatsApp message sent successfully',
            //     'data' => $response_data
            // ];
    }
    
    public function rider_message($mobile){
        
        $dm = Deliveryman::where('mobile_number',$mobile)->first();
        
        $phone = str_replace('+', '', $mobile);
        $message = "Dear ". $dm->first_name . " " . $dm->last_name . ",\n\n" .
                "Thank you for submitting your form.\n" .
                "Your application is currently under review, and you will be notified once it has been approved by the admin.\n\n" .
                "We appreciate your patience and understanding.\n\n" .
                "Best regards,\n" .
                "GreenDriveConnect";

        // $message = $request->message;
        
        $api_key = env('WHATSAPP_API_KEY'); 
        $url = env('WHATSAPP_API_URL'); 
        
        $postdata = array(
            "contact" => array(
                array(
                    "number" => $phone,
                    "message" => $message,
                ),
            ),
        );
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postdata),
            CURLOPT_HTTPHEADER => array(
                'Api-key: ' . $api_key,
                'Content-Type: application/json',
            ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        
        $response_data = json_decode($response, true);
        // print_r($response_data);
        // log::info('WhatsApp Message Send by Deliveryman '.json_encode($response_data));
        
        if (isset($response_data['status']) && $response_data['status'] != 'success') {
            // return [
            //         'status' => 'error',
            //         'message' => 'Failed to send WhatsApp message',
            //         'error_details' => $response_data
            //     ];
        }
        
        curl_close($curl);
        
            // return [
            //     'status' => 'success',
            //     'message' => 'WhatsApp message sent successfully',
            //     'data' => $response_data
            // ];
        // log::info('WhatsApp Message Send by Deliveryman '.json_encode($response_data));
    }
    
    public function store_adhaar_no(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'aadhaar_no' => [
                'required',
                'digits:12', 
                // 'unique:ev_global_aadhaar_no,aadhaar_no', 
            ],
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors.',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        $aadhaar = new EvGlobalAadhaarNo();
        $aadhaar->aadhaar_no = $request->aadhaar_no;
        $aadhaar->status = 1; 
        $aadhaar->save();

        $url = 'https://uat.apiclub.in/api/v1/aadhaar_v2/send_otp';
        if (!$url) {
            return response()->json(['status'=>false,'message' => "The 'api_url' parameter is required."], 400);
        }
        $apiKey = 'apclb_PHh9VL5Ey5Gmb8tWVmUWGF3v1ffe3053';
        if (!$apiKey) {
            return response()->json(['status'=>false,'message' => "'x-api-key' header is not set."], 400);
        }
        $data = [
            'aadhaar_no' => $request->aadhaar_no,
        ];
        $apiResponse = Http::withHeaders([
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $data);
    
        if ($apiResponse->successful()) {
            $responseData = $apiResponse->json();
            EvGlobalAadhaarResponse::create([
                'aadhar_id' => $aadhaar->id,
                'ref_id' => $responseData['response']['ref_id'],
                'response_data' => $responseData['response'],
                'request_id' => $responseData['request_id'],
            ]);
        } else {
            return response()->json(['status'=>false,'message' => 'Failed to send OTP.'], 400);
        }

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'OTP has been sent.',
            'data' => $responseData,
        ], 200);
    }
    


     public function rider_info(Request $request)
    {
         Log::info("Rider Info Api is Called :".json_encode($request->all()));
        // Check if the user already exists in the Deliveryman table
        $existingUser = DeliveryMan::where('mobile_number', $request->mobile_number)->first();
    
        if ($existingUser) {
            // User exists, return the entire data for the user
            return response()->json([
                'success' => true,
                'message' => 'User data fetched successfully.',
                'data' => $existingUser, // Return the entire row
            ], 200); // OK status
        } else {
            // User does not exist, return an error response
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not found. Please register to proceed.',
            ], 404); // Not Found status
        }
    }
    
    public function test_id_generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => [
                'required',
                'string',
                'size:13',
                'regex:/^\+91[0-9]{10}$/',
                // 'unique:ev_tbl_delivery_men,mobile_number',
            ],
            'current_city_id' => 'required|integer',
            'interested_city_id' => 'required|integer',
            'remarks' => 'nullable|string|max:500',
            'role_request' => 'required|in:deliveryman,in-house,adhoc'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
        if ($request->role_request == "adhoc") {
            $temp_id = $permanent_id_set = $this->get_adhoc_tempid_count($request->role_request);
    
            $newRecord = EvGenerateId::create([
                'temp_id' => $temp_id,
                'permanent_id' => null,
                'user_type' => $request->role_request,
                'user_id' => null
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'ID generated successfully',
                'data' => $newRecord
            ], 200);
        }
    
        return response()->json([
            'success' => false,
            'message' => 'Role request is not adhoc'
        ], 400);
    }
    
    public function adhoc_permanent_autoload(Request $request)
    {
        $adhoc_list = Deliveryman::where('approved_status', 1)
            ->where('rider_status', 1)
            ->where('work_type', 'adhoc')
            ->where('emp_id_status',0)
            ->get();
       Log::info("WhatsApp Message Sent checkkinnnnngg: " . json_encode($adhoc_list));
        foreach ($adhoc_list as $adhoc) {
            $lastPunchIn = DB::table('ev_delivery_man_logs')
                ->where('user_id', $adhoc->id)
                ->orderBy('punched_in', 'desc')
                ->first();
    
            $daysSinceLastPunch = $lastPunchIn ? now()->diffInDays(Carbon::parse($lastPunchIn->punched_in)) : null;
            if ($daysSinceLastPunch !== null && $daysSinceLastPunch >= 3) {
                if ($adhoc->rider_status == 1 && $adhoc->approved_status == 1) {
                    $adhoc->update(['rider_status' => 0]);
                }
                continue; 
            }

            $reg_adhoc = Deliveryman::where('id', $adhoc->id)
                ->where('approved_status', 1)
                ->where('rider_status', 1)
                ->where('work_type', 'adhoc')
                ->first();
    
            if ($reg_adhoc) {
                $adhoc_register_days = now()->diffInDays(Carbon::parse($reg_adhoc->register_date_time));
                $get_adhoc_id = EvGenerateId::where('user_id', $adhoc->id)->first();
                // Log::info("Register Date : " . $reg_adhoc->register_date_time);
                // Log::info("Current  Date : " . now());
                // Log::info("Different Day Count : " . $adhoc_register_days);
                
                if ($adhoc_register_days >= 15 && $get_adhoc_id) { //register date without 15 days count
                 $actual_count = $adhoc_register_days - 15;
                //  Log::info("Days after 15 days: " . $actual_count);
                    $permanent_id_set = $this->get_adhoc_permanentid_count($reg_adhoc->work_type);
                    $get_adhoc_id->update(['permanent_id' => $permanent_id_set]);
                    $reg_adhoc->update(['emp_id' => $permanent_id_set,'emp_id_status' => 1]);
                    $this->parmenant_adhoc_whatsapp_message($adhoc->id);
                }
            }
           
        }
         return true;
    }
    
     private function get_adhoc_tempid_count($type){
        $count = EvGenerateId::whereNotNull('temp_id')->where('user_type',$type)->get()->count();
        $temp_id = $count == 0 ? 'TMP-1001' : 'TMP-100' . ($count + 1);
        return $temp_id;
    }
    private function get_adhoc_permanentid_count($type){
        $count = EvGenerateId::whereNotNull('permanent_id')->where('user_type',$type)->get()->count();
        $parmenant_id = $count == 0 ? 'ADH-1001' : 'ADH-100' . ($count + 1);
        return $parmenant_id;
    }
    private function parmenant_adhoc_whatsapp_message($dm_id)
    {
        $dm = Deliveryman::find($dm_id);
        
        if (!$dm) {
            return;
        }
    
        $phone = str_replace('+', '', $dm->mobile_number); 
       $message = "🎉 Dear " . $dm->first_name . " " . $dm->last_name . ",\n\n" .
               "✨ Congratulations! You are now a permanent member of our team. 🎖️\n" .
               "🆔 Your Permanent ID: " . $dm->emp_id . "\n" .
               "We appreciate your hard work and dedication.\n\n" .
               "Best regards,\n🌿 GreenDriveConnect";
    
        $api_key = env('WHATSAPP_API_KEY'); 
        $url = env('WHATSAPP_API_URL'); 
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
    
    public function registerRiderMail(Request $request)
    {
        $dm = Deliveryman::where('id',710)->first();
        
        // Mail::to('gowtham@alabtechnology.com')->send(new RiderRegisterationMail($dm)); // rider mail
        
        //  $admin_email = BusinessSetting::where('key_name', 'admin_sender_mail')->value('value');
         
        //  if ($admin_email) {
        //     Mail::to($admin_email)->send(new RiderAdminNotificationMail($dm)); // admin mail
        // }
    
        // return response()->json(['message' => 'Registration successful and mail sent.']);
        
            $riderType = match($dm->work_type) {
                'deliveryman' => 'R',
                'in-house'    => 'E',
                'adhoc'       => 'A',
                default       => 'N/A',
            };

            
            $id_start = 'GDMAPP' . $riderType;

            $serialNumber = str_pad((string)($count + 1), 5, '0', STR_PAD_LEFT); // ensure it's string
            $reg_application_id = $id_start . $serialNumber;


    }




}
