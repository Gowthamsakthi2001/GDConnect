<?php

namespace Modules\Deliveryman\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\City\Entities\City;
use Modules\LeadSource\Entities\LeadSource;
use Modules\RiderType\Entities\RiderType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Modules\Deliveryman\DataTables\DeliverymanDataTable;
use Modules\Deliveryman\DataTables\ClientDmDataTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Zones\Entities\Zones;
use Modules\City\Entities\Area;
use Modules\Deliveryman\Entities\DeliveryManLogs;
use Modules\Clients\Entities\Client;
use Modules\Clients\Entities\ClientHub;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\LeaveManagement\Entities\LeaveType; //updated by Gowtham.s
use Modules\LeaveManagement\Entities\LeaveRequest;
use App\Exports\LeavePermissionsExport;
use App\Exports\LeaveDaysExport;
use App\Exports\exportLeaveRejectListExport;
use App\Exports\DeliverymanOnboardlist;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\EvGenerateId;
use App\Models\BgvComment;
use App\Exports\DeliverymanLogExport;
use App\Models\BusinessSetting;

class DeliverymanController extends Controller
{ 
    public function index(Request $request)
    {
        try {
            $city = City::where('status', 1)->get();
            $source = LeadSource::where('status', 1)->get();
            $rider_type = RiderType::where('status', 1)->get();
            $Zones = Zones::where('status', 1)->get();
            return view('deliveryman::create', compact('city', 'source', 'rider_type','Zones'));
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while loading Rider.');
        }
    }
    

    public function create(Request $request)
    {
        // Validate the incoming request using the Validator facade
        
        // dd($request->all());
        
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'rider_type' => 'nullable|string|max:255',
            'mobile_number' => [
                'required',
                'string',
                'size:13',
                'regex:/^\+91[0-9]{10}$/'
            ],
            'current_city_id' => 'required|integer',
            'interested_city_id' => 'required|integer',
            'vehicle_type' => 'nullable|string',
            // 'lead_source_id' => 'required|integer',
            'apply_job_source' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // Nullable for update
            'aadhar_card_front' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'aadhar_card_back' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'pan_card_front' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            // 'pan_card_back' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'driving_license_front' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'driving_license_back' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'bank_passbook' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'aadhar_number' => 'required|string|max:20',
            'pan_number' => 'required|string|max:20',
            'license_number' => 'nullable|string|max:20',
            'bank_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:20',
            'account_number' => 'required|string|max:30',
            'account_holder_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'present_address' => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'father_mobile_number' => [
                'required',
                'string',
                'size:13',
                'regex:/^\+91[0-9]{10}$/'
            ],
            // 'mother_name' => 'required|string|max:255',
            'mother_name' => 'string|max:255',
            'mother_mobile_number' => [
                // 'required',
                'string',
                'size:13',
                'regex:/^\+91[0-9]{10}$/'
            ],
            'spouse_name' => 'nullable|string|max:255',
            'spouse_mobile_number' => [
                'nullable',
                'string',
                'min:3',
                'max:13',
                'regex:/^\+91[0-9]{0,10}$/',
            ],
            // 'emergency_contact_person_1_name' => 'required|string|max:255',
            // 'emergency_contact_person_1_mobile' => [
            //     'required',
            //     'string',
            //     'size:13',
            //     'regex:/^\+91[0-9]{10}$/'
            // ],
            // 'emergency_contact_person_2_name' => 'required|string|max:255',
            // 'emergency_contact_person_2_mobile' => [
            //     'required',
            //     'string',
            //     'size:13',
            //     'regex:/^\+91[0-9]{10}$/'
            // ],
            'blood_group' => 'required|string|max:3',
            'remarks' => 'nullable|string',
            'work_type'=>'required|in:in-house,deliveryman,adhoc'
            // 'Zones' => 'required',
        ]);
        
        if ($request->work_type != "in-house") {
            
            if($request->is_llr == "1"){
                
                $validator->addRules([
                'llr_number' => 'required|unique:ev_tbl_delivery_men,llr_number',
                'llr_image' => 'required|mimes:jpg,jpeg,png,pdf|max:10240',
                
                ]);
            
                
            }
            else{
                
              $validator->addRules([
                'license_number' => 'required|unique:ev_tbl_delivery_men,license_number',
                'driving_license_front' => 'required|image|max:10240',
                'driving_license_back' => 'required|image|max:10240'
                
             ]);
             
            }
            
            $validator->addRules([
                'vehicle_type' => 'required|string|max:50',
                'rider_type' => 'required|string|max:50',
                
            ]);
            
        }
        
      
        if ($validator->fails()) {
            return response()->json(['success' => false,'errors' => $validator->errors(),], 422); 
        }
        
        // Proceed with your logic after validation
        $dman = Deliveryman::where('mobile_number', $request->mobile_number)->exists(); 

        if ($dman) {
             return response()->json(['success' => false,'message' => 'This Mobile Number is already registered.'],200); 
        }
        
         if ($request->work_type == "deliveryman") {
            $emp_id = $this->get_deliveryman_permanentid_count($request->work_type);
        }
        if ($request->work_type == 'in-house') {
            $emp_id = $this->get_employee_permanentid_count($request->work_type);
        }

        $dm = new Deliveryman();
        $dm->first_name = $request->first_name;
        $dm->last_name = $request->last_name;
        $dm->emp_id = $emp_id ?? null;
        $dm->emp_id_status = 1;
        $dm->mobile_number = $request->mobile_number;
        $dm->current_city_id = $request->current_city_id;
        $dm->interested_city_id = $request->interested_city_id;
        $dm->vehicle_type = $request->vehicle_type;
        $dm->lead_source_id = $request->lead_source_id;
        $dm->register_date_time = Carbon::now();
        $dm->remarks = $request->remarks ?? null;
        $dm->apply_job_source = $request->apply_job_source;
        $dm->referral = $request->referral;
        $dm->job_agency = $request->job_agency;
        $dm->marital_status = $request->marital_status;
        // $dm->zone_id = $request->Zones;
    
        // Handle file uploads
        if ($request->hasFile('photo')) {
            $dm->photo = $this->uploadFile($request->file('photo'), 'EV/images/photos');
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
        // if ($request->hasFile('pan_card_back')) {
        //     $dm->pan_card_back = $this->uploadFile($request->file('pan_card_back'), 'EV/images/pan');
        // }
        if ($request->hasFile('driving_license_front')) {
            $dm->driving_license_front = $this->uploadFile($request->file('driving_license_front'), 'EV/images/driving_license');
        }
        else{
            $dm->driving_license_front = null;
        }
        if ($request->hasFile('driving_license_back')) {
            $dm->driving_license_back = $this->uploadFile($request->file('driving_license_back'), 'EV/images/driving_license');
        }else{
            $dm->driving_license_back = null;
        }
        if ($request->hasFile('bank_passbook')) {
            $dm->bank_passbook = $this->uploadFile($request->file('bank_passbook'), 'EV/images/bank_passbook');
        }
        
        if ($request->hasFile('llr_image')) {
            $dm->llr_image = $this->uploadFile($request->file('llr_image'), 'EV/images/llr_images');
        }
        else{
            $dm->llr_image = null;
        }
        
    
        // Save additional fields
        $dm->aadhar_number = $request->aadhar_number;
        $dm->pan_number = $request->pan_number;
        $dm->license_number = $request->license_number ?? null;
        $dm->llr_number = $request->llr_number ?? null;
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
        $dm->spouse_name = $request->marital_status == 0 ? null : $request->spouse_name;
        $dm->spouse_mobile_number = $request->marital_status == 0 ? null : $request->spouse_mobile_number;
        // $dm->emergency_contact_person_1_name = $request->emergency_contact_person_1_name;
        // $dm->emergency_contact_person_1_mobile = $request->emergency_contact_person_1_mobile;
        // $dm->emergency_contact_person_2_name = $request->emergency_contact_person_2_name;
        // $dm->emergency_contact_person_2_mobile = $request->emergency_contact_person_2_mobile;
        $dm->blood_group = $request->blood_group;
        $dm->referal_person_name = $request->referal_person_name ?? null;
        $dm->referal_person_number = $request->referal_person_number ?? null;
        $dm->rider_type = $request->rider_type;
        $dm->approved_status = 1;
        $dm->approver_role = auth()->user()->name;
        $dm->approver_id = auth()->user()->id;
        $dm->work_type = $request->work_type;
        $dm->save();
        
        $this->admin_message($dm->mobile_number);
        $this->rider_message($dm->mobile_number);
        
        
        
        if($request->work_type == 'deliveryman'){
           return response()->json(['success' => true,'message' => 'Deliveryman created successfully'],200);  
        }else if($request->work_type == 'in-house'){
            return response()->json(['success' => true,'message' => 'Employee created successfully'],200);  
        }else{
            return response()->json(['success' => true,'message' => 'Adhoc created successfully'],200);  
        }
        
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
    
    public function edit_dm(Request $request){
        // try {
        //     // Fetch data for cities, sources, and rider types
        //     $city = City::where('status',1)->get();
        //     $source = LeadSource::where('status',1)->get();
        //     $rider_type = RiderType::where('status',1)->get();
        //      $Zones = Zones::where('status', 1)->get();
        //     // Find the Rider by ID
        //     $dm = Deliveryman::find($request->id);
            
        //     // Check if the Rider exists
        //     if (!$dm) {
        //         return redirect()->route('admin.Green-Drive-Ev.delivery-man.list')
        //                          ->withToastrError('Rider not found.');
        //     }
    
        //     // Return the view with necessary data
        //     return view('deliveryman::edit', compact('city', 'source', 'dm', 'rider_type','Zones'));
        // } catch (Exception $e) {
        //     // Handle the error and show a toastr error message
        //     return back()->withToastrError('An error occurred while editing the Rider: ' . $e->getMessage());
        // }
        
        try {
            // Fetch data for cities, sources, and rider types
            $city = City::where('status', 1)->get();
            $source = LeadSource::where('status', 1)->get();
            $rider_type = RiderType::where('status', 1)->get();
            $Zones = Zones::where('status', 1)->get();

            // Find the Rider by ID
            $dm = Deliveryman::find($request->id);

            // Check if the Rider exists
            if (!$dm) {
                return redirect()->route('admin.Green-Drive-Ev.delivery-man.list')
                    ->withToastrError('Rider not found.');
            }

            // Fetch next and previous delivery men based on their ID
            $nextDm = Deliveryman::where('id', '>', $dm->id)->orderBy('id')->first();
            $prevDm = Deliveryman::where('id', '<', $dm->id)->orderBy('id', 'desc')->first();

            // Return the view with necessary data and navigation info
            return view('deliveryman::edit', compact('city', 'source', 'dm', 'rider_type', 'Zones', 'nextDm', 'prevDm'));
        } catch (Exception $e) {
            // Handle the error and show a toastr error message
            return back()->withToastrError('An error occurred while editing the Rider: ' . $e->getMessage());
        }
    }
    
    
    public function update(Request $request, $id)
    {
        
        
        // exit;
        
        // Validate the incoming request using the Validator facade
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'rider_type' => 'nullable|string|max:255',
            'mobile_number' => [
                'required',
                'string',
                'size:13',
                'regex:/^\+91[0-9]{10}$/'
            ],
            'current_city_id' => 'required|integer',
            'interested_city_id' => 'required|integer',
            'vehicle_type' => 'nullable|string',
            // 'lead_source_id' => 'required|integer',
            'apply_job_source' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // Nullable for update
            'aadhar_card_front' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'aadhar_card_back' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'pan_card_front' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            // 'pan_card_back' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'driving_license_front' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'driving_license_back' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'bank_passbook' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'aadhar_number' => 'required|string|max:20',
            'pan_number' => 'required|string|max:20',
            'license_number' => 'nullable|string|max:20',
            'bank_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:20',
            'account_number' => 'required|string|max:30',
            'account_holder_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'present_address' => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'father_mobile_number' => [
                'required',
                'string',
                'size:13',
                'regex:/^\+91[0-9]{10}$/'
            ],
            // 'mother_name' => 'required|string|max:255',
            'mother_name' => 'nullable',
            'mother_mobile_number' => [
                'nullable',
                // 'string',
                // 'size:13',
                // 'regex:/^\+91[0-9]{10}$/'
            ],
            'spouse_name' => 'nullable|string|max:255',
            'spouse_mobile_number' => [
                'nullable',
                'string',
                'min:3',
                'max:13',
                'regex:/^\+91[0-9]{0,10}$/',
            ],
            // 'emergency_contact_person_1_name' => 'required|string|max:255',
            // 'emergency_contact_person_1_mobile' => [
            //     'required',
            //     'string',
            //     'size:13',
            //     'regex:/^\+91[0-9]{10}$/'
            // ],
            
            // 'emergency_contact_person_2_name' => 'required|string|max:255',
            // 'emergency_contact_person_2_mobile' => [
            //     'required',
            //     'string',
            //     'size:13',
            //     'regex:/^\+91[0-9]{10}$/'
            // ],
            'blood_group' => 'required|string|max:3',
            'remarks' => 'nullable|string',
            // 'Zones' => 'required',
            'work_type'=>'required|in:in-house,deliveryman'
        ]);
        
          if ($request->work_type != "in-house") {
            $valid_data = Deliveryman::where('id',$id)->where('work_type','!=','in-house')->first();
            
            if($request->is_llr == '1'){
                
                if(empty($valid_data->driving_license_front) && empty($valid_data->driving_license_back) &&  empty($valid_data->license_number)){
                    
                    $validator->addRules([
                        'llr_number' => 'required|unique:ev_tbl_delivery_men,llr_number,' . $id,
                    ]);

                   
                    $validator->addRules([
                        'llr_image' => 'required|mimes:jpg,jpeg,png,pdf',
                    ]);
                    
                }
                  
            }
            else{
                
                
            //   if (empty($valid_data->license_number)) {
                $validator->addRules([
                    'license_number' => 'required|unique:ev_tbl_delivery_men,license_number,' . $id,
                ]);
            // }
            
                if(empty($valid_data->llr_number) && empty($valid_data->llr_image)){
                
                    if (empty($valid_data->driving_license_front)) {
                        $validator->addRules([
                            'driving_license_front' => 'required|image|max:10240',
                        ]);
                    }
                    if (empty($valid_data->driving_license_back)) {
                        $validator->addRules([
                            'driving_license_back' => 'required|image|max:10240',
                        ]);
                    }
                }
            
            
            }


            $validator->addRules([
                'vehicle_type' => 'required|string|max:50',
                'rider_type' => 'required|string|max:50',
            ]);
        }
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $dm = Deliveryman::findOrFail($id);
        // Update fields
        // $dm->emp_id = $request->emp_id ?? null;
        $dm->first_name = $request->first_name;
        $dm->last_name = $request->last_name;
        $dm->mobile_number = $request->mobile_number;
        $dm->current_city_id = $request->current_city_id;
        $dm->interested_city_id = $request->interested_city_id;
        $dm->vehicle_type = $request->vehicle_type ?? null;
        $dm->lead_source_id = $request->lead_source_id;
        $dm->remarks = $request->remarks ?? null;
        $dm->apply_job_source = $request->apply_job_source;
        $dm->referral = $request->referral;
        $dm->job_agency = $request->job_agency;
        $dm->rider_type = $request->rider_type ?? null;
        // $dm->zone_id = $request->Zones;
        // Handle file uploads
        if ($request->hasFile('photo')) {
            $dm->photo = $this->uploadFiles($request->file('photo'), 'EV/images/photos',$dm->photo);
        }
        if ($request->hasFile('aadhar_card_front')) {
              $dm->aadhar_card_front = $this->uploadFiles($request->file('aadhar_card_front'), 'EV/images/aadhar',$dm->aadhar_card_front);
        }
        if ($request->hasFile('aadhar_card_back')) {
            $dm->aadhar_card_back = $this->uploadFiles($request->file('aadhar_card_back'), 'EV/images/aadhar',$dm->aadhar_card_back);
        }
        if ($request->hasFile('pan_card_front')) {
            $dm->pan_card_front = $this->uploadFiles($request->file('pan_card_front'), 'EV/images/pan',$dm->pan_card_front);
        }
        // if ($request->hasFile('pan_card_back')) {
        //     $dm->pan_card_back = $this->uploadFiles($request->file('pan_card_back'), 'EV/images/pan',$dm->pan_card_back);
        // }
        if ($request->hasFile('driving_license_front')) {
            $dm->driving_license_front = $this->uploadFiles($request->file('driving_license_front'), 'EV/images/driving_license',$dm->driving_license_front);
        }
        if ($request->hasFile('driving_license_back')) {
            $dm->driving_license_back = $this->uploadFiles($request->file('driving_license_back'), 'EV/images/driving_license',$dm->driving_license_back);
        }
        
        if ($request->hasFile('bank_passbook')) {
            $dm->bank_passbook = $this->uploadFiles($request->file('bank_passbook'), 'EV/images/bank_passbook',$dm->bank_passbook);
        }
    
    
        if($request->is_llr == '1'){
            $dm->llr_image = $this->uploadFiles($request->file('llr_image'), 'EV/images/llr_images',$dm->llr_image);
            
            if (!empty($dm->driving_license_front)) {
                $frontPath = public_path('EV/images/driving_license/' . $dm->driving_license_front);
                if (file_exists($frontPath)) {
                    unlink($frontPath);
                }
                
            }
        
            if (!empty($dm->driving_license_back)) {
                $backPath = public_path('EV/images/driving_license/' . $dm->driving_license_back);
                if (file_exists($backPath)) {
                    unlink($backPath);
                }
            
            }
    
            $dm->driving_license_front = null;
            $dm->driving_license_back = null;
        }
        else{
         
            if (!empty($dm->llr_image)) {
                $backPath = public_path('EV/images/llr_images/' . $dm->llr_image);
                if (file_exists($backPath)) {
                    unlink($backPath);
                }
                
            }
            
            $dm->llr_image = null;
        }
        
        
        // Save additional fields
        $dm->aadhar_number = $request->aadhar_number;
        $dm->pan_number = $request->pan_number;
        $dm->license_number = $request->license_number ?? null;
        $dm->llr_number = $request->llr_number ?? null;
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
        $dm->spouse_name = $request->spouse_name;
        $dm->marital_status = $request->marital_status;
        $dm->spouse_mobile_number = $request->spouse_mobile_number;
        // $dm->emergency_contact_person_1_name = $request->emergency_contact_person_1_name;
        // $dm->emergency_contact_person_1_mobile = $request->emergency_contact_person_1_mobile;
        // $dm->emergency_contact_person_2_name = $request->emergency_contact_person_2_name;
        // $dm->emergency_contact_person_2_mobile = $request->emergency_contact_person_2_mobile;
        $dm->blood_group = $request->blood_group;
        $dm->referal_person_name = $request->referal_person_name ?? null;
        $dm->referal_person_number = $request->referal_person_number ?? null;
        $dm->approved_status = 1;
        $dm->approver_role = auth()->user()->name;
        $dm->approver_id = auth()->user()->id;
        // $dm->work_type = $request->work_type;
    
        // Save the updated Rider details
        $dm->save();
    
        // Redirect back with a success message
        if($dm->work_type == "deliveryman"){
          return redirect()->route('admin.Green-Drive-Ev.delivery-man.list')->with('success','Rider updated successfully!');
        }
        else if($dm->work_type == "in-house"){
             return redirect()->route('admin.Green-Drive-Ev.employee_management.employee_list')->with('success','Employee updated successfully!');
        }
        else if($dm->work_type == "adhoc"){
             return redirect()->route('admin.Green-Drive-Ev.adhocmanagement.list_of_adhoc')->with('success','Adhoc updated successfully!');
        }else{
            return redirect()->back()->with('success','data updated successfully!');
        }
    }
    
    // public function list(DeliverymanDataTable $dataTable)
    // {
    //     $clients = Client::All();
        // $zones = Zones::where('status',1)->get();
        // $cities = City::where('status',1)->get();
    //     return $dataTable->render('deliveryman::list',compact('zones','clients','cities'));
    // }
    
    // public function list(Request $request)
    // {
    //     $clients = Client::All();
    //     $zones = Zones::where('status',1)->get();
    //     $cities = City::where('status',1)->get();
    //     $city_id = $request->city_id ?? '';
    //     $zone_id = $request->zone_id ?? '';
    //     $client_id = $request->client_id ?? '';
        
    //     // dd(auth()->user());
        
    //  $user_role_id = auth()->check() ? auth()->user()->role : null;
     
     


    //     $query = Deliveryman::where('work_type', 'deliveryman')
    //         ->where('delete_status', 0);
            
    //     if($user_role_id == 3){ //telecaller only
    //         $query->whereNull('register_date_time');
    //     }else{ 
            
    //         $query->whereNotNull('register_date_time'); //all login 
    //     }
            
    //     if ($city_id !== '') {
    //         $query->where('current_city_id', $city_id);
    //     }
    //     if ($zone_id !== '') {
    //         $query->where('zone_id', $zone_id);
    //     }
    //     if ($client_id !== '') {
    //         $query->where('client_id', $client_id);
    //     }
        
    //     $lists = $query->orderBy('id', 'desc')->get();
        
    //     return view('deliveryman::rider_list', compact('lists', 'zones', 'clients', 'cities', 'city_id', 'zone_id', 'client_id'));

    // }

    public function list(Request $request) //updated by Gowtham Sakthi
    {
        if ($request->ajax() && $request->has('field') && $request->has('id')) {
                $deliveryman = Deliveryman::findOrFail($request->id);
                $field = $request->input('field');
                $status = (int) $request->input('status'); // ensure integer (0/1)
            
                $allowedFields = ['aadhar_verify', 'pan_verify', 'bank_verify', 'lisence_verify'];
            
                if (in_array($field, $allowedFields)) {
                    $deliveryman->$field = $status;
                    $deliveryman->save();
            
                    // Proper field name mapping
                    $fieldNames = [
                        'aadhar_verify' => 'Aadhaar Card',
                        'pan_verify' => 'PAN Card',
                        'bank_verify' => 'Bank Details',
                        'lisence_verify' => 'Driving License',
                    ];
            
                    $statusName = $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
                    $statusText = $status == 1 ? 'verified' : 'unverified';
            
                    return response()->json([
                        'success' => true,
                        'message' => "{$statusName} has been marked as {$statusText}."
                    ]);
                }
            
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid field name.'
                ]);
            }

        if ($request->ajax()) {
            $query = Deliveryman::with(['current_city', 'zone', 'client'])
                ->where('work_type', 'deliveryman');

            $city_id = $request->input('city_id');
            $zone_id = $request->input('zone_id');
            $client_id = $request->input('client_id');
            $search = $request->input('search.value');
            $start = $request->input('start', 0);
            $length = $request->input('length', 15);
            $user_role_id = auth()->check() ? auth()->user()->role : null;

            // Role-based filtering
            if($user_role_id == 3){ //telecaller only
                $query->whereNull('register_date_time');
            }else{ 
                $query->whereNotNull('register_date_time'); //all login 
            }

            // Apply filters
            if (!empty($city_id)) {
                $query->where('current_city_id', $city_id);
            }
            if (!empty($zone_id)) {
                $query->where('zone_id', $zone_id);
            }
            if (!empty($client_id)) {
                $query->where('client_id', $client_id);
            }

            // Search filter
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%")
                      ->orWhere('emp_id', 'like', "%$search%")
                      ->orWhere('mobile_number', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhereHas('current_city', function($q) use ($search) {
                          $q->where('city_name', 'like', "%$search%");
                      })
                      ->orWhereHas('zone', function($q) use ($search) {
                          $q->where('name', 'like', "%$search%");
                      })
                      ->orWhereHas('client', function($q) use ($search) {
                          $q->where('client_name', 'like', "%$search%");
                      });
                });
            }

            // Get total records count
            $totalRecords = $query->count();

            if ($length == -1) {
                $length = $totalRecords;
            }

            // Get the data
            $data = $query->orderBy('id', 'desc')
                ->skip($start)
                ->take($length)
                ->get();

            // Format the data for DataTables
            $formattedData = $data->map(function($item, $index) use ($start) {
                $full_name = ($item->first_name ?? '').' '.($item->last_name ?? '');
                
                $roll_type = '';
                if($item->work_type == 'deliveryman'){
                    $roll_type = 'Rider';
                }
                else if($item->work_type == 'in-house'){
                    $roll_type = 'Employee';
                }
                else if($item->work_type == 'adhoc'){
                    $roll_type = 'Adhoc';
                }
                
                $image = $item->photo ? asset('public/EV/images/photos/'.$item->photo) : asset('public/admin-assets/img/person.png');
                
                $hub = \Modules\Clients\Entities\ClientHub::where('id',$item->hub_id)->where('client_id',$item->client_id)->first();
                $hub_name = $hub ? $hub->hub_name : '-';

                // Last login logic
                    $lastPunchIn = \Illuminate\Support\Facades\DB::table('ev_delivery_man_logs')
                        ->where('user_id', $item->id)
                        ->orderBy('punched_in', 'desc')
                        ->first();
                    
                    $last_login = '<span class="badge bg-secondary">No Login</span>';
                    $city = '';
                    
                    if ($lastPunchIn) {
                        try {
                            $punchedInDate = \Carbon\Carbon::parse($lastPunchIn->punched_in);
                            $lastPunchInFormatted = $punchedInDate->format('d-m-Y H:i:s');
                            $daysSinceLastPunch = now()->diffInDays($punchedInDate);
                            
                            if (!empty($lastPunchIn->punchin_latitude) && !empty($lastPunchIn->punchin_longitude)) {
                                $city = \App\Helpers\CustomHandler::get_punchin_city(
                                    $lastPunchIn->punchin_latitude, 
                                    $lastPunchIn->punchin_longitude
                                );
                            }
                            
                            if ($daysSinceLastPunch >= 3) {
                                $last_login = '<span class="badge bg-danger">' . $lastPunchInFormatted . 
                                             '</span><br> <span style="font-size: 10px;">' . e($city) . '</span>';
                            } else {
                                $last_login = '<span class="badge bg-success">' . $lastPunchInFormatted . 
                                             '</span> <br><span style="font-size: 10px;">' . e($city) . '</span>';
                            }
                        } catch (\Exception $e) {
                            $last_login = '<span class="badge bg-warning">Invalid Date</span>';
                        }
                    }
                    
                    // Job status
                    $jobStatus = '';
                    if ($item->job_status == 'active' ) {
                        $jobStatus = '<span class="badge bg-success">Active</span>';
                    } elseif ($item->job_status == 'resigned') {
                        $jobStatus = '<span class="badge bg-danger">Resigned</span>';
                    } else {
                        $jobStatus = '<span class="badge bg-warning">N/A</span>';
                    }
                    
                    

                // Verification status toggles
                // Aadhar Toggle
                $aadharToggle = '<div class="form-check form-switch">
                    <label class="toggle-switch" for="aadhar_statusCheckbox_'.$item->id.'">
                        <input type="checkbox" 
                               class="form-check-input toggle-btn toggle-btn-status" 
                               id="aadhar_statusCheckbox_'.$item->id.'" 
                               data-id="'.$item->id.'" 
                               data-field="aadhar_verify"
                               '.($item->aadhar_verify ? 'checked' : '').'>
                        <span class="toggle-switch-label">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </label>
                </div>';
                
                // PAN Toggle
                $panToggle = '<div class="form-check form-switch">
                    <label class="toggle-switch" for="pan_statusCheckbox_'.$item->id.'">
                        <input type="checkbox" 
                               class="form-check-input toggle-btn toggle-btn-status" 
                               id="pan_statusCheckbox_'.$item->id.'" 
                               data-id="'.$item->id.'" 
                               data-field="pan_verify"
                               '.($item->pan_verify ? 'checked' : '').'>
                        <span class="toggle-switch-label">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </label>
                </div>';
                
                // Bank Toggle
                $bankToggle = '<div class="form-check form-switch">
                    <label class="toggle-switch" for="bank_statusCheckbox_'.$item->id.'">
                        <input type="checkbox" 
                               class="form-check-input toggle-btn toggle-btn-status" 
                               id="bank_statusCheckbox_'.$item->id.'" 
                               data-id="'.$item->id.'" 
                               data-field="bank_verify"
                               '.($item->bank_verify ? 'checked' : '').'>
                        <span class="toggle-switch-label">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </label>
                </div>';
                
                // License Toggle
                $licenseToggle = '<div class="form-check form-switch">
                    <label class="toggle-switch" for="dl_statusCheckbox_'.$item->id.'">
                        <input type="checkbox" 
                               class="form-check-input toggle-btn toggle-btn-status" 
                               id="dl_statusCheckbox_'.$item->id.'" 
                               data-id="'.$item->id.'" 
                               data-field="lisence_verify"
                               '.($item->lisence_verify ? 'checked' : '').'>
                        <span class="toggle-switch-label">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </label>
                </div>';


                // Action buttons
                $deleteIcon = $item->delete_status == 1 ? '<i class="fas fa-undo"></i>' : '<i class="fas fa-trash"></i>';
                $deleteClass = $item->delete_status == 1 ? 'btn-dark-soft btn-outline-dark' : 'btn-danger-soft';
                $deleteText = $item->delete_status == 1 ? 'Restore' : 'Delete';


                $actionBtns = '<div class="d-flex">
                    <a href="'.route('admin.Green-Drive-Ev.delivery-man.preview', $item->id).'" class="btn btn-warning-soft btn-sm me-1">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a onclick="route_alert_with_input(\''.route('admin.Green-Drive-Ev.delivery-man.whatsapp-message').'\', \''.$item->mobile_number.'\')" class="btn btn-success btn-sm me-1">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="'.route('admin.Green-Drive-Ev.delivery-man.edit', $item->id).'" class="btn btn-success-soft btn-sm me-1">
                        <i class="fas fa-pen-to-square"></i>
                    </a>
                    <button onclick="route_alert(\''.route('admin.Green-Drive-Ev.delivery-man.delete', $item->id).'\', \''.$deleteText.' this Deliveryman\',this)" class="btn '.$deleteClass.' btn-sm me-1" title="'.$deleteText.'">
                        '.$deleteIcon.'
                    </button>
                    <a href="'.route('admin.Green-Drive-Ev.delivery-man.zone-asset', $item->id).'" class="btn btn-primary-soft btn-sm me-1">
                        <i class="fas fa-bicycle"></i>
                    </a>
                </div>';

                return [
                    'DT_RowIndex' => $start + $index + 1,
                    'image' => '<div onclick="Profile_Image_View(\''.$image.'\')">
                        <img src="'.$image.'" alt="Image" class="profile-image">
                    </div>',
                    'deliveryman_name' => $full_name,
                    'gdm_id' => $item->emp_id ?? '-',
                    'email' => $item->email ?? '',
                    'mobile_number' => $item->mobile_number,
                    'role' => $roll_type,
                    'city' => $item->current_city->city_name ?? '',
                    'zone' => $item->zone->name ?? '-',
                    'client_name' => $item->client->client_name ?? '-',
                    'hub_name' => $hub_name,
                    'rider_status' => '<div class="form-check form-switch">
                        <label class="toggle-switch" for="statusCheckbox_'.$item->id.'">
                            <input type="checkbox" 
                                   class="form-check-input toggle-btn" 
                                   id="statusCheckbox_'.$item->id.'" 
                                   '.($item->rider_status ? 'checked' : '').' 
                                   onchange="status_change_alert(\''.route('admin.Green-Drive-Ev.delivery-man.status', [$item->id, $item->rider_status ? 0 : 1]).'\', \''.($item->rider_status ? 'Deactivate' : 'Activate').' this Deliveryman?\', event,this)">
                            <span class="toggle-switch-label">
                                <span class="toggle-switch-indicator"></span>
                            </span>
                        </label>
                    </div>',
                    'last_login' =>$last_login,
                    'job_status' =>(string)  $jobStatus,
                    'aadhar_verified' => $aadharToggle,
                    'pan_verified' => $panToggle,
                    'bank_verified' => $bankToggle,
                    'license_verified' => $licenseToggle,
                    'action' => $actionBtns
                ];
            });
            
              return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                 'data' => $formattedData->toArray()
            ]);

           
                        
        }
        
        $clients = Client::All();
        $zones = Zones::where('status',1)->get();
        $cities = City::where('status',1)->get();
        $city_id = $request->city_id ?? '';
        $zone_id = $request->zone_id ?? '';
        $client_id = $request->client_id ?? '';
        return view('deliveryman::rider_list', compact('city_id', 'zone_id', 'client_id','cities', 'zones', 'clients'));

      
    }


    // public function delete_dm($id)
    // {
    //     try {
    //           $dm = Deliveryman::findOrFail($id);
    //           $dm->delete();
    //           DeliveryManLogs::where('user_id', $id)->delete();
    //             return back()->with('Rider removed Successfully');
    //         } catch (Exception $e) {
    //             return back()->with('An error occurred while loading dm: ' . $e->getMessage());
    //         }
    // }
    
    // public function delete_dm($id)
    // {
    //     try {
    //         $dm = Deliveryman::findOrFail($id);
    //         $dm->delete_status = $dm->delete_status == 1 ? 0 : 1;
    //         $dm->rider_status = $dm->delete_status == 1 ? 0 : $dm->rider_status;
    //         $dm->save();
    
    //         $message = $dm->delete_status == 1 ? 'Removed successfully' : 'Restored successfully';
    //         // dd($message);
    //         return response()->json(['success' => true, 'message' => 'Candidate Removed Successfully', 'status' => $dm->delete_status],200);
            
    //     } catch (Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'An error occurred while loading dm: ' . $e->getMessage()]);
    //     }
    // }

    public function delete_dm($id) //updated by Gowtham Sakthi
    {
        try {
            $dm = Deliveryman::findOrFail($id);
            
            $dm->delete_status = $dm->delete_status == 1 ? 0 : 1;
            $dm->rider_status = $dm->delete_status == 1 ? 0 : $dm->rider_status;
            $dm->save();

            $message = $dm->delete_status == 1 ? 'Removed successfully' : 'Restored successfully';
            // dd($message);
            return response()->json(['success' => true, 'message' => 'Candidate Removed Successfully', 'status' => $dm->delete_status],200);
            
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while loading dm: ' . $e->getMessage()]);
        }
    }


    // public function change_status(Request $request, $dm_id, $status)
    // {
    //     try {
    //         $dm = Deliveryman::findOrFail($dm_id);
    //         $dm->rider_status = $status;
    //         $dm->save();
    
    //         $fullName = trim($dm->first_name . ' ' . $dm->last_name);
    //         $workType = strtolower($dm->work_type); 

    //         switch ($workType) {
    //             case 'deliveryman':
    //                 $successText = "Rider status for {$fullName} has been updated successfully.";
    //                 break;
    //             case 'in-house':
    //                 $successText = "Employee status for {$fullName} has been updated successfully.";
    //                 break;
    //             case 'adhoc':
    //                 $successText = "Adhoc status for {$fullName} has been updated successfully.";
    //                 break;
    //             default:
    //                 $successText = "Status for {$fullName} has been updated successfully.";
    //         }
            
    //          if ($status == 1 && $dm->mobile_number) {
    //             $this->sendActivationWhatsAppMessage($dm->mobile_number, $fullName);
    //          }

    
    //         return back()->with('success', $successText);
    
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'An error occurred while updating the rider status: ' . $e->getMessage());
    //     }
    // }
    
     public function change_status(Request $request, $dm_id, $status) //updated by Gowtham Sakthi
    {
        try {
            $dm = Deliveryman::findOrFail($dm_id);
            $dm->rider_status = $status;
            $dm->save();
    
            $fullName = trim($dm->first_name . ' ' . $dm->last_name);
            $workType = strtolower($dm->work_type);
    
            switch ($workType) {
                case 'deliveryman':
                    $successText = "Rider status for {$fullName} has been updated successfully.";
                    break;
                case 'in-house':
                    $successText = "Employee status for {$fullName} has been updated successfully.";
                    break;
                case 'adhoc':
                    $successText = "Adhoc status for {$fullName} has been updated successfully.";
                    break;
                default:
                    $successText = "Status for {$fullName} has been updated successfully.";
            }
    
            if ($status == 1 && $dm->mobile_number) {
                $this->sendActivationWhatsAppMessage($dm->mobile_number, $fullName);
            }
    
            return response()->json([
                'status' => true,
                'message' => $successText,
                'data' => [
                    'id' => $dm->id,
                    'rider_status' => $dm->rider_status,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the rider status: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    
    public function sendActivationWhatsAppMessage($mobileNo, $fullName)
    {
        try {
            // Phone formatting
            $cleanedPhone = preg_replace('/\D+/', '', $mobileNo);
            if (substr($cleanedPhone, 0, 1) === '0') {
                $cleanedPhone = substr($cleanedPhone, 1);
            }
            if (substr($cleanedPhone, 0, 2) === '91' && strlen($cleanedPhone) === 12) {
                $phone = $cleanedPhone;
            } elseif (strlen($cleanedPhone) === 10) {
                $phone = '91' . $cleanedPhone;
            } else {
                \Log::warning("Invalid phone number format for WhatsApp: {$mobileNo}");
                return;
            }
    
            $message = "Hello {$fullName},\n\nWe're pleased to inform you that your account has been activated successfully.\n You may now proceed to log in and start your work.\n\nWelcome to our team!\n\nBest regards,\n Green Drive Connect";
    
            // WhatsApp API
            $api_key = BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
            $url = 'https://whatshub.in/api/whatsapp/send';
    
            $postdata = [
                "contact" => [
                    [
                        "number"    => $phone,
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
                \Log::error('WhatsApp message sending failed: ' . $error);
                return;
            }
    
            $responseData = json_decode($response, true);
            if (!isset($responseData['success']) || $responseData['success'] != true) {
                \Log::error('WhatsApp API error: ', $responseData);
            }
    
            \Log::info("WhatsApp activation message sent to {$fullName} ({$phone})");
    
        } catch (\Exception $e) {
            \Log::error('Error sending WhatsApp message: ' . $e->getMessage());
        }
    }


    
    public function aadhar_status(Request $request)
    {
        try {
            // Find the deliveryman by its ID
            $dm = Deliveryman::findOrFail($request->id);

            // Set the status (assuming you have a 'rider_status' field in your table)
            $dm->aadhar_verify = $request->status; // Set to the new status (e.g., 1 for active, 0 for inactive)
            $dm->save(); // Save the updated status

            // Success message with a session key 'success'
            // return back()->with('success', 'Rider status changed successfully.');
            $success_text = "";
            if($dm->work_type == "deliveryman"){
                $success_text = "Rider status changed successfully!";
            }
            else if($dm->work_type == "in-house"){
                $success_text = "Employee status changed successfully!";
            }
            else if($dm->work_type == "adhoc"){
                $success_text = "Adhoc status changed successfully!";
            }else{
                $success_text = "status changed successfully!";
            }
            return back()->with('success', $success_text);
        } catch (\Exception $e) {
            // Handle errors, for example, if the Deliveryman is not found or any other issues occur
            return back()->with('error', 'An error occurred while changing the Rider status: ' . $e->getMessage());
        }
    }
    public function pan_status(Request $request)
    {
        try {
            // Find the deliveryman by its ID
            $dm = Deliveryman::findOrFail($request->id);

            // Set the status (assuming you have a 'rider_status' field in your table)
            $dm->pan_verify = $request->status; // Set to the new status (e.g., 1 for active, 0 for inactive)
            $dm->save(); // Save the updated status

            // Success message with a session key 'success'
            // return back()->with('success', 'Rider status changed successfully.');
            $success_text = "";
            if($dm->work_type == "deliveryman"){
                $success_text = "Rider status changed successfully!";
            }
            else if($dm->work_type == "in-house"){
                $success_text = "Employee status changed successfully!";
            }
            else if($dm->work_type == "adhoc"){
                $success_text = "Adhoc status changed successfully!";
            }else{
                $success_text = "status changed successfully!";
            }
            return back()->with('success', $success_text);
        } catch (\Exception $e) {
            // Handle errors, for example, if the Deliveryman is not found or any other issues occur
            return back()->with('error', 'An error occurred while changing the Rider status: ' . $e->getMessage());
        }
    }
    public function bank_status(Request $request)
    {
        try {
            // Find the deliveryman by its ID
            $dm = Deliveryman::findOrFail($request->id);

            // Set the status (assuming you have a 'rider_status' field in your table)
            $dm->bank_verify = $request->status; // Set to the new status (e.g., 1 for active, 0 for inactive)
            $dm->save(); // Save the updated status

            // Success message with a session key 'success'
            // return back()->with('success', 'Rider status changed successfully.');
            $success_text = "";
            if($dm->work_type == "deliveryman"){
                $success_text = "Rider status changed successfully!";
            }
            else if($dm->work_type == "in-house"){
                $success_text = "Employee status changed successfully!";
            }
            else if($dm->work_type == "adhoc"){
                $success_text = "Adhoc status changed successfully!";
            }else{
                $success_text = "status changed successfully!";
            }
            return back()->with('success', $success_text);
        } catch (\Exception $e) {
            // Handle errors, for example, if the Deliveryman is not found or any other issues occur
            return back()->with('error', 'An error occurred while changing the Rider status: ' . $e->getMessage());
        }
    }
    public function lisence_status(Request $request)
    {
        try {
            // Find the deliveryman by its ID
            $dm = Deliveryman::findOrFail($request->id);

            // Set the status (assuming you have a 'rider_status' field in your table)
            $dm->lisence_verify = $request->status; // Set to the new status (e.g., 1 for active, 0 for inactive)
            $dm->save(); // Save the updated status

            // Success message with a session key 'success'
            // return back()->with('success', 'Rider status changed successfully.');
            $success_text = "";
            if($dm->work_type == "deliveryman"){
                $success_text = "Rider status changed successfully!";
            }
            else if($dm->work_type == "in-house"){
                $success_text = "Employee status changed successfully!";
            }
            else if($dm->work_type == "adhoc"){
                $success_text = "Adhoc status changed successfully!";
            }else{
                $success_text = "status changed successfully!";
            }
            return back()->with('success', $success_text);
        } catch (\Exception $e) {
            // Handle errors, for example, if the Deliveryman is not found or any other issues occur
            return back()->with('error', 'An error occurred while changing the Rider status: ' . $e->getMessage());
        }
    }
    // public function kyc_verify(Request $request)    
    // {
    //     try {
    //         $dm = Deliveryman::findOrFail($request->id);

    //         $dm->kyc_verify = $request->status; 
    //         $dm->save(); 

    //         $success_text = "";
    //         if($dm->work_type == "deliveryman"){
    //             $success_text = "Rider kyc verified successfully!";
    //         }
    //         else if($dm->work_type == "in-house"){
    //             $success_text = "Employee kyc verified successfully!";
    //         }
    //         else if($dm->work_type == "adhoc"){
    //             $success_text = "Adhoc kyc verified successfully!";
    //         }else{
    //             $success_text = "kyc verified successfully!";
    //         }
    //         return back()->with('success', $success_text);
    //     } catch (\Exception $e) {
    //         // Handle errors, for example, if the Deliveryman is not found or any other issues occur
    //         return back()->with('error', 'An error occurred while changing the Rider status: ' . $e->getMessage());
    //     }
    // }   
    
    public function kyc_verify(Request $request, $id)
    {
        try {
            $dm = Deliveryman::find($id);
            if (!$dm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rider not found.'
                ]);
            }
            
            $notVerified = [];
            if($request->status == 1){
                if (!$dm->aadhar_verify) $notVerified[] = 'Aadhar';
                if (!$dm->pan_verify) $notVerified[] = 'PAN';
                if (!$dm->bank_verify) $notVerified[] = 'BANK';
    
                if ($dm->work_type !== "in-house" && $dm->lisence_verify != 1) {
                    $notVerified[] = 'License';
                }
            }
    
            if (!empty($notVerified)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The following fields are not verified or are missing: ' . implode(', ', $notVerified)
                ]);
            }
        
    
            $dm->kyc_verify = $request->status;
            $dm->bgv_approve_id = auth()->id();
            $dm->bgv_approve_datetime = now();
            $dm->save();
    
            return response()->json([
                'success' => true,
                'message' => 'BGV status updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    } 
    
     public function bgv_comment_update(Request $request)
    {
        try {
            $dm = Deliveryman::find($request->dm_id);
            if (!$dm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rider not found.'
                ]);
            }
            
            if ($request->remarks == '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Remarks field is required. Please enter a comment.'
                ]);
            }
            
            $dm->kyc_verify = $request->bgv_status;
            $dm->bgv_approve_id = auth()->id();
            $dm->bgv_approve_datetime = now();
            $dm->save();
            
            $comment = new BgvComment();
            $comment->dm_id = $request->dm_id;
            $comment->bgv_status = $request->bgv_status;
            $comment->remarks = $request->remarks;
            $comment->bgv_id = auth()->id();
            $comment->comment_type = 'bgv_vendor';
            $comment->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Comments Added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    } 

    public function preview(Request $request)
    { 
        // try {
        //     $dm = Deliveryman::leftJoin('ev_tbl_city as current_city', 'ev_tbl_delivery_men.current_city_id', '=', 'current_city.id')
        //         ->leftJoin('ev_tbl_city as interested_city', 'ev_tbl_delivery_men.interested_city_id', '=', 'interested_city.id')
        //         ->leftJoin('ev_tbl_lead_source as ls', 'ev_tbl_delivery_men.apply_job_source', '=', 'ls.id')
        //         ->leftJoin('ev_tbl_area as area', 'ev_tbl_delivery_men.interested_city_id', '=', 'area.id')
        //         ->select(
        //             'ev_tbl_delivery_men.*',
        //             'current_city.id as city_id',
        //             'current_city.city_name',
        //             'interested_city.id as interested_id',
        //             'interested_city.city_name as interested_city_name',
        //             'ls.id as leads_id',
        //             'ls.source_name as source_name',
        //             'area.Area_name as area_name',
        //         )
        //         ->where('ev_tbl_delivery_men.id', $request->id)
        //         ->first();

        //     // Check if the Rider was found
        //     if (!$dm) {
        //         return back()->with('error', 'Rider not found.');
        //     }

        //     return view('deliveryman::preview', compact('dm'));

        // } catch (\Exception $e) {
        //     // Handle the error using Toastr
        //     return back()->with('error', 'An error occurred while loading Rider: ' . $e->getMessage());
        // }
        
          try {
            // Fetch the current Rider (rider)
            $dm = Deliveryman::leftJoin('ev_tbl_city as current_city', 'ev_tbl_delivery_men.current_city_id', '=', 'current_city.id')
                ->leftJoin('ev_tbl_city as interested_city', 'ev_tbl_delivery_men.interested_city_id', '=', 'interested_city.id')
                ->leftJoin('ev_tbl_lead_source as ls', 'ev_tbl_delivery_men.apply_job_source', '=', 'ls.id')
                ->leftJoin('ev_tbl_area as area', 'ev_tbl_delivery_men.interested_city_id', '=', 'area.id')
                ->select(
                    'ev_tbl_delivery_men.*',
                    'current_city.id as city_id',
                    'current_city.city_name',
                    'interested_city.id as interested_id',
                    'interested_city.city_name as interested_city_name',
                    'ls.id as leads_id',
                    'ls.source_name as source_name',
                    'area.Area_name as area_name',
                )
                ->where('ev_tbl_delivery_men.id', $request->id)
                ->first();

            // Check if the Rider was found
            if (!$dm) {
                return back()->with('error', 'Rider not found.');
            }

            // Fetch next and previous delivery men based on their ID
            $nextDm = Deliveryman::where('id', '>', $dm->id)->orderBy('id')->first();
            $prevDm = Deliveryman::where('id', '<', $dm->id)->orderBy('id', 'desc')->first();
            
            $year = date('Y'); //updated by Gowtham.s
            $totalLeaveDays = LeaveType::where('leave_type', 'day')->sum('days');
            $total_taken_leaves = LeaveRequest::whereYear('start_date',$year)->where('dm_id', $dm->id)->where('approve_status',1)->sum('apply_days');
            $balance_leaves = $totalLeaveDays - $total_taken_leaves;
            $totalpermission_hr = LeaveRequest::whereYear('permission_date',$year)->where('dm_id', $dm->id)->where('approve_status',1)->sum('permission_hr');
            
            $get_permissions = LeaveRequest::with(['leave'])->whereYear('permission_date',$year)->where('dm_id', $dm->id)->where('approve_status',1)->get();
            $get_taken_leaves = LeaveRequest::whereYear('start_date',$year)->where('dm_id', $dm->id)->where('approve_status',1)->get();
            $get_reject_list = LeaveRequest::with(['leave'])->where('dm_id', $dm->id)->where('reject_status',1)->get();
            return view('deliveryman::preview', compact('dm', 'nextDm', 'prevDm','totalLeaveDays','total_taken_leaves','balance_leaves','totalpermission_hr',
            'get_permissions','get_taken_leaves','get_reject_list'));
        } catch (\Exception $e) {
            // Handle the error using Toastr
            return back()->with('error', 'An error occurred while loading Rider: ' . $e->getMessage());
        }
        
    }
    
    public function exportLeavePermissions(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $deliverymanId = $request->input('deliveryman_id');
        $dm = Deliveryman::where('id',$deliverymanId)->first();
        return Excel::download(new LeavePermissionsExport($year, $deliverymanId), $dm->first_name.'-'.$dm->last_name.'-leave_permissions'.date('d-m-Y').'.xlsx');
    }
    
    public function export_leave_days(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $deliverymanId = $request->input('deliveryman_id');
        $dm = Deliveryman::where('id',$deliverymanId)->first();
        return Excel::download(new LeaveDaysExport($year, $deliverymanId), $dm->first_name.'-'.$dm->last_name.'-leave_days'.date('d-m-Y').'.xlsx');
    }
    
    public function export_leave_reject_list(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $deliverymanId = $request->input('deliveryman_id');
        $dm = Deliveryman::where('id',$deliverymanId)->first();
        return Excel::download(new exportLeaveRejectListExport($year, $deliverymanId), $dm->first_name.'-'.$dm->last_name.'-reject-list'.date('d-m-Y').'.xlsx');
    }


    public function uploadFile($file, $directory)
    {
        $imageName = Str::uuid(). '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $imageName);
        return $imageName; // Return the name of the uploaded file
    }
    
    public function uploadFiles($file, $directory,$exist){
       // Check if the profile exists in the details
        if (!empty($exist)) {
            $profilePath = public_path($directory . '/' .$exist);
            
            // If the file exists, delete it
            if (file_exists($profilePath)) {
                unlink($profilePath);
            }
        }
    
        // Upload the new file
        $imageName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $imageName);
        
        return $imageName; // Return the name of the uploaded file
    }

    public function approve(Request $request, $id)
    {
        $dm = Deliveryman::find($id);
        if (!$dm) {
            return back()->with('error', 'Rider not found.');
        }
        
        $notVerified = [];
        
        if (!$dm->aadhar_verify) {
            $notVerified[] = 'Aadhar';
        }
        
        if (!$dm->pan_verify) {
            $notVerified[] = 'PAN';
        }
        
        if (!$dm->bank_verify) {
            $notVerified[] = 'BANK';
        }
        if($dm->work_type != "in-house"){ //updated by Gowtham.s
            if (!$dm->lisence_verify) {
                $notVerified[] = 'License';
            }
            
            if (empty($dm->client_id)) {
                $notVerified[] = 'Client & Hub';
            }
            
            if (empty($dm->zone_id)) {
                $notVerified[] = 'Zone';
            }
        }
        
        if (!empty($notVerified)) {
            $error = 'The following fields are not verified or are missing: ' . implode(', ', $notVerified) . '.';
            return back()->with('error', $error);
        }
        $this->status_handle_whatsapp_message($dm->id,'approve');
        $dm->update([
            'approved_status' => 1,
            'approver_role' => auth()->user()->name,
            'approver_id' => auth()->user()->id,
        ]);
        
        $message = $dm->work_type == 'deliveryman' ? 'Deliveryman Approved successfully.' : 'Employee Approved successfully.';
        return back()->with('success', $message);
    }
    
    // private function rider_generate_id($id)
    // {
    //   $dm = Deliveryman::find($id);

    //     if (!$dm || !$dm->current_city || !$dm->current_city->short_code) {
    //         return null;
    //     }
        
    //     $city_code = strtoupper($dm->current_city->short_code); // ex: CHN, BLR, HYD
        
    //     $riderType = match($dm->work_type) {
    //         'deliveryman' => 'R',
    //         'in-house'    => 'E',
    //         'adhoc'       => 'A',
    //         'helper'       => 'H',
    //         default       => 'N',
    //     };
        
    //     $year = date('y'); // Example: 25 for 2025
    //     $prefix = 'GDM' . $riderType . $year . $city_code;
        
    //     // 👇 Count all deliverymen who have emp_id already and not deleted
    //     $totalCount = Deliveryman::where('delete_status', 0)
    //         ->whereNotNull('emp_id')
    //         ->count();
        
    //     $serial = $totalCount + 1;
        
    //     // 👉 Always 5 digit serial padded with zeros (e.g. 00001, 00010, 00111)
    //     $serialPart = str_pad((string)$serial, 5, '0', STR_PAD_LEFT);
        
    //     $new_emp_id = $prefix . $serialPart;
        
    //     // Save new emp_id
    //     $dm->emp_id = $new_emp_id;
    //     $dm->save();
        
    //     return $new_emp_id;

    // }
    
  private function rider_generate_id($id)
    {
        $dm = Deliveryman::find($id);
    
        if (!$dm || !$dm->current_city || !$dm->current_city->short_code) {
            return null;
        }
    
        $city_code = strtoupper(trim($dm->current_city->short_code));
    
        $riderType = match ($dm->work_type) {
            'deliveryman' => 'R',
            'in-house'    => 'E',
            'adhoc'       => 'A',
            'helper'      => 'H',
            default       => 'N',
        };
    
        $year = date('y'); // e.g. 25
    
        $prefix = 'GDM' . $riderType . $year . $city_code;
    
        $lastEmpId = DB::table('ev_tbl_delivery_men')
            ->where('delete_status', 0)
            ->whereNotNull('emp_id')
            ->select(DB::raw('RIGHT(emp_id, 5) AS emp_number'))
            ->orderByRaw('CAST(emp_number AS UNSIGNED) DESC')
            ->value('emp_number');
    
        $lastSerial = $lastEmpId ? (int)$lastEmpId : 0;
        $newSerial = str_pad((string)($lastSerial + 1), 5, '0', STR_PAD_LEFT);
    
        $new_emp_id = $prefix . $newSerial;
    
        // dd($lastEmpId, $new_emp_id); 
    
        $dm->emp_id = strtoupper($new_emp_id);
        $dm->save();
    
        return $dm->emp_id;
    }




    
    public function application_status_approve(Request $request, $id) //updated by Gowtham.s
    {
        try {
            $dm = Deliveryman::find($id);
            if (!$dm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rider not found.'
                ]);
            }
            
            if ($dm->kyc_verify != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'BGV status not verified. Please complete BGV verification before proceeding further.'
                ]);
            }

            $notVerified = [];
          
            if (!$dm->aadhar_verify) $notVerified[] = 'Aadhar';
            if (!$dm->pan_verify) $notVerified[] = 'PAN';
            if (!$dm->bank_verify) $notVerified[] = 'BANK';

            if ($dm->work_type !== "in-house" && $dm->lisence_verify != 1) {
                $notVerified[] = 'License';
            }
          

            if (!empty($notVerified)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The following fields are not verified or are missing: ' . implode(', ', $notVerified)
                ]);
            }
            
            $rider_id = $this->rider_generate_id($dm->id);
            if($rider_id == null){
                 return response()->json([
                    'success' => false,
                    'message' => 'Rider ID Generate Failed'
                ]);
            }
    
            $this->status_handle_whatsapp_message($dm->id, 'approve');
            
            $probation_from_date = date('Y-m-d', strtotime(now())); // e.g., 2025-01-01
            $probation_to_date   = date('Y-m-d', strtotime('+6 days')); // e.g., 2025-01-07

            if ($dm->work_type == "in-house") {
                $probation_from_date = null;
                $probation_to_date = null;
            }
          
            
            $dm->update([
                'emp_id'=>$rider_id,
                'emp_id_status'=>1,
                'rider_status' => 1,
                'approved_status' => 1,
                'approver_role' => auth()->user()->name,
                'approver_id' => auth()->user()->id,
                'as_approve_datetime'=> now(),
                'probation_from_date'=>$probation_from_date,
                'probation_to_date'=>$probation_to_date
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'The Candidate accepted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }
    public function generate_GDMID(Request $request , $id){
        
        try {
            $dm = Deliveryman::find($id);
            

            if (!$dm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rider not found.'
                ]);
            }
            
            if ($dm->kyc_verify != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'BGV status not verified. Please complete BGV verification before proceeding further.'
                ]);
            }

            $notVerified = [];
          
            if (!$dm->aadhar_verify) $notVerified[] = 'Aadhar';
            if (!$dm->pan_verify) $notVerified[] = 'PAN';
            if (!$dm->bank_verify) $notVerified[] = 'BANK';

            if ($dm->work_type !== "in-house" && $dm->lisence_verify != 1) {
                $notVerified[] = 'License';
            }
          

            if (!empty($notVerified)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The following fields are not verified or are missing: ' . implode(', ', $notVerified)
                ]);
            }
            
            $rider_id = $this->rider_generate_id($dm->id);
            if($rider_id == null){
                 return response()->json([
                    'success' => false,
                    'message' => 'Rider ID Generate Failed'
                ]);
            }
            
             return response()->json([
                    'success' => true,
                    'message' => 'GDM ID Generated Successfully.'
                ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }
    
    public function application_status_reject(Request $request) //updated by Gowtham.s
    {
        try {
            $dm = Deliveryman::find($request->dm_id);
            if (!$dm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rider not found.'
                ]);
            }
 
            
            if ($dm->kyc_verify != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'BGV status not verified. Please complete BGV verification before proceeding further.'
                ]);
            }

            $notVerified = [];
          
            if (!$dm->aadhar_verify) $notVerified[] = 'Aadhar';
            if (!$dm->pan_verify) $notVerified[] = 'PAN';
            if (!$dm->bank_verify) $notVerified[] = 'BANK';

            if ($dm->work_type !== "in-house" && $dm->lisence_verify != 1) {
                $notVerified[] = 'License';
            }
          

            if (!empty($notVerified)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The following fields are not verified or are missing: ' . implode(', ', $notVerified)
                ]);
            }
            
            $remarks = $request->remarks;
    
            if (!$remarks) {
                 return response()->json([
                    'success' => false,
                    'message' => 'Remarks Field is required. Please enter a Remarks'
                ]);
            }
    
            // $this->status_handle_whatsapp_message($dm->id, 'approve');
    
            $dm->update([
                'rider_status' => 2,
                'approved_status' => 2,
                'approver_role' => auth()->user()->name,
                'approver_id' => auth()->user()->id,
                'deny_remarks'=>$remarks,
                'as_approve_datetime'=> now()
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'The Candidate Rejected successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }


    public function status_handle_whatsapp_message($id, $type)
    {
        $dm = Deliveryman::where('id', $id)->first();
    
        if (!$dm) {
            Log::error("Deliveryman not found with ID: " . $id);
            return false;
        }
    
        $phone = str_replace('+', '', $dm->mobile_number);
           if ($dm->work_type == 'deliveryman') {
             $role = "Delivery Partner";
            } elseif ($dm->work_type == 'in-house') {
                $role = "Employee";
            } else {
                $role = "Member";
            }
        
            $message = "Dear " . $dm->first_name . " " . $dm->last_name . ",\n\n" .
                       "✅ Your role as *" . $role . "* has been approved by the Admin! 🎉\n\n" .
                       "Best regards,\n" .
                       "GreenDriveConnect";

    
        $api_key = env('WHATSAPP_API_KEY');
        Log::info('whatsappResponse Data Api Key: ' . $api_key);
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
        curl_close($curl);
    
        $response_data = json_decode($response, true);
        Log::info('whatsappResponse Data: ' . json_encode($response_data));
    
        // return $response_data;
    }


    public function deny(Request $request, $id)
    {
        
        $remarks = $request->remarks; 
    
        if (!$remarks) {
            return back()->with('error', 'Remarks are required.');
        }

        $deliveryMan = Deliveryman::find($id);
    
        if (!$deliveryMan) {
            return back()->with('error', 'Rider not found.');
        }

        $deliveryMan->update([
            'approved_status' => 2,
            'approver_role' => auth()->user()->name,
            'approver_id' => auth()->user()->id,
            'deny_remarks' => $remarks, 
        ]);
        
        $message = $deliveryMan->work_type == 'deliveryman' ? 'Deliveryman' : 'Employee';
    
        return back()->with('success', $message.' denied successfully with remarks: ' . $remarks);
    }

    
    public function deliveryman_reports(Request $request)
    {
       $city_id = $request->city_id ?? '';
        $zone_id = $request->zone_id ?? '';
        $client_id = $request->client_id ?? '';
        $summary_type = $request->get('summary_type', 'all');
        $from_date = $summary_type === "period" ? $request->get('from_date') : '';
        $to_date = $summary_type === "period" ? $request->get('to_date') : '';
        
        // Time filter conditions
        $timeFilters = [
            'all'         => '', // No condition
            'daily'       => "DATE(ev_delivery_man_logs.punched_in) = CURDATE()",
            'yesterday'   => "DATE(ev_delivery_man_logs.punched_in) = CURDATE() - INTERVAL 1 DAY",
            'this_week'   => "YEARWEEK(ev_delivery_man_logs.punched_in, 1) = YEARWEEK(CURDATE(), 1)",
            'last_week'   => "YEARWEEK(ev_delivery_man_logs.punched_in, 1) = YEARWEEK(CURDATE(), 1) - 1",
            'this_month'  => "MONTH(ev_delivery_man_logs.punched_in) = MONTH(CURDATE()) AND YEAR(ev_delivery_man_logs.punched_in) = YEAR(CURDATE())",
            'last_month'  => "MONTH(ev_delivery_man_logs.punched_in) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(ev_delivery_man_logs.punched_in) = YEAR(CURDATE() - INTERVAL 1 MONTH)",
        ];
        
        $timeFilterWhere = '';
        if ($summary_type === 'period' && $from_date && $to_date) {
            $timeFilterWhere = "AND DATE(ev_delivery_man_logs.punched_in) BETWEEN '{$from_date}' AND '{$to_date}'";
        } elseif (!empty($timeFilters[$summary_type])) {
            $timeFilterWhere = "AND " . $timeFilters[$summary_type];
        }
        
        // Other filters
        $cityFilter = $city_id ? "AND ev_tbl_delivery_men.current_city_id = {$city_id}" : '';
        $zoneFilter = $zone_id ? "AND ev_tbl_delivery_men.zone_id = {$zone_id}" : '';
        $clientFilter = $client_id ? "AND ev_tbl_delivery_men.client_id = {$client_id}" : '';
        
        // Final query (starts from delivery men)
        $dm = DB::select("
            SELECT 
                ev_tbl_delivery_men.id AS user_id,
                ev_tbl_delivery_men.first_name,
                ev_tbl_delivery_men.last_name,
                ev_tbl_delivery_men.rider_status,
                ev_tbl_delivery_men.current_city_id,
                ev_tbl_city.city_name,
                ev_tbl_delivery_men.zone_id,
                zones.name AS zone_name,
                ev_tbl_delivery_men.client_id,
                ev_tbl_clients.client_name AS client_name,
                IFNULL(SUM(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out)), 0) AS total_minutes,
                CONCAT(
                    FLOOR(IFNULL(SUM(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out)), 0) / 60), ' hours ', 
                    MOD(IFNULL(SUM(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out)), 0), 60), ' minutes'
                ) AS total_time
            FROM ev_tbl_delivery_men
            LEFT JOIN ev_delivery_man_logs 
                ON ev_tbl_delivery_men.id = ev_delivery_man_logs.user_id
                {$timeFilterWhere}
            LEFT JOIN ev_tbl_city 
                ON ev_tbl_city.id = ev_tbl_delivery_men.current_city_id
            LEFT JOIN zones 
                ON zones.id = ev_tbl_delivery_men.zone_id
            LEFT JOIN ev_tbl_clients 
                ON ev_tbl_clients.id = ev_tbl_delivery_men.client_id
            WHERE ev_tbl_delivery_men.work_type = 'deliveryman'
            {$cityFilter}
            {$zoneFilter}
            {$clientFilter}
            GROUP BY 
                ev_tbl_delivery_men.id, 
                ev_tbl_delivery_men.first_name, 
                ev_tbl_delivery_men.last_name, 
                ev_tbl_delivery_men.rider_status,
                ev_tbl_delivery_men.current_city_id,
                ev_tbl_city.city_name,
                ev_tbl_delivery_men.zone_id,
                zones.name,
                ev_tbl_delivery_men.client_id,
                ev_tbl_clients.client_name
            ORDER BY ev_tbl_delivery_men.first_name ASC
        ");


        
        $zones = Zones::where('status',1)->get();
        $clients = Client::All();
        $cities = City::where('status',1)->get();
        return view('deliveryman::reports', compact('dm','cities','city_id','summary_type','from_date','to_date','zones','clients','zone_id','client_id'));
    }
    
    public function client_based_deliveryman_reports(ClientDmDataTable $dataTable){
        return $dataTable->render('deliveryman::client_dm_reports');
    }

    public function show_deliveryman_report(Request $request, $dm_id)
    {
        $dm = Deliveryman::where('id', $dm_id)->first();
        
        return view('deliveryman::dm_report_list', compact('dm', 'dm_id')); 
    }
    
    // public function report_list_dm(Request $request, $dm_id)
    // {
    //     // Get filter type (daily, weekly, monthly, yearly)
    //   $filter_type = $request->get('filter_type', 'all');

    //     $query = "
    //         SELECT 
    //             ev_delivery_man_logs.id,
    //             ev_delivery_man_logs.user_id,
    //             ev_delivery_man_logs.user_type,
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
    //         WHERE ev_delivery_man_logs.user_id = ? 
    //     ";
        
    //     switch ($filter_type) {
    //         case 'daily':
    //             $query .= " AND DATE(ev_delivery_man_logs.punched_in) = CURDATE()";
    //             break;
        
    //         case 'this_week':
    //             // MySQL's YEARWEEK() with mode 1 means week starts on Monday
    //             $query .= " AND YEARWEEK(ev_delivery_man_logs.punched_in, 1) = YEARWEEK(CURDATE(), 1)";
    //             break;
        
    //         case 'last_week':
    //             // Last week = current week - 1
    //             $query .= " AND YEARWEEK(ev_delivery_man_logs.punched_in, 1) = YEARWEEK(CURDATE(), 1) - 1";
    //             break;
        
    //         case 'this_month':
    //             $query .= " AND MONTH(ev_delivery_man_logs.punched_in) = MONTH(CURDATE()) AND YEAR(ev_delivery_man_logs.punched_in) = YEAR(CURDATE())";
    //             break;
        
    //         case 'last_month':
    //             // Calculate last month relative to current date
    //             $query .= " AND MONTH(ev_delivery_man_logs.punched_in) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(ev_delivery_man_logs.punched_in) = YEAR(CURDATE() - INTERVAL 1 MONTH)";
    //             break;
        
    //         case 'all':
    //         default:
    //             // No date filter
    //             break;
    //     }
        
    //     $reports = DB::select($query, [$dm_id]);

        
    //     $approve_users = \Illuminate\Support\Facades\DB::table('model_has_roles')
    //         ->join('users', 'model_has_roles.model_id', '=', 'users.id') 
    //         ->select('users.id as user_id', 'users.name as user_name')   
    //         ->where('model_has_roles.role_id', 1) // Filter role_id = 1 (Administrator)
    //         ->where('users.status', 'Active')
    //         ->get();
        
    //     $login_user_id = auth()->id();
    //     $get_approve_ids = [];
        
    //     foreach ($approve_users as $user) {
    //         $get_approve_ids[] = $user->user_id;
    //     }
    //     $view_status = 0;
    //     if(!empty($approve_users) && in_array($login_user_id,$get_approve_ids)){
    //         $view_status = 1;
    //     }

    
    //     return response()->json(['status' => true, 'data' => $reports,'view_status'=>$view_status]);
    // }
    
    function report_list_dm(Request $request, $dm_id)
    {
        $timeFilters = [
            'all'         => '', // No filter
            'daily'       => "DATE(punched_in) = CURDATE()",
            'this_week'   => "YEARWEEK(punched_in, 1) = YEARWEEK(CURDATE(), 1)",
            'last_week'   => "YEARWEEK(punched_in, 1) = YEARWEEK(CURDATE(), 1) - 1",
            'this_month'  => "MONTH(punched_in) = MONTH(CURDATE()) AND YEAR(punched_in) = YEAR(CURDATE())",
            'last_month'  => "MONTH(punched_in) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(punched_in) = YEAR(CURDATE() - INTERVAL 1 MONTH)",
        ];
    
        $reportSummary = [];
    
        foreach ($timeFilters as $key => $where) {
            $query = DB::table('ev_delivery_man_logs')
                ->where('user_id', $dm_id)
                ->whereNotNull('punched_out'); // Ensure both punch-in and punch-out are present
    
            if (!empty($where)) {
                $query->whereRaw($where);
            }
    
            $logs = $query->get();
    
            $totalMinutes = 0;
            foreach ($logs as $log) {
                $in = \Carbon\Carbon::parse($log->punched_in);
                $out = \Carbon\Carbon::parse($log->punched_out);
                $totalMinutes += $in->diffInMinutes($out);
            }
    
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
    
            $reportSummary[$key] = sprintf('%02d:%02d', $hours, $minutes);
        }
    
        $filter_type = $request->get('filter_type', 'all');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        
        // Common query base
        $selectedQuery = DB::table('ev_delivery_man_logs')
            ->where('user_id', $dm_id);
        
        if ($filter_type !== "period") {
            $timeFilters = [
                'daily' => "DATE(punched_in) = CURDATE()",
                'this_week' => "YEARWEEK(punched_in, 1) = YEARWEEK(CURDATE(), 1)",
                'last_week' => "YEARWEEK(punched_in, 1) = YEARWEEK(CURDATE(), 1) - 1",
                'this_month' => "MONTH(punched_in) = MONTH(CURDATE()) AND YEAR(punched_in) = YEAR(CURDATE())",
                'last_month' => "MONTH(punched_in) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(punched_in) = YEAR(CURDATE() - INTERVAL 1 MONTH)"
            ];
        
            if (!empty($timeFilters[$filter_type])) {
                $selectedQuery->whereRaw($timeFilters[$filter_type]);
            }
        
        } else {
            // Period filter - use whereBetween for date range
            if (!empty($from_date) && !empty($to_date)) {
                $selectedQuery->whereBetween(DB::raw('DATE(punched_in)'), [$from_date, $to_date]);
            }
        }
        
        // Final query with selected columns
        $reportData = $selectedQuery->select(
            'id',
            'user_id',
            'user_type',
            'punched_in',
            'punched_out',
            DB::raw('DATE(punched_in) AS date'),
            DB::raw('TIME(punched_in) AS in_time'),
            DB::raw('TIME(punched_out) AS out_time'),
            DB::raw("CONCAT(TIMESTAMPDIFF(HOUR, punched_in, punched_out), ' hours ', 
                           MOD(TIMESTAMPDIFF(MINUTE, punched_in, punched_out), 60), ' minutes') AS total_time")
        )->get();

        
        $approve_users = DB::table('model_has_roles')
            ->join('users', 'model_has_roles.model_id', '=', 'users.id')   
            ->select('users.id as user_id')
            ->where('model_has_roles.role_id', 1)
            ->where('users.status', 'Active')
            ->pluck('user_id')
            ->toArray();
    
        $view_status = in_array(auth()->id(), $approve_users) ? 1 : 0;
    
        return response()->json([
            'status' => true,
            'data' => $reportData,
            'view_status' => $view_status,
            'summary' => $reportSummary, 
        ]);
    }

    
    public function get_area(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer', // Ensure the city ID is provided and is an integer
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
            'areas'=>$areas,
        ]);
    }
    
     public function verification($id, $status, $type)
    {
        $deliveryMan = DeliveryMan::findOrFail($id);
    
        if (!$deliveryMan) {
            return back()->with('error', 'Rider not found.');
        }

        if (!auth()->user()->id) {
            return back()->with('error', 'Unauthorized.');
        }
        
        if($type == "aadhar_verify"){
            if($status == 0){
                $deliveryMan->aadhar_verify = 1;
                $deliveryMan->who_aadhar_verify_id = auth()->user()->id;
                $deliveryMan->aadhar_verify_date = now();
                $deliveryMan->save();
                 return back()->with('success', 'Aadhar Verified successfully.');
            }else{
                $deliveryMan->aadhar_verify = 0;
                $deliveryMan->who_aadhar_verify_id = null;
                $deliveryMan->aadhar_verify_date = null;
                $deliveryMan->save();
                return back()->with('success', 'Aadhar Unverified successfully.');
            }
        }
        
        if($type == "pan_verify"){
            if($status == 0){
                $deliveryMan->pan_verify = 1;
                $deliveryMan->who_pan_verify_id = auth()->user()->id;
                $deliveryMan->pan_verify_date = now();
                $deliveryMan->save();
                 return back()->with('success', 'Pan Verified successfully.');
            }else{
                $deliveryMan->pan_verify = 0;
                $deliveryMan->who_pan_verify_id = null;
                $deliveryMan->pan_verify_date = null;
                $deliveryMan->save();
                return back()->with('success', 'Pan Unverified successfully.');
            }
        }
        
         if($type == "bank_verify"){
            if($status == 0){
                $deliveryMan->bank_verify = 1;
                $deliveryMan->who_bank_verify_id = auth()->user()->id;
                $deliveryMan->bank_verify_date = now();
                $deliveryMan->save();
                 return back()->with('success', 'Bank Details Verified successfully.');
            }else{
                $deliveryMan->bank_verify = 0;
                $deliveryMan->who_bank_verify_id = null;
                $deliveryMan->bank_verify_date = null;
                $deliveryMan->save();
                return back()->with('success', 'Bank Details Unverified successfully.');
            }
        }
        
         if($type == "license_verify"){
            if($status == 0){
                $deliveryMan->lisence_verify = 1;
                $deliveryMan->who_license_verify_id = auth()->user()->id;
                $deliveryMan->lisence_verify_date = now();
                $deliveryMan->save();
                 return back()->with('success', 'License Verified successfully.');
            }else{
                $deliveryMan->lisence_verify = 0;
                $deliveryMan->who_license_verify_id = null;
                $deliveryMan->lisence_verify_date = null;
                $deliveryMan->save();
                return back()->with('success', 'License Unverified successfully.');
            }
        }
        
        return back()->with('error', 'Invalid format type.');
    
    }
    
//   public function verification($id, $status, $type)
//     {
//         $deliveryMan = DeliveryMan::findOrFail($id);
    
//         if (!$deliveryMan) {
//             return back()->with('error', 'Rider not found.');
//         }
    
//         // Update the specific verification type (e.g., 'verified', 'approved')
//         $deliveryMan->{$type} = $status;
    
//         // Dynamically construct the property name for the date
//         $propertyDate = $type . '_date';
    
//         // Set the date for the specific type
//         $deliveryMan->{$propertyDate} = Carbon::now()->format('Y-m-d H:i:s');
    
//         // Ensure 'who_verify' and 'who_verify_id' are correctly set
//         $deliveryMan->who_verify = auth()->user()->name; // Assuming 'name' is a string field
//         $deliveryMan->who_verify_id = auth()->user()->id;
    
//         // Save the changes
//         $deliveryMan->save();
    
//         // Provide feedback based on whether the save was successful
//         if ($deliveryMan->wasChanged()) {
//             return back()->with('success', 'Rider Verified successfully.');
//         } else {
//             return back()->with('error', 'Failed to verify Rider.');
//         }
//     }

    
    // public function zone_asset($id){
    //     $existingData = Deliveryman::findOrFail($id);
    //   $zones = Zones::All();
    //   $dm = $id;
    //   $Client = Client::all();
    //   $AssetMasterVehicle = AssetMasterVehicle::all();
    //     return view('deliveryman::assign_zone', compact('zones','dm','Client','AssetMasterVehicle')); 
    // }
    
    // public function asset_assign(Request $request ,$id){
        
    //     // dd($request->all());
    //     // exit;
    //     try {
    //         // Validation rules
    //         $rules = [
    //             'asset'  => 'required|integer',
    //             'zone'   => 'required|integer',
    //             'client' => 'required|integer',
    //         ];

    //         // Create the validator instance
    //         $validator = Validator::make($request->all(), $rules);

    //         // Check if validation fails
    //         if ($validator->fails()) {
    //             return back()->withErrors($validator)->withInput();
    //         }

    //         // Find the deliveryman record
    //         $deliveryman = Deliveryman::findOrFail($id);

    //         // Update fields
    //         $deliveryman->vechile_id  = $request->input('asset');
    //         $deliveryman->zone_id   = $request->input('zone');
    //         $deliveryman->client_id = $request->input('client');
    //         $deliveryman->save();

    //         // Check if any fields were changed
    //         if ($deliveryman->wasChanged()) {
    //             return redirect()->route('admin.Green-Drive-Ev.delivery-man.list')->with('Assigned successfully!');
    //             // return back()->with('success', 'Rider updated successfully.');
    //         } else {
    //             return back()->with('error', 'No changes were made to the Rider.');
    //         }
    //     } catch (\Exception $e) {
    //         // Handle error and return response
    //         return back()->with('error', 'Failed to update Rider: ' . $e->getMessage());
    //     }
        
    // }
    
    public function zone_asset($id)
    {
        $existingData = Deliveryman::findOrFail($id); // Fetch existing deliveryman data
        $zones = Zones::all();
        $dm = $id;
        $Client = Client::all();
        $AssetMasterVehicle = AssetMasterVehicle::all();
    
        return view('deliveryman::assign_zone', compact(
            'zones',
            'dm',
            'Client',
            'AssetMasterVehicle',
            'existingData'
        ));
    }
    
    public function asset_assign(Request $request, $id)
    {
        try {
            // Validation rules
            $rules = [
                'asset'  => 'required|string',
                'zone'   => 'required|integer|exists:zones,id',
                'client' => 'required|integer|exists:ev_tbl_clients,id',
                'hub' => 'required|integer|exists:ev_client_hubs,id',
            ];
    
            // Create the validator instance
            $validator = Validator::make($request->all(), $rules);
    
            // Check if validation fails
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validation failed. Please check the form fields.');
            }
    
            // Find the deliveryman record
            $deliveryman = Deliveryman::findOrFail($id);
    
            // Update fields
            $deliveryman->Chassis_Serial_No = $request->input('asset');
            $deliveryman->zone_id    = $request->input('zone');
            $deliveryman->client_id  = $request->input('client');
            $deliveryman->hub_id  = $request->input('hub');
            $deliveryman->save();
    
            // Check if fields were updated
            if ($deliveryman->wasChanged()) {
                return redirect()
                    ->route('admin.Green-Drive-Ev.delivery-man.list')
                    ->with('success', 'Asset, Zone, and Client updated successfully!');
            } else {
                return back()
                    ->with('info', 'No changes were made to the Rider.');
            }
        } catch (\Exception $e) {
            // Handle unexpected errors
            return back()
                ->with('error', 'Failed to update Rider. Error: ' . $e->getMessage());
        }
        
        
    }
    
    public function whatsapp_message(Request $request){
        
        $dm = Deliveryman::where('mobile_number',$request->number)->first();
        
        $phone = str_replace('+', '', $request->number) ;

        $message = "Dear ".$dm->first_name . " " . $dm->last_name . ",\n\n".
                    $request->message. "\n\n".
                    "Best regards,\n".
                    "GreenDriveConnect";
        // $message = $request->message;
        
        $api_key = env('WHATSAPP_API_KEY'); // Replace with your API key
        $url = 'https://whatshub.in/api/whatsapp/send'; // API endpoint
        
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

        
        
        if (isset($response_data['status']) && $response_data['status'] != 'success') {
            return [
                    'status' => 'error',
                    'message' => 'Failed to send WhatsApp message',
                    'error_details' => $response_data
                ];
        }
        
        curl_close($curl);
        
            return [
                'status' => 'success',
                'message' => 'WhatsApp message sent successfully',
                'data' => $response_data
            ];
        
    }
    
    public function export_deliveryman_verify_list(Request $request, $type)
    {
        $city_id = $request->city_id;
        $zone_id = $request->zone_id;
        $client_id = $request->client_id;
        if($type == 'all'){
          return Excel::download(new DeliverymanOnboardlist($type,$city_id,$zone_id,$client_id), 'Deliveryman-all-list-' . date('d-m-Y') . '.xlsx');
        }
        else if($type == 'approve'){
          return Excel::download(new DeliverymanOnboardlist($type,$city_id,$zone_id,$client_id), 'Deliveryman-approved-list-' . date('d-m-Y') . '.xlsx');
        }else if($type == 'deny'){
             return Excel::download(new DeliverymanOnboardlist($type,$city_id,$zone_id,$client_id), 'Deliveryman-rejected-list-' . date('d-m-Y') . '.xlsx');
        }else{
             return Excel::download(new DeliverymanOnboardlist($type,$city_id,$zone_id,$client_id), 'Deliveryman-pending-list-' . date('d-m-Y') . '.xlsx');
        }
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
    
    public function single_log_edit(Request $request, $id)
    {
        $data = DB::table('ev_delivery_man_logs')->where('id', $id)->first();
        
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'This date has no punch-in or punch-out data.'
            ]);
        }
        $date = $data->punched_in ? date('Y-m-d', strtotime($data->punched_in)) : '';
        $in_time = $data->punched_in ? date('H:i', strtotime($data->punched_in)) : ''; 
        $out_time = $data->punched_out ? date('H:i', strtotime($data->punched_out)) : ''; 
    
        return response()->json([
            'status' => true,
            'message' => 'Log data retrieved successfully.',
            'id' => $data->id, 
            'date' => $date,
            'in_time' => $in_time,
            'out_time' => $out_time,
        ]);
    }
    
    public function single_log_update(Request $request)
    {

        $request->validate([
            'date' => 'required|date',
            'in_time' => 'required|date_format:H:i',
            'out_time' => 'required|date_format:H:i',
        ]);
        
        $data = DB::table('ev_delivery_man_logs')->where('id', $request->id)->first();
        
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Log data not found for the given ID.'
            ]);
        }
    
        $punchedInDate = $request->date . ' ' . $request->in_time . ':00'; 
        $punchedOutDate = $request->date . ' ' . $request->out_time . ':00'; 
        // dd($punchedInDate,$punchedOutDate);
        DB::table('ev_delivery_man_logs')
            ->where('id', $request->id)
            ->update([
                'punched_in' => $punchedInDate,
                'punched_out' => $punchedOutDate,
                'updated_at' => now(), 
            ]);
    
        return response()->json([
            'status' => true,
            'message' => 'Log data updated successfully.',
            'user_id'=>$data->user_id
        ]);
    }

     public function filter_hub_list(Request $request)
    {
        try {
            $hubs = ClientHub::where('client_id',$request->id)->get();
            return response()->json(['success' => true, 'message' => 'data fetched successfully', 'hubs' => $hubs],200);
            
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' =>$e->getMessage()]);
        }
    }
    
     public function export_dm_log_list (Request $request)
    {
     
        $city_id = $request->input('city_id');
        $zone_id = $request->input('zone_id');
        $client_id = $request->input('client_id');
        $summary_type = $request->get('summary_type', 'all');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        return Excel::download(new DeliverymanLogExport($city_id,$zone_id,$client_id,$summary_type,$from_date,$to_date),'Deliveryman_Log_list.xlsx');
    }


    
}
