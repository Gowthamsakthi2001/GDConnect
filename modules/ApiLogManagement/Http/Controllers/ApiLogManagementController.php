<?php

namespace Modules\ApiLogManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Modules\ApiLogManagement\DataTables\AdhaarLogDataTable;
use Modules\ApiLogManagement\DataTables\LicenseLogDataTable;
use Modules\ApiLogManagement\DataTables\BankLogDataTable;
use Modules\ApiLogManagement\DataTables\PancardLogDataTable;
use App\Models\EvApiClubSetting;
use Illuminate\Support\Facades\Http;//updated by Gowtham.S
use App\Services\AuditHeader;//updated by Gowtham.S
use App\Models\SidebarModule;//updated by Gowtham.S
use App\Models\User;//updated by Gowtham.S

class ApiLogManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('apilogmanagement::index');
    }
    
    public function api_log_settings()
    {
        return view('apilogmanagement::api_club_settings');
    }
    
    public function adhaar_api_logs(AdhaarLogDataTable $dataTable){
        return $dataTable->render('apilogmanagement::adhaar_api_log');
    }
    
    public function license_api_logs(LicenseLogDataTable $dataTable){
        return $dataTable->render('apilogmanagement::license_verify_log');
    }
    
    public function bank_detail_api_logs(BankLogDataTable $dataTable){
        return $dataTable->render('apilogmanagement::bankDetail_verify_log');
    }
    
    public function pancard_api_logs(PancardLogDataTable $dataTable){
        return $dataTable->render('apilogmanagement::pancard_verify_log');
    }
    
     public function user_activity_api_logs()
    {
        return view('apilogmanagement::user_activity_log');
    }
    
    public function get_user_activity_logs(Request $request)
    {

        $fromDate = $request->from_date ?? '';
        $toDate   = $request->to_date ?? '';
        $sortBy   = $request->sort_by ?? 'created_at';
        $page     = $request->page ?? 1;
        $limit    = $request->limit ?? 30;

        $offset = ($page - 1) * $limit;
    
        try {

            $url = env('SERVICES_AUDIT_BASE_URL').env('GET_USER_ACTIVITY_ENDPOINT');
            $token = AuditHeader::make();

            $response = Http::withHeaders([
                'X-Audit-Token' => $token,
            ])
            ->timeout(15)
            ->get($url, [
                'from_date' => $fromDate,
                'to_date'   => $toDate,
                'sort_by'   => $sortBy,
                'page'      => $page,
                'limit'     => $limit,
            ]);

             $totalLogCount = 0;
            if ($response->successful()) {
                $data = $response->json();
                // dd($data['data']);
                 $totalLogCount = $data['data']['total_items']; 
            
                $logData = [];
            
                if (!empty($data['data']['logs'])) {
                    foreach ($data['data']['logs'] as $log) {
                        $module_name = SidebarModule::where('id', $log['module_id'])->value('module_name') ?? 'Module';
                        $userName = User::where('id', $log['user_id'])->value('name') ?? 'User';
            
                        $special_chars = ["-", ".","_"];
                        $lower_pageName = str_replace($special_chars, ' ', $log['page_name'] ?? '');
            
                        $logData[] = [
                            'id' => $log['id'] ?? null,
                            'module_name' => $module_name,
                            'short_description' => $log['short_description'] ?? 'N/A',
                            'long_description' => $log['long_description'] ?? 'N/A',
                            'role' => $log['role'] ?? '',
                            'user_name' => $userName,
                            'page_name' => ucfirst($lower_pageName),
                            'created_at'=> date('d M Y h:i:s A',strtotime($log['created_at'])),
                            'updated_at'=> date('d M Y h:i:s A',strtotime($log['updated_at'])),
                        ];
                    }
                }
            
                return response()->json([
                    'success' => true,
                    'data' => $logData,
                    'total_log_count'=>$totalLogCount
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch activity logs',
                    'error' => $response->body()
                ], $response->status());
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('apilogmanagement::create');
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
        return view('apilogmanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('apilogmanagement::edit');
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
    
    public function api_log_settings_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'x_api_key' => 'required|string',
            'production_url' => 'required|url',
            'test_url' => 'required|url',
            'aadhaar_send_otp_endpoint' => 'required|string',
            'aadhaar_verify_otp_endpoint' => 'required|string',
            'pan_verify_endpoint' => 'required|string',
            'license_verify_endpoint' => 'required|string',
            'bank_verify_endpoint' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $newSettings = [
            'X_API_KEY'                  => $request->x_api_key,
            'API_CLUB_PRODUCTION'        => $request->production_url,
            'API_CLUB_TEST'              => $request->test_url,
            'ADHAAR_SEND_OTP_ENDPOINT'   => $request->aadhaar_send_otp_endpoint,
            'ADHAAR_VERIFY_OTP_ENDPOINT' => $request->aadhaar_verify_otp_endpoint,
            'PAN_VERIFY_ENDPOINT'        => $request->pan_verify_endpoint,
            'LICENSE_VERIFY_ENDPOINT'    => $request->license_verify_endpoint,
            'BANK_VERIFY_ENDPOINT'       => $request->bank_verify_endpoint,
            'PAN_VERIFY'                 => $request->pan_verify == 'on' ? 1 : 0,
            'LICENSE_VERIFY'             => $request->license_verify == 'on' ? 1 : 0,
            'BANK_VERIFY'                => $request->bank_verify == 'on' ? 1 : 0,
            'ADHAAR_CARD_VERIFY'         => $request->adhaar_verify == 'on' ? 1 : 0,
        ];
    
        $oldSettingValues = EvApiClubSetting::whereIn('key_name', array_keys($newSettings))
            ->pluck('value', 'key_name')
            ->toArray();
    
        $changes = [];
        foreach ($newSettings as $key => $newValue) {
            $oldValue = $oldSettingValues[$key] ?? null;
    
            if ($oldValue != $newValue) {
                $changes[] = "$key: '$oldValue' → '$newValue'";
            }
        }
    
        foreach ($newSettings as $key => $value) {
            EvApiClubSetting::updateOrInsert(
                ['key_name' => $key],
                ['value' => $value]
            );
        }
    
        $user_id  = auth()->user()->id;
        $roleName = auth()->user()->get_role->name ?? 'Unknown';
    
        $longDescription = empty($changes)
            ? "API Club Settings updated by " . auth()->user()->name . " ($roleName). No changes detected."
            : "API Club Settings updated by " . auth()->user()->name . " ($roleName). Changes: " . implode("\n", $changes);
            
        audit_log_after_commit([
            'module_id'         => 1,
            'short_description' => 'API Club Settings updated by ' . auth()->user()->name,
            'long_description'  => $longDescription,
            'role'              => $roleName,
            'user_id'           => $user_id,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'api_settings.update',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
    
        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    
    public function api_log_mode_update(Request $request)
    {
        // if($request->api_log_mode == false){
        //     return response()->json(['success'=>false,'message'=>'Api Club Mode field is required'],200);
        // }
        $updated = EvApiClubSetting::where('key_name', 'API_CLUB_MODE')->update([
            'value' => $request->api_log_mode
        ]);
        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Settings updated successfully'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to update settings'], 200);
        }
        
    }

}
