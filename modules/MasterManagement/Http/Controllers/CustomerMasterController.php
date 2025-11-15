<?php

namespace Modules\MasterManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\EVState;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Laravel\Fortify\Rules\Password;//updated by Mugesh.B
use Modules\MasterManagement\Entities\CustomerLogin; //updated by Mugesh.B
use Illuminate\Support\Facades\Auth;
use Modules\City\Entities\City;
use Illuminate\Support\Facades\Hash;//updated by Mugesh.B
use Modules\MasterManagement\Entities\HypothecationMaster;
use Modules\MasterManagement\Entities\EvTblAccountabilityType; //updateed by Mugesh.B
use Modules\MasterManagement\Entities\CustomerTypeMaster; //updateed by Mugesh.B
use Modules\MasterManagement\Entities\CustomerMaster;
use Modules\MasterManagement\Entities\CustomerPOCDetail;
use Modules\MasterManagement\Entities\CustomerOperationalHub;
use Modules\MasterManagement\Entities\BusinessConstitutionType;
use App\Exports\CustomerMasterExport;
use App\Exports\MultiSheetExportCustomerMaster;
use App\Helpers\CustomHandler;

class CustomerMasterController extends Controller
{
    
    public function index(Request $request)
    {
        $query = CustomerMaster::with('cities');
    
        $status = $request->status ?? 'all';
        $timeline   = $request->timeline ?? '';
        
        if($request->status != ""){
            $ch_status = $request->status;
        }else{
            $ch_status = 'all';
        }
        
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
    
        if (in_array($status, ['1', '0'])) {
            
            $query->where('status', $status);
        }
        
       // Apply timeline filters
        if ($timeline) {
            switch ($timeline) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
    
                case 'this_week':
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(), now()->endOfWeek()
                    ]);
                    break;
    
                case 'this_month':
                    $query->whereBetween('created_at', [
                        now()->startOfMonth(), now()->endOfMonth()
                    ]);
                    break;
    
                case 'this_year':
                    $query->whereBetween('created_at', [
                        now()->startOfYear(), now()->endOfYear()
                    ]);
                    break;
            }
        } else {
            // Manual date filtering
            if (!empty($from_date)) {
                $query->whereDate('created_at', '>=', $from_date);
            }
    
            if (!empty($to_date)) {
                $query->whereDate('created_at', '<=', $to_date);
            }
        }
    
        $lists = $query->orderBy('id', 'desc')->get();

        return view('mastermanagement::customer_master.index', compact('lists','status', 'from_date', 'to_date','ch_status','timeline'));
    }
    
    public function create(Request $request)
    {
        $constutition_types = BusinessConstitutionType::where('status',1)->get();
        $states = EVState::where('status',1)->get();
        $cities = City::where('status',1)->get();
        $types = EvTblAccountabilityType::where('status',1)->get();
        $customer_types = CustomerTypeMaster::where('status',1)->get();
        
        return view('mastermanagement::customer_master.create',compact('constutition_types','cities','states' , 'types' , 'customer_types'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
//         'email' => 'required|email|unique:ev_tbl_customer_master,email,' . $customerId,
// 'contact_no' => 'required|string|unique:ev_tbl_customer_master,phone,' . $customerId,
// 'gst_no' => 'required|string|unique:ev_tbl_customer_master,gst_no,' . $customerId,
// 'pan_no' => 'required|string|unique:ev_tbl_customer_master,pan_no,' . $customerId,

        // Correct validation merging
        $rules = [
            'customer_type' => 'required|in:1,2',
            'business_type' => 'required|in:1,2',
            'name' => 'required|string|unique:ev_tbl_customer_master,name',
            // 'email' => 'required|email|unique:ev_tbl_customer_master,email',
            'email' => 'required|email',
            'accountability_type' => 'required',
            'client_type' => 'required',
            'pincode' => 'required',
            'trade_name' => 'required',
            'contact_no' => [
            'required',
            'string',
            'unique:ev_tbl_customer_master,phone',
            'regex:/^(\+91[\-\s]?|91[\-\s]?|0)?[6-9]\d{9}$/'
             ],

            
            'gst_no' => [
                'required',
                'string',
                // 'unique:ev_tbl_customer_master,gst_no',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'
            ],
        

        
            'city' => 'required',
            'state' => 'required',
            'address' => 'required|string',
        
            // 'adhaar_front_img' => 'required|mimes:png,jpg,jpeg,pdf',
            // 'adhaar_back_img' => 'required|mimes:png,jpg,jpeg,pdf',
            
            'gst_img' => 'required|mimes:png,jpg,jpeg,pdf',
            'other_business_proof' => 'nullable|mimes:png,jpg,jpeg,pdf',
            'company_logo_img' => 'nullable|mimes:png,jpg,jpeg',
            'profile_img' => 'nullable|mimes:png,jpg,jpeg',
        ];


        if ($request->customer_type == 2) {

            $rules['business_constutition_type'] = 'required';
        }else{
            $rules['pan_no'] = [
                'required',
                'string',
                'unique:ev_tbl_customer_master,pan_no',
                'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'
            ];
            $rules['pan_img'] = 'required|mimes:png,jpg,jpeg,pdf';
            
            $rules['adhaar_front_img'] = 'required|mimes:png,jpg,jpeg,pdf';
            $rules['adhaar_back_img'] = 'required|mimes:png,jpg,jpeg,pdf';
        }
    
        $messages = [
            'gst_no.regex' => 'Enter a valid GST number (e.g., 29ABCDE1234F2Z5).',
            'pan_no.regex' => 'Enter a valid PAN number (e.g., ABCDE1234F).',
            'contact_no.regex' => 'Enter a valid Indian mobile number starting with 6-9 and optionally prefixed with +91.',
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }
    
        DB::beginTransaction();
    
        try {
            
            $lastCode = CustomerMaster::selectRaw("CAST(SUBSTRING(id, 7) AS UNSIGNED) as number")
                ->orderByDesc('number')
                ->value('number');
            
            $newNumber = $lastCode ? $lastCode + 1 : 1001;
            $cCode = 'GDMCUS' . $newNumber;

            
            $customer = new CustomerMaster();
            $customer->id = $cCode;
            // Uploads
            if ($request->hasFile('adhaar_front_img')) {
                $customer->adhaar_front_img = CustomHandler::uploadFileImage(
                    $request->file('adhaar_front_img'),
                    'EV/vehicle_transfer/adhaar_front_images'
                );
            }
    
            if ($request->hasFile('adhaar_back_img')) {
                $customer->adhaar_back_img = CustomHandler::uploadFileImage(
                    $request->file('adhaar_back_img'),
                    'EV/vehicle_transfer/adhaar_back_images'
                );
            }
    
            if ($request->hasFile('pan_img')) {
                $customer->pan_img = CustomHandler::uploadFileImage(
                    $request->file('pan_img'),
                    'EV/vehicle_transfer/pan_card_images'
                );
            }
    
            if ($request->hasFile('gst_img')) {
                $customer->gst_img = CustomHandler::uploadFileImage(
                    $request->file('gst_img'),
                    'EV/vehicle_transfer/gst_images'
                );
            }
    
            if ($request->hasFile('other_business_proof')) {
                $customer->business_proof_img = CustomHandler::uploadFileImage(
                    $request->file('other_business_proof'),
                    'EV/vehicle_transfer/other_business_proof_images'
                );
            }
            
            
            if ($request->hasFile('company_logo_img')) {
                $customer->company_logo = CustomHandler::uploadFileImage(
                    $request->file('company_logo_img'),
                    'EV/vehicle_transfer/company_logos'
                );
            }
            
            
            
            if ($request->hasFile('profile_img')) {
             
                $customer->profile_img = CustomHandler::uploadFileImage(
                    $request->file('profile_img'),
                    'EV/vehicle_transfer/profile_images'
                );
                  
            }

            
    
            // Basic info
            $customer->customer_type = $request->customer_type;
            $customer->business_type = $request->business_type;
            $customer->business_const_type = $request->business_constutition_type;
            $customer->name = $request->name; 
            $customer->email = $request->email;
            $customer->phone = $request->contact_no;
            $customer->gst_no = $request->gst_no;
            $customer->pan_no = $request->pan_no;
            $customer->address = $request->address;
            $customer->city_id = $request->city;
            $customer->state_id = $request->state;
             $customer->pincode = $request->pincode;
            $customer->trade_name = $request->trade_name;
            
            $customer->accountability_type_id = $request->accountability_type;
            $customer->start_date = $request->start_date;
            $customer->end_date = $request->end_date;
            $customer->client_type = $request->client_type ?? '';
            
            
            $customer->save();
    
            // POC details
            $poc_detail_names = $request->poc_name ?? [];
    
            if ($request->add_poc_details == true && !empty($poc_detail_names)) {
                foreach ($poc_detail_names as $index => $name) {
                    CustomerPOCDetail::create([
                        'customer_id' => $cCode,
                        'name' => $request->poc_name[$index],
                        'email' => $request->poc_email[$index] ?? null,
                        'phone' => $request->poc_phone[$index] ?? null,
                    ]);
                }
            }
    
            // Operational hubs
            if ($request->add_hubs_details == true && !empty($request->hub_name)) {
                foreach ($request->hub_name as $index => $hub) {
                    CustomerOperationalHub::create([
                        'customer_id' => $cCode,
                        'hub_name' => $hub,
                    ]);
                }
            }
    
            DB::commit();
            $customer['customer_id'] = $cCode;
            
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
            audit_log_after_commit([
                'module_id'         => 1,
                'short_description' => 'Customer Created',
                'long_description'  => "Customer '{$customer->name}' created successfully. ID: {$customer->id}, Phone: {$customer->phone}, Email: {$customer->email}.",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'customer_master.create',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
            // $this->CustomerSentEmail($customer,'customer_create_notify'); //sent email
    
            return response()->json([
                'success' => true,
                'message' => 'Customer added successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

       function CustomerSentEmail($customer, $forward_type, $account_status = null)
    {
        $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
        $footerContentText = $footerText ?? "For any assistance, please reach out to Admin Support.<br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
    
        // To and CC emails
        $toEmails = $customer->email;
        // $toEmails = 'gowtham@alabtechnology.com';
        $ccEmails = DB::table('roles')
            ->leftJoin('users', 'roles.id', '=', 'users.role')
            ->select('users.email')
            ->whereIn('users.role', [1,13]) // Admin
            ->where('users.status','Active')
            ->pluck('users.email')
            ->filter()
            ->toArray();
    
        if ($forward_type == 'customer_create_notify') {
           
            
            $customer_type = 'N/A';
            if($customer->business_type == 1){
                $customer_type = 'Individual'; 
                
            }else if($customer->business_type == 2){
                $customer_type = 'Company'; 
                
            } 
            
            $business_type = 'N/A';
            if($customer->business_type == 1){
                $business_type = 'Registered';
                }else if($customer->business_type == 2){ 
                    $business_type = 'Unregistered'; 
                    
                } 
            $business_constitionType = $customer->constitution_type->name ?? 'N/A';
            $companyAddress = $customer->address ?? 'N/A';
    
            $subject = "ðŸŽ‰ New Customer Account Created - Green Drive Connect";
    
            $body = "
            <html>
            <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #544e54;'>
                <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 650px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #544e54;'>
                    <tr>
                        <td style='padding: 10px; text-align: center; background-color: #8b8b8b; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                            <h2>Welcome to Green Drive Connect</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding: 20px; color: #544e54;'>
                            <p>Hello <strong>{$customer->name}</strong>,</p>
                            <p>Weâ€™re excited to inform you that your <strong>customer account has been successfully created</strong> ðŸŽ‰</p>
    
                            <table cellpadding='6' cellspacing='0' style='width: 100%; margin-top: 15px; border: 1px solid #ddd; border-radius: 5px;'>
                                <tr style='background-color: #f2f2f2;'>
                                    <td><strong>Customer ID:</strong></td>
                                    <td>{$customer->customer_id}</td>
                                </tr>
                                <tr style='background-color: #f2f2f2;'>
                                    <td><strong>Trade Name:</strong></td>
                                    <td>{$customer->trade_name}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{$customer->email}</td>
                                </tr>
                                <tr style='background-color: #f2f2f2;'>
                                    <td><strong>Phone:</strong></td>
                                    <td>{$customer->phone}</td>
                                </tr>
                                <tr>
                                    <td><strong>Customer Type:</strong></td>
                                    <td>{$customer_type}</td>
                                </tr>
                                <tr style='background-color: #f2f2f2;'>
                                    <td><strong>Business Constitution Type:</strong></td>
                                    <td>{$business_constitutionType}</td>
                                </tr>
                                <tr>
                                    <td><strong>Business Type:</strong></td>
                                    <td>{$business_type}</td>
                                </tr>
                                <tr style='background-color: #f2f2f2;'>
                                    <td><strong>Company Address:</strong></td>
                                    <td>{$companyAddress}</td>
                                </tr>
                            </table>
    
                            <p style='margin-top: 15px;'>Your login credentials have been created. Our team will provide the login information at the earliest convenience.</p>
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
        }
        
        else if ($forward_type == 'customer_account_status_notify'){
            $subject = 'Your Customer Account Status Has Been Updated - Green Drive Connect';
            $body = "
                    <html>
                    <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #544e54;'>
                        <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 650px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); color: #544e54;'>
                            <tr>
                                <td style='padding: 20px; text-align: center; background-color: #4CAF50; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                                    <h2>Account Status Update</h2>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding: 20px; color: #544e54;'>
                                    <p style='font-size: 16px;'>Hello <strong>{$customer->name}</strong>,</p>
                                    
                                    <p style='font-size: 15px; line-height: 1.6;'>
                                        We would like to inform you that your <strong>customer account status</strong> has been updated.
                                    </p>
                    
                                    <table cellpadding='8' cellspacing='0' style='width: 100%; margin-top: 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;'>
                                        <tr style='background-color: #f9f9f9;'>
                                            <td style='width: 40%;'><strong>Customer ID:</strong></td>
                                            <td>{$customer->customer_id}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Account Status:</strong></td>
                                            <td>{$account_status}</td>
                                        </tr>
                                        <tr style='background-color: #f9f9f9;'>
                                            <td><strong>Email:</strong></td>
                                            <td>{$customer->email}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{$customer->phone}</td>
                                        </tr>
                                    </table>
                    
                                    <p style='margin-top: 18px; font-size: 15px; line-height: 1.6;'>
                                        🔹 Please note that your access to Green Drive Connect will be based on this status. 
                                        For any assistance, contact our support team.
                                    </p>
                    
                                    <p style='margin-top: 20px; font-size: 14px; line-height: 1.5; color: #555;'>{$footerContentText}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style='text-align: center; padding: 15px; font-size: 12px; color: #777; background-color: #f2f2f2; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;'>
                                    &copy; " . date('Y') . " GreenDriveConnect. All rights reserved.
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>
                    ";

        }
        
        
    
        CustomHandler::sendEmail($toEmails, $subject, $body, $ccEmails);
    }
    
    function CustomerSentWhatsAppMessage($customer, $forward_type, $account_status = null)
    {
        $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
        $footerContentText = $footerText ?? "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
    
        $message = "";
    
        if ($forward_type == 'customer_create_notify') {
    
            $customer_type = $customer->business_type == 1 ? 'Individual' : 'Company';
            $business_type = $customer->business_type == 1 ? 'Registered' : 'Unregistered';
            $business_constitutionType = $customer->constitution_type->name ?? 'N/A';
            $companyAddress = $customer->address ?? 'N/A';
    
            $message = "🎉 *Welcome to Green Drive Connect*\n\n" .
                "Hello *{$customer->name}*,\n" .
                "Your customer account has been *successfully created*.\n\n" .
                "*Customer ID:* {$customer->customer_id}\n" .
                "*Trade Name:* {$customer->trade_name}\n" .
                "*Email:* {$customer->email}\n" .
                "*Phone:* {$customer->phone}\n" .
                "*Customer Type:* {$customer_type}\n" .
                "*Business Constitution Type:* {$business_constitutionType}\n" .
                "*Business Type:* {$business_type}\n" .
                "*Company Address:* {$companyAddress}\n\n" .
                "🔑 Your login credentials have been created. Our team will provide the login information shortly.\n\n" .
                $footerContentText;
        }
    
        if ($forward_type == 'customer_account_status_notify') {
            $message = "🔔 *Account Status Update*\n\n" .
                "Hello *{$customer->name}*,\n" .
                "Your customer account status has been updated.\n\n" .
                "*Customer ID:* {$customer->customer_id}\n" .
                "*Account Status:* {$account_status}\n" .
                "*Email:* {$customer->email}\n" .
                "*Phone:* {$customer->phone}\n\n" .
                "🔹 Please note that your access to Green Drive Connect will be based on this status.\n\n" .
                $footerContentText;
        }
    
        // Send via custom WhatsApp handler
        CustomHandler::user_whatsapp_message($customer->phone, $message);
    
        // Optional: Send to admin as well
        $admin_message = "*Notification Alert*\nCustomer: {$customer->name}\nType: {$forward_type}\n";
        CustomHandler::admin_whatsapp_message($admin_message);
    }



    public function edit(Request $request,$id)
    {
        $decode_id = decrypt($id);
        $customer_data = CustomerMaster::where('id',$decode_id)->first();
        if(!$customer_data){
            return back()->with('error','Customer not found');
        }
        $constutition_types = BusinessConstitutionType::where('status',1)->get();
        $states = EVState::where('status',1)->get();
        $cities = City::where('status',1)->get();
                $types = EvTblAccountabilityType::where('status',1)->get();
        $customer_types = CustomerTypeMaster::where('status',1)->get();
        
        return view('mastermanagement::customer_master.edit',compact('customer_data','constutition_types','cities','states'  ,'types' , 'customer_types'));
    }
    
    public function update(Request $request,$id)
    {
// dd($id,$request->all());
        $update_customer_data = CustomerMaster::where('id',$id)->first();
        
        if(!$update_customer_data){
            return response()->json([
                'success' => false,
                'message' => 'Customer not found.'
            ]);
        }
        $oldValues = $update_customer_data->getAttributes();
    $rules = [
        'customer_type' => 'required|in:1,2',
        'business_type' => 'required|in:1,2',
        // 'email' => 'required|email|unique:ev_tbl_customer_master,email,' . $id,
        'email' => 'required|email',
        'pincode' => 'required',
        'trade_name' => 'required',
        'contact_no' => [
            'required',
            'string',
            'regex:/^(\+91\s?)?[6-9]\d{9}$/',
            'unique:ev_tbl_customer_master,phone,' . $id,
        ],

        'gst_no' => [
            'required',
            'string',
            'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            // 'unique:ev_tbl_customer_master,gst_no,' . $id,
        ],
        'city' => 'required',
        'state' => 'required',
        'address' => 'required|string',
        'accountability_type' => 'required',
        'client_type' => 'required',
    
        // 'adhaar_front_img' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
        // 'adhaar_back_img' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
        // 'pan_img' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
        'gst_img' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
        'other_business_proof' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
        'company_logo_img' => 'nullable|mimes:png,jpg,jpeg',
        'profile_img' => 'nullable|mimes:png,jpg,jpeg',
    ];


        if ($request->customer_type == 2) {
            $rules['business_constutition_type'] = 'required';
        }else{
            $rules['pan_no'] = [
            'required',
            'string',
            'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'unique:ev_tbl_customer_master,pan_no,' . $id,
            ];
            $rules['pan_img'] = 'required|mimes:png,jpg,jpeg,pdf';
            $rules['adhaar_front_img'] = 'required|mimes:png,jpg,jpeg,pdf';
            $rules['adhaar_back_img'] = 'required|mimes:png,jpg,jpeg,pdf';
        }
    
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }
    
        DB::beginTransaction();
    
        try {
            
            
            // Uploads
            if ($request->hasFile('adhaar_front_img')) {
                $old_file = $update_customer_data->adhaar_front_img;

                $update_customer_data->adhaar_front_img = CustomHandler::uploadFileImage(
                    $request->file('adhaar_front_img'),
                    'EV/vehicle_transfer/adhaar_front_images'
                );
                
                if (!empty($old_file)) {
                    CustomHandler::GlobalFileDelete($old_file, 'EV/vehicle_transfer/adhaar_front_images/');
                }

            }
    
            if ($request->hasFile('adhaar_back_img')) {
                $old_file = $update_customer_data->adhaar_back_img;
                $update_customer_data->adhaar_back_img = CustomHandler::uploadFileImage(
                    $request->file('adhaar_back_img'),
                    'EV/vehicle_transfer/adhaar_back_images'
                );
                if (!empty($old_file)) {
                    CustomHandler::GlobalFileDelete($old_file, 'EV/vehicle_transfer/adhaar_back_images/');
                }
            }
    
            if ($request->hasFile('pan_img')) {
                 $old_file = $update_customer_data->pan_img;
                $update_customer_data->pan_img = CustomHandler::uploadFileImage(
                    $request->file('pan_img'),
                    'EV/vehicle_transfer/pan_card_images'
                );
                if (!empty($old_file)) {
                    CustomHandler::GlobalFileDelete($old_file, 'EV/vehicle_transfer/pan_card_images/');
                }
            }
    
            if ($request->hasFile('gst_img')) {
                $old_file = $update_customer_data->gst_img;
                $update_customer_data->gst_img = CustomHandler::uploadFileImage(
                    $request->file('gst_img'),
                    'EV/vehicle_transfer/gst_images'
                );
                if (!empty($old_file)) {
                    CustomHandler::GlobalFileDelete($old_file, 'EV/vehicle_transfer/gst_images/');
                }
            }
    
            if ($request->hasFile('other_business_proof')) {
                $old_file = $update_customer_data->business_proof_img;
                $update_customer_data->business_proof_img = CustomHandler::uploadFileImage(
                    $request->file('other_business_proof'),
                    'EV/vehicle_transfer/other_business_proof_images'
                );
                 if (!empty($old_file)) {
                    CustomHandler::GlobalFileDelete($old_file, 'EV/vehicle_transfer/other_business_proof_images/');
                }
            }
            
            
            if ($request->hasFile('company_logo_img')) {
               
                $old_file = $update_customer_data->company_logo;
                $update_customer_data->company_logo = CustomHandler::uploadFileImage(
                    $request->file('company_logo_img'),
                    'EV/vehicle_transfer/company_logos'
                );
                 if (!empty($old_file)) {
                    CustomHandler::GlobalFileDelete($old_file, 'EV/vehicle_transfer/company_logos/');
                }
            }
            
                       if ($request->hasFile('profile_img')) {
                $old_file = $update_customer_data->profile_img;
                $update_customer_data->profile_img = CustomHandler::uploadFileImage(
                    $request->file('profile_img'),
                    'EV/vehicle_transfer/profile_images'
                );
                 if (!empty($old_file)) {
                    CustomHandler::GlobalFileDelete($old_file, 'EV/vehicle_transfer/profile_images/');
                }
            }
            
              if ($request->customer_type == 2) {
                  
                  if(!empty($update_customer_data->pan_img)){
                 
                    CustomHandler::GlobalFileDelete($update_customer_data->pan_img, 'EV/vehicle_transfer/pan_card_images/');
                       $update_customer_data->pan_img =null;
                
                  }
                  if(!empty($update_customer_data->adhaar_front_img)){
                  
                    CustomHandler::GlobalFileDelete($update_customer_data->adhaar_front_img, 'EV/vehicle_transfer/adhaar_front_images/');
                    $update_customer_data->adhaar_front_img = null;
                    }
                
                    if(!empty($update_customer_data->adhaar_back_img)){
                  
                    CustomHandler::GlobalFileDelete($update_customer_data->adhaar_back_img, 'EV/vehicle_transfer/adhaar_back_images/');
                    $update_customer_data->adhaar_back_img = null;
                    }
                  
                  
              }
    
            // Basic info
            $update_customer_data->customer_type = $request->customer_type;
            $update_customer_data->business_type = $request->business_type;
            $update_customer_data->business_const_type = $request->customer_type == 2 ? $request->business_constutition_type : null;
            $update_customer_data->name = $request->name; 
            $update_customer_data->email = $request->email;
            $update_customer_data->phone = $request->contact_no;
            $update_customer_data->gst_no = $request->gst_no;
            $update_customer_data->pan_no = $request->pan_no;
            $update_customer_data->address = $request->address;
            $update_customer_data->city_id = $request->city;
            $update_customer_data->state_id = $request->state;
            $update_customer_data->pincode = $request->pincode;
            $update_customer_data->trade_name = $request->trade_name;
                        $update_customer_data->accountability_type_id = $request->accountability_type;
            $update_customer_data->start_date = $request->start_date;
            $update_customer_data->end_date = $request->end_date;
            $update_customer_data->client_type = $request->client_type ?? '';
            
            $update_customer_data->save();
    
            // POC details
            $poc_detail_names  = $request->poc_name ?? [];
            $poc_detail_emails = $request->poc_email ?? [];
            $poc_detail_phones = $request->poc_phone ?? [];
            $poc_detail_ids    = $request->poc_id ?? [];
            
            if ($request->add_poc_details == true && !empty($poc_detail_names)) {
                $exist_ids = [];
            
                foreach ($poc_detail_names as $index => $name) {
                    $poc_id = $poc_detail_ids[$index] ?? null;
            
                    $poc_detail = $poc_id
                        ? CustomerPOCDetail::firstOrNew(['id' => $poc_id])
                        : new CustomerPOCDetail();
            
                    $poc_detail->customer_id = $id;
                    $poc_detail->name        = $name;
                    $poc_detail->email       = $poc_detail_emails[$index] ?? null;
                    $poc_detail->phone       = $poc_detail_phones[$index] ?? null;
                    $poc_detail->save();
            
                    $exist_ids[] = $poc_detail->id;
                }
            
                // Delete removed POC entries
                CustomerPOCDetail::where('customer_id', $id)
                    ->whereNotIn('id', $exist_ids)
                    ->delete();
            }

    
            if ($request->add_hubs_details == true && !empty($request->hub_name)) {
                $hub_names = array_filter($request->hub_name); // remove null/empty names
                $existing_hubs = CustomerOperationalHub::where('customer_id', $id)->pluck('hub_name')->toArray();
            
                // Save or update new hubs
                foreach ($hub_names as $hub) {
                    $hub_detail = CustomerOperationalHub::firstOrNew([
                        'customer_id' => $id,
                        'hub_name' => $hub,
                    ]);
                    $hub_detail->save();
                }
            
                // Delete hubs not in the current request
                $hubs_to_delete = array_diff($existing_hubs, $hub_names);
                if (!empty($hubs_to_delete)) {
                    CustomerOperationalHub::where('customer_id', $id)
                        ->whereIn('hub_name', $hubs_to_delete)
                        ->delete();
                }
            }

    
            DB::commit();
            
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
            $newValues = $update_customer_data->getAttributes();
            // Compare old vs new fields
            $changes = [];
            foreach ($newValues as $key => $newVal) {
                $oldVal = $oldValues[$key] ?? null;
                if ($oldVal != $newVal) {
                    $changes[] = "{$key}: '{$oldVal}' → '{$newVal}'";
                }
            }
            $changesText = empty($changes)
                ? 'No visible changes detected.'
                : implode('; ', $changes);
            audit_log_after_commit([
                'module_id'         => 1,
                'short_description' => 'Customer Updated',
                'long_description'  => "Customer '{$update_customer_data->name}' (ID: {$update_customer_data->id}) updated. Changes: {$changesText}",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'customer_master.update',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
    
    
    
        
     public function login_credential(Request $request,$id)
    {
        $decode_id = decrypt($id);
        $customerData = CustomerMaster::where('id',$decode_id)->first(); //updated by Gowtham.s
        $cities = City::where('id',$customerData->city_id)->where('status',1)->get();
        
        return view('mastermanagement::customer_master.login_credential',compact('decode_id' ,'cities','customerData'));
        
    }
    
     public function create_login(Request $request) //updated by Gowtham.s
    {
            // Validation
            $data = $request->validate(
                [
                    'customer_id'   => ['required'],
                    'status'        => ['required', Rule::in([1, 0])],
                    'login_type'    => ['required', Rule::in(['master', 'zone'])],
                    'email'         => ['required', 'string', 'email', 'max:255', 'unique:ev_tbl_customer_logins,email'],
                    'password'      => ['required', 'string', new Password(), 'confirmed'],
                    'city_id'       => ['required', 'exists:ev_tbl_city,id'],
                    'zone_id'       => ['required_if:login_type,zone', 'nullable', 'exists:zones,id'],
                ],
                [
                    'zone_id.required_if' => 'Zone is required.',
                ]
            );
            
            $zone_id = null;
            if($data['login_type'] == 'zone' && $data['zone_id'] != '')
            {
                $zone_id = $data['zone_id'];
            }
            
            $CustomerLogin = CustomerLogin::create([
                'customer_id' => $data['customer_id'],
                'email'       => $data['email'],
                'password'    => Hash::make($data['password']),
                'type'        => $data['login_type'],
                'city_id'     =>$data['city_id'] ?? null,
                'zone_id'     =>$zone_id ??  null,
                'status'      => $data['status'],
                'created_by'=>auth()->id()
            ]);
            
            $customerName = $CustomerLogin->customer_relation->name ?? 'N/A';
            $customerId = $CustomerLogin->customer_relation->id ?? 'N/A';
            $tradeName = $CustomerLogin->customer_relation->trade_name ?? 'N/A';
            $CustomerMobile = $CustomerLogin->customer_relation->phone ?? '';
            $CustomerComEmail = $CustomerLogin->customer_relation->email ?? '';
            
            $zoneName = 'All Zones';
            if($data['login_type'] == 'zone'){
                $zoneName = $CustomerLogin->zone->name;
            }
            
            
            $customerData = [
                'login_details'=>[
                    'login_type' => ucfirst($data['login_type']),
                    'login_email'=>$data['email'],
                    'login_password' => $data['password'],
                    'city_name' => $CustomerLogin->city->city_name ?? 'N/A',
                    'zone_name' => $zoneName ?? 'N/A',
                ],
                'customer_details'=>[
                    'name'=>$customerName,
                    'customer_id'=>$customerId,
                    'trade_name'=>$tradeName,
                    'phone'=>$CustomerMobile,
                    'customer_company_email'=>$CustomerComEmail
                ]
            ];

            $this->CustomerCredencials_SentWhatsAppMessage($customerData,'customer_login_create_notify');
            try{
                 $this->CustomerCredencials_SentEmailNotify($customerData,'customer_login_create_email_notify');
            }
            catch(\Exception $e){
                    \Log::info("Mail sent failed", [
                    'error'   => $e->getMessage(),
                    'page' => 'Customer login create'
        
                ]);
            }
            
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

            audit_log_after_commit([
                'module_id'         => 1, // customer-related module id
                'short_description' => 'Customer Login Created',
                'long_description'  => "Login ({$CustomerLogin->type}) created for customer '{$customerName}' (Customer ID: {$customerId}). Login email: {$CustomerLogin->email}.",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'customer_login.create',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
        
            return response()->json([
                'success' => true,
                'message' => 'Customer login created successfully.',
            ]);
    }
    
    
    function CustomerCredencials_SentEmailNotify($customer, $forward_type, $account_status = null)
    {
     
        $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
        $footerContentText = $footerText ?? 
            "For any assistance, please reach out to Admin Support.<br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
    
        $login_url = url('/b2b/login');
    
        $customerSubject = '';
        $customerBody = '';
        $adminSubject = '';
        $adminBody = '';
    
        if($forward_type == 'customer_login_create_email_notify'){
            $customerSubject = "Welcome to Green Drive Connect – Your Account is Ready";
                $customerBody = "
                    <html>
                    <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #544e54;'>
                        <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #544e54;'>
                            <tr>
                                <td style='padding: 20px; text-align: center; background-color: #4CAF50; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                                    <h2>Account Created</h2>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding: 20px;'>
                                    <p>Hello <strong>{$customer['customer_details']['name']}</strong>,</p>
                                    <p>Your customer login has been successfully created. Please contact Admin to receive your login credentials securely.</p>
                    
                                    <table cellpadding='5' cellspacing='0' style='width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px; color: #544e54;'>
                                        <tr style='background-color: #f2f2f2;'>
                                            <td><strong>Customer ID:</strong></td>
                                            <td>{$customer['customer_details']['customer_id']}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Trade Name:</strong></td>
                                            <td>{$customer['customer_details']['trade_name']}</td>
                                        </tr>
                                        <tr style='background-color: #f2f2f2;'>
                                            <td><strong>City:</strong></td>
                                            <td>{$customer['login_details']['city_name']}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Zone:</strong></td>
                                            <td>{$customer['login_details']['zone_name']}</td>
                                        </tr>
                                    </table>
                    
                                    <p style='margin-top: 15px;'><strong>Portal Access:</strong> <a href='{$login_url}'>{$login_url}</a></p>
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
    
                $adminSubject = "New Customer Login Created – Green Drive Connect";
                $adminBody = "
                        <html>
                        <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #544e54;'>
                            <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #544e54;'>
                                <tr>
                                    <td style='padding: 20px; text-align: center; background-color: #f44336; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                                        <h2>New Customer Login Created</h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='padding: 20px;'>
                                        <p>A new customer login has been created. Details are as follows:</p>
                        
                                        <table cellpadding='5' cellspacing='0' style='width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px; color: #544e54;'>
                                            <tr style='background-color: #f2f2f2;'>
                                                <td><strong>Name:</strong></td>
                                                <td>{$customer['customer_details']['name']}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Customer ID:</strong></td>
                                                <td>{$customer['customer_details']['customer_id']}</td>
                                            </tr>
                                            <tr style='background-color: #f2f2f2;'>
                                                <td><strong>Trade Name:</strong></td>
                                                <td>{$customer['customer_details']['trade_name']}</td>
                                            </tr>
                                        </table>
                        
                                        <table cellpadding='5' cellspacing='0' style='width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px; color: #544e54;'>
                                            <tr style='background-color: #f2f2f2;'>
                                                <td><strong>Login Type:</strong></td>
                                                <td>{$customer['login_details']['login_type']}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td>{$customer['login_details']['login_email']}</td>
                                            </tr>
                                            <tr style='background-color: #f2f2f2;'>
                                                <td><strong>Password:</strong></td>
                                                <td>{$customer['login_details']['login_password']}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>City:</strong></td>
                                                <td>{$customer['login_details']['city_name']}</td>
                                            </tr>
                                            <tr style='background-color: #f2f2f2;'>
                                                <td><strong>Zone:</strong></td>
                                                <td>{$customer['login_details']['zone_name']}</td>
                                            </tr>
                                        </table>
                        
                                        <p style='margin-top: 15px;'><strong>Portal Access:</strong> <a href='{$login_url}'>{$login_url}</a></p>
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
        }
        else if($forward_type == 'customer_login_account_password_EmailNotify'){
            $customerSubject = "Your Account Password Has Been Updated - Green Drive Connect";
                    
                $customerBody = "
                    <html>
                    <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #544e54;'>
                        <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #544e54;'>
                            <tr>
                                <td style='padding: 20px; text-align: center; background-color: #4CAF50; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                                    <h2>Login Credentials Updated</h2>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding: 20px;'>
                                    <p>Hello <strong>{$customer['customer_details']['name']}</strong>,</p>
                                    <p>Your login credentials have been updated. For security reasons, your password will not be displayed here.</p>
                    
                                    <table cellpadding='5' cellspacing='0' style='width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px; color: #544e54;'>
                                        <tr style='background-color: #f2f2f2;'>
                                            <td><strong>Customer ID:</strong></td>
                                            <td>{$customer['customer_details']['customer_id']}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Trade Name:</strong></td>
                                            <td>{$customer['customer_details']['trade_name']}</td>
                                        </tr>
                                    </table>
                    
                                    <table cellpadding='5' cellspacing='0' style='width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px; color: #544e54;'>
                                        <tr style='background-color: #f2f2f2;'>
                                            <td><strong>Login Type:</strong></td>
                                            <td>{$customer['login_details']['login_type']}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{$customer['login_details']['login_email']}</td>
                                        </tr>
                                        <tr style='background-color: #f2f2f2;'>
                                            <td><strong>Password:</strong></td>
                                            <td>Updated Successfully</td>
                                        </tr>
                                        <tr>
                                            <td><strong>City:</strong></td>
                                            <td>{$customer['login_details']['city_name']}</td>
                                        </tr>
                                        <tr style='background-color: #f2f2f2;'>
                                            <td><strong>Zone:</strong></td>
                                            <td>{$customer['login_details']['zone_name']}</td>
                                        </tr>
                                    </table>
                    
                                    <p style='margin-top: 15px;'><strong>Portal Access:</strong> <a href='{$login_url}'>{$login_url}</a></p>
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
    
                $adminSubject = "Admin Notification – Customer Password Updated";
                 $adminBody = "
                <html>
                <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #544e54;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #544e54;'>
                        <tr>
                            <td style='padding: 20px; text-align: center; background-color: #f44336; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                                <h2>Customer Login Updated</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding: 20px;'>
                                <p>The customer's login credentials have been updated:</p>
                
                                <table cellpadding='5' cellspacing='0' style='width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px; color: #544e54;'>
                                    <tr style='background-color: #f2f2f2;'>
                                        <td><strong>Name:</strong></td>
                                        <td>{$customer['customer_details']['name']}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer ID:</strong></td>
                                        <td>{$customer['customer_details']['customer_id']}</td>
                                    </tr>
                                    <tr style='background-color: #f2f2f2;'>
                                        <td><strong>Trade Name:</strong></td>
                                        <td>{$customer['customer_details']['trade_name']}</td>
                                    </tr>
                                </table>
                
                                <table cellpadding='5' cellspacing='0' style='width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px; color: #544e54;'>
                                    <tr style='background-color: #f2f2f2;'>
                                        <td><strong>Login Type:</strong></td>
                                        <td>{$customer['login_details']['login_type']}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{$customer['login_details']['login_email']}</td>
                                    </tr>
                                    <tr style='background-color: #f2f2f2;'>
                                        <td><strong>Password:</strong></td>
                                        <td>{$customer['login_details']['login_password']}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>City:</strong></td>
                                        <td>{$customer['login_details']['city_name']}</td>
                                    </tr>
                                    <tr style='background-color: #f2f2f2;'>
                                        <td><strong>Zone:</strong></td>
                                        <td>{$customer['login_details']['zone_name']}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Account Status:</strong></td>
                                        <td>{$account_status}</td>
                                    </tr>
                                </table>
                
                                <p style='margin-top: 15px;'><strong>Portal Access:</strong> <a href='{$login_url}'>{$login_url}</a></p>
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
        }else if($forward_type == 'customer_login_account_status_emailNotify'){
            $customerSubject = "Your Account Details Have Been Updated - Green Drive Connect";
                $customerBody = "
                    <html>
                    <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #544e54;'>
                        <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #544e54;'>
                            <tr>
                                <td style='padding: 20px; text-align: center; background-color: #4CAF50; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                                    <h2>Account Details Updated</h2>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding: 20px;'>
                                    <p>Hello <strong>{$customer['customer_details']['name']}</strong>,</p>
                                    <p>Your account details have been updated. Please review your updated information below:</p>
                    
                                    <table cellpadding='5' cellspacing='0' style='width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px;'>
                                        <tr style='background-color: #f2f2f2;'>
                                            <td><strong>Customer ID:</strong></td>
                                            <td>{$customer['customer_details']['customer_id']}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Trade Name:</strong></td>
                                            <td>{$customer['customer_details']['trade_name']}</td>
                                        </tr>
                                    </table>
                    
                                    <table cellpadding='5' cellspacing='0' style='width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px;'>
                                        <tr style='background-color: #f2f2f2;'>
                                            <td><strong>Login Type:</strong></td>
                                            <td>{$customer['login_details']['login_type']}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{$customer['login_details']['login_email']}</td>
                                        </tr>
                                        <tr style='background-color: #f2f2f2;'>
                                            <td><strong>City:</strong></td>
                                            <td>{$customer['login_details']['city_name']}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Zone:</strong></td>
                                            <td>{$customer['login_details']['zone_name']}</td>
                                        </tr>
                                        <tr>
                                        <td><strong>Account Status:</strong></td>
                                            <td>{$account_status}</td>
                                        </tr>
                                    </table>
                    
                                    <p style='margin-top: 15px;'><strong>Portal Access:</strong> <a href='{$login_url}'>{$login_url}</a></p>
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
    
                $adminSubject = "Admin Notification – Customer Account Updated";
                $adminBody = "
                <html>
                <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #544e54;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #544e54;'>
                        <tr>
                            <td style='padding: 20px; text-align: center; background-color: #f44336; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                                <h2>Customer Account Updated</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding: 20px;'>
                                <p>The following customer's account details have been updated:</p>
                
                                <table cellpadding='5' cellspacing='0' style='width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px;'>
                                    <tr style='background-color: #f2f2f2;'>
                                        <td><strong>Name:</strong></td>
                                        <td>{$customer['customer_details']['name']}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer ID:</strong></td>
                                        <td>{$customer['customer_details']['customer_id']}</td>
                                    </tr>
                                    <tr style='background-color: #f2f2f2;'>
                                        <td><strong>Trade Name:</strong></td>
                                        <td>{$customer['customer_details']['trade_name']}</td>
                                    </tr>
                                </table>
                
                                <table cellpadding='5' cellspacing='0' style='width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px;'>
                                    <tr style='background-color: #f2f2f2;'>
                                        <td><strong>Login Type:</strong></td>
                                        <td>{$customer['login_details']['login_type']}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{$customer['login_details']['login_email']}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>City:</strong></td>
                                        <td>{$customer['login_details']['city_name']}</td>
                                    </tr>
                                    <tr style='background-color: #f2f2f2;'>
                                        <td><strong>Zone:</strong></td>
                                        <td>{$customer['login_details']['zone_name']}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Account Status:</strong></td>
                                        <td>{$account_status}</td>
                                    </tr>
                                </table>
                
                                <p style='margin-top: 15px;'><strong>Portal Access:</strong> <a href='{$login_url}'>{$login_url}</a></p>
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
        }
        
           // Send to Admins (password included)
        $ccAdmins = DB::table('roles')
            ->leftJoin('users', 'roles.id', '=', 'users.role')
            ->whereIn('users.role', [1, 13])
            ->where('users.status','Active')
            ->pluck('users.email')
            ->filter()
            ->toArray();
        // Send to Customer

        $toCustomers = [$customer['customer_details']['customer_company_email'],$customer['login_details']['login_email']];
        
        // dd($ccAdmins,$toCustomers,$customerSubject,$customerBody,$adminSubject,$adminBody,$customer['login_details']['login_email']);
        
        CustomHandler::sendEmail($toCustomers, $customerSubject, $customerBody);
    
     
        CustomHandler::sendEmail($ccAdmins, $adminSubject, $adminBody);
    }

      
      
    function CustomerCredencials_SentWhatsAppMessage($customer, $forward_type, $account_status = null)
    {
        $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
        $footerContentText = $footerText ??
            "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
    
        $message = "";
        $admin_message = "";
        
        $login_url = url('/b2b/login');
        
        if ($forward_type == 'customer_login_create_notify') {
    
            $message = "*Welcome to Green Drive Connect*\n\n" .
                "Hello *{$customer['customer_details']['name']}*,\n\n" .
                "Your customer login has been successfully created.\n\n" .
                "*Customer Information*\n" .
                "• Customer ID: {$customer['customer_details']['customer_id']}\n" .
                "• Trade Name: {$customer['customer_details']['trade_name']}\n\n" .
                "• City: {$customer['login_details']['city_name']}\n" .
                "• Zone: {$customer['login_details']['zone_name']}\n\n" .
                "Your login credentials have been created.\n" .
                "Please contact the Admin to receive your username and password securely.\n\n" .
                "*Portal Access:* {$login_url}\n\n" .
                $footerContentText;
    
            // Message for Admin
            $admin_message = "*New Customer Login Created*\n\n" .
                "A new customer login has been successfully created. Please review the details below:\n\n" .
                "*Customer Information*\n" .
                "• Name: {$customer['customer_details']['name']}\n" .
                "• Customer ID: {$customer['customer_details']['customer_id']}\n" .
                "• Trade Name: {$customer['customer_details']['trade_name']}\n\n" .
                "*Login Credentials*\n" .
                "• Login Type: {$customer['login_details']['login_type']}\n" .
                "• Email: {$customer['login_details']['login_email']}\n" .
                "• City: {$customer['login_details']['city_name']}\n" .
                "• Zone: {$customer['login_details']['zone_name']}\n\n" .
                "*B2B Customer Portal:* {$login_url}\n\n" .
                "Kindly share the login details with the customer securely.";
        }
        
       if ($forward_type == 'customer_login_account_status_notify') {
            // Message for Customer
            $message = "🔔 *Green Drive Connect – Account Status Update*\n\n" .
                "Hello *{$customer['customer_details']['name']}*,\n\n" .
                "We would like to inform you that your customer account status has been updated.\n\n" .
                "*Customer ID:* {$customer['customer_details']['customer_id']}\n" .
                "*Account Status:* *{$account_status}*\n" .
                "*Email:* {$customer['login_details']['login_email']}\n" .
                "*Phone:* {$customer['customer_details']['phone']}\n\n" .
                "Please note that your access to Green Drive Connect will be based on this status.\n\n" .
                $footerContentText;
        
            // Message for Admin
            $admin_message = "📌 *Admin Notification – Customer Account Status Updated*\n\n" .
                "A customer's account status has been changed. Please review the details below:\n\n" .
                "*Customer Information*\n" .
                "• Name: {$customer['customer_details']['name']}\n" .
                "• Customer ID: {$customer['customer_details']['customer_id']}\n" .
                "• Trade Name: {$customer['customer_details']['trade_name']}\n\n" .
                "*Login Details*\n" .
                "• Login Type: {$customer['login_details']['login_type']}\n" .
                "• Email: {$customer['login_details']['login_email']}\n" .
                "• City: {$customer['login_details']['city_name']}\n" .
                "• Zone: {$customer['login_details']['zone_name']}\n\n" .
                "*Updated Account Status:* *{$account_status}*\n\n" .
                "*Action Required:* Verify this status change if necessary and inform the customer accordingly.";
        }
        
        if ($forward_type == 'customer_login_account_password_notify') {
        
            $login_url = url('/b2b/login');
            $message = "🔑 *Green Drive Connect – Login Credentials Updated*\n\n" .
                "Hello *{$customer['customer_details']['name']}*,\n\n" .
                "Your customer login details have been updated.\n\n" .
                "*Customer Information*\n" .
                "• Customer ID: {$customer['customer_details']['customer_id']}\n" .
                "• Trade Name: {$customer['customer_details']['trade_name']}\n\n" .
                "*Login Details*\n" .
                "• Login Type: {$customer['login_details']['login_type']}\n" .
                "• Email: {$customer['login_details']['login_email']}\n" .
                "• Password: *Updated Successfully*\n" . // never send the real password directly
                "• City: {$customer['login_details']['city_name']}\n" .
                "• Zone: {$customer['login_details']['zone_name']}\n\n" .
                "You can access the portal here: {$login_url}\n\n" .
                "Please keep your login credentials secure.\n\n" .
                $footerContentText;
            $admin_message = "📌 *Admin Notification – Customer Login Password Updated*\n\n" .
                "The login credentials of a customer have been updated. Details are as follows:\n\n" .
                "*Customer Information*\n" .
                "• Name: {$customer['customer_details']['name']}\n" .
                "• Customer ID: {$customer['customer_details']['customer_id']}\n" .
                "• Trade Name: {$customer['customer_details']['trade_name']}\n\n" .
                "*Login Details*\n" .
                "• Login Type: {$customer['login_details']['login_type']}\n" .
                "• Email: {$customer['login_details']['login_email']}\n" .
                "• Password: {$customer['login_details']['login_password']}\n" .
                "• City: {$customer['login_details']['city_name']}\n" .
                "• Zone: {$customer['login_details']['zone_name']}\n\n" .
                "*Account Status:* {$account_status}\n\n" .
                "*Action Required:* If necessary, communicate the updated credentials securely to the customer.";
        
        }
        
        if ($forward_type == 'customer_login_account_update_notify') {

            $message = "🔔 *Green Drive Connect – Account Details Updated*\n\n" .
                "Hello *{$customer['customer_details']['name']}*,\n\n" .
                "Your account details have been updated successfully. Here are your updated details:\n\n" .
                "*Customer Information*\n" .
                "• Customer ID: {$customer['customer_details']['customer_id']}\n" .
                "• Trade Name: {$customer['customer_details']['trade_name']}\n\n" .
                "*Login Details*\n" .
                "• Login Type: {$customer['login_details']['login_type']}\n" .
                "• Email: {$customer['login_details']['login_email']}\n" .
                "• City: {$customer['login_details']['city_name']}\n" .
                "• Zone: {$customer['login_details']['zone_name']}\n\n" .
                "Portal Access: {$login_url}\n\n" .
                "Please review your updated details and keep your account information secure.\n\n" .
                $footerContentText;

            $admin_message = "📌 *Admin Notification – Customer Account Updated*\n\n" .
                "The following customer's account details have been updated:\n\n" .
                "*Customer Information*\n" .
                "• Name: {$customer['customer_details']['name']}\n" .
                "• Customer ID: {$customer['customer_details']['customer_id']}\n" .
                "• Trade Name: {$customer['customer_details']['trade_name']}\n\n" .
                "*Updated Login Details*\n" .
                "• Login Type: {$customer['login_details']['login_type']}\n" .
                "• Email: {$customer['login_details']['login_email']}\n" .
                "• Password: {$customer['login_details']['login_password']}\n" .
                "• City: {$customer['login_details']['city_name']}\n" .
                "• Zone: {$customer['login_details']['zone_name']}\n\n" .
                "*Account Status:* {$account_status}\n\n" .
                "*Action Required:* Please verify the updates if necessary and ensure the customer is informed securely.";
        }



        if (!empty($customer['customer_details']['phone'])) {
            CustomHandler::user_whatsapp_message($customer['customer_details']['phone'], $message);
        }

        if (!empty($admin_message)) {
            CustomHandler::admin_whatsapp_message($admin_message);
        }
    }

    public function login_update(Request $request)
    {
           
            $request->merge([
                'login_type' => $request->logintype,
                'email'      => $request->email_id,
                'status'     => $request->status_value,
                'city_id'    => $request->edit_city_id,
                'zone_id'    => $request->edit_zone_id,
            ]);
        
            $rules = [
                'customer_id' => ['required'],
                'status'      => ['required', Rule::in([1, 0])],
                // 'login_type'  => ['required', Rule::in(['master', 'zone'])],
                'email'       => ['required', 'string', 'email', 'max:255', 'unique:ev_tbl_customer_logins,email,'.$request->id],
                'city_id'     =>['required', 'exists:ev_tbl_city,id'],
                'zone_id'     => ['required_if:login_type,zone', 'nullable', 'exists:zones,id'],
            ];

            if ($request->filled('edit_password')) {
                $request->merge(['password' => $request->edit_password]);
                $rules['password'] = ['string', new Password(), 'confirmed'];
                $request->merge(['password_confirmation' => $request->passwordconfirmation]);
            }
        
            $messages = [
                'zone_id.required_if' => 'Zone is required when login type is zone.',
                'password.confirmed'  => 'Password and confirmation do not match.',
            ];

            $data = $request->validate($rules, $messages);

            $login = CustomerLogin::findOrFail($request->id);
            
            $originalData = $login->only(['email', 'city_id', 'zone_id', 'status']);

 
            $updateData = [
                'email'   => $data['email'],
                // 'type'    => $data['login_type'],
                'city_id' => $data['city_id'],
                'zone_id' => $data['zone_id'] ?? null,
                'status'  => $data['status'],
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }
        
            $login->update($updateData);
            
            if (!empty($data['password'])) {
                $updateData['password'] = $data['password'];
            }
            
               $changes = [];
                foreach ($updateData as $key => $value) {
                    if ($key == 'password' && !empty($data['password'])) {
                        $changes['password'] = 'Updated';
                    } elseif (isset($originalData[$key]) && $originalData[$key] != $value) {
                        $changes[$key] = $value;
                    }
                }
                
                     // ✅ Human-readable column name formatting
            $formattedChanges = [];
            foreach ($changes as $key => $value) {
                // Replace underscores with spaces
                $label = str_replace('_', ' ', $key);
                // Remove trailing "_id"
                if (str_ends_with($label, ' id')) {
                    $label = str_replace(' id', '', $label);
                }
                // Capitalize first letter
                $label = ucfirst($label);
        
                // Format change text
                if ($key === 'password') {
                    $formattedChanges[] = "{$label}: Updated";
                } else {
                    $oldVal = $originalData[$key] ?? 'N/A';
                    $formattedChanges[] = "{$label}: {$oldVal} → {$value}";
                }
            }
        
            $changesText = empty($formattedChanges)
                ? 'No visible changes detected.'
                : implode('; ', $formattedChanges);
                
                // Prepare customer info
                $customerName   = $login->customer_relation->name ?? 'N/A';
                $customerId     = $login->customer_relation->id ?? 'N/A';
                $tradeName      = $login->customer_relation->trade_name ?? 'N/A';
                $customerPhone  = $login->customer_relation->phone ?? '';
                $zoneName       = ($login->type == 'zone') ? ($login->zone->name ?? 'N/A') : 'All Zones';
                $CustomerComEmail = $login->customer_relation->email ?? '';
                $accountStatus  = $login->status == 1 ? 'Active' : 'Inactive';
            
                $customerData = [
                    'login_details' => [
                        'login_type'  => ucfirst($login->type),
                        'login_email' => $login->email,
                        'login_password' => !empty($data['password']) ? $data['password'] : null,
                        'city_name'   => $login->city->city_name ?? 'N/A',
                        'zone_name'   => $zoneName,
                    ],
                    'customer_details' => [
                        'name'        => $customerName,
                        'customer_id' => $customerId,
                        'trade_name'  => $tradeName,
                        'phone'       => $customerPhone,
                        'customer_company_email'=>$CustomerComEmail
                    ]
                ];
            
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

            // human friendly changes text
            $changesText = empty($changes) ? 'No visible changes detected.' : implode('; ', array_map(function($k,$v){ return "{$k}: {$v}"; }, array_keys($changes), $changes));

            audit_log_after_commit([
                'module_id'         => 1,
                'short_description' => 'Customer Login Updated',
                'long_description'  => "Login for customer '{$customerName}' (Customer ID: {$customerId}) updated. Changes: {$changesText}",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'customer_login.update',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
            
            if (!empty($data['password']) && $login->status == 1) {
                $this->CustomerCredencials_SentWhatsAppMessage($customerData,'customer_login_account_password_notify', $accountStatus);
                $this->CustomerCredencials_SentEmailNotify($customerData,'customer_login_account_password_EmailNotify', $accountStatus);
            }
            else if ($login->status == 1){
                $this->CustomerCredencials_SentWhatsAppMessage($customerData,'customer_login_account_update_notify', $accountStatus);
                $this->CustomerCredencials_SentEmailNotify($customerData,'customer_login_account_status_emailNotify', $accountStatus);
            }
        
            // Return response
            return response()->json([
                'success' => true,
                'message' => 'Login details updated successfully!',
                'data'    => $login
            ]);
        }


    
    public function login_status_update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:ev_tbl_customer_logins,id',
            'status' => 'required|in:0,1'
        ]);
    
        try {
            $login = CustomerLogin::findOrFail($request->id);
            $oldStatus = $login->status== 1 ? 'Active' : 'Inactive';
            $newStatus = $request->status== 1 ? 'Active' : 'Inactive';
            $login->status = $request->status;
            $login->save();
            
             $account_status = $request->status == 1 ? 'Active' : 'Inactive';
            
            $customerName = $login->customer_relation->name ?? 'N/A';
            $customerId = $login->customer_relation->id ?? 'N/A';
            $tradeName = $login->customer_relation->trade_name ?? 'N/A';
            $CustomerMobile = $login->customer_relation->phone ?? '';
            $CustomerComEmail = $login->customer_relation->email ?? '';
            
            $zoneName = 'All Zones';
            if ($login->type == 'zone') {
                $zoneName = $login->zone->name ?? 'N/A';
            }
    
            $customerData = [
                'login_details' => [
                    'login_type'     => ucfirst($login->type),
                    'login_email'    => $login->email,
                    'login_password' => '-', 
                    'city_name'      => $login->city->city_name ?? 'N/A',
                    'zone_name'      => $zoneName,
                ],
                'customer_details' => [
                    'name'        => $customerName,
                    'customer_id' => $customerId,
                    'trade_name'  => $tradeName,
                    'phone'       => $CustomerMobile,
                    'customer_company_email'=>$CustomerComEmail
                ]
            ];

            $this->CustomerCredencials_SentWhatsAppMessage($customerData,'customer_login_account_status_notify', $account_status); //whatsapp notify

             try{
                 $this->CustomerCredencials_SentEmailNotify($customerData,'customer_login_account_status_emailNotify',$account_status); //email notify
            }
            catch(\Exception $e){
                // dd($e->getMessage());
                    \Log::info("Mail sent failed", [
                    'error'   => $e->getMessage(),
                    'page' => 'Customer login status update'
        
                ]);
            }
            
                $user = Auth::user();
                $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

                audit_log_after_commit([
                    'module_id'         => 1,
                    'short_description' => 'Customer Login Status Updated',
                    'long_description'  => "Login for customer '{$customerName}' (Customer ID: {$customerId}) status changed: '{$oldStatus}' -> '{$newStatus}'.",
                    'role'              => $roleName,
                    'user_id'           => $user->id ?? null,
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'customer_login.status_update',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                ]);
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ]);
        }
    }



    public function get_customer_logins(Request $request)
    {
        try {
            $customer_id = $request->customer_id;

            $logins = CustomerLogin::with(['city','zone'])
                ->where('customer_id', $customer_id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($data) {
                    return [
                        'id' =>$data->id,
                        'email'      => $data->email ?? '',
                        'type'       => $data->type,
                        'city'       => $data->city->city_name ?? '-',
                        'zone'       => $data->zone->name ?? '-',
                        'created_at' => $data->created_at,
                        'status' =>$data->status,
                        'city_id' => $data->city_id,
                        'zone_id' => $data->zone_id ?? ''
                    ];
                });

    
            return response()->json([
                'success' => true,
                'data'    => $logins
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }



    
    public function status_update(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|string',
                'status' => 'required|boolean', 
            ]);
    
            
            $updated = CustomerMaster::where('id', $request->id)->first();
            $oldStatus = $updated->status ? 'Active':'Inactive';
            $newStatus = $request->status ? 'Active':'Inactive';
            $updated->update(['status' => $request->status]);
            
            if ($updated) {
                $user = Auth::user();
                $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

                audit_log_after_commit([
                    'module_id'         => 1,
                    'short_description' => 'Customer Status Updated',
                    'long_description'  => "Customer '{$updated->name}' (ID: {$updated->id}) status changed: '{$oldStatus}' -> '{$newStatus}'.",
                    'role'              => $roleName,
                    'user_id'           => $user->id ?? null,
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'customer_master.status_update',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                    
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Status updated successfully.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update status or no changes detected.'
                ]);
            }
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function export_customer_master(Request $request){
        
        $selectedIds = json_decode($request->query('selected_ids', '[]'), true);
        $status = $request->status;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $timeline = $request->timeline;
       $selectedFields = json_decode($request->query('fields'), true);
        
        // Prepare sample of selected IDs for log (first few only)
        $idsSample = empty($selectedIds)
            ? 'All Records'
            : implode(',', array_slice($selectedIds, 0, 5)) . (count($selectedIds) > 5 ? '...' : '');
        if (is_array($selectedFields) && !empty($selectedFields)) {
            $flatFields = [];
            foreach ($selectedFields as $field) {
                if (is_array($field)) {
                    $value = $field['field'] ?? reset($field);
                } else {
                    $value =$field;
                }
                
                $formatted = ucwords(str_replace('_', ' ', $value));
                $flatFields[] = $formatted;
            }
            $fieldsText = implode(', ', $flatFields);
        } else {
            $fieldsText = 'All Default Fields';
        }
        // Fetch user and role for log
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        // Build long description
        $longDescription = sprintf(
            "Customer Master Export triggered. Filters → Status: %s | From: %s | To: %s | Timeline: %s | Selected IDs: %s | Fields: %s",
            $status ?? '-',
            $from_date ?? '-',
            $to_date ?? '-',
            $timeline ?? '-',
            $idsSample,
            $fieldsText
        );
        // ✅ Log after successful commit
        audit_log_after_commit([
            'module_id'         => 1,
            'short_description' => 'Customer Master Export Initiated',
            'long_description'  => $longDescription,
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'customer_master.export',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
        
        // return Excel::download(new CustomerMasterExport($status ,$from_date,$to_date , $selectedIds), 'customer-master-export' . date('d-m-Y') . '.xlsx');
         return Excel::download(new MultiSheetExportCustomerMaster($status ,$from_date,$to_date ,$timeline, $selectedIds ,$selectedFields), 'customer-master-export-' . date('d-m-Y') . '.xlsx');
        
    }
    
    
    
}