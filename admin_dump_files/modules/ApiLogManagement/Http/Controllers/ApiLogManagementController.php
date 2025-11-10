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

        $data = $request->except('_token');

           $settings = [
            'X_API_KEY' => $request->x_api_key,
            'API_CLUB_PRODUCTION' => $request->production_url,
            'API_CLUB_TEST' => $request->test_url,
            'ADHAAR_SEND_OTP_ENDPOINT' => $request->aadhaar_send_otp_endpoint,
            'ADHAAR_VERIFY_OTP_ENDPOINT' => $request->aadhaar_verify_otp_endpoint,
            'PAN_VERIFY_ENDPOINT' => $request->pan_verify_endpoint,
            'LICENSE_VERIFY_ENDPOINT' => $request->license_verify_endpoint,
            'BANK_VERIFY_ENDPOINT' => $request->bank_verify_endpoint,
            'PAN_VERIFY' => $request->pan_verify == 'on' ? 1 : 0,
            'LICENSE_VERIFY' => $request->license_verify == 'on' ? 1 : 0,
            'BANK_VERIFY' => $request->bank_verify == 'on' ? 1 : 0,
            'ADHAAR_CARD_VERIFY' => $request->adhaar_verify == 'on' ? 1 : 0,
        ];
        
        foreach ($settings as $key => $value) {
            EvApiClubSetting::updateOrInsert(
                ['key_name' => $key], // Search condition
                ['value' => $value]   // Update/Insert data
            );
        }

    
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
