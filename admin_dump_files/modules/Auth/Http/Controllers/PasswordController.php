<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Rules\Password;
use App\Models\BusinessSetting;

class PasswordController extends Controller
{
    /**
     * showing update password form
     */

    /**
     * Show profile settings page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        cs_set('theme', [
            'title' => 'Password Update Settings',
            'description' => 'Password Update Settings Page',
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ], [
                    'name' => 'Update Password',
                    'link' => false,
                ],
            ],
        ]);

        return view('auth::profile.password', [
            'active_tab' => 'password',
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', new Password, 'confirmed'],
        ]);
        $user = auth()->user();
        if (! isset($request->current_password) || ! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'The provided password does not match your current password.']);
        }
        $user->forceFill([
            'password' => Hash::make($request->password),
            'password_changed_at'=>now()
        ])->save();
        // Session::flash('success', 'Your Password has been updated. Please Login Again');

        // return redirect()->back();
        
        if($user){
             Auth::logout();

               return redirect()->route('login')
               ->with('success', 'Your Password has been updated. Please Login Again!');
        }else{
            return redirect()->back();
        }
    }
   public function sms_settings_view()
    {
        cs_set('theme', [
            'title' => 'SMS Update Settings',
            'description' => 'SMS Update Settings Page',
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
        $sms_temp_id = BusinessSetting::where('key_name', 'sms_temp_id')->value('value');
        $sms_auth_id = BusinessSetting::where('key_name', 'sms_auth_id')->value('value');
        $sms_pe_registration_id = BusinessSetting::where('key_name', 'sms_pe_registration_id')->value('value');
    
        // Pass data to view using compact()
        return view('auth::profile.sms_settings_tab', compact(
            'sms_temp_id', 
            'sms_auth_id', 
            'sms_pe_registration_id'
        ))->with('active_tab', 'sms_settings_tab');
    }

    

    public function sms_settings_update(Request $request)
    {
        $request->validate([
            'sms_temp_id' => 'required|string|max:255',
            'sms_auth_id' => 'required|string|max:255',
            'sms_pe_registration_id' => 'required|string|max:255',
        ]);
    
        BusinessSetting::updateOrCreate(
            ['key_name' => 'sms_temp_id'],
            ['value' => $request->sms_temp_id]
        );
    
        BusinessSetting::updateOrCreate(
            ['key_name' => 'sms_auth_id'],
            ['value' => $request->sms_auth_id]
        );
    
        BusinessSetting::updateOrCreate(
            ['key_name' => 'sms_pe_registration_id'],
            ['value' => $request->sms_pe_registration_id]
        );
    
        return redirect()->back()->with('success', 'SMS settings updated successfully!');
    }

    
}
