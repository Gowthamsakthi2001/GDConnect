<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Rules\Password;
use App\Models\BusinessSetting;

class SettingController extends Controller
{
    // public function app_version_manage_view()
    // {
    //     cs_set('theme', [
    //         'title' => 'App Version Update Settings',
    //         'description' => 'App Version Update Settings Page',
    //         'breadcrumb' => [
    //             [
    //                 'name' => 'Dashboard',
    //                 'link' => route('admin.dashboard'),
    //             ], 
    //             [
    //                 'name' => 'Update Password',
    //                 'link' => false,
    //             ],
    //         ],
    //     ]);
    
    //     // Fetching settings
    //     $app_live_version = BusinessSetting::where('key_name', 'app_live_version')->value('value');
    //     $app_test_version = BusinessSetting::where('key_name', 'app_test_version')->value('value');
    //     $live_latest_apk_url = BusinessSetting::where('key_name', 'live_latest_apk_url')->value('value');
    //     $test_latest_apk_url = BusinessSetting::where('key_name', 'test_latest_apk_url')->value('value');
    
    //     // Pass data to view using compact()
    //     return view('auth::settings.app_version_mange.app_version_manage_view', compact(
    //         'app_live_version', 
    //         'app_test_version', 
    //         'live_latest_apk_url',
    //         'test_latest_apk_url'
    //     ))->with('active_tab', 'app_version_settings_tab');
    // }
    
        public function app_version_manage_view()
    {
        cs_set('theme', [
            'title' => 'App Version Update Settings',
            'description' => 'App Version Update Settings Page',
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ], 
                [
                    'name' => 'Update Password',
                    'link' => false,
                ],
            ],
        ]);
    
        // Fetching settings
        $app_live_version = BusinessSetting::where('key_name', 'app_live_version')->value('value');
        $app_test_version = BusinessSetting::where('key_name', 'app_test_version')->value('value');
        $live_latest_apk_url = BusinessSetting::where('key_name', 'live_latest_apk_url')->value('value');
        $test_latest_apk_url = BusinessSetting::where('key_name', 'test_latest_apk_url')->value('value');
        
        $agent_app_live_version = BusinessSetting::where('key_name', 'b2b_agent_app_live_version')->value('value');
        $agent_app_test_version = BusinessSetting::where('key_name', 'b2b_agent_app_test_version')->value('value');
        $agent_live_latest_apk_url = BusinessSetting::where('key_name', 'b2b_agent_live_latest_apk_url')->value('value');
        $agent_test_latest_apk_url = BusinessSetting::where('key_name', 'b2b_agent_test_latest_apk_url')->value('value');
        
        $rider_app_live_version = BusinessSetting::where('key_name', 'b2b_rider_app_live_version')->value('value');
        $rider_app_test_version = BusinessSetting::where('key_name', 'b2b_rider_app_test_version')->value('value');
        $rider_live_latest_apk_url = BusinessSetting::where('key_name', 'b2b_rider_live_latest_apk_url')->value('value');
        $rider_test_latest_apk_url = BusinessSetting::where('key_name', 'b2b_rider_test_latest_apk_url')->value('value');
        $b2b_app_password = BusinessSetting::where('key_name', 'b2b_app_version_password')->value('value');
        // Pass data to view using compact()
        return view('auth::settings.app_version_mange.app_version_manage_view', compact(
            'app_live_version', 
            'app_test_version', 
            'live_latest_apk_url',
            'test_latest_apk_url',
            'agent_app_live_version', 
            'agent_app_test_version', 
            'agent_live_latest_apk_url',
            'agent_test_latest_apk_url',
            'rider_app_live_version', 
            'rider_app_test_version', 
            'rider_live_latest_apk_url',
            'rider_test_latest_apk_url',
            'b2b_app_password'
        ))->with('active_tab', 'app_version_settings_tab');
    }
    
    public function app_version_manage_update(Request $request)
    {
        $request->validate([
            'app_live_version' => 'required|string|max:255',
            'app_test_version' => 'required|string|max:255',
            'app_live_download_url' => 'required|string',
            'app_test_download_url' => 'required|string',
            'password' => 'required|string',
        ]);
    
        $app_version_password = BusinessSetting::where('key_name', 'app_version_update_password')->value('value');
    
        if ($request->password !== $app_version_password) {
            return response()->json([
                'status' => false,
                'message' => 'Incorrect password!',
            ], 403);
        }
    
        BusinessSetting::updateOrCreate(
            ['key_name' => 'app_live_version'],
            ['value' => $request->app_live_version]
        );
    
        BusinessSetting::updateOrCreate(
            ['key_name' => 'app_test_version'],
            ['value' => $request->app_test_version]
        );
    
        BusinessSetting::updateOrCreate(
            ['key_name' => 'live_latest_apk_url'],
            ['value' => $request->app_live_download_url]
        );
    
        BusinessSetting::updateOrCreate(
            ['key_name' => 'test_latest_apk_url'],
            ['value' => $request->app_test_download_url]
        );
    
        return response()->json([
            'status' => true,
            'message' => 'App Version Management settings updated successfully!',
        ]);
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

}