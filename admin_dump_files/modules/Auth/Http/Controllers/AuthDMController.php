<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Inventory\Entities\Expense;
use Modules\Inventory\Entities\InventoryParts;
use Modules\Purchase\Entities\PurchaseDetail;
use Modules\VehicleMaintenance\Entities\VehicleMaintenance;
use Modules\VehicleMaintenance\Entities\VehicleMaintenanceDetail;
use Modules\VehicleManagement\Entities\LegalDocumentation;
use Modules\VehicleManagement\Entities\PickupAndDrop;
use Modules\VehicleManagement\Entities\VehicleRequisition;
use Modules\VehicleRefueling\Entities\FuelRequisition;
use Illuminate\Http\Request;
use App\Models\LoginTimeRecord;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\City\Entities\City;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class AuthDMController extends Controller
{
    
    public function login(Request $request)
    {
        // Validate the input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        // Extract credentials
        $credentials = $request->only('email', 'password');
    
        
       if (Auth::guard('customer')->attempt($credentials)) {
            $customer = Auth::guard('customer')->user();
        
            if (in_array($customer->role, ['1', '2']) && $customer->delete_status == 0) {
                return redirect()->route('auth.customer.vehicle-ticket.create');
            } else {
                Auth::guard('customer')->logout();
                return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
            }
        }

    

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    
    
            public function b2b_login_view(Request $request)
    {
        return view('auth::b2b_login');
    }
    
    
    
      public function b2b_login_check(Request $request)
    {
        // Validate input
        $request->validate([
            'email'      => 'required|email',
            'password'   => 'required',
            'login_type' => 'required|in:master,zone', // master or zone
        ]);

        $credentials = $request->only('email', 'password');
        $guard = $request->login_type;

        // Check if email belongs to the selected guard
        $model = $guard === 'master'
            ? \App\Models\B2BCustomerLogin::where('type', 'master')
            : \App\Models\B2BCustomerLogin::where('type', 'zone');

        $userExists = $model->where('email', $request->email)->exists();

        if (! $userExists) {
            return back()
                ->withErrors(['email' => 'This email is not registered for ' . ucfirst($guard) . ' login.'])
                ->withInput();
        }
        

        // Attempt login
        if (Auth::guard($guard)->attempt($credentials)) {
            $customer = Auth::guard($guard)->user();

            Auth::shouldUse($guard);

            return redirect()->route('b2b.dashboard')
                ->with('success', 'Login successful as ' . ucfirst($customer->type));
        }

        // Invalid credentials
        return back()
            ->withErrors(['email' => 'Invalid credentials'])
            ->withInput();
    }
    
    
    
    public function b2b_logout()
    {
        // Detect which guard is currently authenticated
        $guard = Auth::guard('master')->check() ? 'master' : (Auth::guard('zone')->check() ? 'zone' : null);
    
        if ($guard) {
            Auth::guard($guard)->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
    
        return redirect('/b2b/login')->with('success', 'Logout successful');
    }
    
    
    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect('/ticket-portal/login')->with('success','Logout successfully');
    }
    
    public function hr_manager_login_view(Request $request)
    {
        return view('auth::hr_manager_login');
    }
    
    public function hr_manager_login_check(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            $customer = Auth::user();
    
            if (in_array($customer->role, ['12']) && $customer->delete_status == 0) {
    
            } else {
                Auth::logout(); // <- logout from the default guard
                return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
            }
        }
    
    
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }


}