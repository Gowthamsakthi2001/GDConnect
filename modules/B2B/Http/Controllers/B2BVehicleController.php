<?php

namespace Modules\B2B\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; 
use Modules\B2B\Entities\B2BServiceRequest;
use App\Models\BusinessSetting;
use App\Models\User;
use Modules\VehicleServiceTicket\Entities\VehicleTicket;
use Modules\VehicleServiceTicket\Entities\FieldProxyTicket;//updated by Mugesh.B
use Modules\VehicleServiceTicket\Entities\FieldProxyLog;//updated by Mugesh.B
use Modules\AssetMaster\Entities\VehicleTransferChassisLog;//updated by Mugesh.B
use Modules\MasterManagement\Entities\RepairTypeMaster;
use Modules\B2B\Entities\B2BReturnRequest;
use App\Exports\B2BRiderExport;
use App\Exports\B2BReturnedListExport;
use App\Exports\B2BVehicleListExport;
use Modules\B2B\Entities\B2BReportAccident;
use Modules\B2B\Entities\B2BRecoveryRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Modules\VehicleManagement\Entities\VehicleType; //updated by Mugesh.B
use Modules\MasterManagement\Entities\InventoryLocationMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\CustomerMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\CustomerLogin;
use Modules\B2B\Entities\B2BVehicleAssignment;//updated by Mugesh.B
use Modules\B2B\Entities\B2BVehicleAssignmentLog;//updated by Mugesh.B
use Modules\B2B\Entities\B2BRider;//updated by Mugesh.B
use Modules\B2B\Entities\B2BVehicleRequests; //updated by Mugesh.B
use Modules\AssetMaster\Entities\AssetVehicleInventory;
use Illuminate\Support\Facades\Mail;//updated by Mugesh.B
use App\Exports\B2BVehicleRequestExport;//updated by Mugesh.B
use Maatwebsite\Excel\Facades\Excel;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use App\Helpers\CustomHandler;
use App\Helpers\ServiceTicketHandler;
use App\Mail\B2BVehicleRequestMail;//updated by Mugesh.B
use App\Mail\B2BTermsAndCondition;//updated by Mugesh.B
use Illuminate\Support\Facades\Auth;//updated by Mugesh.B
use Modules\City\Entities\City;
use Modules\AssetMaster\Entities\LocationMaster;
use Modules\Zones\Entities\Zones; //updated by Gowtham.s
use Carbon\Carbon;
use App\Services\FirebaseNotificationService; //updated by Gowtham.s
use App\Exports\B2BAccidentReportExport;//logesh
use App\Exports\B2BRecoveryRequestExport;
use App\Exports\B2BServiceRequestExport;
use Modules\B2B\Entities\B2BAgent;
use Modules\MasterManagement\Entities\EvTblAccountabilityType; //updated by Gowtham.s
use Modules\RecoveryManager\Entities\RecoveryComment; //updated by logesh
use Modules\Deliveryman\Entities\Deliveryman; //updated by logesh
use Modules\Role\Entities\Role; //updated by logesh
use App\Helpers\RecoveryNotifyHandler; //updated by Gowtham.S
use Modules\MasterManagement\Entities\RecoveryReasonMaster;//updated by Gowtham.S
use Modules\MasterManagement\Entities\RecoveryUpdatesMaster; //updated by logesh

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

use App\Jobs\ProcessB2BRiderCreationJob; //updated by Mugesh.B
use App\Jobs\ProcessVehicleRequestCreationJob; //updated by Mugesh.B
use App\Jobs\SendWhatsappMessageJob; //updated by Mugesh.B
use App\Jobs\ProcessB2BReturnRequestCreationJob; //updated by Mugesh.B
use App\Jobs\ProcessB2BRecoveryRequestCreationJob; //updated by Mugesh.B
use App\Jobs\ProcessB2BServiceRequestCreationJob; //updated by Mugesh.B

class B2BVehicleController extends Controller
{
    

        public function create()
        {
            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
            $user = Auth::guard($guard)->user();
            $user->load(['city', 'zone']);

            if(!$user){
                return back()->with('error','Auth user not found');
            }
            
            if($user->type == 'master' && empty($user->city_id)){
                 return back()->with('error','Auth user not found');
            }
            
            $login_type = $user->type;
            $zone_id = $user->zone_id ?? null;
            $zones = Zones::where('city_id',$user->city_id)->where('status',1)->get();
            
            return view('b2b::vehicles.add_rider',compact('zones','login_type','zone_id'));
        }
        
    
    
    
        public function store_rider(Request $request)
        {
            Log::info('function is start'.now());
            $rules = [
                'assign_zone'           => 'required|exists:zones,id',
                'name'                  => 'required|max:200',
                'mobile'                => 'required|max:15|unique:b2b_tbl_riders,mobile_no',
                'email'                 => 'nullable|email|max:150|unique:b2b_tbl_riders,email',
                'dob'                   => 'nullable|date',
                'aadhaar_number'        => 'required|string|max:20|unique:b2b_tbl_riders,adhar_number',
                'pan_number'            => 'nullable|string|max:20|unique:b2b_tbl_riders,pan_number',
                'driving_licence_number'=> 'nullable|string|max:20|unique:b2b_tbl_riders,driving_license_number',
                'llr_number'            => 'nullable|string|max:20|unique:b2b_tbl_riders,llr_number',
                'driving_license_expiry_date' =>'nullable|date',
    
                'aadhaar_back'          => 'nullable|mimes:jpg,jpeg,png,pdf',
                'aadhaar_front'         => 'nullable|mimes:jpg,jpeg,png,pdf',
                'pan_front'             => 'nullable|mimes:jpg,jpeg,png,pdf',
                'pan_back'              => 'nullable|mimes:jpg,jpeg,png,pdf',
                'driving_front'         => 'nullable|mimes:jpg,jpeg,png,pdf',
                'driving_back'          => 'nullable|mimes:jpg,jpeg,png,pdf',
                'llr_image'             => 'nullable|mimes:jpg,jpeg,png,pdf',
                'submission_type'       => 'required|in:license,llr,terms',
            ];
            $messages = [
                // 'aadhaar_back.max'  => 'The Aadhaar back file may not be greater than 1 MB.',
                // 'aadhaar_front.max' => 'The Aadhaar front file may not be greater than 1 MB.',
                // 'pan_front.max'     => 'The PAN front file may not be greater than 1 MB.',
                // 'pan_back.max'      => 'The PAN back file may not be greater than 1 MB.',
                // 'driving_front.max' => 'The Driving License front file may not be greater than 1 MB.',
                // 'driving_back.max'  => 'The Driving License back file may not be greater than 1 MB.',
                // 'llr_image.max'     => 'The LLR file may not be greater than 1 MB.',
            ];

            $validator = Validator::make($request->all(), $rules ,$messages);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
            $user = Auth::guard($guard)->user();
            
            if($user->city_id == '' || $user->city_id == null){ //updated by Gowtham.s
                return response()->json([
                    'success' => false,
                    'message' => 'Auth user not found'
                ]);
            }
    
            // Helper function for file upload
            $uploadFile = function ($file, $folder) {
                if ($file) {
                    return $file->store($folder, 'public');
                }
                return null;
            };
            
            
            $uploadFile = function ($file, $folder) {
                    $directory = public_path('b2b/' . $folder);

                    if (!is_dir($directory)) {

                        mkdir($directory, 0777, true);

                    }
                    if ($file) {

                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                        $file->move($directory, $filename);

                        return $filename;

                    }
                };
 
    
            DB::beginTransaction();
    
            try {
                $drivingLicenseFront = $request->file('driving_front');
                $drivingLicenseBack  = $request->file('driving_back');
                $llrImage            = $request->file('llr_image');
                $drivingNumber       = $request->driving_licence_number;
                $llrNumber           = $request->llr_number;
                $dl_expiry_date      = $request->driving_license_expiry_date;
    
                // Handle submission type cases
               if ($request->submission_type === 'license') {
                    // License only
                    $llrImage   = null;
                    $llrNumber  = null;
                
                    $drivingLicenseFront = $uploadFile($request->file('driving_front'), 'driving_license_images');
                    $drivingLicenseBack  = $uploadFile($request->file('driving_back'), 'driving_license_images');
                    $llrImagePath        = null;
                } elseif ($request->submission_type === 'llr') {
                    // LLR only
                    $drivingLicenseFront = null;
                    $drivingLicenseBack  = null;
                    $drivingNumber       = null;
                    $dl_expiry_date      = null;
                
                    $drivingLicenseFront = null;
                    $drivingLicenseBack  = null;
                    $llrImagePath        = $uploadFile($request->file('llr_image'), 'llr_images');
                } elseif ($request->submission_type === 'terms') {
                    // Terms only
                    $drivingLicenseFront = null;
                    $drivingLicenseBack  = null;
                    $llrImage            = null;
                    $drivingNumber       = null;
                    $dl_expiry_date      = null;
                    $llrNumber           = null;
                
                    $drivingLicenseFront = null;
                    $drivingLicenseBack  = null;
                    $llrImagePath        = null;
                } else {
                    // Default case (if needed)
                    $drivingLicenseFront = $uploadFile($request->file('driving_front'), 'driving_license_images');
                    $drivingLicenseBack  = $uploadFile($request->file('driving_back'), 'driving_license_images');
                    $llrImagePath        = $uploadFile($request->file('llr_image'), 'llr_images');
                }
                
                // Always upload Aadhaar & PAN (common for all submission types)
                $aadhaarFront = $uploadFile($request->file('aadhaar_front'), 'aadhar_images');
                $aadhaarBack  = $uploadFile($request->file('aadhaar_back'), 'aadhar_images');
                $panFront     = $uploadFile($request->file('pan_front'), 'pan_images');
                $panBack      = $uploadFile($request->file('pan_back'), 'pan_images');
    
                $rider = B2BRider::create([
                    'assign_zone_id'        => $request->assign_zone, //Updated by Gowtham.s
                    'name'                  => $request->name,
                    'mobile_no'             => $request->mobile,
                    'email'                 => $request->email,
                    'dob'                   => $request->dob,
                    'adhar_number'          => $request->aadhaar_number,
                    'pan_number'            => $request->pan_number,
                    'driving_license_number'=> $drivingNumber,
                    'llr_number'            => $llrNumber,
                    'adhar_front'           => $aadhaarFront,
                    'adhar_back'            => $aadhaarBack,
                    'pan_front'             => $panFront,
                    'pan_back'              => $panBack,
                    'driving_license_front' => $drivingLicenseFront,
                    'driving_license_back'  => $drivingLicenseBack,
                    'llr_image'             => $llrImagePath,
                    'terms_condition'       => $request->submission_type === 'terms' ? 1 : 0,
                    'dl_expiry_date'        => $dl_expiry_date,
                    'created_by'            => $user->id,
                    'createdby_city'        => $user->city_id //Updated by Gowtham.s
                ]);
    
                $user->load(['city', 'zone' , 'customer_relation']);
                
                

                
                
                DB::commit();
                Log::info('function is comitted'.now());

                $riderData = B2BRider::with(['city', 'zone', 'customerLogin.customer_relation'])
                    ->where('id', $rider->id)
                    ->first();
                

                    // $this->RiderCredencials_SentWhatsAppMessage($riderData, 'b2b_rider_account_created');
                
                
                    // $this->riderWelcomeNotification($riderData);
                
                
                    // $this->RiderCredencials_SentEmailNotify($riderData, 'b2b_rider_ac_emailNotify');
                
                    // if ($request->submission_type === 'terms') {
                    //     $this->RiderTermsAndCondition_SentEmailNotify($riderData, 'b2b_rider_terms_emailNotify');
                        
                    // }
                
                
                // ProcessRiderCreationJob::dispatchAfterResponse($rider->id, $request->submission_type);
                // ProcessRiderCreationJob::dispatch($rider->id, $request->submission_type);
                ProcessB2BRiderCreationJob::dispatch($rider->id, $request->submission_type);
                
                Log::info('function is closed'.now());
                return response()->json([
                    'success' => true,
                    'message' => 'Rider created successfully',
                    'data'    => ['rider' => $rider]
                ]);
                

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong: ' . $e->getMessage()
                ], 500);
            }
        }
        
        
        public function RiderTermsAndCondition_SentEmailNotify($rider, $forward_type, $account_status = null)
        {
            
            $riderPhone = $rider->mobile_no;
            $riderEmail = $rider->email;
            $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
            $customerEmail = $rider->customerLogin->customer_relation->email ?? 'N/A';
            $customerLoginEmail = $rider->customerLogin->email ?? 'N/A';
            $customerPhone = $rider->customerLogin->customer_relation->phone ?? 'N/A';
            $customerId = $rider->customerLogin->customer_relation->id ?? 'N/A';
        
            $toAdmins = DB::table('roles')
                ->leftJoin('users', 'roles.id', '=', 'users.role')
                ->select('users.email')
                ->whereIn('users.role', [1, 13]) // Admins
                ->where('users.status', 'Active')
                ->pluck('users.email')
                ->filter()
                ->toArray();
        
            $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
            $footerContentText = $footerText ?? "For any assistance, please reach out to Admin Support.<br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
        
            $riderSubject = '';
            $customerSubject = '';
            $adminSubject = '';
            $riderBody = '';
            $customerBody = '';
            $adminBody = '';
        
            if ($forward_type === 'b2b_rider_terms_emailNotify') {
        
                $customerSubject = "Action Required: Accept Terms & Conditions for Rider – {$rider->name}";
                $encryptedRiderId = encrypt($rider->id);
                $termsUrl = url("/customer/terms-and-conditions") . "?id={$encryptedRiderId}";
                
                $customerBody = "
                <html>
                <body style='font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px; color:#544e54;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='max-width:600px; margin:auto; background:#fff; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.1);'>
                        <tr>
                            <td style='padding:20px; text-align:center; background:#2196F3; color:#fff; border-top-left-radius:8px; border-top-right-radius:8px;'>
                                <h2>Action Required: Accept Terms & Conditions</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding:20px; color:#544e54;'>
                                <p>Hello <strong>{$customerName}</strong>,</p>
                                <p>You have created a new rider <strong>{$rider->name}</strong> ({$riderPhone}) who does not have a Driving License (DL) or Learner’s License (LLR).</p>
                                <p>Please review and accept the <strong>Terms & Conditions</strong> on behalf of this rider before they can proceed with onboarding.</p>
                                
                                <p style='margin-top:20px;'>
                                    <a href='{$termsUrl}' style='background:#2196F3; color:#fff; text-decoration:none; padding:10px 20px; border-radius:5px; display:inline-block; font-weight:bold;'>Review & Accept Terms</a>
                                </p>
                
                                <p style='margin-top:20px;'>Once you accept, the rider will be able to proceed with onboarding.</p>
                
                                <p style='margin-top:20px;'>{$footerContentText}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align:center; padding:15px; font-size:12px; color:#544e54;'>
                                &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                            </td>
                        </tr>
                    </table>
                </body>
                </html>";
        
                $adminSubject = "New Rider Created Without DL/LLR – {$rider->name}";
                $adminBody = "
                <html>
                <body style='font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px; color:#544e54;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='max-width:600px; margin:auto; background:#fff; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.1);'>
                        <tr>
                            <td style='padding:20px; text-align:center; background:#8b8b8b; color:#fff; border-top-left-radius:8px; border-top-right-radius:8px;'>
                                <h2>New Rider Created Without DL/LLR</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding:20px; color:#544e54;'>
                                <p>A new rider <strong>{$rider->name}</strong> ({$riderPhone}) has been created by customer <strong>{$customerName}</strong> without a Driving License (DL) or Learner’s License (LLR).</p>
                                <p>Customer acceptance of Terms & Conditions is pending.</p>
                                
                                <table cellpadding='8' cellspacing='0' style='width:100%; margin-top:15px; border:1px solid #ddd; border-radius:5px; color:#544e54;'>
                                    <tr style='background:#f2f2f2;'><td><strong>Rider Name:</strong></td><td>{$rider->name}</td></tr>
                                    <tr><td><strong>Rider Phone:</strong></td><td>{$riderPhone}</td></tr>
                                    <tr style='background:#f2f2f2;'><td><strong>Customer Name:</strong></td><td>{$customerName}</td></tr>
                                </table>
                
                                <p style='margin-top:20px;'>{$footerContentText}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align:center; padding:15px; font-size:12px; color:#544e54;'>
                                &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                            </td>
                        </tr>
                    </table>
                </body>
                </html>";
            }
        
        
            if (!empty($customerLoginEmail)) {
                CustomHandler::sendEmail($customerLoginEmail, $customerSubject, $customerBody ,$customerEmail);
            }
        
            if (!empty($toAdmins)) {
                CustomHandler::sendEmail($toAdmins, $adminSubject, $adminBody);
            }
            
        }

        
        
        public function RiderCredencials_SentEmailNotify($rider, $forward_type, $account_status = null){
            
            $riderPhone = $rider->mobile_no;
            $riderEmail = $rider->email;
            $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
            $customerEmail = $rider->customerLogin->customer_relation->email ?? 'N/A';
            $customerLoginEmail = $rider->customerLogin->email ?? 'N/A';
            $customerPhone = $rider->customerLogin->customer_relation->phone;
            $customerEmail = $rider->customerLogin->customer_relation->email;
            $customerId = $rider->customerLogin->customer_relation->id;
            $toAdmins = DB::table('roles')
            ->leftJoin('users', 'roles.id', '=', 'users.role')
            ->select('users.email')
            ->whereIn('users.role', [1,13]) // Admins
            ->where('users.status','Active')
            ->pluck('users.email')
            ->filter()
            ->toArray();
            $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
            $footerContentText = $footerText ?? "For any assistance, please reach out to Admin Support.
                <br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
            
            $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.
                <br>Email: {$customerEmail}<br>Thank you,<br>{$customerName}";
                
        $riderSubject = '';
        $customerSubject = '';
        $adminSubject = '';
        $riderBody = '';
        $customerBody = '';
        $adminBody = '';
        
        if ($forward_type === 'b2b_rider_ac_emailNotify') {
            // Rider Email
            $riderSubject = "Welcome to {$customerName} – Rider Account Created";
            $riderBody = "
                <html>
                <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #544e54;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #544e54;'>
                        <tr>
                            <td style='padding: 20px; text-align: center; background-color: #8b8b8b; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                                <h2>Rider Account Created</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding: 20px; color: #544e54;'>
                                <p>Hello <strong>{$rider->name}</strong>,</p>
                                <p>Your rider account has been <strong>successfully created</strong> under <strong>{$customerName}</strong>.</p>
                                
                                <table cellpadding='8' cellspacing='0' style='width: 100%; margin-top: 15px; border: 1px solid #ddd; border-radius: 5px; color: #544e54;'>
                                    <tr style='background-color: #f2f2f2;'>
                                        <td><strong>Customer Name:</strong></td>
                                        <td>{$customerName}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer ID:</strong></td>
                                        <td>{$customerId}</td>
                                    </tr>
                                    <tr style='background-color: #f2f2f2;'>
                                        <td><strong>Account Status:</strong></td>
                                        <td>Active</td>
                                    </tr>
                                </table>
                
                                <p style='margin-top: 20px;'>{$CustomerfooterContentText}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align: center; padding: 15px; font-size: 12px; color: #544e54;'>
                                &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                            </td>
                        </tr>
                    </table>
                </body>
                </html>
                ";

    
            // Customer Email
            $customerSubject = "New Rider Registered – {$rider->name}";
            $customerBody = "
                <html>
                <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #544e54;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #544e54;'>
                        <tr>
                            <td style='padding: 20px; text-align: center; background-color: #4CAF50; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                                <h2>New Rider Registered</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding: 20px; color: #544e54;'>
                                <p>Hello <strong>{$customerName}</strong>,</p>
                                <p>Your rider <strong>{$rider->name}</strong> has been successfully registered under your account.</p>
                                
                                <table cellpadding='8' cellspacing='0' style='width: 100%; margin-top: 15px; border: 1px solid #ddd; border-radius: 5px; color: #544e54;'>
                                    <tr style='background-color: #f2f2f2;'>
                                        <td><strong>Rider Name:</strong></td>
                                        <td>{$rider->name}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Rider Phone:</strong></td>
                                        <td>{$riderPhone}</td>
                                    </tr>
                                </table>
                
                                <p style='margin-top: 20px;'>{$footerContentText}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align: center; padding: 15px; font-size: 12px; color: #544e54;'>
                                &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                            </td>
                        </tr>
                    </table>
                </body>
                </html>
                ";

    
            // Admin Email
            $adminSubject = "B2B New Rider Created – {$rider->name}";
            $adminBody = "
            <html>
            <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #544e54;'>
                <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #544e54;'>
                    <tr>
                        <td style='padding: 20px; text-align: center; background-color: #2196F3; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                            <h2>B2B Rider Created</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding: 20px; color: #544e54;'>
                            <p>A new rider has been created under a B2B Customer.</p>
                            
                            <table cellpadding='8' cellspacing='0' style='width: 100%; margin-top: 15px; border: 1px solid #ddd; border-radius: 5px; color: #544e54;'>
                                <tr style='background-color: #f2f2f2;'>
                                    <td colspan='2' style='font-weight: bold; text-align: center;'>Rider Information</td>
                                </tr>
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{$rider->name}</td>
                                </tr>
                                <tr style='background-color: #f9f9f9;'>
                                    <td><strong>Phone:</strong></td>
                                    <td>{$riderPhone}</td>
                                </tr>
                                <tr style='background-color: #f2f2f2;'>
                                    <td colspan='2' style='font-weight: bold; text-align: center;'>Customer Information</td>
                                </tr>
                                <tr>
                                    <td><strong>Customer Name:</strong></td>
                                    <td>{$customerName}</td>
                                </tr>
                                <tr style='background-color: #f9f9f9;'>
                                    <td><strong>Customer ID:</strong></td>
                                    <td>{$customerId}</td>
                                </tr>
                            </table>
            
                            <p style='margin-top: 20px;'>{$footerContentText}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align: center; padding: 15px; font-size: 12px; color: #544e54;'>
                            &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                        </td>
                    </tr>
                </table>
            </body>
            </html>
            ";

        } elseif ($forward_type === 'rider_account_status_update') {
            $statusText = $account_status == 1 ? "Active ✅" : "Inactive ❌";
    
            // Rider Email
            $riderSubject = "Rider Account Status Updated";
            $riderBody = "
                <p>Hello <strong>{$rider->name}</strong>,</p>
                <p>Your rider account status has been updated.</p>
                
                <strong>Rider Information</strong><br>
                • Rider ID: {$rider->id}<br>
                • Status: {$statusText}<br><br>
                
                <p>{$footerContentText}</p>
            ";
    
            // Customer Email
            $customerSubject = "Rider Account Status Update";
            $customerBody = "
                <p>Hello <strong>{$customerName}</strong>,</p>
                <p>Your rider <strong>{$rider->name}</strong>'s account status has been updated.</p>
                
                <strong>Status:</strong> {$statusText}<br><br>
                
                <p>{$footerContentText}</p>
            ";
    
            // Admin Email
            $adminSubject = "Admin Alert – Rider Status Changed";
            $adminBody = "
                <p>The following rider's account status has been updated:</p>
                
                <strong>Rider Information</strong><br>
                • Name: {$rider->name}<br>
                • Rider ID: {$rider->id}<br>
                • Status: {$statusText}<br><br>
                
                <p>{$footerContentText}</p>
            ";
        }
            $toRiders = $riderEmail;
            $cc_Customers = $customerEmail;
    
            if(!empty($toRiders)){
                CustomHandler::sendEmail($toRiders, $riderSubject, $riderBody,$cc_Customers);
            }
            
            $toCustomers = $customerLoginEmail;
            
            if(!empty($toCustomers)){
                CustomHandler::sendEmail($toCustomers, $customerSubject, $customerBody,$cc_Customers);
            }

            if(!empty($toAdmins)){
              CustomHandler::sendEmail($toAdmins, $adminSubject, $adminBody);   
            }
            
            
             
        }
        
        public function riderWelcomeNotification($rider)
        {
            $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
            $title = "Rider Registration Successful – {$customerName}";
            $body = "Hello {$rider->name}, welcome onboard! 🎉 You have been successfully registered as a rider for {$customerName}.";
            
            CustomHandler::RiderstoreNotification($title,$body,$rider->id);
            
        }


        public function resend_mail(Request $request){
            
               try {
       
                $rider = B2BRider::with('customerLogin.customer_relation')->find($request->rider_id);
        
                if (!$rider) {
                    return response()->json(['status' => false, 'message' => 'Rider not found.']);
                }
                
                $customerEmail = $rider->customerLogin->customer_relation->email ?? 'N/A';
                $customerLoginEmail = $rider->customerLogin->email ?? 'N/A';
                
                $customerSubject = "Action Required: Accept Terms & Conditions for Rider – {$rider->name}";
                $encryptedRiderId = encrypt($rider->id);
                $termsUrl = url("/customer/terms-and-conditions") . "?id={$encryptedRiderId}";
                $customerName = $rider->customerLogin->customer_relation->name ?? 'Customer';
                $riderPhone = $rider->mobile_no ?? 'N/A';
                
                $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
                $footerContentText = $footerText ?? "For any assistance, please reach out to Admin Support.
                    <br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
                
                $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.
                    <br>Email: {$customerEmail}<br>Thank you,<br>{$customerName}";
                    
                    
                if (!$customerEmail || !$customerLoginEmail) {
                return response()->json(['status' => false, 'message' => 'Customer email not available.']);
                 }
                
                $Subject = "Action Required: Accept Terms & Conditions for Rider – {$rider->name}";
                
                $Body = "
                <html>
                <body style='font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px; color:#544e54;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='max-width:600px; margin:auto; background:#fff; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.1);'>
                        <tr>
                            <td style='padding:20px; text-align:center; background:#2196F3; color:#fff; border-top-left-radius:8px; border-top-right-radius:8px;'>
                                <h2>Action Required: Accept Terms & Conditions</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding:20px; color:#544e54;'>
                                <p>Hello <strong>{$customerName}</strong>,</p>
                                <p>You have created a new rider <strong>{$rider->name}</strong> ({$riderPhone}) who does not have a Driving License (DL) or Learner’s License (LLR).</p>
                                <p>Please review and accept the <strong>Terms & Conditions</strong> on behalf of this rider before they can proceed with onboarding.</p>
                                
                                <p style='margin-top:20px;'>
                                    <a href='{$termsUrl}' style='background:#2196F3; color:#fff; text-decoration:none; padding:10px 20px; border-radius:5px; display:inline-block; font-weight:bold;'>Review & Accept Terms</a>
                                </p>
                
                                <p style='margin-top:20px;'>Once you accept, the rider will be able to proceed with onboarding.</p>
                
                                <p style='margin-top:20px;'>{$footerContentText}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align:center; padding:15px; font-size:12px; color:#544e54;'>
                                &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                            </td>
                        </tr>
                    </table>
                </body>
                </html>";
            
            
                 // Send email via your custom handler
                CustomHandler::sendEmail($customerLoginEmail, $Subject, $Body, $customerEmail);
                
        
                \Log::info("Resent Terms & Conditions mail successfully", ['rider_id' => $rider->id]);
        
                return response()->json([
                    'status' => true,
                    'message' => 'Mail sent successfully!'
                ]);
        
            } catch (\Throwable $e) {
                \Log::error("Resend mail failed", ['error' => $e->getMessage()]);
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong: ' . $e->getMessage()
                ]);
            }
            
        }
        
        public function RiderCredencials_SentWhatsAppMessage($rider, $forward_type, $account_status = null)
        {
            $riderPhone = $rider->mobile_no;
            $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
            $customerEmail = $rider->customerLogin->customer_relation->email ?? 'N/A';
            $customerPhone = $rider->customerLogin->customer_relation->phone;
    
            $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
            $footerContentText = $footerText ??
                "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
            $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.\n" .
                         "Email: {$customerEmail}\n" .
                         "Thank you,\n" .
                         "{$customerName}";
        
    
            $message = "";
            $customer_message = "";
            $admin_message = "";
            
            
                if ($forward_type === 'b2b_rider_account_created') {
                    $message = "*Welcome to {$customerName}*\n\n" .
                        "Hello *{$rider->name}*,\nYour rider account has been successfully created.\n\n" .
                        "Customer Name: {$customerName}\n" .
                        "Customer ID: {$rider->customerLogin->customer_relation->id}\n".
                        // "Assigned Zone: {$rider->zone->name}\n".
                        "*Account Status:* Active\n\n".
                        $CustomerfooterContentText;
                
                    $customer_message = "*Register Notification*\nYour rider *{$rider->name}* has been successfully registered.\n\n" .
                        $footerContentText;
                
                    $admin_message = "*B2B New Rider Created*\n" .
                        "Rider Name: {$rider->name}\n" .
                        "Rider Phone: {$riderPhone}\n" .
                        // "Assigned Zone: {$rider->zone->name}\n".
                        "Customer Name: {$customerName}\n" .
                        "Customer ID: {$rider->customerLogin->customer_relation->id}\n\n".
                        
                        $footerContentText;
                }
                elseif ($forward_type === 'rider_account_status_update') {
                
                $statusText = $account_status == 1 ? "Active": "Inactive";
                $message = "*Account Status Update*\n\n" .
                    "Hello *{$rider->name}*,\nYour account status has been updated.\n\n" .
                    "*Rider ID:* {$rider->id}\n" .
                    "*Status:* {$statusText}\n\n" .
                    $footerContentText;
    
                $customer_message = "*Rider Account Status Update*\nRider: {$rider->name}\nStatus: {$statusText}\n";
    
                $admin_message = "*Admin Alert*\nRider: {$rider->name}\nStatus changed to: {$statusText}";
            } else {
                $admin_message = "*Unhandled forward_type:* {$forward_type}";
            }
    
            // ✅ Send messages if available
            if (!empty($riderPhone) && !empty($message)) {
                CustomHandler::user_whatsapp_message($riderPhone, $message);
            }
    
            if (!empty($customerPhone) && !empty($customer_message)) {
                CustomHandler::user_whatsapp_message($customerPhone, $customer_message);
            }
    
            if (!empty($admin_message)) {
                CustomHandler::admin_whatsapp_message($admin_message);
            }
        }
        
        
    
        public function rider_list(Request $request)
        {

            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
            $user  = Auth::guard($guard)->user();
            $login_type = $user->type;
            $customerId = $user->customer_id;
            $accountability_Types = $user->customer_relation->accountability_type_id;
            if (!is_array($accountability_Types)) {
                $accountability_Types = json_decode($accountability_Types, true) ?? [];
            }
            
            $accountability_Type = in_array(2, $accountability_Types) ? 2 : 1;
            $zones = Zones::where('city_id',$user->city_id)->where('status',1)->get();  
            $zone_id = null;
            if($login_type == 'zone'){
                $zone_id = $user->zone_id;  
            }
            
            if ($request->ajax()) {
                try {
                    $start  = $request->input('start', 0);
                    $length = $request->input('length', 25);
                    $search = $request->input('search.value');
                    $from   = $request->input('from_date'); 
                    $to     = $request->input('to_date');   
                    
                    $query = B2BRider::with('customerLogin');
        
                    // Apply search filter
                    if (!empty($search)) {
                        $query->where(function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhere('mobile_no', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
                    }
                    

                
                    // Always filter within current customer
                    $customerId = $user->customer_id;
        
                    $query->whereHas('customerLogin', function ($q) use ($customerId) {
                        $q->where('customer_id', $customerId);
                    });
                    
                    if ($guard === 'master') {
                        $query->where('createdby_city', $user->city_id);
                    } elseif ($guard === 'zone') {
                        $query->where('createdby_city', $user->city_id)
                              ->where('assign_zone_id', $user->zone_id);
                    }

                    
        
                    if (!empty($from)) {
                        $query->whereDate('created_at', '>=', $from);
                    }
                    if (!empty($to)) {
                        $query->whereDate('created_at', '<=', $to);
                    }
            
                    $totalRecords = $query->count();
        
                    if ($length == -1) {
                        $length = $totalRecords;
                    }
        
                    $datas = $query->orderBy('id', 'desc')
                                   ->skip($start)
                                   ->take($length)
                                   ->get();
        
                    $formattedData = $datas->map(function ($rider, $index) use ($start) {
                        $idEncode = encrypt($rider->id);
                        
                        
                        $activeRequestCount = B2BVehicleRequests::where('rider_id', $rider->id)
                        ->where('is_active', 1)
                        ->count();
                        
                        $pendingRequestCount = B2BVehicleRequests::where('rider_id', $rider->id)
                        ->where('status', 'pending')
                        ->count();
                        
                        
                        
                        $actionButtons = '
                        <div class="d-flex align-items-center gap-1">
                            <a href="'.route('b2b.rider_view', $idEncode).'" title="View Rider Details"
                                class="d-flex align-items-center justify-content-center border-0"
                                style="background-color:#CAEDCE; color:#155724; border-radius:8px; width:31px; height:31px;">
                                <i class="bi bi-eye fs-5"></i>
                            </a>
                        ';
                        
                        
                        if ($activeRequestCount == 0 && $pendingRequestCount == 0) {
                            $actionButtons .= '
                            <a href="javascript:void(0);"
                               data-bs-toggle="modal"
                               data-bs-target="#vehicleRequestModal"
                               data-id="'.$rider->id.'" data-get_zone_id="'.$rider->assign_zone_id.'" data-get_zone_name="'.$rider->zone->name.'" title="New Vehicle Request"
                               class="d-flex align-items-center justify-content-center border-0"
                               style="border-radius:8px;cursor:pointer;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="31" height="31" viewBox="0 0 27 27" fill="none">
                                <rect width="27" height="27" rx="8" fill="#D0ACFF"/>
                                <path d="M13.5003 8.00016C14.5128 8.00016 15.3337 7.17935 15.3337 6.16683C15.3337 5.15431 14.5128 4.3335 13.5003 4.3335C12.4878 4.3335 11.667 5.15431 11.667 6.16683C11.667 7.17935 12.4878 8.00016 13.5003 8.00016Z" stroke="#9747FF" stroke-width="1.375"/>
                                <path d="M11.6667 6.1665H8" stroke="#9747FF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.9997 6.1665H15.333" stroke="#9747FF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10.75 20.8335C9.53313 20.801 8.82533 20.676 8.38662 20.196C7.7935 19.547 7.97186 18.555 8.3286 16.571L8.88907 13.4541C9.11388 12.2037 9.22629 11.5786 9.55325 11.1042C9.87486 10.6376 10.3408 10.2718 10.8902 10.0544C11.4487 9.8335 12.1325 9.8335 13.5 9.8335C14.8675 9.8335 15.5513 9.8335 16.1098 10.0544C16.6592 10.2718 17.1251 10.6376 17.4468 11.1042C17.7737 11.5786 17.8862 12.2037 18.1109 13.4541L18.6714 16.571C19.0281 18.555 19.2065 19.547 18.6134 20.196C18.1767 20.6738 17.4733 20.8001 16.2665 20.8335" stroke="#9747FF" stroke-width="1.375" stroke-linecap="round"/>
                                <path d="M13.5 19V22.6667" stroke="#9747FF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>';
                        }
                        else{
                        //  $activeRequest_id = B2BVehicleRequests::where('rider_id', $rider->id)
                        // ->orderBy('id','desc')
                        // ->first();
                        
                        //  $req_idEncode = encrypt($activeRequest_id->req_id); 
                             $actionButtons .= '
                                <a href="javascript:void(0);" style="border-radius:8px;cursor:default;" title="vehicle request has been created. you can go to vehicle request list">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 30 29" fill="none">
                                    <g clip-path="url(#clip0_8_219)">
                                    <path d="M19 1.1665H8C3.58172 1.1665 0 4.74823 0 9.1665V20.1665C0 24.5848 3.58172 28.1665 8 28.1665H19C23.4183 28.1665 27 24.5848 27 20.1665V9.1665C27 4.74823 23.4183 1.1665 19 1.1665Z" fill="#CAEDCE"></path>
                                    <path d="M13.5003 9.1667C14.5128 9.1667 15.3337 8.34585 15.3337 7.33333C15.3337 6.32081 14.5128 5.5 13.5003 5.5C12.4878 5.5 11.667 6.32081 11.667 7.33333C11.667 8.34585 12.4878 9.1667 13.5003 9.1667Z" stroke="#1E580F" stroke-width="1.375"></path>
                                    <path d="M11.6667 7.33301H8" stroke="#1E580F" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M18.9997 7.33301H15.333" stroke="#1E580F" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M10.75 22C9.53313 21.9675 8.82533 21.8425 8.38662 21.3625C7.7935 20.7135 7.97186 19.7215 8.3286 17.7375L8.88907 14.6206C9.11388 13.3702 9.22629 12.7451 9.55325 12.2707C9.87486 11.8041 10.3408 11.4383 10.8902 11.2209C11.4487 11 12.1325 11 13.5 11C14.8675 11 15.5513 11 16.1098 11.2209C16.6592 11.4383 17.1251 11.8041 17.4468 12.2707C17.7737 12.7451 17.8862 13.3702 18.1109 14.6206L18.6714 17.7375C19.0281 19.7215 19.2065 20.7135 18.6134 21.3625C18.1767 21.8403 17.4733 21.9666 16.2665 22" stroke="#1E580F" stroke-width="1.375" stroke-linecap="round"></path>
                                    <path d="M13.5 20.1665V23.8332" stroke="#1E580F" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M29.1663 4.16666C29.1663 1.86548 27.3008 0 24.9997 0C22.6985 0 20.833 1.86548 20.833 4.16666C20.833 6.46783 22.6985 8.33333 24.9997 8.33333C27.3008 8.33333 29.1663 6.46783 29.1663 4.16666Z" fill="#1E580F" stroke="white" stroke-width="0.625"></path>
                                    <path d="M23.333 4.47916C23.333 4.47916 23.9997 4.85937 24.333 5.41666C24.333 5.41666 25.333 3.22916 26.6663 2.5" stroke="white" stroke-width="0.625" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_8_219">
                                    <rect width="30" height="29" fill="white"></rect>
                                    </clipPath>
                                    </defs>
                                    </svg>
                                </a>';
                        }
                        $actionButtons .= '</div>';
                        
                 $profileImage = $rider->profile_image 
                    ? asset('b2b/profile_images/'.$rider->profile_image) 
                    : asset('b2b/img/default_profile_img.png');
        
                        return [
                            // S.No
                            $start + $index + 1,
        
                            '<img src="'.$profileImage.'" 
                                  alt="Rider Profile" 
                                  class="rounded-circle shadow-sm border border-2" 
                                  style="width:48px; height:48px; object-fit:cover; border-color:#dee2e6; padding:2px; transition:transform 0.2s ease-in-out;" 
                                  onmouseover="this.style.transform=\'scale(1.1)\'" 
                                  onmouseout="this.style.transform=\'scale(1)\'">'
                            ,
                           
                            
                            // Rider Name
                            e($rider->name ?? ''),
        
                            // Contact No
                            e($rider->mobile_no ?? ''),
        
                            // Zone Name - Updated By Gowtham.S
                            e($rider->zone->name ?? 'N/A'),
        
                            // Created Date
                            \Carbon\Carbon::parse($rider->created_at)->format('d M Y, h:i A'),
                            $actionButtons

                        ];
                    });
        
                    return response()->json([
                        'draw'            => intval($request->input('draw')),
                        'recordsTotal'    => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data'            => $formattedData
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Rider List Error: '.$e->getMessage());
        
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => 0,
                        'recordsFiltered' => 0,
                        'data' => [],
                        'error' => $e->getMessage()
                    ], 500);
                }
            }
            
            $vehicle_types = VehicleType::where('is_active', 1)->get();
            
        
        return view('b2b::rider.rider_list' , compact('vehicle_types','zones','login_type','zone_id','guard','user','customerId','accountability_Type','accountability_Types'));

        }

    
    public function getRiderDetails(Request $request)
    {
        $rider = B2BRider::find($request->id);
    
        if ($rider) {
            // prepare file paths
            $rider->aadhaar_front_url   = $rider->adhar_front   ? asset('b2b/aadhar_images/'.$rider->adhar_front) : null;
            $rider->aadhaar_back_url    = $rider->adhar_back    ? asset('b2b/aadhar_images/'.$rider->adhar_back) : null;
            $rider->pan_front_url       = $rider->pan_front       ? asset('b2b/pan_images/'.$rider->pan_front) : null;
            $rider->pan_back_url        = $rider->pan_back        ? asset('b2b/pan_images/'.$rider->pan_back) : null;
            $rider->driving_front_url   = $rider->driving_license_front ? asset('b2b/driving_license_images/'.$rider->driving_license_front) : null;
            $rider->driving_back_url    = $rider->driving_license_back  ? asset('b2b/driving_license_images/'.$rider->driving_license_back) : null;
            $rider->llr_url             = $rider->llr_image       ? asset('b2b/llr_images/'.$rider->llr_image) : null;
    
            return response()->json([
                'success' => true,
                'data' => $rider
            ]);
        }
    
        return response()->json([
            'success' => false,
            'message' => 'Rider not found'
        ], 404);
    }




 
    
        public function create_vehicle_request(Request $request)
        {
            // dd($request->all());
            // Validation
            $validator = Validator::make($request->all(), [
                'rider_id'      => 'required|exists:b2b_tbl_riders,id',
                'start_date'    => 'nullable|date|after_or_equal:today',//Updated By Gowtham.S
                'end_date'      => 'nullable|date|after_or_equal:start_date',//Updated By Gowtham.S
                'vehicle_type'  => 'required|integer',
                'battery_type'  => 'nullable|integer',
                'terms_agreed'  => 'required|in:1',
                'assign_zone'   => 'required|exists:zones,id',
                'account_ability_type'=>'required|exists:ev_tbl_accountability_types,id'
            ], [
                'rider_id.required' => 'Rider is required.',
                'rider_id.exists'   => 'Invalid rider selected.',
                'start_date.required' => 'Start date is required.',
                'start_date.after_or_equal' => 'Start date must be today or a future date.',
                'end_date.required' => 'End date is required.',
                'end_date.after_or_equal' => 'End date must be after or equal to start date.',
                'vehicle_type.required' => 'Vehicle type is required.',
                'terms_agreed.in' => 'You must agree to the terms and conditions.',
            ]);
            

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Authenticated user
            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
            $user = Auth::guard($guard)->user();
            
            if ($user->city_id == '' || $user->city_id == null) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Auth user not found!'
                ]);
            }
            
            $user->load(['city', 'zone' , 'customer_relation']);
            
            
            $customerId = $user->customer_id;
            $accountability_Types = $user->customer_relation->accountability_type_id ?? null;  //updated by Gowtham.S

            if (!is_array($accountability_Types)) {
                $accountability_Types = json_decode($accountability_Types, true) ?? [];
            }

            $accountability_Type = in_array(2, $accountability_Types) ? 2 : 1;

            $ac_types = \Modules\MasterManagement\Entities\EvTblAccountabilityType::where('status', 1)
                ->whereIn('id', $accountability_Types)
                ->get();
            
            
             $rider = B2BRider::find($request->rider_id);

            
              if ($rider->terms_condition == 1 && $rider->terms_condition_status != 1) {
                $customerTradeName = $user->customer_relation->trade_name ?? $user->customer_relation->name ?? 'N/A';
                $customerEmailID     = $user->customer_relation->email ?? 'N/A';
            
                return response()->json([
                    'success' => false,
                    'title'   => 'Cannot Create Vehicle Request',
                    'message' => "
                        Terms & Conditions Status: " . 
                            ($rider->terms_condition_status == 2 ? 'Rejected' : 'Not Accepted') . "<br><br>
                        <strong>Important:</strong> This rider does <u>not have a valid Driving License (DL) or Learner's License (LLR)</u>.<br>
                        You must accept the rider's Terms & Conditions before creating a vehicle request.<br><br>
                        Please contact your customer <strong>{$customerTradeName}</strong> at 
                        <a href='mailto:{$customerEmailID}'>{$customerEmailID}</a> for any assistance."
                ], 400);
            }
            
            
            
            
            $client_type = ($accountability_Type == 2) ? 'fixed_client' : 'variable_client';
            
            $get_rfd_vehicle_data = [];
            $get_rfd_vehicle_count = 0;

            if ($accountability_Type == 2) {
                $get_rfd_vehicle_data = \App\Helpers\CustomHandler::Get_Customer_rfd_count($customerId, $user, $guard, $accountability_Type);
                $get_rfd_vehicle_count = $get_rfd_vehicle_data['total_count'] ?? 0;
            }

            if ($client_type === 'fixed_client') {
                if ($request->account_ability_type == 2 && $get_rfd_vehicle_count == 0) {  //updated by Gowtham.S
                    return response()->json([
                        'success' => false,
                        'message'  => "Your RFD vehicle count is {$get_rfd_vehicle_count}. You cannot select Fixed Accountability Type."
                    ]);
                }
                if ($request->account_ability_type == 1 && $get_rfd_vehicle_count > 0) {
                    return response()->json([
                        'success' => false,
                        'message'  => "Your RFD vehicle count is {$get_rfd_vehicle_count}. You cannot select Variable Accountability Type."
                    ]);
                }
            }
            
           
           
           if ($client_type === 'fixed_client' && $request->account_ability_type == 2) { //updated by Gowtham.S
                $selectedZoneId = $request->assign_zone;
                $selectedZoneCount = 0;
                $zoneData = collect($get_rfd_vehicle_data['zone_data'] ?? []);
                if ($zoneData->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message'  => "Your selected zone RFD vehicle count is 0. You cannot select this zone."
                    ]);
                }
                $selectedZone = $zoneData->firstWhere('zone_id', $selectedZoneId);
            
                if ($selectedZone) {
                    $selectedZoneCount = $selectedZone['total_count'] ?? 0;
                }
                if (!$selectedZone || $selectedZoneCount == 0) {
                    return response()->json([
                        'success' => false,
                        'message'  => "Your selected zone RFD vehicle count is {$selectedZoneCount}. You cannot select this zone."
                    ]);
                }
            }

          
           


            
            $requests_validate = B2BVehicleRequests::where('rider_id', $request->rider_id)
                ->where('status', 'pending')
                ->exists();
            
            if ($requests_validate) {
                return response()->json([
                    'success' => false,
                    'message' => 'A pending vehicle request already exists for this rider.'
                ], 400);
            }

    
            $rider->assign_zone_id = $request->assign_zone;
            $rider->save();
            
            // Generate unique request ID
            do {
                $randomNumber = mt_rand(10000000, 99999999);
                $requestId = 'REQ' . $randomNumber;
            } while (B2BVehicleRequests::where('req_id', $requestId)->exists());
    
            // -----------------------------
            // Generate QR code using phpqrcode
            // -----------------------------
            require_once public_path('phpqrcode/qrlib.php');
    
            $qrDir = public_path('b2b/qr');
            if (!is_dir($qrDir)) mkdir($qrDir, 0777, true);
    
            $qrFileName = "qr_" . uniqid() . ".png";
            $qrFilePath = $qrDir . '/' . $qrFileName;
    
            // Generate PNG QR code
            \QRcode::png($requestId, $qrFilePath, QR_ECLEVEL_L, 6);
    
            // Convert QR code to Base64 for frontend
            $qrCodeBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($qrFilePath));
    
            // Save vehicle request
            $vehicleRequest = B2BVehicleRequests::create([
                'req_id'          => $requestId,
                'rider_id'        => $request->rider_id,
                'start_date'      => $request->start_date,
                'end_date'        => $request->end_date,
                'vehicle_type'    => $request->vehicle_type,
                'battery_type'    => $request->battery_type,
                'terms_condition' => $request->terms_agreed,
                'status'          => 'pending',
                'qrcode_image'    => $qrFileName,
                'created_by'      => $user->id,
                'city_id'         => $user->city_id,
                'zone_id'         => $request->assign_zone,
                'account_ability_type'=>$request->account_ability_type
                
            ]);
            
            
    

            
            // Start Mail Section
            $admins = User::whereIn('role', ['1','13'])->pluck('email')->toArray();
            
            // Agents under same city & zone
            $agents = User::where('role', 17)
                ->where('city_id', $user->city_id)
                ->where('zone_id', $user->zone_id)
                ->pluck('email')
                ->toArray();
        
            $customerEmail = $user->email ?? null;
            
      
    
            // End Mail Section
            
                        
            $agent_Arr = User::where('role', 17)
                ->where('city_id', $user->city_id)
                ->where('zone_id', $user->zone_id)
                ->where('status', 'Active')
                ->get(['id', 'phone', 'mb_fcm_token']);
            
            
            // $this->AutoRiderSendQrCodeWhatsApp($request->rider_id);
            // $this->AutoCustomerSendQrCodeWhatsApp($request->rider_id);
            $acTypeName = $vehicleRequest->accountAbilityRelation->name ?? 'N/A';
            // $this->AutoSendQrCodeWhatsApp($request->rider_id);
            // $this->pushRiderNotificationSent($rider,$requestId);
            // $this->AutoAgentSendQrCodeWhatsApp($agent_Arr,$request->rider_id);
            // $this->pushAgentNotificationSent($agent_Arr,$requestId,$acTypeName);
            
            ProcessVehicleRequestCreationJob::dispatch(
                $admins,
                $agents,
                $customerEmail,
                $user->customer_relation->email ?? null,
                $vehicleRequest,
                $rider,
                $user,
                $request->rider_id,
                $requestId,
                $agent_Arr,
                $acTypeName
            );
    
            return response()->json([
                'success' => true,
                'message' => 'Vehicle request created successfully!',
                'data'    => [
                    'request' => $vehicleRequest,
                    'rider'   => $rider,
                    'qr_code' => $qrCodeBase64
                ]
            ]);
        }
        
        
    public function pushRiderNotificationSent($rider, $requestId)
    {
        $svc = new FirebaseNotificationService();
        $title = 'New Vehicle Request!';
        $image = null;
        $notifications = [];

        $riderId    = $rider->id;
        $token      = $rider->fcm_token;
        $body       = "Dear {$rider->name}, a new Vehicle Request has been created. Your Vehicle Request ID: {$requestId}";
        $data       = [];
        $icon       = null; 

        if ($token) {
            $svc->sendToToken($token, $title, $body, $data, $image, $icon, $riderId);
        }

         $notifications[] = [
            'title'    => $title,
            'description' => $body,
            'image'    => $image,
            'status'   => 1,
            'rider_id' => $riderId,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        
        if (!empty($notifications)) {
            \DB::table('b2b_tbl_riders_notifications')->insert($notifications);
        }

    }
    public function pushAgentNotificationSent($agent_Arr, $requestId,$actypeName)
    {
        $svc = new FirebaseNotificationService();
        $title = 'New Vehicle Request!';
        $image = null;
        $notifications = [];


        foreach ($agent_Arr as $agent) {
            $agentId    = $agent->id;
            $token      = $agent->mb_fcm_token;
            $body       = "Dear Agent, a new Vehicle Request has been created. Request ID: {$requestId}, Accountability Type - {$actypeName}";
            $data       = [];
            $icon       = null; 

            if ($token) {
                $svc->sendToToken($token, $title, $body, $data, $image, $icon, $agentId);
            }
    
             $notifications[] = [
                'title'    => $title,
                'description' => $body,
                'image'    => $image,
                'status'   => 1,
                'agent_id' => $agentId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if (!empty($notifications)) {
            \DB::table('b2b_tbl_agent_notifications')->insert($notifications);
        }

    }

        
                 
        
        public function AutoAgentSendQrCodeWhatsApp($agent_mobileArr, $rider_id)
        {
            $rider = B2BRider::with('vehicleRequest', 'customerLogin.customer_relation')
                ->where('id', $rider_id)
                ->first();
        
            if (!$rider || !$rider->vehicleRequest->count()) {
                Log::info('QR File : Rider or Vehicle Request not found');
                return false;
            }
        
            $vehicleRequest = $rider->vehicleRequest->last();
        
            // WhatsApp API
            $api_key = BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
            $url = 'https://whatshub.in/api/whatsapp/send';
        
            $riderName     = $rider->name ?? 'Rider';
            $riderPhone    = $rider->mobile_no ?? 'N/A';
            $requestId     = $vehicleRequest->req_id ?? '';
            $zoneName      = $vehicleRequest->zone->name ?? 'N/A';
            $actypeName      = $vehicleRequest->accountAbilityRelation->name ?? 'N/A';
            $customerID    = $rider->customerLogin->customer_relation->id ?? 'N/A';
            $customerName  = $rider->customerLogin->customer_relation->name ?? 'N/A';
            $customerEmail = $rider->customerLogin->customer_relation->email ?? 'N/A';
            $customerPhone = $rider->customerLogin->customer_relation->phone ?? 'N/A';
        
            $footerText = BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
            $footerContentText = $footerText ??
                "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
        
            foreach ($agent_mobileArr as $agent) {
                
                $agentPhone = $agent->phone;
                // clean phone number
                $cleanedPhone = preg_replace('/\D+/', '', $agentPhone);
                if (substr($cleanedPhone, 0, 1) === '0') {
                    $cleanedPhone = substr($cleanedPhone, 1);
                }
                if (substr($cleanedPhone, 0, 2) !== '91' && strlen($cleanedPhone) === 10) {
                    $cleanedPhone = '91' . $cleanedPhone;
                }
        
                $message = "Dear Agent,\n\n"
                    . "A new Vehicle Request has been created.\n\n"
                    . "🔹 Request ID: {$requestId}\n\n"
                    . "🔹 Accountability Type: {$actypeName}\n\n"
                    . "📌 Rider Details:\n"
                    . "• Name: {$riderName}\n"
                    . "• Phone: {$riderPhone}\n\n"
                    . "📌 Customer Details:\n"
                    . "• Name: {$customerName}\n"
                    . "• ID: {$customerID}\n"
                    . "• Phone: {$customerPhone}\n\n"
                    . "📍 Assigned Zone: {$zoneName}\n\n"
                    . "{$footerContentText}";
        
                $postdata = [
                    "contact" => [
                        [
                            "number"    => $cleanedPhone,
                            "message"   => $message,
                        ]
                    ]
                ];
                
                SendWhatsappMessageJob::dispatch($agentPhone, $message);
        
                // $curl = curl_init();
                // curl_setopt_array($curl, [
                //     CURLOPT_URL => $url,
                //     CURLOPT_RETURNTRANSFER => true,
                //     CURLOPT_POST => true,
                //     CURLOPT_POSTFIELDS => json_encode($postdata),
                //     CURLOPT_HTTPHEADER => [
                //         'Api-key: ' . $api_key,
                //         'Content-Type: application/json',
                //     ],
                //     CURLOPT_TIMEOUT => 30,
                // ]);
        
                // $response = curl_exec($curl);
                // $error = curl_error($curl);
                // curl_close($curl);
        
                // if ($error) {
                //     Log::info("QR File - cURL Error for Agent {$agentPhone}: " . $error);
                // } else {
                //     $responseData = json_decode($response, true);
                //     if (!isset($responseData['success']) || $responseData['success'] != true) {
                //         Log::info("QR File - WhatsApp API Response for Agent {$agentPhone}: " . print_r($responseData, true));
                //     } else {
                //         Log::info("QR File : WhatsApp notification sent successfully to Agent {$agentPhone}");
                //     }
                // }
            }
        
            return true;
        }

        
        
        
        public function AutoSendQrCodeWhatsApp($rider_id)
        {
            $rider = B2BRider::with('vehicleRequest', 'customerLogin.customer_relation')
                ->where('id', $rider_id)
                ->first();
        
            if (!$rider || !$rider->mobile_no) {
                Log::info('QR File : Rider or mobile number not found');
                return false;
            }
        
            $vehicleRequest = $rider->vehicleRequest->last();
            if (!$vehicleRequest) {
                Log::info('QR File : No vehicle request found for this rider');
                return false;
            }
        
            $filename = $vehicleRequest->qrcode_image;
            $protocol = request()->getScheme();
            $host = request()->getHost();
            $fileUrl = "{$protocol}://{$host}/b2b/qr/{$filename}";
        
            Log::info('QR File URL Generated: ' . $fileUrl);
        
            // WhatsApp API
            $api_key = BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
            $url = 'https://whatshub.in/api/whatsapp/send';
        
            $riderName    = $rider->name ?? 'Rider';
            $riderPhone   = $rider->mobile_no;
            $requestId    = $vehicleRequest->req_id ?? '';
            $actypeName      = $vehicleRequest->accountAbilityRelation->name ?? 'N/A';
            $customerID   = $rider->customerLogin->customer_relation->id ?? 'N/A';
            $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
            $customerEmail= $rider->customerLogin->customer_relation->email ?? 'N/A';
            $customerPhone= $rider->customerLogin->customer_relation->phone ?? '';

        
            $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
            $footerContentText = $footerText ??
                "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
        
            // Build all messages
            $messages = [];
        
            // Rider message
            if (!empty($riderPhone)) {
               $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.\n" .
                 "Email: {$customerEmail}\n" .
                 "Thank you,\n" .
                 "{$customerName}";
                $messages[] = [
                    "number"    => $this->formatPhoneNumber($riderPhone),
                    "message"   => "Dear {$riderName},\n\nYour Vehicle Request ID is: {$requestId}.\nHere is your QR Code.\n\n{$CustomerfooterContentText}",
                    "media"     => "image",
                    "url"       => $fileUrl,
                    "file_name" => $filename
                ];
            }
        
            // Customer message
            if (!empty($customerPhone)) {
                $messages[] = [
                    "number"    => $this->formatPhoneNumber($customerPhone),
                    "message"   => "Dear {$customerName},\n\nNew Vehicle Request ID is: {$requestId}.\n Accountability Type - {$actypeName} \n Here is QR Code.\nRider Name: {$riderName}\nRider Phone: {$riderPhone}\n\n{$footerContentText}",
                    "media"     => "image",
                    "url"       => $fileUrl,
                    "file_name" => $filename
                ];
            }
 
            $adminPhone = BusinessSetting::where('key_name', 'admin_whatsapp_no')->value('value');
            if (!empty($adminPhone)) {
                $messages[] = [
                    "number"    => $this->formatPhoneNumber($adminPhone),
                    "message"   => "Dear Admin,\n\nNew Vehicle Request ID is: {$requestId}.\n Accountability Type - {$actypeName} \n Here is QR Code.\nCustomer Name: {$customerName}\nCustomer ID: {$customerID}\nRider Name: {$riderName}\nRider Phone: {$riderPhone}\n\n{$footerContentText}",
                    "media"     => "image",
                    "url"       => $fileUrl,
                    "file_name" => $filename
                ];
            }
            
            // $agents = User::where('role', 17) //production Database - agent id
            //     ->where('city_id', $user->city_id)
            //     ->where('zone_id', $user->zone_id)
            //     ->where('status', 'Active')
            //     ->pluck('phone')
            //     ->toArray();
        
            if (empty($messages)) {
                Log::info('QR File : No valid recipients found');
                return false;
            }
        
            // Send request to WhatsApp API
            $postdata = ["contact" => $messages];
        
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
                Log::info('QR File - cURL Error:' . $error);
                return false;
            }
        
            $responseData = json_decode($response, true);
            if (!isset($responseData['success']) || $responseData['success'] != true) {
                Log::info('QR File - WhatsApp API Response:' . print_r($responseData, true));
                return false;
            }
        
            Log::info('QR File : QR Code sent via WhatsApp successfully');
            return true;
        }
        
        // Helper to format phone number correctly
        private function formatPhoneNumber($phone)
        {
            $cleanedPhone = preg_replace('/\D+/', '', $phone);
        
            if (substr($cleanedPhone, 0, 1) === '0') {
                $cleanedPhone = substr($cleanedPhone, 1);
            }
        
            if (substr($cleanedPhone, 0, 2) === '91' && strlen($cleanedPhone) === 12) {
                return $cleanedPhone;
            } elseif (strlen($cleanedPhone) === 10) {
                return '91' . $cleanedPhone;
            }
        
            return null;
        }

        
    // public function AutoRiderSendQrCodeWhatsApp($rider_id)
    // {

    
    //     $rider = B2BRider::with('vehicleRequest')->where('id',$rider_id)->first();       
    
    //     if (!$rider || !$rider->mobile_no) {

    //         Log::info('QR File : Rider or mobile number not found');
    //         return;
    //     }

    //     // $cleanedPhone = preg_replace('/\D+/', '', $rider->mobile_no);
        
    //     $cleanedPhone = '917812880655';
        
    //     if (substr($cleanedPhone, 0, 1) === '0') {
    //         $cleanedPhone = substr($cleanedPhone, 1);
    //     }
    //     if (substr($cleanedPhone, 0, 2) === '91' && strlen($cleanedPhone) === 12) {
    //         $phone = $cleanedPhone;
    //     } elseif (strlen($cleanedPhone) === 10) {
    //         $phone = '91' . $cleanedPhone;
    //     } else {
      
    //         Log::info('QR File : Provided number is not valid');
    //         return;
    //     }
    
    //     $vehicleRequest = $rider->vehicleRequest->last();
    //         if (!$vehicleRequest) {
    //             Log::info('QR File : No vehicle request found for this rider');
    //         }

    //     $filename = $vehicleRequest->qrcode_image;
    
    //      $protocol = request()->getScheme(); // http or https
    //      $host = request()->getHost();
    //      $fileUrl = "{$protocol}://{$host}/b2b/qr/{$filename}";
    //      Log::info('QR File URL Generated: ' . $fileUrl);
    
    //     // WhatsApp API
    //     $api_key =BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
    //     $url = 'https://whatshub.in/api/whatsapp/send';
        
    //     $riderName = $rider->name ?? 'Rider';
    //     $requestId = $vehicleRequest->req_id ?? '';
        
        // $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.\n" .
        //                  "Email: {$customerEmail}\n" .
        //                  "Thank you,\n" .
        //                  "{$customerName}";
        
    //     $postdata = [
    //         "contact" => [
    //             [
    //                 "number"    => $phone,
    //                 "message"   => "Dear {$riderName},\n\nYour Vehicle Request ID is: {$requestId}.\nHere is your QR Code.\n\n {$CustomerfooterContentText}.",
    //                 "media"     => "image",
    //                 "url"       => $fileUrl,
    //                 "file_name" => $filename
    //             ]
    //         ]
    //     ];
    
    //     $curl = curl_init();
    //     curl_setopt_array($curl, [
    //         CURLOPT_URL => $url,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_POST => true,
    //         CURLOPT_POSTFIELDS => json_encode($postdata),
    //         CURLOPT_HTTPHEADER => [
    //             'Api-key: ' . $api_key,
    //             'Content-Type: application/json',
    //         ],
    //         CURLOPT_TIMEOUT => 30,
    //     ]);
    
    //     $response = curl_exec($curl);
    //     $error = curl_error($curl);
    //     curl_close($curl);
    
    //     if ($error) {

    //         Log::info('QR File - cURL Error:'.$error);
    //         return;
    //     }
    
    //     $responseData = json_decode($response, true);
    
    //     if (!isset($responseData['success']) || $responseData['success'] != true) {
          
    //          Log::info('QR File - WhatsApp API Response:'.print_r($responseData, true));
    //          return;
    //     }

    //     log::info('QR File : QR Code sent via WhatsApp successfully');

    //     return true;
    // }
    
    // public function AutoCustomerSendQrCodeWhatsApp($rider_id)
    // {

    
    //     $rider = B2BRider::with('vehicleRequest','customerLogin.customer_relation')->where('id',$rider_id)->first();       
        
    //     $riderName = $rider->name;
    //     $riderPhone = $rider->mobile_no;
    //     $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
    //     $customerEmail = $rider->customerLogin->customer_relation->email ?? 'N/A';
    //     $customerPhone = $rider->customerLogin->customer_relation->phone;
    
    //     if (!$rider || !$rider->mobile_no) {

    //         Log::info('QR File : Rider or mobile number not found');
    //         return;
    //     }

    //     // $cleanedPhone = preg_replace('/\D+/', '', $rider->mobile_no);
        
    //     $cleanedPhone = '917812880655';
        
    //     if (substr($cleanedPhone, 0, 1) === '0') {
    //         $cleanedPhone = substr($cleanedPhone, 1);
    //     }
    //     if (substr($cleanedPhone, 0, 2) === '91' && strlen($cleanedPhone) === 12) {
    //         $phone = $cleanedPhone;
    //     } elseif (strlen($cleanedPhone) === 10) {
    //         $phone = '91' . $cleanedPhone;
    //     } else {
      
    //         Log::info('QR File : Provided number is not valid');
    //         return;
    //     }
    
    //     $vehicleRequest = $rider->vehicleRequest->last();
    //         if (!$vehicleRequest) {
    //             Log::info('QR File : No vehicle request found for this rider');
    //         }

    //     $filename = $vehicleRequest->qrcode_image;
    
    //      $protocol = request()->getScheme(); // http or https
    //      $host = request()->getHost();
    //      $fileUrl = "{$protocol}://{$host}/b2b/qr/{$filename}";
    //      Log::info('QR File URL Generated: ' . $fileUrl);
    
    //     // WhatsApp API
    //     $api_key =BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
    //     $url = 'https://whatshub.in/api/whatsapp/send';
        
    //     $riderName = $rider->name ?? 'Rider';
    //     $requestId = $vehicleRequest->req_id ?? '';
        
    //     $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
    //     $footerContentText = $footerText ??
    //             "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
                
    //     $postdata = [
    //         "contact" => [
    //             [
    //                 "number"    => $phone,
    //                 "message"   => "Dear {$customerName},\n\nNew Vehicle Request ID is: {$requestId}.\nHere is QR Code.\n Rider Name: {$riderName}\nRider Phone: {$riderPhone}\n\n {$footerContentText}.",
    //                 "media"     => "image",
    //                 "url"       => $fileUrl,
    //                 "file_name" => $filename
    //             ]
    //         ]
    //     ];
    
    //     $curl = curl_init();
    //     curl_setopt_array($curl, [
    //         CURLOPT_URL => $url,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_POST => true,
    //         CURLOPT_POSTFIELDS => json_encode($postdata),
    //         CURLOPT_HTTPHEADER => [
    //             'Api-key: ' . $api_key,
    //             'Content-Type: application/json',
    //         ],
    //         CURLOPT_TIMEOUT => 30,
    //     ]);
    
    //     $response = curl_exec($curl);
    //     $error = curl_error($curl);
    //     curl_close($curl);
    
    //     if ($error) {

    //         Log::info('QR File - cURL Error:'.$error);
    //         return;
    //     }
    
    //     $responseData = json_decode($response, true);
    
    //     if (!isset($responseData['success']) || $responseData['success'] != true) {
          
    //          Log::info('QR File - WhatsApp API Response:'.print_r($responseData, true));
    //          return;
    //     }

    //     log::info('QR File : QR Code sent via WhatsApp successfully');

    //     return true;
    // }
    
    // public function AutoAdminSendQrCodeWhatsApp($rider_id)
    // {

    
    //     $rider = B2BRider::with('vehicleRequest','customerLogin.customer_relation')->where('id',$rider_id)->first();       
        
    //     $riderName = $rider->name;
    //     $riderPhone = $rider->mobile_no;
    //     $customerID = $rider->customerLogin->customer_relation->id ?? 'N/A';
    //     $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
    //     $customerEmail = $rider->customerLogin->customer_relation->email ?? 'N/A';
    //     $customerPhone = $rider->customerLogin->customer_relation->phone;
    
    //     if (!$rider || !$rider->mobile_no) {

    //         Log::info('QR File : Rider or mobile number not found');
    //         return;
    //     }

    //     // $cleanedPhone = preg_replace('/\D+/', '', $rider->mobile_no);
        
    //     $cleanedPhone = '917812880655';
        
    //     if (substr($cleanedPhone, 0, 1) === '0') {
    //         $cleanedPhone = substr($cleanedPhone, 1);
    //     }
    //     if (substr($cleanedPhone, 0, 2) === '91' && strlen($cleanedPhone) === 12) {
    //         $phone = $cleanedPhone;
    //     } elseif (strlen($cleanedPhone) === 10) {
    //         $phone = '91' . $cleanedPhone;
    //     } else {
      
    //         Log::info('QR File : Provided number is not valid');
    //         return;
    //     }
    
    //     $vehicleRequest = $rider->vehicleRequest->last();
    //         if (!$vehicleRequest) {
    //             Log::info('QR File : No vehicle request found for this rider');
    //         }

    //     $filename = $vehicleRequest->qrcode_image;
    
    //      $protocol = request()->getScheme(); // http or https
    //      $host = request()->getHost();
    //      $fileUrl = "{$protocol}://{$host}/b2b/qr/{$filename}";
    //      Log::info('QR File URL Generated: ' . $fileUrl);
    
    //     // WhatsApp API
    //     $api_key =BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
    //     $url = 'https://whatshub.in/api/whatsapp/send';
        
    //     $riderName = $rider->name ?? 'Rider';
    //     $requestId = $vehicleRequest->req_id ?? '';
        
    //     $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
    //     $footerContentText = $footerText ??
    //             "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
                
    //     $postdata = [
    //         "contact" => [
    //             [
    //                 "number"    => $phone,
    //                 "message"   => "Dear Admin,\n\nNew Vehicle Request ID is: {$requestId}.\nHere is QR Code.\n Customer Name:{$customerName} \n Customer ID: {$customerID}\n Rider Name: {$riderName}\nRider Phone: {$riderPhone}\n\n {$footerContentText}.",
    //                 "media"     => "image",
    //                 "url"       => $fileUrl,
    //                 "file_name" => $filename
    //             ]
    //         ]
    //     ];
    
    //     $curl = curl_init();
    //     curl_setopt_array($curl, [
    //         CURLOPT_URL => $url,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_POST => true,
    //         CURLOPT_POSTFIELDS => json_encode($postdata),
    //         CURLOPT_HTTPHEADER => [
    //             'Api-key: ' . $api_key,
    //             'Content-Type: application/json',
    //         ],
    //         CURLOPT_TIMEOUT => 30,
    //     ]);
    
    //     $response = curl_exec($curl);
    //     $error = curl_error($curl);
    //     curl_close($curl);
    
    //     if ($error) {

    //         Log::info('QR File - cURL Error:'.$error);
    //         return;
    //     }
    
    //     $responseData = json_decode($response, true);
    
    //     if (!isset($responseData['success']) || $responseData['success'] != true) {
          
    //          Log::info('QR File - WhatsApp API Response:'.print_r($responseData, true));
    //          return;
    //     }

    //     log::info('QR File : QR Code sent via WhatsApp successfully');

    //     return true;
    // }

    
    
    public function vehicle_request_list(Request $request)
    {
        
        $guard = Auth::guard('master')->check() ? 'master' : 'zone';
        $user = Auth::guard($guard)->user();
        $customerId = $user->customer_id;
        
        $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
            ->where('city_id', $user->city_id)
            ->pluck('id');
            
        $user->load('customer_relation');
            
        $accountability_Types = $user->customer_relation->accountability_type_id;

        // Make sure it's an array (sometimes could be stored as string or null)
        if (!is_array($accountability_Types)) {
            $accountability_Types = json_decode($accountability_Types, true) ?? [];
        }
        
        if ($request->ajax()) {
            try {
            $start  = $request->input('start', 0);
            $length = $request->input('length', 25);
            $search = $request->input('search.value');

            $query = B2BVehicleRequests::with('rider','zone','city','accountAbilityRelation');
            
            // if ($guard === 'master') {
                    // $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
                    //     ->where('city_id', $user->city_id)
                    //     ->pluck('id');
    
            //         $query->whereHas('rider.customerLogin', function ($q) use ($customerLoginIds) {
            //             $q->whereIn('id', $customerLoginIds);
            //         });
    
            //     } elseif ($guard === 'zone') {
            //         $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
            //             ->where('city_id', $user->city_id)
            //             ->where('zone_id', $user->zone_id)
            //             ->pluck('id');
    
            //         $query->whereHas('rider.customerLogin', function ($q) use ($customerLoginIds) {
            //             $q->whereIn('id', $customerLoginIds);
            //         });
            //     }
            
            if(!empty($customerLoginIds)){
                $query->whereIn('created_by', $customerLoginIds);
            }
            
            if ($guard === 'master') {  
                // Filter by city
                $query->where('city_id', $user->city_id);
            }
            
            if ($guard === 'zone') {
                // Filter by zone
                $query->where('zone_id', $user->zone_id);
            }

            if ($request->filled('status') && $request->status != 'all') {
                $query->where('status', $request->status);
            }
            if (!empty($request->accountability_type)) {
                $query->where('account_ability_type', $request->accountability_type);
            }
            
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereDate('created_at', '>=', $request->from_date)
                      ->whereDate('created_at', '<=', $request->to_date);
            }

            
            if (!empty($search)) {
                $query->whereHas('rider', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('mobile_no', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $totalRecords = $query->count();

            if ($length == -1) {
                $length = $totalRecords;
            }

            $datas = $query->orderBy('id', 'desc')
                           ->skip($start)
                           ->take($length)
                           ->get();

            $formattedData = $datas->map(function ($item) {
                
                     if ($item->status === 'completed' && $item->completed_at) {
                    $created   = \Carbon\Carbon::parse($item->created_at);
                    $completed = \Carbon\Carbon::parse($item->completed_at);
                    $diffInDays = $created->diffInDays($completed);
                    $diffInHours = $created->diffInHours($completed);
                    $diffInMinutes = $created->diffInMinutes($completed);
                
                    if ($diffInDays > 0) {
                        $aging = $diffInDays . ' days';
                    } elseif ($diffInHours > 0) {
                        $aging = $diffInHours . ' hours';
                    } else {
                        $aging = $diffInMinutes . ' mins';
                    }
                } else {
                    $created   = \Carbon\Carbon::parse($item->created_at);
                    $now       = now();
                    $diffInDays = $created->diffInDays($now);
                    $diffInHours = $created->diffInHours($now);
                    $diffInMinutes = $created->diffInMinutes($now);
                
                    if ($diffInDays > 0) {
                        $aging = $diffInDays . ' days';
                    } elseif ($diffInHours > 0) {
                        $aging = $diffInHours . ' hours';
                    } else {
                        $aging = $diffInMinutes . ' mins';
                    }
                }
                
                
                
                $statusColumn = '';
                if ($item->status === 'pending') {
                    $statusColumn = '
                        <span style="background-color:#EECACB; color:#721c24;" class="px-2 py-1 rounded-pill">
                            <i class="bi bi-x-circle me-1"></i> Pending
                        </span>';
                } elseif ($item->status === 'completed') {
                    $statusColumn = '
                        <span style="background-color:#CAEDCE; color:#155724;" class="px-2 py-1 rounded-pill">
                            <i class="bi bi-check-circle me-1"></i> Completed
                        </span>';
                }

                $rider = $item->rider;
                $requestId = $item->req_id;
                $idEncode = encrypt($item->id); // for route link

                return [
                    '<div class="form-check">
                        <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox" value="'.$item->id.'">
                    </div>',
                    $requestId,
                    e($item->accountAbilityRelation->name ?? 'N/A'), //Updated By Gowtham.S
                    e($rider->name ?? ''),
                    e($rider->mobile_no ?? ''),
                    e($item->zone->name ?? 'N/A'),  // Zone Name - Updated By Gowtham.S
                    \Carbon\Carbon::parse($item->created_at)->format('d M Y, h:i A'),
                    \Carbon\Carbon::parse($item->updated_at)->format('d M Y, h:i A'),
                    $aging,
                    $statusColumn,
                    '<a href="'.route('b2b.vehicle_request.vehicle_request_view', $idEncode).'"
                        class="d-flex align-items-center justify-content-center border-0" title="view"
                        style="background-color:#CAEDCE;color:#155724;border-radius:8px;width:35px;height:31px;">
                        <i class="bi bi-eye fs-5"></i>
                    </a>'
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $formattedData
            ]);
            } catch (\Exception $e) {
                \Log::error('Vehicle Request List Error: '.$e->getMessage());
    
                return response()->json([
                    'draw' => intval($request->input('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        $accountability_types = EvTblAccountabilityType::where('status', 1)
        ->whereIn('id',$accountability_Types)
        ->orderBy('id', 'desc')
        ->get();
    
        return view('b2b::vehicles.vehicle_request_list', compact('accountability_types'));
    }

    
    public function rider_view(Request $request , $id){
        
        $decrypt_id = decrypt($id);
        
       $rider = B2BRider::where('id', $decrypt_id)->first();
       
       
       
        return view('b2b::rider.rider_view' , compact('rider'));
        
    }
    
    // public function vehicle_request_view(Request $request , $id)
    // {
    //     $decrypt_id = decrypt($id);
        
    //   $request = B2BVehicleRequests::with('rider')->where('id', $decrypt_id)->first();
       
    // $vehicle_types = VehicleType::where('is_active', 1)->get();
        
        
    //     return view('b2b::vehicles.vehicle_request_view' , compact('request' ,'vehicle_types'));
    // }
    
     public function vehicle_request_view(Request $request , $id)
    {
        $decrypt_id = decrypt($id);
        
       $request = B2BVehicleRequests::with('rider','assignment','city','vehicle_type_relation')->where('id', $decrypt_id)->first();
       if(!empty($request->assignment)){
           $vehicle = AssetVehicleInventory::
            join('ev_tbl_asset_master_vehicles as amv', 'asset_vehicle_inventories.asset_vehicle_id', '=', 'amv.id')
            ->leftJoin('ev_tbl_inventory_location_master as ilm', 'asset_vehicle_inventories.transfer_status', '=', 'ilm.id')
            ->select(
                'ilm.name as vehicleStatus')
            ->where('amv.id',$request->assignment->asset_vehicle_id)
            ->first();
            $closed_by = '';
        if($request->closed_by){
              $closed_by = B2BAgent::select('name')->where('id',$request->closed_by)->first();
        } 
        $vehicle->closed_by = $closed_by;
       }else{
           $vehicle = '';
       }
      
          
    $vehicle_types = VehicleType::where('is_active', 1)->get();
        
        
        return view('b2b::vehicles.vehicle_request_view' , compact('request' ,'vehicle_types','vehicle'));
    }
    
    public function vehicle_return_request(Request $request,$id)
    {
        $decrypt_id = decrypt($id);
        $data = B2BVehicleAssignment::with('vehicle' ,'rider.customerLogin.customer_relation')
            ->where('id', $decrypt_id)
            ->first();
      
        return view('b2b::vehicle_list.return_request',compact('data'));
    }
    
        public function vehicle_details_view(Request $request , $id)
    {
        $vehicle_types = VehicleType::where('is_active', 1)->get();
        $inventory_locations = InventoryLocationMaster::where('status',1)->get();
        $assign_id = decrypt($id);
        
        $data = B2BVehicleAssignment::with('vehicle' ,'rider')
            ->where('id', $assign_id)
            ->first();
        $inventory_data = AssetVehicleInventory::where('asset_vehicle_id',$data->vehicle->id)->first();
            
        $locations = City::where('status', 1)->get();
        
        return view('b2b::vehicle_list.vehicle_details_view' , compact('vehicle_types' , 'inventory_locations' , 'data' ,'locations','inventory_data'));
        
    }
    
    
     public function rider_details_view(Request $request , $id)
    {
         $assign_id = decrypt($id);
         
         $data = B2BVehicleAssignment::with('rider')
            ->where('id', $assign_id)
            ->first();

        return view('b2b::vehicle_list.rider_details_view' , compact('data'));
        
    }
    
    
        
     public function vehicle_service_request(Request $request , $id)
    {
        
        $assign_id = decrypt($id);
         
         $data = B2BVehicleAssignment::with('rider' ,'vehicle','vehicleRequest')
            ->where('id', $assign_id)
            ->first();
            
            
        $vehicle_types = VehicleType::where('is_active', 1)->get();
        $cities = City::where('status',1)->where('id',$data->vehicleRequest->city_id)->get();
        $zones = Zones::where('status',1)->where('city_id',$data->vehicleRequest->city_id)->get();
        
        $apiKey = BusinessSetting::where('key_name', 'google_map_api_key')->value('value');
        $repair_types = RepairTypeMaster::where('status',1)->get();
        
        return view('b2b::vehicle_list.service_request' , compact('vehicle_types' ,'data' ,'cities' ,'apiKey','zones' , 'repair_types'));
        
    }
    
    //      public function vehicle_recovery_request(Request $request , $id)
    // {
        
    //     $decrypt_id = decrypt($id);
    //     $data = B2BVehicleAssignment::with('vehicle' ,'rider.customerLogin.customer_relation')
    //         ->where('id', $decrypt_id)
    //         ->first();
            
    //     return view('b2b::vehicle_list.recovery_request',compact('data'));
        
    // }
    
    public function vehicle_recovery_request(Request $request, $id)
{
    $guard = Auth::guard('master')->check() ? 'master' : 'zone';
    $user  = Auth::guard($guard)->user();
    $customerId = $user->customer_id;
    $user->load('customer_relation');

    $decrypt_id = decrypt($id);

    $data = B2BVehicleAssignment::with('vehicle', 'vehicleRequest', 'rider.customerLogin.customer_relation')
        ->where('id', $decrypt_id)
        ->first();
    
    $create = true;
    $reason = '';

    // 🛑 Step 0: Prevent creation on Sundays
    if (now()->isSunday()) {
        $create = false;
        $reason = 'Recovery requests cannot be created on Sundays.';
    }
    
    // if (now()->isFriday()) {
    //     $create = false;
    //     $reason = 'Recovery requests cannot be created on Fridays.';
    // }
    
    // ✅ Step 1: Total vehicles count
    $totalVehicles = AssetMasterVehicle::whereHas('quality_check', function ($query) use ($user, $guard, $customerId) {
        if ($guard === 'master') {
            $query->where('location', $user->city_id);
        } elseif ($guard === 'zone') {
            $query->where('location', $user->city_id)
                  ->where('zone_id', $user->zone_id);
        }
        $query->where('customer_id', $customerId);
    })->count();

    // ✅ Step 2: 2% of total vehicles (allowed per day)
    $allowedRequests = ceil(($totalVehicles * 2) / 100);

    // ✅ Step 3: Get today's recovery requests
    $today = now()->toDateString();
    $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
        ->where('city_id', $user->city_id)
        ->pluck('id');

    $totalRecoveryRequestsToday = B2BRecoveryRequest::whereDate('created_at', $today)
        ->whereHas('assignment.vehicleRequest', function ($q) use ($customerLoginIds, $guard, $user) {
            if ($customerLoginIds->isNotEmpty()) {
                $q->whereIn('created_by', $customerLoginIds);
            }

            if ($guard === 'master') {
                $q->where('city_id', $user->city_id);
            } elseif ($guard === 'zone') {
                $q->where('city_id', $user->city_id)
                  ->where('zone_id', $user->zone_id);
            }
        })
        ->count();

    // ✅ Step 4: Check remaining requests allowed
    $remainingCount = $allowedRequests - $totalRecoveryRequestsToday;
    
    if ($remainingCount <= 0 && $reason === '') {
        $create = false;
        $reason = "You’ve reached your daily limit of recovery requests ({$allowedRequests}), which is 2% of your total vehicles. You can create new requests tomorrow.";
    } else {
        $create = true;
        $reason = "You can create {$remainingCount} more recovery request" . ($remainingCount > 1 ? 's' : '') . " today (out of {$allowedRequests} allowed).";
    }


    // ✅ Return view with validation data
    return view('b2b::vehicle_list.recovery_request', compact(
        'data',
        'remainingCount',
        'allowedRequests',
        'totalRecoveryRequestsToday',
        'totalVehicles',
        'create',
        'reason'
    ));
}

    

    public function store(Request $request): RedirectResponse
    {
        //
    }
    
    public function edit($id)
    {
        return view('b2b::edit');
    }

  
     public function vehicle_list(Request $request)
    {
        
                $guard = Auth::guard('master')->check() ? 'master' : 'zone';
                $user  = Auth::guard($guard)->user();
                $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                
                $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
                ->where('city_id', $user->city_id)
                ->pluck('id');
                
                $user->load('customer_relation');


                $accountability_Types = $user->customer_relation->accountability_type_id;
            
                // Make sure it's an array (sometimes could be stored as string or null)
                if (!is_array($accountability_Types)) {
                    $accountability_Types = json_decode($accountability_Types, true) ?? [];
                }
                $accountability_type = $request->accountability_type;
                
                
        if ($request->ajax()) {
            try {
                
                // Pagination and ordering inputs from DataTables
                $start  = $request->input('start', 0);
                $length = $request->input('length', 25);
                $search = $request->input('search.value');
    

                // Base query
               $query = B2BVehicleAssignment::with(['rider', 'vehicle.vehicle_type_relation','vehicle.vehicle_model_relation','vehicle.quality_check', 'zone','VehicleRequest','VehicleRequest.accountAbilityRelation' , 'recovery_Request']);
    
 
          
                
                $query->whereHas('VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds ,$accountability_type) {
                    // Always filter by created_by if IDs exist
                    if ($customerLoginIds->isNotEmpty()) {
                        $q->whereIn('created_by', $customerLoginIds);
                    }
                
                    // Apply guard-specific filters
                    if ($guard === 'master') {
                        $q->where('city_id', $user->city_id);
                    }
                    if (!empty($accountability_type)) {
                        $q->where('account_ability_type', $accountability_type);
                    }
                
                    if ($guard === 'zone') {
                        $q->where('city_id', $user->city_id)
                          ->where('zone_id', $user->zone_id);
                    }
                });

        
            
                if ($request->filled('from_date') && $request->filled('to_date')) {
                    $query->whereDate('created_at', '>=', $request->from_date)
                          ->whereDate('created_at', '<=', $request->to_date);
                }
                
                
                // Search filter
                if (!empty($search)) {
                    $query->where(function ($q) use ($search) {
                        $q->where('id', 'like', "%{$search}%")
                         
                          ->orWhereHas('vehicle', function ($v) use ($search) {
                              $v->where('permanent_reg_number', 'like', "%{$search}%");
                          })
                          ->orWhereHas('VehicleRequest', function ($v) use ($search) {
                              $v->where('req_id', 'like', "%{$search}%");
                          })
                          ->orWhereHas('vehicle', function ($v) use ($search) {
                              $v->where('chassis_number', 'like', "%{$search}%");
                          })
                          ->orWhereHas('vehicle.vehicle_model_relation', function ($v) use ($search) {
                              $v->where('vehicle_model', 'like', "%{$search}%");
                          })
                          ->orWhereHas('rider', function ($v) use ($search) {
                              $v->where('name', 'like', "%{$search}%");
                          })
                          ->orWhereHas('rider', function ($v) use ($search) {
                              $v->where('mobile_no', 'like', "%{$search}%");
                          })
                          ->orWhereHas('zone', function ($z) use ($search) {
                              $z->where('name', 'like', "%{$search}%");
                          });
                    });
                }
    
                    if(!empty($request->status)){
                  $query->where('status' , $request->status);
                }
              $query->whereNotIn('status', ['returned']);
              
              
                // Total count
                $totalRecords = $query->count();
    
                // Handle "Show All"
                if ($length == -1) {
                    $length = $totalRecords;
                }
    
                // Paginate
                $datas = $query->orderBy('id', 'desc')
                               ->skip($start)
                               ->take($length)
                               ->get();
                // print_r($datas);exit;               
                               
                // Format for DataTables
                
                $formattedData = $datas->map(function ($data) {
                    $id_encode  = encrypt($data->id);
                    
                    $status = $data->status;
                    
                    $city_name = $data->rider->customerLogin->city->city_name ?? '';
                    
                    $recovery_status = $data->vehicle->quality_check->is_recoverable;
                
                    $statusBadge = '';

                    if ($data->status === 'running') {
                        $statusBadge = '<span class="badge-status badge-running">
                                            <i class="bi bi-check-circle"></i> Running
                                        </span>';
                    } elseif ($data->status === 'accident') {
                        $statusBadge = '<span class="badge-status badge-accident">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <g clip-path="url(#clip0_1839_1951)">
                                                    <path d="M5.93766 15.8335C5.93766 16.708 5.22878 17.4169 4.35433 17.4169C3.47988 17.4169 2.771 16.708 2.771 15.8335M5.93766 15.8335C5.93766 14.9591 5.22878 14.2502 4.35433 14.2502C3.47988 14.2502 2.771 14.9591 2.771 15.8335M5.93766 15.8335H7.521C7.95823 15.8335 8.31266 15.4791 8.31266 15.0419V12.6783C8.31266 12.4227 8.18916 12.1828 7.98111 12.0342L5.54183 10.2919M2.771 15.8335H1.5835M5.54183 10.2919H1.5835M5.54183 10.2919L3.0074 6.67118C2.85924 6.45952 2.61712 6.33347 2.35876 6.3335L1.5835 6.33357" stroke="#DC2626" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M13.0625 15.8335C13.0625 16.708 13.7714 17.4169 14.6458 17.4169C15.5203 17.4169 16.2292 16.708 16.2292 15.8335M13.0625 15.8335C13.0625 14.9591 13.7714 14.2502 14.6458 14.2502C15.5203 14.2502 16.2292 14.9591 16.2292 15.8335M13.0625 15.8335H11.4792C11.0419 15.8335 10.6875 15.4791 10.6875 15.0419V12.6783C10.6875 12.4227 10.811 12.1828 11.0191 12.0342L13.4583 10.2919M16.2292 15.8335H17.4167M13.4583 10.2919L15.9928 6.67118C16.1409 6.45952 16.3831 6.33347 16.6414 6.3335L17.4167 6.33357M13.4583 10.2919H17.4167" stroke="#DC2626" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M7.52067 7.91678L5.5415 5.89446L7.12484 5.54178L6.01075 2.38045L8.70817 3.56262L9.8583 1.5835L10.6873 4.75012L13.4582 3.9453L11.9022 7.91686" stroke="#DC2626" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M9.89583 7.91683L9.5 6.3335" stroke="#DC2626" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_1839_1951">
                                                        <rect width="19" height="19" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                            Accident
                                        </span>';
                    }elseif ($data->status === 'under_maintenance') { 
                                            $statusBadge = '<span class="badge-status badge-ticket" style="background-color:#dbeafe; color:#1d4ed8; border-color:#1d4ed8;">
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.6022 6.05671C15.7267 5.9323 15.9411 5.91962 16.0671 6.05715C16.8055 6.86252 17.2141 7.45886 17.3487 8.11926C17.4262 8.49926 17.4372 8.88599 17.3813 9.26219C17.2302 10.2788 16.4065 11.1026 14.759 12.75L12.7495 14.7595C11.1021 16.407 10.2784 17.2307 9.2617 17.3818C8.8855 17.4377 8.49877 17.4266 8.11877 17.3492C7.45844 17.2146 6.86216 16.8061 6.05693 16.0678C5.91923 15.9416 5.93196 15.727 6.05651 15.6025C6.75016 14.9088 6.71719 13.7513 5.9829 13.0169C5.2486 12.2826 4.09102 12.2497 3.39737 12.9434C3.27283 13.0679 3.05821 13.0806 2.93197 12.9429C2.19375 12.1377 1.78517 11.5414 1.65061 10.8811C1.57318 10.5011 1.56217 10.1143 1.61807 9.73814C1.76915 8.72148 2.59286 7.89773 4.24029 6.25031L6.24982 4.24078C7.89724 2.59335 8.72099 1.76964 9.73765 1.61855C10.1138 1.56266 10.5006 1.57367 10.8806 1.6511C11.541 1.78567 12.1373 2.19432 12.9427 2.93271C13.0802 3.05881 13.0676 3.27317 12.9431 3.39758C12.2494 4.09122 12.2825 5.24879 13.0167 5.9831C13.751 6.7174 14.9087 6.75036 15.6022 6.05671Z" stroke="#2563EB" stroke-width="1.1875" stroke-linejoin="round"/>
                        <path d="M15.0417 11.8752L7.125 3.9585" stroke="#2563EB" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg> Under Maintenance
                      </span>';   
                        
                    }
                    elseif ($data->status === 'recovery_request') { 
                        
                        $recoveryStatus = $data->recovery_Request->created_by_type ?? null;
                           
             
                        $status_Text = 'Client Recovery Initiated';
                    
                    
                        // Conditional override
                        if ($recoveryStatus === 'b2b-admin-dashboard') {
                            $status_Text = 'GDM Recovery Initiated';
                            
                        }
                        
                    $statusBadge = '<span class="badge-status badge-gdm-init '.$recoveryStatus.'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                        <path d="M13.4601 16.6252C14.3343 16.6252 15.0429 15.9163 15.0429 15.0418C15.0429 14.1674 14.3343 13.4585 13.4601 13.4585C12.5861 13.4585 11.8774 14.1674 11.8774 15.0418C11.8774 15.9163 12.5861 16.6252 13.4601 16.6252Z" stroke="#A6661D" stroke-width="1.1875"/>
                        <path d="M5.54658 16.6252C6.42069 16.6252 7.12929 15.9163 7.12929 15.0418C7.12929 14.1674 6.42069 13.4585 5.54658 13.4585C4.67247 13.4585 3.96387 14.1674 3.96387 15.0418C3.96387 15.9163 4.67247 16.6252 5.54658 16.6252Z" stroke="#A6661D" stroke-width="1.1875"/>
                        <path d="M9.50293 9.5L4.75479 2.375M4.75479 2.375L6.3375 10.2917M4.75479 2.375H3.03819C2.88854 2.375 2.7623 2.50244 2.74376 2.67225L2.47985 5.08928C2.97146 5.08928 3.37 5.545 3.37 6.10715C3.37 6.66929 2.97146 7.125 2.47985 7.125C2.09227 7.125 1.71156 6.84177 1.58936 6.44643M15.0425 15.0417C17.1659 15.0417 17.4165 14.307 17.4165 12.2807C17.4165 11.3109 17.4165 10.826 17.2265 10.4166C17.0281 9.98909 16.6706 9.72277 15.9187 9.17787C15.1719 8.63669 14.6409 8.02829 14.135 7.19023C13.4134 5.995 13.0527 5.39739 12.5116 5.0737C11.9706 4.75 11.3324 4.75 10.0561 4.75H9.50293V10.2917" stroke="#A6661D" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3.96365 15.0384C3.96365 15.0384 3.04573 15.0465 2.76084 15.0099C2.52343 14.9149 2.23495 14.692 2.04862 14.5681C1.47885 14.1893 1.5928 14.3449 1.5928 14.0029C1.5928 13.4682 1.58958 11.0882 1.58958 11.0882V10.3282C1.58958 10.2807 1.54075 10.2908 1.90612 10.2965H17.0052M7.12918 15.0431H11.8773" stroke="#A6661D" stroke-width="1.1875" stroke-linecap="round"/>
                        </svg>'.$status_Text.'</span>';   
                        
                    }
                    
                    elseif ($data->status === 'return_request') { 
                    $statusBadge = '
                        <span class="badge-status d-inline-flex align-items-center px-2 py-1" 
                              style="background-color:#EEE9CA; font-size:14px; font-weight:500; gap:6px; line-height:1;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 23 23" fill="none">
                                <rect width="24" height="24" rx="8" fill="#EEE9CA"/>
                                <path d="M4.3335 9.8335V20.8335C4.3335 21.846 5.15431 22.6668 6.16683 22.6668H20.8335C21.846 22.6668 22.6668 21.846 22.6668 20.8335V9.8335" stroke="#58490F" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6.57677 5.34694L4.3335 9.8335H22.6668L20.4236 5.34694C20.113 4.72584 19.4782 4.3335 18.7837 4.3335H8.21656C7.52214 4.3335 6.88733 4.72583 6.57677 5.34694Z" stroke="#58490F" stroke-width="1.375" stroke-linejoin="round"/>
                                <path d="M13.5 9.8335V4.3335" stroke="#58490F" stroke-width="1.375"/>
                                <path d="M10.2918 15.3333H15.3335C16.346 15.3333 17.1668 16.1541 17.1668 17.1667C17.1668 18.1792 16.346 19 15.3335 19H14.4168M11.6668 13.5L9.8335 15.3333L11.6668 17.1667" stroke="#58490F" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Return Request
                        </span>';
  
                        
                    }
                    
                    elseif ($data->status === 'recovered') { 
                    $statusBadge = '<span class="badge-status badge-gdm-init">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                        <path d="M13.4601 16.6252C14.3343 16.6252 15.0429 15.9163 15.0429 15.0418C15.0429 14.1674 14.3343 13.4585 13.4601 13.4585C12.5861 13.4585 11.8774 14.1674 11.8774 15.0418C11.8774 15.9163 12.5861 16.6252 13.4601 16.6252Z" stroke="#A6661D" stroke-width="1.1875"/>
                        <path d="M5.54658 16.6252C6.42069 16.6252 7.12929 15.9163 7.12929 15.0418C7.12929 14.1674 6.42069 13.4585 5.54658 13.4585C4.67247 13.4585 3.96387 14.1674 3.96387 15.0418C3.96387 15.9163 4.67247 16.6252 5.54658 16.6252Z" stroke="#A6661D" stroke-width="1.1875"/>
                        <path d="M9.50293 9.5L4.75479 2.375M4.75479 2.375L6.3375 10.2917M4.75479 2.375H3.03819C2.88854 2.375 2.7623 2.50244 2.74376 2.67225L2.47985 5.08928C2.97146 5.08928 3.37 5.545 3.37 6.10715C3.37 6.66929 2.97146 7.125 2.47985 7.125C2.09227 7.125 1.71156 6.84177 1.58936 6.44643M15.0425 15.0417C17.1659 15.0417 17.4165 14.307 17.4165 12.2807C17.4165 11.3109 17.4165 10.826 17.2265 10.4166C17.0281 9.98909 16.6706 9.72277 15.9187 9.17787C15.1719 8.63669 14.6409 8.02829 14.135 7.19023C13.4134 5.995 13.0527 5.39739 12.5116 5.0737C11.9706 4.75 11.3324 4.75 10.0561 4.75H9.50293V10.2917" stroke="#A6661D" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3.96365 15.0384C3.96365 15.0384 3.04573 15.0465 2.76084 15.0099C2.52343 14.9149 2.23495 14.692 2.04862 14.5681C1.47885 14.1893 1.5928 14.3449 1.5928 14.0029C1.5928 13.4682 1.58958 11.0882 1.58958 11.0882V10.3282C1.58958 10.2807 1.54075 10.2908 1.90612 10.2965H17.0052M7.12918 15.0431H11.8773" stroke="#A6661D" stroke-width="1.1875" stroke-linecap="round"/>
                        </svg>
                         Recovered</span>';   
                        
                    }
                    
                    else {
                        $statusBadge = '<span class="badge-status badge-default">Unknown</span>';
                    }
                    
                    $actions = '<div class="d-flex gap-0 action-icons">
                            <a href="' . route('b2b.vehicle_details_view', $id_encode) . '" class="btn" data-bs-toggle="tooltip" data-bs-placement="top" title="View Vehicle Details">
                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                            <rect width="27" height="27" rx="8" fill="#CAEDCE"/>
                            <path d="M22.2488 12.6247C22.5275 13.0155 22.6668 13.211 22.6668 13.5002C22.6668 13.7894 22.5275 13.9848 22.2488 14.3756C20.9966 16.1315 17.7986 19.9168 13.5002 19.9168C9.20171 19.9168 6.00375 16.1315 4.75153 14.3756C4.47284 13.9848 4.3335 13.7894 4.3335 13.5002C4.3335 13.211 4.47284 13.0155 4.75153 12.6247C6.00375 10.8688 9.20171 7.0835 13.5002 7.0835C17.7986 7.0835 20.9966 10.8688 22.2488 12.6247Z" stroke="#1E580F" stroke-width="1.375"/>
                            <path d="M16.25 13.5C16.25 11.9812 15.0188 10.75 13.5 10.75C11.9812 10.75 10.75 11.9812 10.75 13.5C10.75 15.0188 11.9812 16.25 13.5 16.25C15.0188 16.25 16.25 15.0188 16.25 13.5Z" stroke="#1E580F" stroke-width="1.375"/>
                            </svg>
                            </a>
                            <a href="' . route('b2b.rider_details_view', $id_encode) . '" class="btn" data-bs-toggle="tooltip" data-bs-placement="top" title="View Rider Details">
                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                            <rect width="27" height="27" rx="8" fill="#E1D2FF"/>
                            <path d="M13.4998 8.00016C14.5124 8.00016 15.3332 7.17935 15.3332 6.16683C15.3332 5.15431 14.5124 4.3335 13.4998 4.3335C12.4873 4.3335 11.6665 5.15431 11.6665 6.16683C11.6665 7.17935 12.4873 8.00016 13.4998 8.00016Z" stroke="#9747FF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.6667 6.1665H8" stroke="#9747FF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M19.0002 6.1665H15.3335" stroke="#9747FF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.25 20.8335H17.5685C18.7613 20.8335 19.6364 19.7126 19.3471 18.5555L17.5138 11.2222C17.3097 10.406 16.5765 9.8335 15.7352 9.8335H11.2647C10.4235 9.8335 9.69018 10.406 9.48615 11.2222L7.65281 18.5555C7.36353 19.7126 8.23869 20.8335 9.4314 20.8335H10.75" stroke="#9747FF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M13.5 19V22.6667" stroke="#9747FF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg> 
                            </a>';
                        if ($data->status == 'recovery_request' || $data->status == 'recovered' || $data->status == 'return_request') {
                            $actions .= '</div>';
                        } 
                        elseif  ($data->status == 'under_maintenance' || $data->status == 'accident') {
                          $actions.='<a href="' . route('b2b.return_request', $id_encode) . '" class="btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Return Request">
                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                            <rect width="27" height="27" rx="8" fill="#EEE9CA"/>
                            <path d="M4.3335 9.8335V20.8335C4.3335 21.846 5.15431 22.6668 6.16683 22.6668H20.8335C21.846 22.6668 22.6668 21.846 22.6668 20.8335V9.8335" stroke="#58490F" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.57677 5.34694L4.3335 9.8335H22.6668L20.4236 5.34694C20.113 4.72584 19.4782 4.3335 18.7837 4.3335H8.21656C7.52214 4.3335 6.88733 4.72583 6.57677 5.34694Z" stroke="#58490F" stroke-width="1.375" stroke-linejoin="round"/>
                            <path d="M13.5 9.8335V4.3335" stroke="#58490F" stroke-width="1.375"/>
                            <path d="M10.2918 15.3333H15.3335C16.346 15.3333 17.1668 16.1541 17.1668 17.1667C17.1668 18.1792 16.346 19 15.3335 19H14.4168M11.6668 13.5L9.8335 15.3333L11.6668 17.1667" stroke="#58490F" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            </a>
                            </div>'  ;
                        }else{
                          $actions.='<a href="' . route('b2b.service_request', $id_encode) . '" class="btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Service Request">
                             <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none" >
                            <rect width="27" height="27" rx="8" fill="#D8E4FE"/>
                            <path d="M13.9582 9.8335L11.6665 13.5002H15.3332L13.0415 17.1668" stroke="#2563EB" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4.3335 11.6969V15.3028C6.95212 15.3028 8.65489 18.1471 7.32912 20.4053L10.5045 22.2082C11.1758 21.0648 12.3379 20.4055 13.5002 20.4053C14.6624 20.4055 15.8246 21.0648 16.4957 22.2082L19.6712 20.4053C18.3454 18.1471 20.0482 15.3028 22.6668 15.3028V11.6969C20.0482 11.6969 18.3439 8.85266 19.6698 6.59441L16.4944 4.7915C15.8234 5.93433 14.6619 6.62464 13.5002 6.62489C12.3385 6.62464 11.1769 5.93433 10.506 4.7915L7.33057 6.59441C8.65637 8.85266 6.95217 11.6969 4.3335 11.6969Z" stroke="#2563EB" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            </a>
                            <a href="' . route('b2b.return_request', $id_encode) . '" class="btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Return Request">
                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none" >
                            <rect width="27" height="27" rx="8" fill="#EEE9CA"/>
                            <path d="M4.3335 9.8335V20.8335C4.3335 21.846 5.15431 22.6668 6.16683 22.6668H20.8335C21.846 22.6668 22.6668 21.846 22.6668 20.8335V9.8335" stroke="#58490F" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.57677 5.34694L4.3335 9.8335H22.6668L20.4236 5.34694C20.113 4.72584 19.4782 4.3335 18.7837 4.3335H8.21656C7.52214 4.3335 6.88733 4.72583 6.57677 5.34694Z" stroke="#58490F" stroke-width="1.375" stroke-linejoin="round"/>
                            <path d="M13.5 9.8335V4.3335" stroke="#58490F" stroke-width="1.375"/>
                            <path d="M10.2918 15.3333H15.3335C16.346 15.3333 17.1668 16.1541 17.1668 17.1667C17.1668 18.1792 16.346 19 15.3335 19H14.4168M11.6668 13.5L9.8335 15.3333L11.6668 17.1667" stroke="#58490F" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            </a>';
                        if($recovery_status == 1){
                            $actions.='
                            <a href="' . route('b2b.recovery_request', $id_encode) . '" class="btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Recovery Request">
                          <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                            <rect width="27" height="27" rx="8" fill="#A1DBD0"/>
                            <path d="M8.68754 13.5C8.68754 13.7317 8.70404 13.9641 8.73636 14.1916L7.37511 14.3862C7.33305 14.0927 7.31214 13.7965 7.31254 13.5C7.31254 10.0879 10.0887 7.3125 13.5 7.3125C14.9046 7.3125 16.2803 7.7965 17.3741 8.67444L16.5127 9.74694C15.6605 9.05723 14.5963 8.68299 13.5 8.6875C10.8463 8.6875 8.68754 10.8463 8.68754 13.5ZM8.00004 16.25C8.00004 16.4323 8.07248 16.6072 8.20141 16.7361C8.33034 16.8651 8.50521 16.9375 8.68754 16.9375C8.86988 16.9375 9.04475 16.8651 9.17368 16.7361C9.30261 16.6072 9.37504 16.4323 9.37504 16.25C9.37504 16.0677 9.30261 15.8928 9.17368 15.7639C9.04475 15.6349 8.86988 15.5625 8.68754 15.5625C8.50521 15.5625 8.33034 15.6349 8.20141 15.7639C8.07248 15.8928 8.00004 16.0677 8.00004 16.25ZM13.5 5.25C18.0492 5.25 21.75 8.95081 21.75 13.5H23.125C23.125 8.1925 18.8075 3.875 13.5 3.875C12.3705 3.875 11.2636 4.06888 10.2104 4.45181L10.6806 5.74431C11.5844 5.41651 12.5386 5.24923 13.5 5.25ZM17.625 10.75C17.625 10.9323 17.6975 11.1072 17.8264 11.2361C17.9553 11.3651 18.1302 11.4375 18.3125 11.4375C18.4949 11.4375 18.6697 11.3651 18.7987 11.2361C18.9276 11.1072 19 10.9323 19 10.75C19 10.5677 18.9276 10.3928 18.7987 10.2639C18.6697 10.1349 18.4949 10.0625 18.3125 10.0625C18.1302 10.0625 17.9553 10.1349 17.8264 10.2639C17.6975 10.3928 17.625 10.5677 17.625 10.75ZM8.68754 6.625C8.86988 6.625 9.04475 6.55257 9.17368 6.42364C9.30261 6.2947 9.37504 6.11984 9.37504 5.9375C9.37504 5.75516 9.30261 5.5803 9.17368 5.45136C9.04475 5.32243 8.86988 5.25 8.68754 5.25C8.50521 5.25 8.33034 5.32243 8.20141 5.45136C8.07248 5.5803 8.00004 5.75516 8.00004 5.9375C8.00004 6.11984 8.07248 6.2947 8.20141 6.42364C8.33034 6.55257 8.50521 6.625 8.68754 6.625ZM5.25004 13.5C5.25004 11.2966 6.10804 9.22444 7.66661 7.66656L6.69379 6.69375C5.79699 7.58531 5.08606 8.646 4.6022 9.81434C4.11834 10.9827 3.87118 12.2354 3.87504 13.5C3.87504 18.8075 8.19254 23.125 13.5 23.125V21.75C8.95086 21.75 5.25004 18.0492 5.25004 13.5ZM22.0938 20.0312C22.0938 21.1684 21.1684 22.0938 20.0313 22.0938C18.8942 22.0938 17.9688 21.1684 17.9688 20.0312C17.9688 19.7136 18.0472 19.4166 18.175 19.1478L14.3835 15.3556C14.1154 15.4841 13.8177 15.5625 13.5 15.5625C12.3629 15.5625 11.4375 14.6371 11.4375 13.5C11.4375 12.3629 12.3629 11.4375 13.5 11.4375C14.6372 11.4375 15.5625 12.3629 15.5625 13.5C15.5625 13.8176 15.4849 14.1146 15.3563 14.3834L19.1479 18.1757C19.416 18.0471 19.7137 17.9688 20.0313 17.9688C21.1684 17.9688 22.0938 18.8941 22.0938 20.0312ZM13.5 14.1875C13.8789 14.1875 14.1875 13.8788 14.1875 13.5C14.1875 13.1212 13.8789 12.8125 13.5 12.8125C13.1212 12.8125 12.8125 13.1212 12.8125 13.5C12.8125 13.8788 13.1212 14.1875 13.5 14.1875ZM20.7188 20.0312C20.7187 19.8488 20.6461 19.6739 20.5171 19.545C20.388 19.416 20.213 19.3437 20.0306 19.3438C19.8482 19.3438 19.6733 19.4164 19.5443 19.5455C19.4154 19.6745 19.343 19.8495 19.3431 20.0319C19.3432 20.2144 19.4158 20.3893 19.5448 20.5182C19.6739 20.6471 19.8489 20.7195 20.0313 20.7194C20.2137 20.7193 20.3886 20.6468 20.5176 20.5177C20.6465 20.3887 20.7189 20.2137 20.7188 20.0312Z" fill="#14A388"/>
                            </svg>
                            </a>';
                        }
                        $actions.='
                            <a href="' . route('b2b.accident_report', $id_encode) . '" class="btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Accident Report">
                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                            <rect width="27" height="27" rx="8" fill="#FFC1BE"/>
                            <path d="M9.37516 20.8335C9.37516 21.8461 8.55435 22.6669 7.54183 22.6669C6.52931 22.6669 5.7085 21.8461 5.7085 20.8335M9.37516 20.8335C9.37516 19.8211 8.55435 19.0002 7.54183 19.0002C6.52931 19.0002 5.7085 19.8211 5.7085 20.8335M9.37516 20.8335H11.2085C11.7148 20.8335 12.1252 20.4232 12.1252 19.9169V17.1802C12.1252 16.8842 11.9822 16.6063 11.7413 16.4343L8.91683 14.4169M5.7085 20.8335H4.3335M8.91683 14.4169H4.3335M8.91683 14.4169L5.98222 10.2245C5.81067 9.97942 5.53032 9.83347 5.23117 9.8335L4.3335 9.83358" stroke="#DC2626" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.625 20.8335C17.625 21.8461 18.4458 22.6669 19.4583 22.6669C20.4709 22.6669 21.2917 21.8461 21.2917 20.8335M17.625 20.8335C17.625 19.8211 18.4458 19.0002 19.4583 19.0002C20.4709 19.0002 21.2917 19.8211 21.2917 20.8335M17.625 20.8335H15.7917C15.2854 20.8335 14.875 20.4232 14.875 19.9169V17.1802C14.875 16.8842 15.018 16.6063 15.2589 16.4343L18.0833 14.4169M21.2917 20.8335H22.6667M18.0833 14.4169L21.018 10.2245C21.1895 9.97942 21.4699 9.83347 21.769 9.8335L22.6667 9.83358M18.0833 14.4169H22.6667" stroke="#DC2626" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.2082 11.6668L8.9165 9.32514L10.7498 8.91677L9.45984 5.25629L12.5832 6.62511L13.9149 4.3335L14.8748 8.00011L18.0832 7.06822L16.2816 11.6669" stroke="#DC2626" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M13.9583 11.6668L13.5 9.8335" stroke="#DC2626" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            </a>
                        </div>';  
                        }
                    return [
                         '<div class="form-check">
                            <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" type="checkbox" value="' . $data->id . '">
                        </div>',
                        $data->req_id ?? '',
                        $data->VehicleRequest->accountAbilityRelation->name ?? 'N/A',
                        $data->rider->name ?? '',
                        $data->rider->mobile_no ?? '',
                        $data->vehicle->permanent_reg_number ?? '',
                        $data->vehicle->vehicle_type_relation->name ?? '',
                        $data->vehicle->vehicle_model_relation->make ?? '',
                        $city_name ?? '',
                        $data->VehicleRequest->zone->name ?? 'N/A',
                        '<span class="badge-vehicle">' . ucfirst($data->handover_type ?? '') . '</span>',
                        $data->created_at ? \Carbon\Carbon::parse($data->created_at)->format('d M Y h:i A') : '',
                         $statusBadge,
                        
                        
                        $actions,
            
                    ];
                });
    
                return response()->json([
                    'draw'            => intval($request->input('draw')),
                    'recordsTotal'    => $totalRecords,
                    'recordsFiltered' => $totalRecords,
                    'data'            => $formattedData
                ]);
            } catch (\Exception $e) {
                \Log::error('Vehicle Request List Error: '.$e->getMessage());
    
                return response()->json([
                    'draw' => intval($request->input('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'An error occurred while processing your request.',
                    'errorCode'=>$e->getMessage()
                ], 500);
            }
        }
        
        $accountability_types = EvTblAccountabilityType::where('status', 1)
        ->whereIn('id', $accountability_Types)
        ->orderBy('id', 'desc')
        ->get();
    
        return view('b2b::vehicle_list.vehicle_list' , compact('accountability_types'));
    }

    
    public function accident_report_view(Request $request, $id)
    {
        $decrypt_id = decrypt($id);
        $data = B2BVehicleAssignment::with('vehicle' ,'rider.customerLogin.customer_relation')
            ->where('id', $decrypt_id)
            ->first();
        return view('b2b::vehicle_list.accident_report', compact('data'));
    }
    
    
    public function service_request_functionality(Request $request)
    {    

        $validated = $request->validate([
            'vehicle_number'   => 'required|string|max:100',
            'description'      => 'required|string',
            'address'          => 'string',
            'city'             =>'required',
            'zone'             => 'required',
            'repair_type'      => 'nullable|exists:ev_tbl_repair_types,id'
        ]);
    
    
        $guard = Auth::guard('master')->check() ? 'master' : 'zone';
        $user  = Auth::guard($guard)->user();
        
    
        DB::beginTransaction();
    
        try {
            
           $vehicle = AssetMasterVehicle::where('permanent_reg_number', $request->vehicle_no)
                ->first();
                
            
        
            if (!$vehicle) {
            return response()->json([
                'status'  => false,
                'message' => 'Vehicle not found!',
                ], 404);
            }

            
            $ticket_id = CustomHandler::GenerateTicketId($request->city);
           
            if ($ticket_id == "" || $ticket_id == null) {
                return response()->json(['success' => false,'message'  =>'Ticket ID creation failed']);
            }
        
        

            $repair_type = null;
            if (!empty($validated['repair_type'])) {
                $repair_type = RepairTypeMaster::find($validated['repair_type']);
            }

            
            $customer = CustomerLogin::with('customer_relation')
            ->where('id', $user->id) 
            ->first();
            
            
            $assignment = B2BVehicleAssignment::with('vehicle' ,'rider')->find($request->assign_id);
            if ($assignment) {
                $assignment->update(['status' => 'under_maintenance']);
    
    
            $inventory = AssetVehicleInventory::where('asset_vehicle_id', $assignment->asset_vehicle_id)->first();

            // Store current (old) location before update
            $from_location_source = $inventory ? $inventory->transfer_status : null; 
            
            // Update inventory status for this vehicle
            AssetVehicleInventory::where('asset_vehicle_id', $assignment->asset_vehicle_id)
                    ->update(['transfer_status' => 23]);
            }
            
            $service = B2BServiceRequest::create([
                'assign_id'       => $request->assign_id,
                'ticket_id'       =>$ticket_id ?? '',
                'vehicle_number'  => $validated['vehicle_number'],
                'description'     => $validated['description'] ?? null,
                'address'         => '',
                'repair_type'     => $validated['repair_type'],
                'city'            => $validated['city'] ,
                'zone_id'         =>   $validated['zone'] ,
                'gps_pin_address'   => $request->gps_pin_address,
                'poc_name'          => $customer->customer_relation->trade_name ?? '',
                'poc_number'    => $customer->customer_relation->phone ?? '',
                'driver_name'   => $assignment->rider->name ?? '',
                'driver_number'   => $assignment->rider->mobile_no ?? '',
                'current_status'   => 'open',
                'latitude'               => $request->latitude ?? '',
                'longitude'              => $request->longitude ?? '',
                'status'          => 'unassigned',
                'created_by'      => $user->id,
                'type'            => 'customer'
            ]);
            
           

            
            
            $remarks = "Inventory status updated to 'On Rent - Ticket Raised' due to customer service request.";
        
            // // Log this inventory action
            VehicleTransferChassisLog::create([
                'chassis_number' => $assignment->vehicle->chassis_number,
                'from_location_source' => $from_location_source,
                'to_location_destination' => 23,
                'vehicle_id'     => $assignment->vehicle->id,
                'status'         => 'updated',
                'remarks'        => $remarks,
                'created_by'     => $user->id,
                'type'           => 'b2b-web-dashboard'
            ]);

            
            B2BVehicleAssignmentLog::create([
                'assignment_id' => $request->assign_id,
                'status'        => 'unassigned',
                'current_status' => 'open',
                'remarks'       => "Service request raised for vehicle {$validated['vehicle_number']}",
                'action_by'     => $user->id,
                'type'          => 'b2b-web-dashboard',
                'request_type'  => 'service_request',
                'request_type_id' => $service->id
            ]);
            
                
            $ticket = VehicleTicket::create([
                'ticket_id'         => $ticket_id,
                'vehicle_no'        => $validated['vehicle_number'],
                'city_id'           => $request->city,
                'area_id'           => $request->zone,
                'vehicle_type'      => $assignment->vehicle->vehicle_type ?? '',
                'poc_name'          => $customer->customer_relation->trade_name ?? '',
                'poc_contact_no'    => $customer->customer_relation->phone ?? '',
                'issue_remarks'     => $validated['description'],
                'repair_type'       => $validated['repair_type'] ?? null,
                'address'           => '',
                'gps_pin_address'   => $request->gps_pin_address,
                'lat'               => $request->latitude ?? '',
                'long'              => $request->longitude ?? '',
                'driver_name'   => $assignment->rider->name ?? '',
                'driver_number'   => $assignment->rider->mobile_no ?? '',
                'image'             => '',
                'created_datetime'  => now(),                                                                                                          
                'created_by'        => '',
                'created_role'      => '',
                'customer_id'             => $user->id,
                'web_portal_status' => 0,
                'platform'          => 'b2b-web-dashboard',
                'ticket_status'     => 0,
            ]);
        
            $city = City::find($request->city);

            $createdDatetime = Carbon::now()->utc();
            
            $customerLongitude = $request->longitude ?? null;
            $customerLatitude  = $request->latitude ?? null;
                
                
             $ticketData = [
                "vehicle_number" => $validated['vehicle_number'],
                "updatedAt" => $createdDatetime,
                "ticket_status" => "unassigned",
                "chassis_number" => $assignment->vehicle->chassis_number ?? null,
                "telematics" => $assignment->vehicle->telematics_imei_number ?? null,
                "battery" => $assignment->vehicle->battery_serial_no ?? null,
                "vehicle_type" => $assignment->vehicle->vehicle_type_relation->name ?? null,
                "state" => $city->state->state_name ?? '',
                "priority" => 'High',
                "point_of_contact_info" => $customer->customer_relation->phone.' - '. $customer->customer_relation->trade_name,
                "job_type" => $repair_type->name ?? null,
                "issue_description" => $validated['description'] ?? '',
                'image' => [],
                'address'   => $request->gps_pin_address,
                "greendrive_ticketid" => $ticket_id,
                'driver_name'   => $assignment->rider->name ?? '',
                'driver_number'   => $assignment->rider->mobile_no ?? '',
                "customer_number" => $customer->customer_relation->phone ?? '',
                "customer_name" => $customer->customer_relation->trade_name ?? '',
                'customer_email' => $customer->customer_relation->email ?? '',
                'customer_location' => [
                    $customerLongitude,
                    $customerLatitude
                ], 
                "current_status" => 'open',
                "createdAt" => $createdDatetime,
                "city" => $city->city_name ?? null,
            ];
            
            
            $fieldProxyTicket = FieldProxyTicket::create(array_merge($ticketData, [
                'created_by' => $user->id,
                'type'       => 'b2b-web-dashboard',
            ]));
            
            
            
            FieldProxyLog::create([
                'fp_id'      => $fieldProxyTicket->id,   
                'status'     => 'unassigned',  // ticket status
                "current_status" => 'open',
                'remarks'    => "Service request raised for vehicle {$validated['vehicle_number']}",
                'created_by' => $user->id,
                'type'       => 'b2b-web-dashboard',
            ]);
            
            $apiTicketData = $ticketData;
            $apiTicketData['driver_number'] = preg_replace('/^\+91/', '', $ticketData['driver_number']);
            $apiTicketData['customer_number'] = preg_replace('/^\+91/', '', $ticketData['customer_number']);

            $apiData = [
                "sheetId" => "tickets",
                "tableData" => $apiTicketData
            ];
            
           
            $fieldproxy_base_url = BusinessSetting::where('key_name', 'fieldproxy_base_url')->value('value');
            $fieldproxy_create_endpoint = BusinessSetting::where('key_name', 'fieldproxy_create_enpoint')->value('value');
            $apiUrl = $fieldproxy_base_url . $fieldproxy_create_endpoint;
            $apiKey = env('FIELDPROXY_API_KEY', null); 
    
            $ch = curl_init($apiUrl);
            $payload = json_encode($apiData);
    
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "x-api-key: {$apiKey}",
                "Content-Type: application/json",
                "Accept: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
            $responseBody = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
    
            $fieldproxyResult = null;
            // ========== THROW ERROR TO TRIGGER ROLLBACK ==========
            if ($curlError) {
                throw new \Exception("FieldProxy cURL Error: {$curlError}");
            }
    
            if ($httpCode >= 400) {
                throw new \Exception("FieldProxy HTTP {$httpCode} Error: {$responseBody}");
            }
    
            $decoded = json_decode($responseBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("FieldProxy returned invalid JSON");
            }
            // ======================================================
            
            
            
            $customerName = $customer->customer_relation->name ?? 'Customer';  //updated by Gowtham.s
            $riderID = $assignment->rider->id;
            $riderData = $assignment->rider;
            $vehicleId = $assignment->vehicle->id;
            $issue_description = $request->description;
            $address = $request->gps_pin_address;
            $lat = $request->latitude;
            $long = $request->longitude;
            $repairInfo = [
              'issue_description'=>$issue_description,
              'address'=>$address,
              'latitude'=>$lat,
              'longitude'=>$long
            ];
            $tc_create_type = 'create_by_customer';
            
            
            // ServiceTicketHandler::pushRiderServiceTicketNotification($riderData, $ticket_id, $repairInfo,'create_by_customer', $customerName);//push notification
            // ServiceTicketHandler::AutoSendServiceRequestEmail($ticket_id,$riderID,$vehicleId,$repairInfo,'customer_create_ticket','create_by_customer'); //email
            // ServiceTicketHandler::AutoSendServiceRequestWhatsApp($ticket_id,$riderID,$vehicleId,$repairInfo,'customer_create_ticket','create_by_customer');//whatsapp
            
            
           ProcessB2BServiceRequestCreationJob::dispatch(
                $ticket_id,
                $riderData,
                $vehicleId,
                $repairInfo,
                $tc_create_type,
                $customerName
            );
            
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Service request submitted successfully.',
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            
            // ========== ACCURATE FAIL LOG ==========
            Log::error('Service Request Failed - Exception', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'input'   => $request->all(),
                'user_id' => $user->id ?? null,
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: '.$e->getMessage(),
            ], 500);
        }
    }
    
    
    public function return_request_functionality(Request $request){
        $assignment = B2BVehicleAssignment::find($request->id);
          $vehicle_request = B2BVehicleRequests::with(['rider'])->where('req_id',$assignment->req_id)->where('is_active',1)->first();
        //   dd($vehicle_request);
       
         $validated = $request->validate([
            'return_reason'        => 'required|string|max:255',
            'rider_id'           => 'nullable|string|max:100',
            'chassis_number'       => 'nullable|string|max:100',
            'register_number'      => 'nullable|string|max:100',
            'rider_name'           => 'nullable|string|max:255',
            'client_business_name' => 'nullable|string|max:255',
            'rider_mobile_no'      => 'nullable',
            'contact_no'           => 'nullable|string|max:20',
            'contact_email'        => 'nullable|email|max:255',
            'description'          => 'nullable|string',
        ]);
       
            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
            $user = Auth::guard($guard)->user();
            
            $vehicleData = AssetMasterVehicle::where('permanent_reg_number',$request->register_number)->first();
            if(!$vehicleData){
                return response()->json([
                    'status'  => false,
                    'message' => 'Asset Vehicle Not Found!'
                ]);
            }
           
             
            // dd("hii");
 
        $returnRequest=B2BReturnRequest::create([
            'assign_id'            => $request->id ,
            'return_reason'        => $validated['return_reason'],
            'rider_id'             => $validated['rider_id'],
            'chassis_number'       => $validated['chassis_number'] ?? null,
            'register_number'      => $validated['register_number'] ?? null,
            'rider_name'           => $validated['rider_name'] ?? null,
            'client_business_name' => $validated['client_business_name'] ?? null,
            'rider_mobile_no'  => $validated['rider_mobile_no'] ?? null,
            'status'               => 'opened',
            'contact_no'           => $validated['contact_no'] ?? null,
            'contact_email'        => $validated['contact_email'] ?? null,
            'description'          => $validated['description'] ?? null,
            'created_by'       => $user->id,
        ]);
        $assignment = B2BVehicleAssignment::find($request->id);
            if ($assignment) {
                $assignment->update([
                    'status' => 'return_request'
                ]);
                
            $inventory = AssetVehicleInventory::where('asset_vehicle_id', $assignment->asset_vehicle_id)->first();


            $from_location_source = $inventory ? $inventory->transfer_status : null; 
            
                           
            AssetVehicleInventory::where('asset_vehicle_id', $assignment->asset_vehicle_id)
                    ->update(['transfer_status' => 29]);
                    
                                
            $remarks = "Inventory status updated to 'Return Pending' due to customer return request.";
        
            // // Log this inventory action
            VehicleTransferChassisLog::create([
                'chassis_number' => $assignment->vehicle->chassis_number,
                'from_location_source' => $from_location_source,
                'to_location_destination' => 29,
                'vehicle_id'     => $assignment->vehicle->id,
                'status'         => 'updated',
                'remarks'        => $remarks,
                'created_by'     => $user->id,
                'type'           => 'b2b-web-dashboard'
            ]);
            
            
            $vehicle_request = B2BVehicleRequests::where('req_id',$assignment->req_id)->where('is_active',1)->first();
                // if ($vehicle_request) {
                //         $vehicle_request->is_active = 0;
                //         $vehicle_request->save();
                //     }
            
            }
            
        B2BVehicleAssignmentLog::create([
            'assignment_id' => $request->id,
            'status'        => 'opened',
            'remarks'       => "Vehicle {$validated['register_number']} has been return requested",
            'action_by'     => $user->id ?? null,
            'type'          => 'b2b-web-dashboard',
            'request_type'  => 'return_request',
            'request_type_id'=>$vehicle_request->id?? null
        ]);
        
       $rider = $vehicle_request->rider ?? null; //updated by Gowtham.s
       $requestId = $assignment->req_id;
       $selectReason = $request->selected_reason ?? null;
       $returnDescription = $validated['description'] ?? null;
       $zoneData = Zones::where('id',$rider->assign_zone_id)->first();
       $agent_Arr = User::where('role', 17)
            ->where('city_id', $zoneData->city_id)
            ->where('zone_id', $rider->assign_zone_id)
            ->where('status', 'Active')
            ->get(['id', 'phone', 'mb_fcm_token']);
            
        // $this->AutoSendReturnRequestEmail($requestId, $rider->id, $vehicleData->id,$selectReason,$returnDescription);
        // $this->AutoReturnRequestSendWhatsApp($rider->id, $selectReason, $returnDescription);//done
        // $this->pushRiderReturnRequestNotificationSent($rider, $requestId, $selectReason, $returnDescription); //done
        // $this->pushAgentReturnRequestNotificationSent($agent_Arr, $requestId, $selectReason, $returnDescription); //done
        // $this->AutoAgentReturnRequestSendWhatsApp($agent_Arr, $rider->id, $selectReason, $returnDescription); //done
        
        ProcessB2BReturnRequestCreationJob::dispatch($requestId, $rider->id, $vehicleData->id,$selectReason,$returnDescription ,$agent_Arr);
            
        return response()->json([
                'status'  => 'success',
                'message' => 'Return Request submitted successfully!',
                'data'    => $returnRequest
            ], 200);
        
    }
    
    public static function AutoSendReturnRequestEmail($requestId, $rider_id, $vehicle_id,$reason,$description = null)
    {
        
        $rider = B2BRider::with('customerLogin.customer_relation')
            ->where('id', $rider_id)
            ->first();

        if (!$rider || !$rider->mobile_no) {
            Log::info('Email Notify : Rider or mobile number not found');
            return false;
        }
    
        // Rider details
        $riderName    = $rider->name ?? 'Rider';
        $riderPhone   = $rider->mobile_no;
        $riderEmail   = $rider->email;
        $Request_ID    = $requestId ?? '';
        $customerID   = $rider->customerLogin->customer_relation->id ?? 'N/A';
        $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
        $customerEmail= $rider->customerLogin->customer_relation->email ?? 'N/A';
        $customerPhone = $rider->customerLogin->customer_relation->phone ?? 'N/A';
        $customerLoginEmail= $rider->customerLogin->email ?? 'N/A';

        $vehicleData = AssetMasterVehicle::with(['quality_check'])
            ->where('id', $vehicle_id)
            ->first();

        // Vehicle details
        $AssetvehicleId = $vehicleData->id ?? 'N/A';
        $vehicleNo      = $vehicleData->permanent_reg_number ?? 'N/A';
        $vehicleType    = $vehicleData->quality_check->vehicle_type_relation->name ?? 'N/A';
        $vehicleModel   = $vehicleData->quality_check->vehicle_model_relation->vehicle_model ?? 'N/A';
    
        $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
        $footerContentText = $footerText ?? "For any assistance, please reach out to Admin Support.<br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
        
        $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.
                <br>Email: {$customerEmail}<br>Thank you,<br>{$customerName}";
    

    
            // Rider email
            if (!empty($riderEmail)) {

                        $subject = "Return Request Notification - Request ID #{$Request_ID}";
                        $intro = "A new return request has been created by your customer {$customerName} on your behalf.";
             
                  $additionalNotes = '';
                    if (!empty($description)) {
                        $additionalNotes = "<strong>Additional Notes:</strong> {$description}<br>";
                    }

                $body = "
                    <html>
                    <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; margin: 0; color: #544e54;'>
                        <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden;'>
                            
                            <tr>
                                <td style='background-color: #4a90e2; color: #ffffff; text-align: center; padding: 25px 20px;'>
                                    <h2 style='margin: 0; font-size: 22px;'>Return Request Notification</h2>
                                </td>
                            </tr>

                            <tr>
                                <td style='padding: 25px 20px;'>
                                    <p style='font-size: 16px; margin-bottom: 15px;'>Hello <strong>{$riderName}</strong>,</p>
                                    <p style='font-size: 15px; margin-bottom: 20px;'>{$intro}</p>

                                    <table cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                        <tr style='background-color: #f2f2f2;'>
                                            <th style='text-align: left;'>Request ID</th>
                                            <td>{$Request_ID}</td>
                                        </tr>
                                    </table>

                                    <h3 style='margin-top: 25px; font-size: 18px; color: #333;'>Reason</h3>
                                    <p style='font-size: 15px; line-height: 1.5;'>
                                        {$reason}<br>
                                        {$additionalNotes}
                                    </p>

                                    <p style='margin-top: 25px; font-size: 14px; line-height: 1.5;'>{$CustomerfooterContentText}</p>
                                </td>
                            </tr>

                            <tr>
                                <td style='background-color: #f2f2f2; text-align: center; padding: 15px; font-size: 12px; color: #777;'>
                                    &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>
                    ";

                    CustomHandler::sendEmail([$riderEmail], $subject, $body);
                }

            // Customer email
           $toCustomerEmails = array_filter([$customerLoginEmail, $customerEmail]);
            if (!empty($toCustomerEmails)) {

                $subject   = "Return Request Confirmation - Request ID #{$Request_ID}";
                $introText = "We have successfully received your Vehicle Return Request. Below are the details of the assigned rider and vehicle for your reference:";

                $additionalNotes = '';
                if (!empty($description)) {
                    $additionalNotes = "<strong>Additional Notes:</strong> {$description}<br>";
                }

                $body = "
                <html>
                <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; margin:0; color: #544e54;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 650px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden;'>
                        
                        <!-- Header -->
                        <tr>
                            <td style='background-color: #4a90e2; color: #ffffff; text-align: center; padding: 25px 20px;'>
                                <h2 style='margin:0; font-size: 22px;'>Vehicle Return Request Confirmation</h2>
                                <p style='margin:5px 0 0; font-size: 14px;'>Request ID: {$Request_ID}</p>
                            </td>
                        </tr>

                        <!-- Body -->
                        <tr>
                            <td style='padding: 25px 20px;'>
                                <p style='font-size:16px; margin-bottom:15px;'>Dear <strong>{$customerName}</strong>,</p>
                                <p style='font-size:15px; margin-bottom:20px;'>{$introText}</p>

                                <!-- Rider Details -->
                                <table cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                    <tr style='background-color: #f2f2f2;'>
                                        <th colspan='2' style='text-align:center; font-size:16px; padding:10px;'>Rider Details</th>
                                    </tr>
                                    <tr>
                                        <td style='width: 40%; font-weight:bold; border-bottom: 1px solid #ddd;'>Name</td>
                                        <td style='border-bottom: 1px solid #ddd;'>{$riderName}</td>
                                    </tr>
                                    <tr>
                                        <td style='font-weight:bold; background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>Phone</td>
                                        <td style='background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>{$riderPhone}</td>
                                    </tr>
                                    <tr>
                                        <td style='font-weight:bold; border-bottom: 1px solid #ddd;'>Email</td>
                                        <td style='border-bottom: 1px solid #ddd;'>{$riderEmail}</td>
                                    </tr>
                                </table>

                                <!-- Vehicle Details -->
                                <table cellpadding='10' cellspacing='0' style='width: 100%; margin-top:20px; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                    <tr style='background-color: #f2f2f2;'>
                                        <th colspan='2' style='text-align:center; font-size:16px; padding:10px;'>Vehicle Details</th>
                                    </tr>
                                    <tr>
                                        <td style='width: 40%; font-weight:bold; border-bottom: 1px solid #ddd;'>Vehicle ID</td>
                                        <td style='border-bottom: 1px solid #ddd;'>{$AssetvehicleId}</td>
                                    </tr>
                                    <tr>
                                        <td style='font-weight:bold; background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>Vehicle No</td>
                                        <td style='background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>{$vehicleNo}</td>
                                    </tr>
                                    <tr>
                                        <td style='font-weight:bold; border-bottom: 1px solid #ddd;'>Type</td>
                                        <td style='border-bottom: 1px solid #ddd;'>{$vehicleType}</td>
                                    </tr>
                                    <tr>
                                        <td style='font-weight:bold; background-color: #f9f9f9;'>Model</td>
                                        <td style='background-color: #f9f9f9;'>{$vehicleModel}</td>
                                    </tr>
                                </table>

                                <!-- Reason & Notes -->
                                <h3 style='margin-top: 25px; font-size: 18px; color: #333;'>Reason for Return</h3>
                                <p style='font-size: 15px; line-height: 1.5;'>
                                    {$reason}<br>
                                    {$additionalNotes}
                                </p>

                                <p style='margin-top: 25px; font-size: 14px; line-height: 1.5;'>{$footerContentText}</p>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td style='background-color: #f2f2f2; text-align: center; padding: 15px; font-size: 12px; color: #777;'>
                                &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                            </td>
                        </tr>
                    </table>
                </body>
                </html>
                ";

                CustomHandler::sendEmail($toCustomerEmails, $subject, $body);
            }


        // Admin email
        $adminEmails = DB::table('roles')
            ->leftJoin('users', 'roles.id', '=', 'users.role')
            ->whereIn('users.role', [1, 13]) // Admins
            ->where('users.status','Active')
            ->pluck('users.email')
            ->filter()
            ->toArray();

        if (!empty($adminEmails)) {

            $subject = "New Vehicle Return Request - Request ID #{$Request_ID}";
            $creatorText = "This return request was submitted by the customer <strong>{$customerName}</strong>.";

            $additionalNotes = '';
            if (!empty($description)) {
                $additionalNotes = "<strong>Additional Notes:</strong> {$description}<br>";
            }

            $body = "
            <html>
            <body style='font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 30px; color: #333;'>
                <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 650px; margin: auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08);'>

                    <!-- Header -->
                    <tr>
                        <td style='background-color: #4a90e2; color: #ffffff; text-align: center; padding: 25px 20px;'>
                            <h2 style='margin:0; font-size: 22px;'>New Vehicle Return Request Received</h2>
                            <p style='margin:5px 0 0; font-size: 14px;'>Request ID: {$Request_ID}</p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style='padding: 25px; line-height: 1.6; font-size: 15px;'>
                            <p style='margin: 0 0 15px;'>Dear Admin,</p>
                            <p style='margin: 0 0 20px;'>A new Vehicle Return Request has been created and assigned. {$creatorText}</p>

                            <!-- Customer Details -->
                            <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                                <tr style='background-color: #f7f7f7;'>
                                    <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Customer Details</td>
                                </tr>
                                <tr><td width='40%'><strong>Name:</strong></td><td>{$customerName}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>Customer ID:</strong></td><td>{$customerID}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>{$customerEmail}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>Phone:</strong></td><td>{$customerPhone}</td></tr>
                            </table>

                            <!-- Rider Details -->
                            <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                                <tr style='background-color: #f7f7f7;'>
                                    <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Rider Details</td>
                                </tr>
                                <tr><td><strong>Name:</strong></td><td>{$riderName}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>Phone:</strong></td><td>{$riderPhone}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>{$riderEmail}</td></tr>
                            </table>

                            <!-- Vehicle Details -->
                            <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                                <tr style='background-color: #f7f7f7;'>
                                    <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Vehicle Details</td>
                                </tr>
                                <tr><td><strong>Vehicle ID:</strong></td><td>{$AssetvehicleId}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>Vehicle No:</strong></td><td>{$vehicleNo}</td></tr>
                                <tr><td><strong>Type:</strong></td><td>{$vehicleType}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>Model:</strong></td><td>{$vehicleModel}</td></tr>
                            </table>

                            <!-- Reason & Notes -->
                            <h3 style='margin-top: 25px; font-size: 18px; color: #333;'>Reason for Return</h3>
                            <p style='font-size: 15px; line-height: 1.5;'>
                                {$reason}<br>
                                {$additionalNotes}
                            </p>

                            <p style='margin-top: 20px; font-size: 14px; color: #555;'>{$footerContentText}</p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style='background-color: #f7f7f7; text-align: center; padding: 15px; font-size: 12px; color: #666;'>
                            &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                        </td>
                    </tr>

                </table>
            </body>
            </html>
            ";

            CustomHandler::sendEmail($adminEmails, $subject, $body);
        }

        
    }
    
    
    public function AutoReturnRequestSendWhatsApp($rider_id, $reason, $description = null)
    {
        $rider = B2BRider::with('vehicleRequest.zone', 'customerLogin.customer_relation')
            ->where('id', $rider_id)
            ->first();
    
        if (!$rider || !$rider->mobile_no) {
            Log::error('Return Request : Rider or mobile number not found');
            return false;
        }
    
        $vehicleRequest = $rider->vehicleRequest->last();
        if (!$vehicleRequest) {
            Log::error('Return Request : No vehicle request found for this rider');
            return false;
        }
    
        // WhatsApp API
        $api_key = BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
        if (empty($api_key)) {
            Log::error('Return Request : WhatsApp API key not configured');
            return false;
        }
    
        $url = 'https://whatshub.in/api/whatsapp/send';
    
        // Rider / Customer / Admin info
        $riderName     = $rider->name ?? 'Rider';
        $riderPhone    = $rider->mobile_no;
        $requestId     = $vehicleRequest->req_id ?? '';
        $zoneName      = $vehicleRequest->zone->name ?? 'N/A';
    
        $customerID    = $rider->customerLogin->customer_relation->id ?? 'N/A';
        $customerName  = $rider->customerLogin->customer_relation->name ?? 'N/A';
        $customerEmail = $rider->customerLogin->customer_relation->email ?? 'N/A';
        $customerPhone = $rider->customerLogin->customer_relation->phone ?? '';
    
        // Footer
        $footerText = BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
        $footerContentText = $footerText ??
            "For any assistance, please reach out to Admin Support.\n"
            . "Email: support@greendrivemobility.com\n"
            . "Thank you,\nGreenDriveConnect Team";
    
    
        $messages = [];
    
        if (!empty($riderPhone)) {
            $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.\n" .
                 "Email: {$customerEmail}\n" .
                 "Thank you,\n" .
                 "{$customerName}";
            $messages[] = [
                "number"  => $this->formatPhoneNumber($riderPhone),
                "message" => "Dear {$riderName},\n\n"
                    . "A Vehicle Return Request has been created by the {$customerName}.\n\n"
                    . "Return Request ID: {$requestId}\n"
                    . "📍 Assigned Zone: {$zoneName}\n\n"
                    . "*Reason:* {$reason}\n"
                    . (!empty($description) ? "*Description:* {$description}\n\n" : "\n")
                    . "{$footerContentText}"
            ];
        }
    
        if (!empty($customerPhone)) {
            $messages[] = [
                "number"  => $this->formatPhoneNumber($customerPhone),
                "message" => "Dear {$customerName},\n\n"
                    . "Your Vehicle Return Request has been submitted successfully.\n\n"
                    . "Return Request ID: {$requestId}\n"
                    . "📌 Rider Details:\n"
                    . "• Name: {$riderName}\n"
                    . "• Phone: {$riderPhone}\n"
                    . "📍 Assigned Zone: {$zoneName}\n\n"
                    . "*Reason:* {$reason}\n"
                    . (!empty($description) ? "*Description:* {$description}\n\n" : "\n")
                    . "{$CustomerfooterContentText}"
            ];
        }
    
        $adminPhone = BusinessSetting::where('key_name', 'admin_whatsapp_no')->value('value');
        if (!empty($adminPhone)) {
            $messages[] = [
                "number"  => $this->formatPhoneNumber($adminPhone),
                "message" => "Dear Admin,\n\n"
                    . "A new Vehicle Return Request has been submitted by the {$customerName}\n\n"
                    . "Return Request ID: {$requestId}\n"
                    . "📌 Customer Details:\n"
                    . "• ID: {$customerID}\n"
                    . "• Phone: {$customerPhone}\n"
                    . "📌 Rider Details:\n"
                    . "• Name: {$riderName}\n"
                    . "• Phone: {$riderPhone}\n"
                    . "📍 Assigned Zone: {$zoneName}\n\n"
                    . "*Reason:* {$reason}\n"
                    . (!empty($description) ? "*Description:* {$description}\n\n" : "\n")
                    . "{$footerContentText}"
            ];
        }
    
        if (empty($messages)) {
            Log::error('Return Request : No valid recipients found');
            return false;
        }
    
        $postdata = ["contact" => $messages];
    
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
            Log::error("Return Request - cURL Error: {$error}");
            return false;
        }
    
        $responseData = json_decode($response, true);
        if (!isset($responseData['success']) || $responseData['success'] != true) {
            Log::error("Return Request - WhatsApp API Response: " . print_r($responseData, true));
            return false;
        }
    
        Log::info("Return Request : WhatsApp notification sent successfully (Request ID: {$requestId})");
        return true;
    }

    
    
    public function AutoAgentReturnRequestSendWhatsApp($agent_mobileArr, $rider_id,$reason, $description = null)
    {
            $rider = B2BRider::with('vehicleRequest', 'customerLogin.customer_relation')
                ->where('id', $rider_id)
                ->first();
        
            if (!$rider || !$rider->vehicleRequest->count()) {
                Log::info('Return Request : Rider or Vehicle Request not found');
                return false;
            }
        
            $vehicleRequest = $rider->vehicleRequest->last();
        
            // WhatsApp API
            $api_key = BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
            $url = 'https://whatshub.in/api/whatsapp/send';
        
            $riderName     = $rider->name ?? 'Rider';
            $riderPhone    = $rider->mobile_no ?? 'N/A';
            $requestId     = $vehicleRequest->req_id ?? '';
            $zoneName      = $vehicleRequest->zone->name ?? 'N/A';
            $customerID    = $rider->customerLogin->customer_relation->id ?? 'N/A';
            $customerName  = $rider->customerLogin->customer_relation->name ?? 'N/A';
            $customerEmail = $rider->customerLogin->customer_relation->email ?? 'N/A';
            $customerPhone = $rider->customerLogin->customer_relation->phone ?? 'N/A';
        
            $footerText = BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
            $footerContentText = $footerText ??
                "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
        
            foreach ($agent_mobileArr as $agent) {
                $agentPhone = $agent->phone;
                // clean phone number
                $cleanedPhone = preg_replace('/\D+/', '', $agentPhone);
                if (substr($cleanedPhone, 0, 1) === '0') {
                    $cleanedPhone = substr($cleanedPhone, 1);
                }
                if (substr($cleanedPhone, 0, 2) !== '91' && strlen($cleanedPhone) === 10) {
                    $cleanedPhone = '91' . $cleanedPhone;
                }
        
                $message = "Dear Agent,\n\n"
                    . "A new Vehicle Return Request has been received.\n\n"
                    . "🔹 Request ID: {$requestId}\n"
                    . "📌 Rider Details:\n"
                    . "• Name: {$riderName}\n"
                    . "• Phone: {$riderPhone}\n"
                    . "📌 Customer Details:\n"
                    . "• Name: {$customerName}\n"
                    . "• ID: {$customerID}\n"
                    . "• Phone: {$customerPhone}\n"
                    . "📍 Assigned Zone: {$zoneName}\n"
                    . "📌 Reason: {$reason}\n";

                if (!empty($description)) {
                    $message .= "📝 Description: {$description}\n";
                }

                $message .= "{$footerContentText}";

        
                $postdata = [
                    "contact" => [
                        [
                            "number"    => $cleanedPhone,
                            "message"   => $message,
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
                    Log::info("Vehicle Return Request File - cURL Error for Agent {$agentPhone}: " . $error);
                } else {
                    $responseData = json_decode($response, true);
                    if (!isset($responseData['success']) || $responseData['success'] != true) {
                        Log::info("Vehicle Return Request - WhatsApp API Response for Agent {$agentPhone}: " . print_r($responseData, true));
                    } else {
                        Log::info("Vehicle Return Request : WhatsApp notification sent successfully to Agent {$agentPhone}");
                    }
                }
            }
        
            return true;
        }
    
    public function pushAgentReturnRequestNotificationSent($agent_Arr, $requestId, $reason, $description = null)
    {
        $svc = new FirebaseNotificationService();
        $title = 'New Vehicle Return Request!';
        $image = null;
        $notifications = [];
    
        foreach ($agent_Arr as $agent) {
            $agentId = $agent->id;
            $token   = $agent->mb_fcm_token;
            $data    = [];
            $icon    = null;
    
            $body = "Dear Agent, a new Vehicle Return Request has been received "
                  . "for your Vehicle Request ID: {$requestId}. "
                  . "Reason: {$reason}";
    
            if (!empty($description)) {
                $body .= " | Description: {$description}";
            }
    
            if ($token) {
                $svc->sendToToken($token, $title, $body, $data, $image, $icon, $agentId);
            }
    
            $notifications[] = [
                'title'       => $title,
                'description' => $body,
                'image'       => $image,
                'status'      => 1,
                'agent_id'    => $agentId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }
    
        if (!empty($notifications)) {
            \DB::table('b2b_tbl_agent_notifications')->insert($notifications);
        }
    }

    
    public function pushRiderReturnRequestNotificationSent($rider, $requestId, $reason, $description = null)
    {
        $svc = new FirebaseNotificationService();
        $title = 'Vehicle Return Request!';
        $image = null;
        $notifications = [];
    
        $riderId = $rider->id;
        $token   = $rider->fcm_token;
    
        $body = "Dear {$rider->name}, a new Vehicle Return Request has been received "
              . "for your Vehicle Return Request ID: {$requestId}. "
              . "Reason: {$reason}";
    
        if (!empty($description)) {
            $body .= " | Description: {$description}";
        }
    
        $data = [];
        $icon = null;
    
        if ($token) {
            $svc->sendToToken($token, $title, $body, $data, $image, $icon, $riderId);
        }
    
        $notifications[] = [
            'title'       => $title,
            'description' => $body,
            'image'       => $image,
            'status'      => 1,
            'rider_id'    => $riderId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    
        if (!empty($notifications)) {
            \DB::table('b2b_tbl_riders_notifications')->insert($notifications);
        }
    }
    
   public function accident_report_functionallity(Request $request)
    {
        $validated = $request->validate([
            // 'accident_report_id' => 'required',
            'datetime' => 'required|date',
            'location_of_accident' => 'required',
            'accident_type' => 'required|in:Collision,Fall,Fire,Other',
            'description' => 'required',
            'vehicle_number' => 'required',
            'chassis_number' => 'required',
            'rider_id' => 'required',
            'rider_name' => 'required',
            'rider_contact_number' => 'nullable',
            'rider_license_number' => 'nullable',
            'rider_llr_number' => 'nullable',
            'vehicle_damage' => 'required|in:Minor,Moderate,Severe,Total Loss',
            'rider_injury_description' => 'nullable',
            'third_party_injury_description' => 'nullable',
            'client_business_name' => 'nullable',
            'contact_person_name' => 'nullable',
            'contact_number' => 'nullable',
            'contact_email' => 'nullable|email',
            'terms_condition' => 'accepted',
            'accident_attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,mp4,mov,avi,webm|max:10240',
            'police_report' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240'
        ]);
    
    
        // Handle multiple attachments
        // $attachments = [];
        // if ($request->hasFile('accident_attachments')) {
        //     foreach ($request->file('accident_attachments') as $file) {
        //         if ($file->isValid()) {
        //             $fileName = $this->uploadFile($file, 'b2b/accident_reports/attachments');
        //             $attachments[] = [
        //                 'name' => $fileName,
        //             ];
        //         }
        //     }
        // }
        
        // Handle multiple attachments (store as JSON object with numeric keys)
        $attachments = [];
        if ($request->hasFile('accident_attachments')) {
            foreach ($request->file('accident_attachments') as $index => $file) {
                if ($file->isValid()) {
                    $fileName = $this->uploadFile($file, 'b2b/accident_reports/attachments');
                    $attachments[$index] = $fileName; // numeric keys
                }
            }
        }

    
        // Handle police report
        $policeReport = null;
        if ($request->hasFile('police_report') && $request->file('police_report')->isValid()) {
            $fileName = $this->uploadFile($request->file('police_report'), 'b2b/accident_reports/police_reports');
            $policeReport = [
                'name' => $fileName,
            ];
        }
    
        // Detect logged-in guard
        $guard = Auth::guard('master')->check() ? 'master' : 'zone';
        $user = Auth::guard($guard)->user();
    
        // Save into DB using JSON encode
        DB::table('b2b_tbl_report_accident')->insert([
             'assign_id'            => $request->id ,
            // 'accident_report_id' => $validated['accident_report_id'],
            'datetime' => $validated['datetime'],
            // 'time_of_accident' => $validated['time_of_accident'],
            'location_of_accident' => $validated['location_of_accident'],
            'accident_type' => $validated['accident_type'],
            'description' => $validated['description'],
            'vehicle_number' => $validated['vehicle_number'],
            'chassis_number' => $validated['chassis_number'],
            'rider_id' => $validated['rider_id'],
            'rider_name' => $validated['rider_name'],
            'rider_contact_number' => $validated['rider_contact_number'],
            'rider_license_number' => $validated['rider_license_number']??null,
            'rider_llr_number' => $validated['rider_llr_number']??null,
            'vehicle_damage' => $validated['vehicle_damage'],
            'rider_injury_description' => $validated['rider_injury_description'] ?? null,
            'third_party_injury_description' => $validated['third_party_injury_description'] ?? null,
            'accident_attachments' => !empty($attachments) ? json_encode($attachments) : null,
            'police_report' => !empty($policeReport) ? json_encode($policeReport) : null,
            'client_business_name' => $validated['client_business_name'],
            // 'contact_person_name' => $validated['contact_person_name'],
            'contact_number' => $validated['contact_number'],
            'contact_email' => $validated['contact_email'],
             'terms_condition' => $request->boolean('terms_condition'),
            'created_by'       => $user->id,
        ]);
    
        $assignment = B2BVehicleAssignment::find($request->id);
        
            if ($assignment) {
                $assignment->update([
                    'status' => 'accident'
                ]);
                
                 $inventory = AssetVehicleInventory::where('asset_vehicle_id', $assignment->asset_vehicle_id)->first();

                // Store current (old) location before update
                $from_location_source = $inventory ? $inventory->transfer_status : null; 
                
                AssetVehicleInventory::where('asset_vehicle_id', $assignment->asset_vehicle_id)
                    ->update(['transfer_status' => 17]);
                    
                    
                $remarks = "Inventory status updated to 'Accident' due to vehicle damage reported by the customer.";
            
                // // Log this inventory action
                VehicleTransferChassisLog::create([
                    'chassis_number' => $assignment->vehicle->chassis_number,
                    'from_location_source' => $from_location_source,
                    'to_location_destination' => 17,
                    'vehicle_id'     => $assignment->vehicle->id,
                    'status'         => 'updated',
                    'remarks'        => $remarks,
                    'created_by'     => $user->id,
                    'type'           => 'b2b-web-dashboard'
                ]);
            
            
              $vehicle_request = B2BVehicleRequests::where('req_id',$assignment->req_id)->where('is_active',1)->first();
                 if ($vehicle_request) {
                        $vehicle_request->is_active = 0;
                        $vehicle_request->save();
                    }
            
            }
            
        B2BVehicleAssignmentLog::create([
            'assignment_id' => $request->id,
            'status'        => 'claim_initiated',
            'remarks'       => "Vehicle {$request->vehicle_number} has been reported in an accident",
            'action_by'     => $user->id ?? null,
            'type'          => 'b2b-web-dashboard',
            'request_type'  => 'accident',
            'request_type_id'=>$vehicle_request->id??null
        ]);
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Accident Report submitted successfully!',
            
        ], 200);
    }   
    
    
     public function recovery_request_functionality(Request $request)
    {
        $validator = Validator::make($request->all(), [
           
            // 'datetime'             => 'required|date',
            'city_id'              =>'required|integer',
            'zone_id'              =>'required|integer',
            'reason_for_recovery'  => 'required|string',
            'vehicle_number'       => 'required|string|max:255',
            'chassis_number'       => 'nullable|string|max:255',
            'rider_id'             => 'nullable|string|max:255',
            'rider_name'           => 'nullable|string|max:255',
            'client_business_name' => 'nullable|string|max:255',
            'contact_person_name'  => 'nullable|string|max:255',
            'contact_no'           => 'nullable|string|max:20',
            'contact_email'        => 'nullable|email|max:255',
            'description'          => 'nullable|string',
            'terms_condition'      => 'accepted',
            // 'files.*'              => 'nullable|mimes:jpg,jpeg,png,pdf,mp4,mov,avi|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        
       try {
             DB::beginTransaction();
            // Upload multiple files
            $uploadedFiles = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $uploadedFiles[] = $this->uploadFile($file, 'b2b/recovery_request');
                }
            }
            
            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
            $user = Auth::guard($guard)->user();
    
    
            // Save to DB
            $recovery = new B2BRecoveryRequest();
            $recovery->assign_id              = $request->id;
            $recovery->city_id              = $request->city_id; //updated by logesh
            $recovery->zone_id              = $request->zone_id; //updated by logesh
            $recovery->reason                = $request->reason_for_recovery;
            $recovery->vehicle_number           = $request->vehicle_number;
            $recovery->chassis_number        = $request->chassis_number;
            $recovery->rider_id              = $request->rider_id;
            $recovery->rider_name            = $request->rider_name;
            $recovery->client_name           = $request->client_business_name;
            $recovery->rider_mobile_no   = $request->rider_mobile_no; //updated by logesh
            $recovery->contact_no            = $request->contact_no;
            $recovery->contact_email         = $request->contact_email;
            $recovery->description           = $request->description;
            // $recovery->accident_photos       = json_encode($uploadedFiles);
            $recovery->terms_condition       = $request->has('terms_condition') ? 1 : 0;
            $recovery->created_by = $user->id;
            $recovery->created_by_type = 'b2b-web-dashboard';
         
    
            $recovery->save();
            
            $assignment = B2BVehicleAssignment::find($request->id);
                if ($assignment) {
                    $assignment->update([
                        'status' => 'recovery_request'
                    ]);
                    
                    
                $inventory = AssetVehicleInventory::where('asset_vehicle_id', $assignment->asset_vehicle_id)->first();


                $from_location_source = $inventory ? $inventory->transfer_status : null; 
                
                               
                AssetVehicleInventory::where('asset_vehicle_id', $assignment->asset_vehicle_id)
                        ->update(['transfer_status' => 28]);
                        
                                    
                $remarks = "Inventory status updated to 'Recovery Pending' due to customer recovery request.";
            
                // // Log this inventory action
                VehicleTransferChassisLog::create([
                    'chassis_number' => $assignment->vehicle->chassis_number,
                    'from_location_source' => $from_location_source,
                    'to_location_destination' => 28,
                    'vehicle_id'     => $assignment->vehicle->id,
                    'status'         => 'updated',
                    'remarks'        => $remarks,
                    'created_by'     => $user->id,
                    'type'           => ''
                ]);
                
                // $vehicle_request = B2BVehicleRequests::where('req_id',$assignment->req_id)->where('is_active',1)->first();
                //     if ($vehicle_request) {
                //             $vehicle_request->is_active = 0;
                //             $vehicle_request->save();
                //         }
                
                }
            B2BVehicleAssignmentLog::create([
                'assignment_id' => $request->id,
                'status'        => 'opened',
                'remarks'       => "Vehicle {$request->vehicle_number} has been requested for recovery",
                'action_by'     => $user->id ?? null,
                'type'          => 'b2b-web-dashboard',
                'request_type'  => 'recovery_request',
                'request_type_id'=>$recovery->id??null  //updated by logesh
            ]);
            
            RecoveryComment::create([
                'req_id'    => $recovery->id,
                'status'    => 'opened',
                'comments'  => "Vehicle {$request->vehicle_number} has been requested for recovery",
                'user_id'   => $user->id ?? null,
                'user_type' => 'b2b-web-dashboard',
            ]);
            
            DB::commit();
            
            $requestID = $assignment->req_id;
            $rider_id = $assignment->rider_id;
            $vehicle_id = $assignment->asset_vehicle_id;
            $tc_create_type = 'b2b-web-dashboard';
            $recoveryInfo = [
                'recovery_reason' => $request->reason_for_recovery_txt,
                'recovery_description' => $request->description
            ];
            if(!empty($requestID)){
                $requestID = $assignment->req_id;
                $riderId   = $assignment->rider_id;
                $vehicleId = $assignment->asset_vehicle_id;
                $tc_create_type = 'b2b-web-dashboard';
                $recoveryInfo = [
                    'recovery_reason'      => $request->reason_for_recovery,
                    'recovery_description' => $request->description,
                ];
                // RecoveryNotifyHandler::AutoSendRecoveryRequestEmail($requestID, $rider_id, $vehicle_id, $recoveryInfo, $tc_create_type);
                // RecoveryNotifyHandler::AutoSendRecoveryRequestWhatsApp($requestID, $rider_id, $vehicle_id, $recoveryInfo, $tc_create_type);
                
                ProcessB2BRecoveryRequestCreationJob::dispatch(
                    $requestID,
                    $riderId,
                    $vehicleId,
                    $recoveryInfo,
                    $tc_create_type);
                    
                     \Log::info('ProcessB2BRecoveryRequestCreationJob dispatched successfully', [
                        'request_id' => $requestID,
                        'rider_id'   => $riderId,
                        'vehicle_id' => $vehicleId,
                        'type'       => $tc_create_type,
                    ]);
            }
    
            return response()->json([
                'status'  => 'success',
                'message' => 'Recovery Request submitted successfully!',
                'data'    => $recovery
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Recovery Request Error: '.$e->getMessage());
    
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong while submitting the recovery request.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    
    public function export_vehicle_request(Request $request){
        
       
        $selectedIds = json_decode($request->query('selected_ids', '[]'), true);
        $selectedFields = json_decode($request->query('fields'), true);
         
        
        $status = $request->status;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
         $accountability_type = $request->accountability_type;
        
        
        return Excel::download(new B2BVehicleRequestExport($status , $from_date  , $to_date , $selectedIds , $selectedFields , $accountability_type), 'vehicle-requests-' . date('d-m-Y') . '.xlsx');
        
    }
      
    public function export_vehicle_details(Request $request){
        
       
        $selectedFields  = $request->input('fields', []);  
        $from_date = $request->input('from_date');
        $to_date   = $request->input('to_date');
        $zone = $request->input('zone')?? null;
        $status = $request->input('status')?? null;
        $city = $request->input('city')?? null;
         $selectedIds = $request->input('selected_ids', []);

    
        if (empty($selectedFields)) {
            return back()->with('error', 'Please select at least one field to export.');
        }
        
        return Excel::download(new B2BVehicleListExport(
                $from_date,      // 1. from_date
                $to_date,        // 2. to_date
                $selectedIds,    // 3. selected IDs
                $selectedFields, // 4. selected fields
                $city,           // 5. city
                $zone,           // 6. zone
                $status          // 7. status
            ), 'vehicle-list-' . date('d-m-Y') . '.xlsx');
        
    }  
        
        
        
       public function uploadFile($file, $directory)
    {
        $imageName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $imageName);
        return $imageName;
    }


       public function rider_export(Request $request)
    {
        
        $fields    = $request->input('fields', []);  
        $from_date = $request->input('from_date');
        $to_date   = $request->input('to_date');
        $zone = $request->input('zone_id')?? null;
        $city = $request->input('city_id')?? null;
         $selectedIds = $request->input('selected_ids', []);

    
        if (empty($fields)) {
            return back()->with('error', 'Please select at least one field to export.');
        }
    
        return Excel::download(
            new B2BRiderExport($from_date, $to_date, $selectedIds, $fields,$city,$zone),
            'rider_list-' . date('d-m-Y') . '.xlsx'
        );
    }
        
    
    public function returned_list(Request $request)
        {
            if ($request->ajax()) {
                try {
                    $start  = $request->input('start', 0);
                    $length = $request->input('length', 25);
                    $search = $request->input('search.value');
        
                    $guard = Auth::guard('master')->check() ? 'master' : 'zone';
                    $user  = Auth::guard($guard)->user();
                    $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                    
                    $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
                    ->where('city_id', $user->city_id)
                    ->pluck('id');
                     $accountability_type = $request->accountability_type;
                
                    $query = B2BReturnRequest::with([
                        'rider',
                        'assignment',
                        'assignment.vehicle',
                        'assignment.VehicleRequest',
                        'assignment.VehicleRequest.city',
                        'assignment.VehicleRequest.zone',
                        'rider.customerlogin.customer_relation',
                        'assignment.VehicleRequest.accountAbilityRelation'
                    ]);
        
                    $query->whereHas('assignment.VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds , $accountability_type) {
                    // Always filter by created_by if IDs exist
                        if ($customerLoginIds->isNotEmpty()) {
                            $q->whereIn('created_by', $customerLoginIds);
                        }
                    
                        // Apply guard-specific filters
                        if ($guard === 'master') {
                            $q->where('city_id', $user->city_id);
                        }
                    
                        if (!empty($accountability_type)) {
                            $q->where('account_ability_type', $accountability_type);
                        }
                        
                        
                        if ($guard === 'zone') {
                            $q->where('city_id', $user->city_id)
                              ->where('zone_id', $user->zone_id);
                        }
                    });
                    // Filter by status
                    if ($request->filled('status') && $request->status !== 'all') {
                        $query->where('status', $request->status);
                    }
        
                    // Filter by date range
                    if ($request->filled('from_date') && $request->filled('to_date')) {
                        $query->whereDate('created_at', '>=', $request->from_date)
                              ->whereDate('created_at', '<=', $request->to_date);
                    }
        
        
                    if ($request->filled('city_id')) {
                        $query->whereHas('assignment.VehicleRequest.city', function($ct) use ($request) {
                            $ct->where('id', $request->city_id);
                        });
                    }
                    
                    // Filter by zone_id
                    if ($request->filled('zone_id')) {
                        $query->whereHas('assignment.VehicleRequest.zone', function($zn) use ($request) {
                            $zn->where('id', $request->zone_id);
                        });
                    }
                    

                    // Search filters
                    if (!empty($search)) {
                        $query->where(function($q) use ($search) {
                            // Status, dates
                            $q->where('status', 'like', "%{$search}%")
                              ->orWhereDate('created_at', $search)
                              ->orWhereDate('updated_at', $search);
                    
                            // Vehicle Request (req_id)
                            $q->orWhereHas('assignment.VehicleRequest', function($vr) use ($search) {
                                $vr->where('req_id', 'like', "%{$search}%");
                            });
                    
                            // Vehicle details
                            $q->orWhereHas('assignment.vehicle', function($v) use ($search) {
                                $v->where('permanent_reg_number', 'like', "%{$search}%")
                                  ->orWhere('chassis_number', 'like', "%{$search}%");
                            });
                    
                            // Rider details
                            $q->orWhereHas('rider', function($r) use ($search) {
                                $r->where('name', 'like', "%{$search}%")
                                  ->orWhere('mobile_no', 'like', "%{$search}%");
                            });
                    
                            // Client details
                            $q->orWhereHas('rider.customerlogin.customer_relation', function($c) use ($search) {
                                $c->where('trade_name', 'like', "%{$search}%");
                            });
                            
                            $q->orWhereHas('assignment.VehicleRequest.city', function($ct) use ($search) {
                                $ct->where('city_name', 'like', "%{$search}%");
                            });
                    
                            // Zone
                            $q->orWhereHas('assignment.VehicleRequest.zone', function($zn) use ($search) {
                                $zn->where('name', 'like', "%{$search}%");
                            });
        
                        });
                    }
        
                    $totalRecords = $query->count();
                    if ($length == -1) {
                        $length = $totalRecords;
                    }
        
                    $datas = $query->orderBy('id', 'desc')
                                   ->skip($start)
                                   ->take($length)
                                   ->get();
        
                    $formattedData = $datas->map(function ($item) {
                        // Status display
                        $statusColumn = '';
                        if ($item->status === 'opened') {
                            $statusColumn = '
                                <span style="background-color:#CAEDCE; color:#155724;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-x-circle me-1"></i> Opened
                                </span>';
                        } elseif ($item->status === 'closed') {
                            $statusColumn = '
                                <span style="background-color:#EECACB; color:#721c24;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> Closed
                                </span>';
                        }
        
                        // Aging
                        if ($item->status === 'closed' && $item->closed_at) {
                            $aging = \Carbon\Carbon::parse($item->created_at)
                                        ->diffForHumans(\Carbon\Carbon::parse($item->closed_at), true);
                        } else {
                            $aging = \Carbon\Carbon::parse($item->created_at)
                                        ->diffForHumans(now(), true);
                        }
        
                        // NULL-safe values using data_get
                        $requestId  = data_get($item, 'assignment.VehicleRequest.req_id', 'N/A');
                        $accountability_name = data_get($item, 'assignment.VehicleRequest.accountAbilityRelation.name', 'N/A');
                        $regNumber  = data_get($item, 'assignment.vehicle.permanent_reg_number', '');
                        $chassis    = data_get($item, 'assignment.vehicle.chassis_number', '');
                        $riderName  = data_get($item, 'rider.name', '');
                        $riderPhone = data_get($item, 'rider.mobile_no', '');
                        $clientName = data_get($item, 'rider.customerlogin.customer_relation.trade_name', 'N/A');
                        $cityName = data_get($item, 'assignment.VehicleRequest.city.city_name', 'N/A');
                        $zoneName = data_get($item, 'assignment.VehicleRequest.zone.name', 'N/A');
        
                        $createdAt  = $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y, h:i A') : '';
                        $updatedAt  = $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('d M Y, h:i A') : '';
        
                        $idEncode = encrypt($item->id);
        
                        return [
                            '<div class="form-check">
                                <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" 
                                    name="is_select[]" type="checkbox" value="'.$item->id.'">
                            </div>',
                            e($requestId),
                            e($accountability_name),
                            e($regNumber),
                            e($chassis),
                            e($riderName),
                            e($riderPhone),
                            // e($clientName),
                            e($cityName),      // City
                             e($zoneName),      // Zone
                            $createdAt,
                            $updatedAt,
                            $aging,
                            $statusColumn,
                            '<a href="'.route('b2b.returned.checkview', $idEncode).'"
                                class="d-flex align-items-center justify-content-center border-0" title="View"
                                style="background-color:#CAEDCE;color:#155724;border-radius:8px;width:35px;height:31px;">
                                <i class="bi bi-eye fs-5"></i>
                            </a>'
                        ];
                    });
                    
                 
        
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $formattedData
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Return Request List Error: '.$e->getMessage().' on line '.$e->getLine());
        
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => 0,
                        'recordsFiltered' => 0,
                        'data' => [],
                        'error' => 'Something went wrong: '.$e->getMessage()
                    ], 500);
                }
            }
            
            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
            $user  = Auth::guard($guard)->user();
            $user->load('customer_relation');
    

            $accountability_Types = $user->customer_relation->accountability_type_id;
        
            // Make sure it's an array (sometimes could be stored as string or null)
            if (!is_array($accountability_Types)) {
                $accountability_Types = json_decode($accountability_Types, true) ?? [];
            }
            
            $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                    
            $zoneIds = CustomerLogin::where('customer_id', $customerId)
            ->where('type', 'zone')
            ->pluck('zone_id');
              $zones = Zones::whereIn('id', $zoneIds)->get();
                 $cities = City::where('status',1)->get();
        
            $accountability_types = EvTblAccountabilityType::where('status', 1)
              ->whereIn('id', $accountability_Types)
            ->orderBy('id', 'desc')
            ->get();
        
         return view('b2b::vehicles.returned_list' , compact('cities','zones','guard' , 'accountability_types'));
        }
   
    public function returned_check_view(Request $request , $id)
    {
      
       $return_id = decrypt($id);
       
       $data = B2BReturnRequest::where('id', $return_id)
                ->first();
                
        
        return view('b2b::vehicles.returned_view' , compact('data'));
    }
    
    public function returned_export(Request $request)
        {

            $fields    = $request->input('fields', []);  
            $from_date = $request->input('from_date');
            $to_date   = $request->input('to_date');
            $zone = $request->input('zone_id')?? null;
            $city = $request->input('city_id')?? null;
            $accountability_type = $request->input('accountability_type')?? null;
             $selectedIds = $request->input('selected_ids', []);
    
        
            if (empty($fields)) {
                return back()->with('error', 'Please select at least one field to export.');
            }
        
            return Excel::download(
                new B2BReturnedListExport($from_date, $to_date, $selectedIds, $fields,$city,$zone , $accountability_type),
                'returned-list-' . date('d-m-Y') . '.xlsx'
            );
        }
        
         public function accident_list(Request $request)
{
    if ($request->ajax()) {
        try {
            $start  = $request->input('start', 0);
            $length = $request->input('length', 25);
            $search = $request->input('search.value');
            
            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
                    $user  = Auth::guard($guard)->user();
                    $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                    
            $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
                    ->where('city_id', $user->city_id)
                    ->pluck('id');
                     $accountability_type = $request->accountability_type;
                    
            $query = B2BReportAccident::with([
                        'rider',
                        'assignment',
                        'assignment.vehicle',
                        'assignment.VehicleRequest',
                        'assignment.VehicleRequest.city',
                        'assignment.VehicleRequest.zone',
                        'rider.customerlogin.customer_relation' ,
                        'assignment.VehicleRequest.accountAbilityRelation'
            ]);
            
            $query->whereHas('assignment.VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds , $accountability_type) {
                    // Always filter by created_by if IDs exist
                        if ($customerLoginIds->isNotEmpty()) {
                            $q->whereIn('created_by', $customerLoginIds);
                        }
                    
                        // Apply guard-specific filters
                        if ($guard === 'master') {
                            $q->where('city_id', $user->city_id);
                        }
                    
                        if ($guard === 'zone') {
                            $q->where('city_id', $user->city_id)
                              ->where('zone_id', $user->zone_id);
                        }
                        
                                                if ($accountability_type) {
                            $q->where('account_ability_type', $accountability_type);
                        }
                    });
                    
            // Filter by status
                    if ($request->filled('status') && $request->status !== 'all') {
                        $query->where('status', $request->status);
                    }
        
                    // Filter by date range
                    if ($request->filled('from_date') && $request->filled('to_date')) {
                        $query->whereDate('created_at', '>=', $request->from_date)
                              ->whereDate('created_at', '<=', $request->to_date);
                    }
        
        
                    if ($request->filled('city_id')) {
                        $query->whereHas('assignment.VehicleRequest.city', function($ct) use ($request) {
                            $ct->where('id', $request->city_id);
                        });
                    }
                    
                    // Filter by zone_id
                    if ($request->filled('zone_id')) {
                        $query->whereHas('assignment.VehicleRequest.zone', function($zn) use ($request) {
                            $zn->where('id', $request->zone_id);
                        });
                    }
                    

                    // Search filters
                    if (!empty($search)) {
                        $query->where(function($q) use ($search) {
                            // Status, dates
                            $q->where('status', 'like', "%{$search}%")
                              ->orWhereDate('created_at', $search)
                              ->orWhereDate('updated_at', $search);
                    
                            // Vehicle Request (req_id)
                            $q->orWhereHas('assignment.VehicleRequest', function($vr) use ($search) {
                                $vr->where('req_id', 'like', "%{$search}%");
                            });
                    
                            // Vehicle details
                            $q->orWhereHas('assignment.vehicle', function($v) use ($search) {
                                $v->where('permanent_reg_number', 'like', "%{$search}%")
                                  ->orWhere('chassis_number', 'like', "%{$search}%");
                            });
                    
                            // Rider details
                            $q->orWhereHas('rider', function($r) use ($search) {
                                $r->where('name', 'like', "%{$search}%")
                                  ->orWhere('mobile_no', 'like', "%{$search}%");
                            });
                    
                            // Client details
                            $q->orWhereHas('rider.customerlogin.customer_relation', function($c) use ($search) {
                                $c->where('trade_name', 'like', "%{$search}%");
                            });
                            
                            $q->orWhereHas('assignment.VehicleRequest.city', function($ct) use ($search) {
                                $ct->where('city_name', 'like', "%{$search}%");
                            });
                    
                            // Zone
                            $q->orWhereHas('assignment.VehicleRequest.zone', function($zn) use ($search) {
                                $zn->where('name', 'like', "%{$search}%");
                            });
        
                        });
                    }

            $totalRecords = $query->count();
            if ($length == -1) {
                $length = $totalRecords;
            }

            $datas = $query->orderBy('id', 'desc')
                           ->skip($start)
                           ->take($length)
                           ->get();

            $formattedData = $datas->map(function ($item) {
                // Status Badge UI
                $statuses = [
                        'claim_initiated' => [
                            'label'  => 'Claim Initiated',
                            'colors' => ['#EDCACA', '#580F0F']
                        ],
                        'insurer_visit_confirmed' => [
                            'label'  => 'Insurer Visit Confirmed',
                            'colors' => ['#EDE0CA', '#58490F']
                        ],
                        'inspection_completed' => [
                            'label'  => 'Inspection Completed',
                            'colors' => ['#DEEDCA', '#56580F']
                        ],
                        'approval_pending' => [
                            'label'  => 'Approval Pending',
                            'colors' => ['#CAEDCE', '#1E580F']
                        ],
                        'repair_started' => [
                            'label'  => 'Repair Started',
                            'colors' => ['#CAEDE7', '#0F5847']
                        ],
                        'repair_completed' => [
                            'label'  => 'Repair Completed',
                            'colors' => ['#CAE7ED', '#0F4858']
                        ],
                        'invoice_submitted' => [
                            'label'  => 'Invoice Submitted',
                            'colors' => ['#CAD2ED', '#1A0F58']
                        ],
                        'payment_approved' => [
                            'label'  => 'Payment Approved',
                            'colors' => ['#EDCAE3', '#580F4B']
                        ],
                        'claim_closed' => [
                            'label'  => 'Claim Closed',
                            'colors' => ['#EDE9CA', '#584F0F']
                        ],
                    ];

                $status = $item->status ?? 'N/A';
                
                $label  = $statuses[$status]['label'] ?? ucfirst(str_replace('_', ' ', $status));
                $colors = $statuses[$status]['colors'] ?? ['#ddd', '#333'];
                
                $statusColumn = '<span style="background-color:'.$colors[0].'; color:'.$colors[1].'; border:'.$colors[1].' 1px solid" class="px-2 py-1 rounded-pill">'
                                .e($label).'</span>';

                // Values
                
                
                        $aging = \Carbon\Carbon::parse($item->created_at)
                                        ->diffForHumans(now(), true);
                       
                        
                        $requestId  = data_get($item, 'assignment.VehicleRequest.req_id', 'N/A');
                        $accountability_name  = data_get($item, 'assignment.VehicleRequest.accountAbilityRelation.name', 'N/A');
                        $regNumber  = data_get($item, 'assignment.vehicle.permanent_reg_number', '');
                        $chassis    = data_get($item, 'assignment.vehicle.chassis_number', '');
                        $riderName  = data_get($item, 'rider.name', '');
                        $riderPhone = data_get($item, 'rider.mobile_no', '');
                        $clientName = $item->client_business_name?? 'N/A';
                        $cityName = data_get($item, 'assignment.VehicleRequest.city.city_name', 'N/A');
                        $zoneName = data_get($item, 'assignment.VehicleRequest.zone.name', 'N/A');

                $createdAt = $item->created_at ? $item->created_at->format('d M Y, h:i A') : '';
                $updatedAt = $item->updated_at ? $item->updated_at->format('d M Y, h:i A') : '';
                $idEncode = encrypt($item->id);
                // Actions
                $actions = '
                        <a title="View Ticket Details" href="'.route('b2b.accident.view', $idEncode).'"
                           class="d-flex align-items-center justify-content-center border-0"
                           style="background-color:#CAEDCE; color:#155724; border-radius:8px; width:40px; height:40px;">
                           <i class="bi bi-eye fs-5"></i>
                        </a>
                ';

                return [
                    '<input class="form-check-input sr_checkbox" style="width:25px; height:25px;" type="checkbox" value="'.$item->id.'">',
                    e($requestId),
                    e($accountability_name),
                    e($regNumber),
                    e($chassis),
                    e($riderName),
                    e($riderPhone),
                    // e($clientName),
                    e($cityName),
                    e($zoneName),
                    $createdAt,
                    $updatedAt,
                    $aging,
                    $statusColumn,
                    $actions,
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            \Log::error('Accident List Error: '.$e->getMessage().' on line '.$e->getLine());

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Something went wrong: '.$e->getMessage()
            ], 500);
        }
    }


            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
            $user  = Auth::guard($guard)->user();
            $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                    
                    
            $user->load('customer_relation');
    

            $accountability_Types = $user->customer_relation->accountability_type_id;
        
            // Make sure it's an array (sometimes could be stored as string or null)
            if (!is_array($accountability_Types)) {
                $accountability_Types = json_decode($accountability_Types, true) ?? [];
            }  
            
            $zoneIds = CustomerLogin::where('customer_id', $customerId)
            ->where('type', 'zone')
            ->pluck('zone_id');
          $zones = Zones::whereIn('id', $zoneIds)->get();
            $cities = City::where('status',1)->get();
            $accountability_types = EvTblAccountabilityType::where('status', 1)
                ->whereIn('id' , $accountability_Types)
                ->orderBy('id', 'desc')
                ->get();
    return view('b2b::vehicles.accident_list', compact('cities','guard','zones' , 'accountability_types'));
}

        
    public function accident_view(Request $request,$id)
    {
        $accident_id = decrypt($id);
       
       $data = B2BReportAccident::with('rider','logs')->where('id', $accident_id)
                ->first();
                
        return view('b2b::vehicles.accident_view',compact('data'));
    }

    public function accident_export(Request $request)
        {



            $fields    = $request->input('fields', []);  
            $from_date = $request->input('from_date');
            $to_date   = $request->input('to_date');
            $zone = $request->input('zone_id')?? null;
            $city = $request->input('city_id')?? null;
            $status = $request->input('status')?? null;
            $accountability_type = $request->input('accountability_type')?? null;
            $selectedIds = $request->input('selected_ids', []);
    
        
            if (empty($fields)) {
                return back()->with('error', 'Please select at least one field to export.');
            }
        
            return Excel::download(
                new B2BAccidentReportExport($from_date, $to_date, $selectedIds, $fields,$city,$zone,$status , $accountability_type),
                'accident-report-' . date('d-m-Y') . '.xlsx'
            );
        }
        
        
      public function recovery_list(Request $request)
        {
            if ($request->ajax()) {
                try {
                    $start  = $request->input('start', 0);
                    $length = $request->input('length', 25);
                    $search = $request->input('search.value');
        
                    $guard = Auth::guard('master')->check() ? 'master' : 'zone';
                    $user  = Auth::guard($guard)->user();
                    $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                    $accountability_type = $request->accountability_type ?? '';
                    $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
                    ->where('city_id', $user->city_id)
                    ->pluck('id');
                
                    $query = B2BRecoveryRequest::with([
                        'rider',
                        'assignment',
                        'assignment.vehicle',
                        'assignment.VehicleRequest',
                        'assignment.VehicleRequest.city',
                        'assignment.VehicleRequest.zone',
                        'rider.customerlogin.customer_relation'
                    ]);
                    
                    $query->whereHas('assignment.VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds , $accountability_type) {
                    // Always filter by created_by if IDs exist
                        if ($customerLoginIds->isNotEmpty()) {
                        
                            $q->whereIn('created_by', $customerLoginIds);
                        }
                    
                        // Apply guard-specific filters
                        if ($guard === 'master') {
                            $q->where('city_id', $user->city_id);
                        }
                    
                        if ($guard === 'zone') {
                            $q->where('city_id', $user->city_id)
                              ->where('zone_id', $user->zone_id);
                        }
                       
                        if ($accountability_type) {
                            $q->where('account_ability_type', $accountability_type);
                        }
                    });
                    
                    // Filter by status
                    if ($request->filled('status') && $request->status !== 'all') {
                        $query->where('status', $request->status);
                    }
        
                    // Filter by date range
                    if ($request->filled('from_date') && $request->filled('to_date')) {
                        $query->whereDate('created_at', '>=', $request->from_date)
                              ->whereDate('created_at', '<=', $request->to_date);
                    }
        
        
                    if ($request->filled('city_id')) {
                        $query->whereHas('assignment.VehicleRequest.city', function($ct) use ($request) {
                            $ct->where('id', $request->city_id);
                        });
                    }
                    
                    // Filter by zone_id
                    if ($request->filled('zone_id')) {
                        $query->whereHas('assignment.VehicleRequest.zone', function($zn) use ($request) {
                            $zn->where('id', $request->zone_id);
                        });
                    }
                    

                    // Search filters
                    if (!empty($search)) {
                        $query->where(function($q) use ($search) {
                            // Status, dates
                            $q->where('status', 'like', "%{$search}%")
                              ->orWhereDate('created_at', $search)
                              ->orWhereDate('updated_at', $search);
                    
                            // Vehicle Request (req_id)
                            $q->orWhereHas('assignment.VehicleRequest', function($vr) use ($search) {
                                $vr->where('req_id', 'like', "%{$search}%");
                            });
                    
                            // Vehicle details
                            $q->orWhereHas('assignment.vehicle', function($v) use ($search) {
                                $v->where('permanent_reg_number', 'like', "%{$search}%")
                                  ->orWhere('chassis_number', 'like', "%{$search}%");
                            });
                    
                            // Rider details
                            $q->orWhereHas('rider', function($r) use ($search) {
                                $r->where('name', 'like', "%{$search}%")
                                  ->orWhere('mobile_no', 'like', "%{$search}%");
                            });
                    
                            // Client details
                            $q->orWhereHas('rider.customerlogin.customer_relation', function($c) use ($search) {
                                $c->where('trade_name', 'like', "%{$search}%");
                            });
                            
                            $q->orWhereHas('assignment.VehicleRequest.city', function($ct) use ($search) {
                                $ct->where('city_name', 'like', "%{$search}%");
                            });
                    
                            // Zone
                            $q->orWhereHas('assignment.VehicleRequest.zone', function($zn) use ($search) {
                                $zn->where('name', 'like', "%{$search}%");
                            });
        
                        });
                    }
        
                    $totalRecords = $query->count();
                    if ($length == -1) {
                        $length = $totalRecords;
                    }
        
                    $datas = $query->orderBy('id', 'desc')
                                   ->skip($start)
                                   ->take($length)
                                   ->get();
                  
                    $formattedData = $datas->map(function ($item) {
                        // Status display
                        $statusColumn = '';
                        if ($item->status === 'opened') {
                            $statusColumn = '
                                <span style="background-color:#CAEDCE; color:#155724;border:1px solid #155724;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-x-circle me-1"></i> Opened
                                </span>';
                        } elseif ($item->status === 'closed') {
                            $statusColumn = '
                                <span style="background-color:#EECACB; color:#721c24;border:1px solid #721c24;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> Closed
                                </span>';
                        } elseif ($item->status === 'agent_assigned') {
                            $statusColumn = '
                                <span style="background-color:#FFF3CD; color:#856404;border:1px solid #856404;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-person-check me-1"></i> Agent Assigned
                                </span>';
                        } elseif ($item->status === 'not_recovered') {
                            $statusColumn = '
                                <span style="background-color:#E2E3E5; color:#383D41;border:1px solid #383D41;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Not Recovered
                                </span>';
                        }
                        
                        $agentStatusColumn = '';
                        if ($item->agent_status === 'opened') {
                            $agentStatusColumn = '
                                <span style="background-color:#CAEDCE; color:#155724;border:1px solid #155724;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-x-circle me-1"></i> Opened
                                </span>';
                        } elseif ($item->agent_status === 'in_progress') {
                            $agentStatusColumn = '
                                <span style="background-color:#FFF3CD; color:#856404;border:1px solid #856404;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-arrow-repeat me-1"></i> In Progress
                                </span>';
                        } elseif ($item->agent_status === 'reached_location') {
                            $agentStatusColumn = '
                                <span style="background-color:#CCE5FF; color:#004085;border:1px solid #004085;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-geo-alt me-1"></i> Reached Location
                                </span>';
                        } elseif ($item->agent_status === 'revisit_location') {
                            $agentStatusColumn = '
                                <span style="background-color:#D6D8D9; color:#383D41;border:1px solid #383D41;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Revisited Location
                                </span>';
                        } elseif ($item->agent_status === 'recovered') {
                            $agentStatusColumn = '
                                <span style="background-color:#D4EDDA; color:#155724;border:1px solid #155724;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> Vehicle Found
                                </span>';
                        } elseif ($item->agent_status === 'closed') {
                            $agentStatusColumn = '
                                <span style="background-color:#EECACB; color:#721c24;border:1px solid #721c24;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> Closed
                                </span>';
                        } elseif ($item->agent_status === 'not_recovered') {
                            $agentStatusColumn = '
                                <span style="background-color:#F8D7DA; color:#721c24;border:1px solid #721c24;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Vehicle Not Found
                                </span>';
                        } elseif ($item->agent_status === 'hold') {
                            $agentStatusColumn = '
                                <span style="background-color:#D1ECF1; color:#0C5460;border:1px solid #0C5460;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-pause-circle me-1"></i> Hold
                                </span>';
                        }elseif ($item->agent_status === 'rider_contacted') {
                                $agentStatusColumn = '
                                    <span style="background-color:#E2EAFD; color:#1A237E; border:1px solid #1A237E;" class="px-2 py-1 rounded-pill">
                                        <i class="bi bi-telephone-forward me-1"></i> Follow-up Call
                                    </span>';
                            }
                        else{
                            $agentStatusColumn = '<span style="background-color:#E2E3E5; color:#383D41; border:1px solid #383D41;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-info-circle me-1"></i> Not Assigned
                                </span>';
                        }
                        // Aging
                        if ($item->status === 'closed' && $item->closed_at) {
                            $aging = \Carbon\Carbon::parse($item->created_at)
                                        ->diffForHumans(\Carbon\Carbon::parse($item->closed_at), true);
                        } else {
                            $aging = \Carbon\Carbon::parse($item->created_at)
                                        ->diffForHumans(now(), true);
                        }
        
                        // NULL-safe values using data_get
                        $requestId  = data_get($item, 'assignment.VehicleRequest.req_id', 'N/A');
                        $accountability_name   = data_get($item, 'assignment.VehicleRequest.accountAbilityRelation.name', 'N/A');
                        $regNumber  = data_get($item, 'assignment.vehicle.permanent_reg_number', '');
                        $chassis    = data_get($item, 'assignment.vehicle.chassis_number', '');
                        $riderName  = data_get($item, 'rider.name', '');
                        $riderPhone = data_get($item, 'rider.mobile_no', '');
                        $clientName = data_get($item, 'rider.customerlogin.customer_relation.trade_name', '');
                        $cityName = data_get($item, 'assignment.VehicleRequest.city.city_name', 'N/A');
                        $zoneName = data_get($item, 'assignment.VehicleRequest.zone.name', 'N/A');
        
                        $createdAt  = $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y, h:i A') : '';
                        $updatedAt  = $item->closed_at ? \Carbon\Carbon::parse($item->closed_at)->format('d M Y, h:i A') : '-';
                        
                        $idEncode = encrypt($item->id);
                        $action ='
                        <div class="d-flex align-content-center" style="gap:8px;">
                                <!-- View Button -->
                                <a href="'.route('b2b.recovery.view', $idEncode).'"
                                   class="d-flex align-items-center justify-content-center border-0"
                                   title="View"
                                   style="background-color:#CAEDCE; color:#155724; border-radius:8px; width:35px; height:35px;">
                                   <i class="bi bi-eye fs-5"></i>
                                </a>
                            
                                <!-- Logs Button -->
                                <a href="javascript:void(0);"
                                   data-bs-toggle="modal"
                                   data-bs-target="#showLogModal"
                                   data-agent_id="'.$item->recovery_agent_id.'"
                                   data-id="'.$item->id.'"
                                   data-get_zone_id="'.$item->assignment->vehicleRequest->zone_id.'"
                                   data-get_city_id="'.$item->assignment->vehicleRequest->city_id.'"
                                   title="Logs"
                                   class="view-comments d-flex align-items-center justify-content-center border-0"
                                   style="background-color:#E2E3E5; color:#383D41; border-radius:8px; width:35px; height:35px;">
                                   <i class="bi bi-clock-history fs-5"></i>
                                </a>
                        ';
                        if($item->faq_id && $item->faq_id == 4){
                            $action .='
                            <a href="javascript:void(0);"
                               data-id="'.$item->id.'"
                               title="Close Request"
                               class="close-request d-flex align-items-center justify-content-center border-0"
                               style="background-color:#F8D7DA; color:#721C24; border-radius:8px; width:35px; height:35px;">
                               <i class="bi bi-x-circle fs-5"></i>
                            </a>
                            </div>
                            ';
                        }else{
                            $action .='</div>';
                        }
                        $created_by = 'Unknown';
                        if($item->created_by_type == 'b2b-web-dashboard'){
                            $created_by = 'Customer';
                        }elseif($item->created_by_type == 'b2b-admin-dashboard'){
                            $created_by = 'GDM';
                        }
                        return [
                            '<div class="form-check">
                                <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" 
                                    name="is_select[]" type="checkbox" value="'.$item->id.'">
                            </div>',
                            e($requestId),
                            e($accountability_name),
                            e($regNumber),
                            e($chassis),
                            e($riderName),
                            e($riderPhone),
                            // e($clientName),
                            e($cityName),
                            e($zoneName),
                            $created_by,
                            $createdAt,
                            $updatedAt,
                            $aging,
                            $agentStatusColumn,
                            $statusColumn,
                            $action
                        ];
                    });
                    
                 
        
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $formattedData
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Return Request List Error: '.$e->getMessage().' on line '.$e->getLine());
        
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => 0,
                        'recordsFiltered' => 0,
                        'data' => [],
                        'error' => 'Something went wrong: '.$e->getMessage()
                    ], 500);
                }
            }
            
            
            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
            $user  = Auth::guard($guard)->user();
            $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
            
            $user->load('customer_relation');
    

            $accountability_Types = $user->customer_relation->accountability_type_id;
        
            // Make sure it's an array (sometimes could be stored as string or null)
            if (!is_array($accountability_Types)) {
                $accountability_Types = json_decode($accountability_Types, true) ?? [];
            }        
            $zoneIds = CustomerLogin::where('customer_id', $customerId)
            ->where('type', 'zone')
            ->pluck('zone_id');
              $zones = Zones::whereIn('id', $zoneIds)->get();
                 $cities = City::where('status',1)->get();
              $accountability_types = EvTblAccountabilityType::where('status', 1)
              ->whereIn('id' , $accountability_Types)
            ->orderBy('id', 'desc')
            ->get();
        
            return view('b2b::vehicles.recovery_list' , compact('cities','guard','zones'  , 'accountability_types'));
        }

    
    
    public function recovery_view(Request $request , $id)
    {
       $recovery_id = decrypt($id);
       
       $data = B2BRecoveryRequest::where('id', $recovery_id)
                ->first();
                
        
        return view('b2b::vehicles.recovery_view' , compact('data'));
    }

     public function recovery_export(Request $request)
        {

            $fields    = $request->input('fields', []);  
            $from_date = $request->input('from_date');
            $to_date   = $request->input('to_date');
            $zone = $request->input('zone_id')?? null;
            $city = $request->input('city_id')?? null;
            $status = $request->input('status')?? null;
            $accountability_type = $request->input('accountability_type')?? null;
            $selectedIds = $request->input('selected_ids', []);
    
        
            if (empty($fields)) {
                return back()->with('error', 'Please select at least one field to export.');
            }
        
            return Excel::download(
                new B2BRecoveryRequestExport($from_date, $to_date, $selectedIds, $fields,$city,$zone,$status , $accountability_type),
                'recovery-request-list-' . date('d-m-Y') . '.xlsx'
            );
        }
        

    public function service_list(Request $request)
    {
        
            if ($request->ajax()) {
        try {
            
            
            $start  = $request->input('start', 0);
            $length = $request->input('length', 25);
            $search = $request->input('search.value');
            $from   = $request->input('from_date'); 
            $to     = $request->input('to_date');   
            $zone   = $request->input('zone_id');
            $city   = $request->input('city_id');
            
            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
                    $user  = Auth::guard($guard)->user();
                    $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                    
            $accountability_type   = $request->input('accountability_type');
            $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
                    ->where('city_id', $user->city_id)
                    ->pluck('id');
                    
            $query = B2BServiceRequest::with([
                'assignment.VehicleRequest.city',
                'assignment.VehicleRequest.zone',
                'assignment.vehicle',
                'assignment.VehicleRequest',
                'assignment.rider.customerlogin.customer_relation'
            ]);

            $query->whereHas('assignment.VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds , $accountability_type) {
                    // Always filter by created_by if IDs exist
                        if ($customerLoginIds->isNotEmpty()) {
                            $q->whereIn('created_by', $customerLoginIds);
                        }
                    
                        // Apply guard-specific filters
                        if ($guard === 'master') {
                            $q->where('city_id', $user->city_id);
                        }
                    
                        if ($guard === 'zone') {
                            $q->where('city_id', $user->city_id)
                              ->where('zone_id', $user->zone_id);
                        }
                        if (!empty($accountability_type)) {
                            $q->where('account_ability_type', $accountability_type);
                        }
                    });
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('assignment.VehicleRequest', function($qr) use ($search) {
                        $qr->where('req_id', 'like', "%{$search}%");
                    })
                    ->orWhereHas('assignment.vehicle', function($qr) use ($search) {
                        $qr->where('permanent_reg_number', 'like', "%{$search}%")
                           ->orWhere('chassis_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('assignment.rider', function($qr) use ($search) {
                        $qr->where('name', 'like', "%{$search}%")
                           ->orWhere('mobile_no', 'like', "%{$search}%");
                    })
                    ->orWhereHas('assignment.rider.customerlogin.customer_relation', function($qr) use ($search) {
                        $qr->where('trade_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('assignment.VehicleRequest.city', function($qr) use ($search) {
                        $qr->where('city_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('assignment.VehicleRequest.zone', function($qr) use ($search) {
                        $qr->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('ticket_id', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
                });
            }
            

            if (!empty($from)) {
                $query->whereDate('created_at', '>=', $from);
            }
            if (!empty($to)) {
                $query->whereDate('created_at', '<=', $to);
            }
            if ($request->status) {
                $query->where('status',$request->status);
            }
            if ($city) {
                $query->whereHas('assignment.VehicleRequest', function ($q) use ($city) {
                    $q->where('city_id', $city); // column inside VehicleRequest table
                });
            }
            
            if ($zone) {
                $query->whereHas('assignment.VehicleRequest', function ($q) use ($zone) {
                    $q->where('zone_id', $zone); // column inside VehicleRequest table
                });
            }

            
            
            $totalRecords = $query->count();

            if ($length == -1) {
                $length = $totalRecords;
            }

            $datas = $query->orderBy('id', 'desc')
                           ->skip($start)
                           ->take($length)
                           ->get();
                           
        

           $formattedData = $datas->map(function ($service, $index) use ($start) {
                $idEncode = encrypt($service->id);
            
                // Status column
                $statusColumn = '';
                if ($service->status === 'unassigned') {
                    $statusColumn = '
                        <span style="background-color:#EECACB; color:#A61D1D; border:#A61D1D 1px solid" 
                              class="px-2 py-1 rounded-pill">
                            Unassigned
                        </span>';
                } 
                 else if ($service->status === 'inprogress') {
                    $statusColumn = '
                      <span style="background-color:#D9CAED; color:#7E25EB; border:#7E25EB 1px solid" class="px-2 py-1 rounded-pill">
                         In Progress
                      </span>';
                }
              else if ($service->status === 'closed') {
                    $statusColumn = '
                    <span style="background-color:#CAEDCE; color:#005D27; border:#005D27 1px solid" class="px-2 py-1 rounded-pill">
                        Closed
                      </span>';
                }
                
                else {
                    $statusColumn = '
                        <span style="background-color:#EEE9CA; color:#947B14; border:#947B14 1px solid" 
                              class="px-2 py-1 rounded-pill">
                            Unknown
                        </span>';
                }
            
                // Action buttons
                $actionButtons = '
                    <div class="d-flex align-items-center gap-1">
                        <a href="'.route('b2b.service.view', ['id' => $idEncode]).'" 
                           title="View Rider Details"
                           class="d-flex align-items-center justify-content-center border-0"
                           style="background-color:#CAEDCE; color:#155724; border-radius:8px; width:35px; height:35px;">
                            <i class="bi bi-eye fs-5"></i>
                        </a>
                    </div>
                ';
                
                  if ($service->status === 'closed') {
                            $created   = \Carbon\Carbon::parse($service->created_at);
                            $completed = \Carbon\Carbon::parse($service->updated_at);
                            $diffInDays = $created->diffInDays($completed);
                            $diffInHours = $created->diffInHours($completed);
                            $diffInMinutes = $created->diffInMinutes($completed);
                        
                            if ($diffInDays > 0) {
                                $aging = $diffInDays . ' days';
                            } elseif ($diffInHours > 0) {
                                $aging = $diffInHours . ' hours';
                            } else {
                                $aging = $diffInMinutes . ' mins';
                            }
                        } else {
                            $created   = \Carbon\Carbon::parse($service->created_at);
                            $now       = now();
                            $diffInDays = $created->diffInDays($now);
                            $diffInHours = $created->diffInHours($now);
                            $diffInMinutes = $created->diffInMinutes($now);
                        
                            if ($diffInDays > 0) {
                                $aging = $diffInDays . ' days';
                            } elseif ($diffInHours > 0) {
                                $aging = $diffInHours . ' hours';
                            } else {
                                $aging = $diffInMinutes . ' mins';
                            }
                        }
            
                return [
                    '<div class="form-check">
                                    <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" 
                                           name="is_select[]" type="checkbox" value="'.$service->id.'">
                                </div>',
                                
                    // Request Id
                    e($service->assignment->VehicleRequest->req_id ?? '-'),
                    
                    e($service->assignment->VehicleRequest->accountAbilityRelation->name ?? '-'),
                    
                    
                    e($service->ticket_id ?? '-'),
                    
                    // Vehicle No
                    e($service->assignment->vehicle->permanent_reg_number ?? '-'),
            
                    // Chassis No
                    e($service->assignment->vehicle->chassis_number ?? '-'),
            
                    // Rider Name
                    e($service->assignment->rider->name ?? '-'),
            
                    // Contact Details
                    e($service->assignment->rider->mobile_no ?? '-'),
            
                    // Client
                    // e($service->assignment->rider->customerlogin->customer_relation->trade_name ?? ''),
            
                    // City
                    e($service->assignment->VehicleRequest->city->city_name ?? '-'),
            
                    // Zone
                    e($service->assignment->VehicleRequest->zone->name ?? '-'),
            
                    // Created Date and Time
                    $service->created_at ? $service->created_at->format('d M Y h:i A') : '-',
            
                    // Updated Date and Time
                    $service->updated_at ? $service->updated_at->format('d M Y h:i A') : '-',
                    
                    $aging ,
            
                    // Created By (Type - first letter capital)
                    ucfirst($service->type ?? ''),
            
                    // Status
                    $statusColumn,
            
                    // Action Buttons
                    $actionButtons
                ];
            });
            
            return response()->json([
                'draw'            => intval($request->input('draw')),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data'            => $formattedData
            ]);

        } catch (\Exception $e) {
            \Log::error('Rider List Error: '.$e->getMessage());

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
            $user  = Auth::guard($guard)->user();
            $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
            $user->load('customer_relation');
    

            $accountability_Types = $user->customer_relation->accountability_type_id;
        
            // Make sure it's an array (sometimes could be stored as string or null)
            if (!is_array($accountability_Types)) {
                $accountability_Types = json_decode($accountability_Types, true) ?? [];
            }
                    
            $zoneIds = CustomerLogin::where('customer_id', $customerId)
            ->where('type', 'zone')
            ->pluck('zone_id');
            $zones = Zones::whereIn('id', $zoneIds)->get();
             $cities = City::where('status',1)->get();
             
            $accountability_types = EvTblAccountabilityType::where('status', 1)
            ->whereIn('id', $accountability_Types)
            ->orderBy('id', 'desc')
            ->get();
    
        return view('b2b::vehicles.service_list',compact('cities','guard','zones' , 'accountability_types'));
    }
    
    
    public function service_view(Request $request , $id)
    {
        $service_id = decrypt($id);
    
        
        
        $data = B2BServiceRequest::where('id' ,$service_id)->first();
        
        $repair_types = RepairTypeMaster::where('status',1)->get();
        
        $apiKey = BusinessSetting::where('key_name', 'google_map_api_key')->value('value');
        
        return view('b2b::vehicles.service_view' , compact('apiKey' ,'data' , 'repair_types'));
    }
      
      public function service_export(Request $request)
    {
        
        $fields    = $request->input('fields', []);  
        $from_date = $request->input('from_date');
        $to_date   = $request->input('to_date');
        $zone = $request->input('zone_id')?? null;
        $city = $request->input('city_id')?? null;
        $status = $request->input('status')?? null;
        $accountability_type = $request->input('accountability_type')?? null;
        $selectedIds = $request->input('selected_ids', []);

    
        if (empty($fields)) {
            return back()->with('error', 'Please select at least one field to export.');
        }
        return Excel::download(
            new B2BServiceRequestExport($from_date, $to_date, $selectedIds, $fields,$city,$zone,$status , $accountability_type),
            'service-request-list-' . date('d-m-Y') . '.xlsx'
        );
    }
    
    
        public function closeRequest(Request $request,$id)
    {   
        
        $recoveryId = $id; // from email link

        if (!$recoveryId) {
            return response()->json([
                'status'  => false,
                'message' => 'Recovery ID is required.'
            ], 400);
        }

        $recovery = B2BRecoveryRequest::with('rider','recovery_agent')->find($recoveryId);

        if (!$recovery) {
            return response()->json([
                'status'  => false,
                'message' => 'Recovery request not found.'
            ], 404);
        }

        if ($recovery->status === 'closed') {
            return response()->json([
                'status'  => false,
                'message' => 'This recovery request is already closed.'
            ], 200);
        }

        // Update status
        $recovery->status = 'closed';
        $recovery->agent_status = 'closed';
        $recovery->closed_by = $recovery->created_by; 
        $recovery->closed_by_type = 'b2b-customer';
        $recovery->closed_at = now();
        $recovery->faq_id = null;
        $recovery->save();
        
        B2BVehicleAssignmentLog::create([
            'assignment_id'   => $recovery->assign_id,
            'status'          => 'closed',
            'remarks'         => 'Closed by customer via b2b dashboard',
            'action_by'       => $recovery->created_by ?? null,
            'type'            => 'b2b-customer' ,
            'request_type'    => 'recovery_request',
            'request_type_id' => $recovery->id,
            'location_lat'    => null,
            'location_lng'    => null
            ]);
        
        $remark = RecoveryComment::create([
            'req_id'    => $recovery->id,
            'status'    => 'closed',
            'comments'  => 'Closed by customer via b2b dashboard',
            'user_id'   => $recovery->created_by ?? null,
            'user_type' => 'b2b-customer',
            'location_lat'    => null,
            'location_lng'    => null
        ]);
        Log::info("Recovery request #{$recovery->id} closed by customer.");
        
        $admins = User::whereIn('role', [1,13])
            ->where('status', 'Active')
            ->pluck('email')
            ->toArray();
        
        $manager = '';
    
        if($recovery->city_manager_id){
            $manager =  User::where('id',$recovery->city_manager_id)
                ->where('status', 'Active')
                ->pluck('email');
        }
        
        $customerEmail = $recovery->rider->customerLogin->customer_relation->email;
        // $recipients = [
        //     [
        //         'to'  => $customerEmail,
        //         'cc'  => [$manager],
        //         'bcc' => $admins
        //     ]
        // ];
        
        $recipients = [
            [
                'to'  => 'logeshmudaliyar2802@gmail.com',
                'cc'  => ['mudaliyarlogesh@gmail.com'],
                'bcc' => array_merge(['pratheesh@alabtechnology.com'],['gowtham@alabtechnology.com'])
            ]
        ];
        
        $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
        $footerContent = $footerText ?? "For any assistance, please reach out to Admin Support.<br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
        
        $faqSubject = 'Thank You – Your Recovery Request Has Been Closed (ID: #'.$recovery->assignment->req_id.')';
        $customer = $recovery->rider->customerLogin->customer_relation->trade_name ?? $recovery->client_name;
        $faqBody = '
        <html>
        <body style="font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px;">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:auto; background:#fff; border-radius:8px;">
                <tr>
                    <td style="padding:20px; text-align:center; background:#2196F3; color:#fff;">
                        <h2>Recovery Request Closed</h2>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px;">
                        <p>Dear <strong>'.$customer.'</strong>,</p>
                        <p>Thank you for confirming the closure of your recovery request <strong>#'.$recovery->assignment->req_id.'</strong>.</p>
                        <p>We appreciate your prompt response and cooperation throughout the recovery process.</p>
                        
                        <p><strong>Summary of Request</strong></p>
                        <table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; max-width: 600px;">
                            <tr style="background:#f2f2f2;">
                                <td><strong>Vehicle Number</strong></td>
                                <td>'.($recovery->vehicle_number ?? 'N/A').'</td>
                            </tr>
                            <tr>
                                <td><strong>Chassis Number</strong></td>
                                <td>'.($recovery->chassis_number ?? 'N/A').'</td>
                            </tr>
                            <tr style="background:#f2f2f2;">
                                <td><strong>Closed On</strong></td>
                                <td>'.now()->format('d M Y, h:i A').'</td>
                            </tr>
                        </table>

                        <p>'.$footerContent.'</p>
                    </td>
                </tr>
            </table>
        </body>
        </html>';
        
        $this->sendDynamicEmailNotify($recipients,$faqSubject,$faqBody,false);
        $this->AutoSendRecoveryWhatsAppMessage($recovery,'closed_by_customer_notify','closed');
        return response()->json([
            'status'  => true,
            'message' => 'The recovery request has been successfully closed.',
            'data'    => [
                'recovery_id' => $recovery->id,
                'status' => $recovery->status,
            ]
        ], 200);
    }
    
       public function AutoSendRecoveryWhatsAppMessage($recovery,$forward_type,$status='null')
    {
            // $recovery = B2BRecoveryRequest::with('rider.customerLogin.customer_relation','recovery_agent','rider' 
            // ,'assignment.vehicle' ,'assignment.VehicleRequest' )->find($request_id); 
            
            if (!$recovery) {
                Log::info('Assign Recovery Agent : Recovery Request not found');
                return false;
            }
            
            if($recovery->recovery_agent_id){
                $agent = Deliveryman::find($recovery->recovery_agent_id);
                $agentName    = $agent->first_name .' '. $agent->first_name  ?? 'Agent';
                $agentPhone   = $agent->mobile_number ;
                
                if (!$agent || !$agent->mobile_number) {
                Log::info('Assign Recovery Agent : Agent or mobile number not found');
                return false;
                }
            }
            
            $manager = User::find($recovery->city_manager_id);

            if (!$manager || !$manager->phone) {
                Log::info('Assign Recovery Agent : Manager or mobile number not found');
                return false;
            }
            

            // WhatsApp API
            $api_key = BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
            $url = BusinessSetting::where('key_name', 'whatshub_api_url')->value('value');
        
            
            $requestId    = $recovery->assignment->req_id ?? '';
            $customerID   = $recovery->rider->customerLogin->customer_relation->id ?? 'N/A';
            $customerName = $recovery->rider->customerLogin->customer_relation->name ?? 'N/A';
            $customerEmail= $recovery->rider->customerLogin->customer_relation->email ?? 'N/A';
            $customerPhone= $recovery->rider->customerLogin->customer_relation->phone ?? '';
            //vehicle details
            $AssetvehicleId    = $recovery->assignment->asset_vehicle_id ?? 'N/A'; 
            $vehicleNo    = $recovery->assignment->vehicle->permanent_reg_number ?? 'N/A'; 
            $vehicleType  = $recovery->assignment->vehicle->vehicle_type_relation->name ?? 'N/A'; 
            $vehicleModel  = $recovery->assignment->vehicle->vehicle_model_relation->vehicle_model ?? 'N/A'; 
            $cityData = City::select('city_name')->where('id',$manager->city_id)->first();
            $zoneData = Zones::select('name')->where('id',$manager->zone_id)->first();
            //agent details
            $assignBy_managerName    = $manager->name;
            $assignBy_managerPhone   = $manager->phone;
            $assignBy_managerCity = 'N/A';
            $assignBy_managerZone = 'N/A';

            $reasonData = RecoveryReasonMaster::where('id',$recovery->reason)->first();
            $reason = $reasonData->label_name ?? 'Unknown';
            if($cityData){
                $assignBy_managerCity = $cityData->city_name;
            }
            if($zoneData){
                $assignBy_managerZone = $zoneData->name;
            }
            //   dd($vehicle_id,$AssetvehicleId,$vehicleNo,$vehicleType,$vehicleModel,$agentName,$agentPhone,$requestId,$customerID,$customerName,$customerEmail,$customerPhone,$assignBy_managerName,$assignBy_managerPhone,$assignBy_managerCity,$assignBy_managerZone);

            $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
            $footerContentText = $footerText ??
                "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
            
             $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.\n" .
                 "Email: {$customerEmail}\n" .
                 "Thank you,\n" .
                 "{$customerName}";

            if($forward_type == 'closed_by_customer_notify'){
                $agent_message = 
                    "Hello {$agentName},\n\n" .
                    "Customer has been successfully closed recovery request assigned to you.\n\n" .
                    "📌 *Request Details:*\n" .
                    "• Request ID: {$requestId}\n" .
                    "• Recovery Reason: {$reason}\n" .
                    "• Recovery Description: {$recovery->description}\n\n" .
                    "*Vehicle Information:*\n" .
                    "• Vehicle ID: {$AssetvehicleId}\n" .
                    "• Vehicle No: {$vehicleNo}\n" .
                    "• Vehicle Type: {$vehicleType}\n" .
                    "• Vehicle Model: {$vehicleModel}\n\n" .
                    "{$footerContentText}";
                    
                $customer_message = 
                    "Hello {$customerName},\n\n" .
                    "You have successfully closed recovery request Id :#{requestId} .\n\n" .
                    "📌 *Request Details:*\n" .
                    "• Request ID: {$requestId}\n" .
                    "• Recovery Reason: {$reason}\n" .
                    "• Recovery Description: {$recovery->description}\n\n" .
                    "*Vehicle Information:*\n" .
                    "• Vehicle ID: {$AssetvehicleId}\n" .
                    "• Vehicle No: {$vehicleNo}\n" .
                    "• Vehicle Type: {$vehicleType}\n" .
                    "• Vehicle Model: {$vehicleModel}\n\n" .
                    "*Recovery Manager:* {$assignBy_managerName}\n\n" .
                    "{$footerContentText}";

                $manager_message = 
                    "Hello {$assignBy_managerName},\n\n" .
                    "Customer have successfully closed a Recovery Request.\n\n" .
                    "📌 *Request Details:*\n" .
                    "• Request ID: {$requestId}\n\n" .
                    "*Customer Information:*\n" .
                    "• Customer Name: {$customerName}\n\n" .
                    "*Rider Information:*\n" .
                    "• Name: {$agentName}\n" .
                    "• Phone: {$agentPhone}\n\n" .
                    "*Vehicle Information:*\n" .
                    "• Vehicle ID: {$AssetvehicleId}\n" .
                    "• Vehicle No: {$vehicleNo}\n" .
                    "• Vehicle Type: {$vehicleType}\n" .
                    "• Vehicle Model: {$vehicleModel}\n\n" .
                    "📍 *Assigned Zone:* {$assignBy_managerZone}, {$assignBy_managerCity}\n\n" .
                    "{$footerContentText}";
                
                $admin_message = 
                "Dear Admin,\n\n" .
                "A recovery request has been closed by Customer.\n\n" .
                "📌 *Request Details:*\n" .
                "• Request ID: {$requestId}\n\n" .
                "*Customer Information:*\n" .
                "• Customer Name: {$customerName}\n" .
                "• Customer ID: {$customerID}\n\n" .
                "*Agent Information:*\n" .
                "• Name: {$agentName}\n" .
                "• Phone: {$agentPhone}\n\n" .
                "*Vehicle Information:*\n" .
                "• Vehicle ID: {$AssetvehicleId}\n" .
                "• Vehicle No: {$vehicleNo}\n" .
                "• Vehicle Type: {$vehicleType}\n" .
                "• Vehicle Model: {$vehicleModel}\n\n" .
                "*Recovery Manager:* {$assignBy_managerName}\n" .
                "📍 *Recovery Manager Zone:* {$assignBy_managerZone}, {$assignBy_managerCity}\n\n" .
                "{$footerContentText}";
            }
            
            if($forward_type == 'manager_status_update_whatsapp_notify'){
                if(!$status || !in_array($status, ['closed', 'not_recovered'])){
                   Log::info('Recovery Status Update by Manager : Status not available or it is invalid');
                    return false; 
                }
                    $statusLabel = [
                        "closed" => "Closed",
                        "not_recovered" => "Not Recovered"
                    ];
                    $statusText = $statusLabel[$status] ?? ucfirst($status);
                $agent_content = '';
                if($recovery->recovery_agent_id){
                    $agent_content = 
                        "*Agent Information:*\n" .
                        "• Name: {$agentName}\n" .
                        "• Phone: {$agentPhone}\n\n";
                    
                    $agent_message = 
                        "Hello {$agentName},\n\n" .
                        "Your Recovery Request Id :#{$requestId} status has been changed to {$statusText}.\n\n" .
                        "📌 *Request Details:*\n" .
                        "• Request ID: {$requestId}\n\n" .
                        "*Vehicle Information:*\n" .
                        "• Vehicle ID: {$AssetvehicleId}\n" .
                        "• Vehicle No: {$vehicleNo}\n" .
                        "• Vehicle Type: {$vehicleType}\n" .
                        "• Vehicle Model: {$vehicleModel}\n\n" .
                        "{$footerContentText}";
                }
                    
                
                    
                $customer_message = 
                    "Hello {$customerName},\n\n" .
                    "A recovery request id :#{$requestId} status has been changed to {$statusText}.\n\n" .
                    "📌 *Request Details:*\n" .
                    "• Request ID: {$requestId}\n\n" .
                     "{$agent_content}".
                    "*Vehicle Information:*\n" .
                    "• Vehicle ID: {$AssetvehicleId}\n" .
                    "• Vehicle No: {$vehicleNo}\n" .
                    "• Vehicle Type: {$vehicleType}\n" .
                    "• Vehicle Model: {$vehicleModel}\n\n" .
                    "*Managed By:* {$assignBy_managerName}\n" .
                    "📍 *Manager Zone:* {$assignBy_managerZone}, {$assignBy_managerCity}\n\n" .
                    "{$footerContentText}";

                $manager_message = 
                "Hello {$assignBy_managerName},\n\n" .
                "You have successfully updated the status of recovery request id :#{$requestId} to {$statusText}.\n\n" .
                "📌 *Request Details:*\n" .
                "• Request ID: {$requestId}\n\n" .
                "{$agent_content}".
                "*Vehicle Information:*\n" .
                "• Vehicle ID: {$AssetvehicleId}\n" .
                "• Vehicle No: {$vehicleNo}\n" .
                "• Vehicle Type: {$vehicleType}\n" .
                "• Vehicle Model: {$vehicleModel}\n\n" .
                "📍 *Manager Zone:* {$assignBy_managerZone}, {$assignBy_managerCity}\n\n" .
                "{$footerContentText}";
                
                $admin_message = 
                "Dear Admin,\n\n" .
                "A recovery request id :#{$requestId} status has been changed to {$statusText}.\n\n" .
                "📌 *Request Details:*\n" .
                "• Request ID: {$requestId}\n\n" .
                "*Customer Information:*\n" .
                "• Customer Name: {$customerName}\n" .
                "• Customer ID: {$customerID}\n\n" .
                "{$agent_content}".
                "*Vehicle Information:*\n" .
                "• Vehicle ID: {$AssetvehicleId}\n" .
                "• Vehicle No: {$vehicleNo}\n" .
                "• Vehicle Type: {$vehicleType}\n" .
                "• Vehicle Model: {$vehicleModel}\n\n" .
                "*Managed By:* {$assignBy_managerName}\n" .
                "📍 *Manager Zone:* {$assignBy_managerZone}, {$assignBy_managerCity}\n\n" .
                "{$footerContentText}";
            }
            
            
            // Rider message
            // if($recovery->recovery_agent_id){
            //     if (!empty($agentPhone)) {
            //         CustomHandler::user_whatsapp_message('+917812880655', $agent_message);
            //         // CustomHandler::user_whatsapp_message($agentPhone, $agent_message);
            //     }
            // }

            // Customer message
            if (!empty($customerPhone)) {
                CustomHandler::user_whatsapp_message('+917812880655', $customer_message);
                // CustomHandler::user_whatsapp_message($customerPhone, $customer_message);
            }
            // Agent message
            if (!empty($assignBy_managerPhone)) {
                CustomHandler::user_whatsapp_message('+917812880655', $manager_message);
                // CustomHandler::user_whatsapp_message($assignBy_managerPhone, $manager_message);

            }
            
            $adminPhone = BusinessSetting::where('key_name', 'admin_whatsapp_no')->value('value');
            if (!empty($adminPhone)) {

                // CustomHandler::admin_whatsapp_message($admin_message);
            }
           
    
        }
        
    public function sendDynamicEmailNotify(array $recipients, string $subject, string $body, bool $footer = false)
        {
            // Add footer content dynamically if needed
            if ($footer) {
                $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
                $footerContent = $footerText ?? "For any assistance, please reach out to Admin Support.<br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
                $body .= "<p style='margin-top:20px;'>{$footerContent}</p>";
            }
        
            foreach ($recipients as $recipient) {
                $to  = $recipient['to'] ?? null;
                $cc  = (array) ($recipient['cc'] ?? []);
                $bcc = (array) ($recipient['bcc'] ?? []);
        
                if (!empty($to)) {
                    CustomHandler::updatedSendEmail($to, $subject, $body, $cc, $bcc);
                }
            }
        
            return true;
        }
        
    public function recovery_logs(Request $request,$req_id){
        
        $logs = B2BVehicleAssignmentLog::where('request_type', 'recovery_request')
        ->where('request_type_id', $req_id)
                ->orderBy('created_at', 'asc')
                ->get();
        
        $roles = Role::All();
        $customers = CustomerMaster::All();
         $updates = RecoveryUpdatesMaster::where('status',1)->get();
        $html = view('b2b::vehicles.recovery_logs', compact('logs','roles','customers','updates'))->render();
    
        return response()->json(['success' => true, 'html' => $html]);
    }

    
}
