<?php

namespace Modules\AdhocManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\AdhocManagement\DataTables\AdhocListDataTable;
use Modules\AdhocManagement\DataTables\AdhocLogListDataTable;
use Illuminate\Http\Response;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\City\Entities\City;
use Illuminate\Support\Facades\DB;
use Modules\Zones\Entities\Zones;
use Modules\City\Entities\Area;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Modules\Clients\Entities\Client;
use Modules\Clients\Entities\ClientHub;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AdhocOnboardList;
use Illuminate\Pagination\Paginator;
use Modules\LeadSource\Entities\LeadSource;
use Modules\RiderType\Entities\RiderType;
use App\Models\EvGenerateId;

class AdhocManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('adhocmanagement::index');
    }

    public function create()
    {
        $city = City::where('status', 1)->get();
        $source = LeadSource::where('status', 1)->get();
        $rider_type = RiderType::where('status', 1)->get();
        $Zones = Zones::where('status', 1)->get();
        return view('adhocmanagement::create', compact('city', 'source', 'rider_type','Zones'));
    }
    
    public function list_of_adhoc(AdhocListDataTable $dataTable)
    {
        $clients = Client::All();
        $zones = Zones::where('status',1)->get();
        $cities = City::where('status',1)->get();
        return $dataTable->render('adhocmanagement::adhoc_list',compact('zones','clients','cities'));
    }
    
     public function sp_asset_assign(Request $request, $id)
    {
        $existingData = Deliveryman::findOrFail($id); 
        $zones = Zones::all();
        $dm = $id;
        $Client = Client::all();
        $client_hubs = ClientHub::where('client_id',$existingData->client_id)->get();
        $AssetMasterVehicle = AssetMasterVehicle::all();
        
        return view('adhocmanagement::sp_asset_zone_assign', compact(
            'zones',
            'dm',
            'Client',
            'AssetMasterVehicle',
            'existingData',
            'client_hubs'
        ));
    }
    
    public function edit_adhoc(Request $request, $id)
    {
        $dm = Deliveryman::where('id',$id)->first(); 
        if(!$dm){
            return back()->with('error', 'Adhoc not found.');
        }
        $zones = Zones::all();
        $Client = Client::all();
        $AssetMasterVehicle = AssetMasterVehicle::all();
    
        return view('adhocmanagement::adhoc_edit', compact(
            'zones',
            'dm',
            'Client',
            'AssetMasterVehicle',
        ));
    }
    
    
    public function update_active_date(Request $request){
          $rules = [
                'active_date' => 'required',
                'active_date_dm_id'=>'required|exists:ev_tbl_delivery_men,id'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validation error.');
            }
            $deliveryman = Deliveryman::where('id',$request->active_date_dm_id)->first();
            if (!$deliveryman) {
                return back()->with('error', 'Adhoc not found.');
            }
            if (empty($deliveryman->work_status)) {
                return back()->with('error', 'Please assign a work status first before updating the active date.');
            }
            $deliveryman->active_date = $request->active_date;
            $deliveryman->save();
             return back()->with('success', 'Active date updated successfully');
    }
    
    public function sp_asset_assign_store(Request $request, $id) //created by Gowtham.s
    {
      
        try {
            $rules = [
                // 'asset'  => 'required|string',
                // 'zone'   => 'required|integer|exists:zones,id',
                'client' => 'required|integer|exists:ev_tbl_clients,id',
                'hub' => 'required|integer|exists:ev_client_hubs,id',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validation failed. Please check the form fields.');
            }
            $deliveryman = Deliveryman::where('id',$id)->first();
            // $deliveryman->Chassis_Serial_No = $request->input('asset');
            // $deliveryman->zone_id    = $request->input('zone');
            $deliveryman->client_id  = $request->input('client');
            $deliveryman->hub_id  = $request->input('hub');
            $deliveryman->ad_client_hub_created_by = 1;
            $deliveryman->ad_client_hub_assign_at = now();
            $deliveryman->save();
            if ($deliveryman->save()) {
                return redirect()->route('admin.Green-Drive-Ev.adhocmanagement.list_of_adhoc')->with('success', 'Client assigned successfully!');
            } else {
                return back()->with('info', 'No changes were made to the Adhoc.');
            }
        } catch (\Exception $e) {

            return back()->with('error', 'Something went wrong. Please try again');
        }
        
        
    }

    
    public function adhoc_approve_status(Request $request){
        $dm = Deliveryman::where('id',$request->id)->first();
    
        if (!$dm) {
            return response()->json(['success'=>false, 'message'=>'Adhoc not found.'], 200);
        }
    
        $notVerified = [];
        
        if (!$dm->client_id) {
            $notVerified[] = 'Client';
        }
    
        if (!empty($notVerified)) {
            return response()->json(['success'=>false, 'message'=>'The following fields are not assigned: ' . implode(', ', $notVerified) . '.'], 200);
        }
    
        $dm->update([
            'approved_status' => 1,//previous update
            'approver_role' => auth()->user()->name,
            'approver_id' => auth()->user()->id,
            
            'ad_client_hub_approve' => 1,//gowtham reference update - not focus ok
            'ad_client_hub_approve_by' => auth()->user()->id,
            'ad_client_hub_approve_name' => auth()->user()->name,
            'ad_client_hub_status_at' => now(),
        ]);
        
        $this->adhoc_approve_sent($dm);//whatsapp message sent by adhoc
        
        $hr_team = DB::table('model_has_roles')
            ->join('users', 'model_has_roles.model_id', '=', 'users.id')
            ->select('users.id as user_id', 'users.name as user_name', 'users.phone as mobile_number')
            ->where('model_has_roles.role_id', 4)
            ->where('users.status', 'Active')
            ->get();
    
        $api_key = env('WHATSAPP_API_KEY');
        $url = env('WHATSAPP_API_URL', 'https://whatshub.in/api/whatsapp/send');
    
        if ($hr_team->isNotEmpty()) { 
            foreach ($hr_team as $hr) {
                $phone = preg_replace('/[^0-9]/', '', $hr->mobile_number);
                $dm_first_name = $dm->first_name ?? 'User';
                $dm_last_name = $dm->last_name ?? '';
    
                $message = "Dear " . $hr->user_name . ",\n\n" .
                        "The " . $dm_first_name . " " . $dm_last_name . " Adhoc has been approved. Approved by " . auth()->user()->name . ".\n\n" .
                        "Best regards,\n" .
                        "GreenDriveConnect";
    
                $postdata = [
                    "contact" => [
                        [
                            "number" => $phone,
                            "message" => $message,
                        ],
                    ],
                ];
    
                $headers = [
                    'Api-key' => $api_key,
                    'Content-Type' => 'application/json',
                ];
    
                // Log the request
                // Log::info("Sending WhatsApp API Request", ['url' => $url, 'data' => $postdata]);
    
                $res = Http::withHeaders($headers)->post($url, $postdata);
    
                // Log the response
                // Log::info("WhatsApp API Response: " . $res->body());
            }
        }
    
        return response()->json(['success' => true, 'message' => 'Adhoc Approved successfully.'], 200);
    }
    
    public function adhoc_deny_status(Request $request){
          $dm = Deliveryman::where('id',$request->id)->first();
    
        if (!$dm) {
            return response()->json(['success'=>false, 'message'=>'Adhoc not found.'], 200);
        }
    
    
        $dm->update([
             'approved_status' => 2,//previous update
            'approver_role' => auth()->user()->name,
            'approver_id' => auth()->user()->id,
            'deny_remarks' => $request->remarks,
            
            'ad_client_hub_deny_reason' => $request->remarks,//gowtham reference update - not focus ok
            'ad_client_hub_approve_by' => auth()->user()->id,
            'ad_client_hub_approve_name' => auth()->user()->name,
            'ad_client_hub_status_at' => now(),
        ]);
        
        $this->adhoc_deny_sent($dm);
    
        $hr_team = DB::table('model_has_roles')
            ->join('users', 'model_has_roles.model_id', '=', 'users.id')
            ->select('users.id as user_id', 'users.name as user_name', 'users.phone as mobile_number')
            ->where('model_has_roles.role_id', 4) //fetching for usertype hr
            ->where('users.status', 'Active')
            ->get();
    
        $api_key = env('WHATSAPP_API_KEY');
        $url = env('WHATSAPP_API_URL');
    
        if ($hr_team->isNotEmpty()) { 
            foreach ($hr_team as $hr) {
                $phone = preg_replace('/[^0-9]/', '', $hr->mobile_number);
                $dm_first_name = $dm->first_name ?? 'User';
                $dm_last_name = $dm->last_name ?? '';
    
                $message = "Dear " . $hr->user_name . ",\n\n" .
                        "The " . $dm_first_name . " " . $dm_last_name . " Adhoc has been cancelled. Cacelled by " . auth()->user()->name . ".\n\n" .
                        "Best regards,\n" .
                        "GreenDriveConnect";
    
                $postdata = [
                    "contact" => [
                        [
                            "number" => $phone,
                            "message" => $message,
                        ],
                    ],
                ];
    
                $headers = [
                    'Api-key' => $api_key,
                    'Content-Type' => 'application/json',
                ];
    
                // Log the request
                // Log::info("Sending WhatsApp API Request Cancelled", ['url' => $url, 'data' => $postdata]);
    
                $res = Http::withHeaders($headers)->post($url, $postdata);
    
                // Log the response
                // Log::info("WhatsApp API Response Cancelled: " . $res->body());
            }
        }
    
        return response()->json(['success' => true, 'message' => 'Adhoc Cancelled successfully.'], 200);
    }
    
    public function export_adhoc_verify_list(Request $request, $type)
    {
        if($type == 'all'){
          return Excel::download(new AdhocOnboardList($type), 'Adhoc-all-list-' . date('d-m-Y') . '.xlsx');
        }
        else if($type == 'approve'){
          return Excel::download(new AdhocOnboardList($type), 'Adhoc-approved-list-' . date('d-m-Y') . '.xlsx');
        }else if($type == 'deny'){
             return Excel::download(new AdhocOnboardList($type), 'Adhoc-rejected-list-' . date('d-m-Y') . '.xlsx');
        }else{
             return Excel::download(new AdhocOnboardList($type), 'Adhoc-pending-list-' . date('d-m-Y') . '.xlsx');
        }
    }
    
    public function log_list(Request $request){
        $reports = DB::table('ev_delivery_man_logs')
            ->join('ev_tbl_delivery_men', 'ev_delivery_man_logs.user_id', '=', 'ev_tbl_delivery_men.id')
            ->selectRaw("
                ev_delivery_man_logs.user_id,
                ev_tbl_delivery_men.first_name,
                ev_tbl_delivery_men.last_name,
                ev_tbl_delivery_men.rider_status,
                ev_tbl_delivery_men.client_id,
                CONCAT(
                    FLOOR(SUM(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out)) / 60), ' hours ',
                    MOD(SUM(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out)), 60), ' minutes'
                ) AS total_time
            ")
            ->where('ev_tbl_delivery_men.work_type', 'adhoc')
            ->groupBy('ev_delivery_man_logs.user_id', 'ev_tbl_delivery_men.first_name', 'ev_tbl_delivery_men.last_name', 'ev_tbl_delivery_men.rider_status', 'ev_tbl_delivery_men.client_id')
            ->paginate(10); // Limit to 10 per page
    
        return view('adhocmanagement::adhoc_log_reports', compact('reports'));
    }
    
    public function show_adhoc_log_report(Request $request, $dm_id)
    {
        $dm = Deliveryman::where('id', $dm_id)->first();

        return view('adhocmanagement::adhoc_log_preview', compact('dm', 'dm_id'));
    }

    private function get_adhoc_tempid_count($type)
    {
        $count = EvGenerateId::whereNotNull('temp_id')->where('user_type', $type)->count();
        return $count == 0 ? 'TMP-1001' : 'TMP-' . (1001 + $count);
    }
    
    private function get_adhoc_permanentid_count($type)
    {
        $count = EvGenerateId::whereNotNull('permanent_id')->where('user_type', $type)->count();
        return $count == 0 ? 'ADH-1001' : 'ADH-' . (1001 + $count);
    }
    
    public function update_work_status(Request $request)
    {
        $rules = [
            'work_status' => 'required|in:1,2',
            'work_status_dm_id' => 'required|exists:ev_tbl_delivery_men,id',
        ];
    
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validation error.');
        }
    
        $deliveryman = Deliveryman::find($request->work_status_dm_id);
    
        if (!$deliveryman) {
            return back()->with('error', 'Deliveryman not found.');
        }
    
        $emp_id = $request->work_status == 1 ? $this->get_adhoc_tempid_count($deliveryman->work_type) : $this->get_adhoc_permanentid_count($deliveryman->work_type);
    
        $deliveryman->work_status = $request->work_status;
        $deliveryman->emp_id = $emp_id;
        $deliveryman->emp_id_status = 1;
        if ($request->work_status == 2 && !$deliveryman->adhoc_parmenant_date) { //parmenant date update only adhoc drivers
            $deliveryman->adhoc_parmenant_date = now();
        }
        $deliveryman->save();
        
       if ($emp_id) {
            $existingRecord = EvGenerateId::where('user_id', $deliveryman->id)->first();
        
            if ($existingRecord) {
                if ($request->work_status == 2) {
                    $existingRecord->permanent_id = $emp_id;
                    $existingRecord->save();
                    Log::info("Permanent ID Updated: " . json_encode($existingRecord));
                }
            } else {
                $newRecord = EvGenerateId::create([
                    'temp_id' => $request->work_status == 1 ? $emp_id : null,
                    'permanent_id' => $request->work_status == 2 ? $emp_id : null,
                    'user_type' => $deliveryman->work_type ?? null,
                    'user_id' => $deliveryman->id ?? null
                ]);
        
                Log::info("New ID Generated: " . json_encode($newRecord));
            }
        }

    
        // Send messages
        $this->admin_message_id_generate($deliveryman);
        $this->rider_message_id_generate($deliveryman);
    
        return back()->with('success', 'Work status updated successfully');
    }

    public function adhoc_approve_sent($dm)
    {
        $phone = str_replace('+', '', $dm->mobile_number);
    
        $welcome_message = "Your Adhoc request has been approved. Further details will be shared soon.";
        $message = "Dear {$dm->first_name} {$dm->last_name},\n\n" .
            "{$welcome_message}\n\n" .
            "Best regards,\n🌿 GreenDriveConnect Team";
    
        $this->sendWhatsAppMessage($phone, $message);
    }
    
   public function adhoc_deny_sent($dm)
    {
        $phone = str_replace('+', '', $dm->mobile_number);
    
        $reject_message = "Your Adhoc request has been rejected.";
        $message = "Dear {$dm->first_name} {$dm->last_name},\n\n" .
            "{$reject_message}\n\n" .
            "📌 **Reason:** {$dm->deny_remarks}\n\n" .
            "Best regards,\n🌿 GreenDriveConnect Team";
    
        $this->sendWhatsAppMessage($phone, $message);
    }


    
   public function admin_message_id_generate($dm)
    {
        $phone = '919606945066';  
        if ($dm->work_status == 2) {
            $role = 'Adhoc Driver';
        } else {
            $role = 'Adhoc Helper';
        }
        $message = "Dear Admin,\n\n" .
            "We are pleased to inform you that you have successfully onboarded a **{$role}** in our team! 🚀\n\n" .
            "👤 **Name:** {$dm->first_name} {$dm->last_name}\n" .
            "🆔 **{$role} ID:** {$dm->emp_id}\n\n" .
            "Best regards,\n🌿 **GreenDriveConnect Team**";
    
        $this->sendWhatsAppMessage($phone, $message);
    }
    
    public function rider_message_id_generate($dm)
    {
        $phone = str_replace('+', '', $dm->mobile_number);
        if ($dm->work_status == 2) {
            $role = 'Adhoc Driver';
            $welcome_message = "We are excited to welcome you as a **{$role}** at **GreenDriveConnect**! 🚴‍♂️📦";
        } else {
            $role = 'Adhoc Helper';
            $welcome_message = "Welcome aboard as a **{$role}** at **GreenDriveConnect**! 🎖️";
        }
        $message = "🎉 Dear {$dm->first_name} {$dm->last_name},\n\n" .
            "{$welcome_message}\n\n" .
            "🆔 **Your {$role} ID:** {$dm->emp_id}\n\n" .
            "We appreciate your dedication and look forward to a great journey together! 🚀\n\n" .
            "Best regards,\n🌿 **GreenDriveConnect Team**";
        $this->sendWhatsAppMessage($phone, $message);
    }
    
    private function sendWhatsAppMessage($phone, $message)
    {
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
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        Log::info('WhatsApp API Response:', [
            'status_code' => $http_code,
            'response' => json_decode($response, true),
        ]);
    }

    

    public function store(Request $request): RedirectResponse
    {
    }

    public function show($id)
    {
        return view('adhocmanagement::show');
    }


    public function edit($id)
    {
        return view('adhocmanagement::edit');
    }

    public function update(Request $request, $id): RedirectResponse
    {
    }

    public function destroy($id)
    {
    }
}
