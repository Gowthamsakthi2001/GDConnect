<?php

use Illuminate\Support\Facades\Route;
use Modules\B2B\Http\Controllers\B2BDashboardController;
use Modules\B2B\Http\Controllers\B2BController;
use Modules\B2B\Http\Controllers\B2BVehicleController;
use Modules\B2B\Http\Controllers\B2BTrackingController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::group([], function () {
//     Route::resource('b2b', B2BController::class)->names('b2b');
// });
// Route::get('/b2b/dashboard', [B2BDashboardController::class, 'index'])->name('b2b.dashboard');

// Route::get('/b2b/login', [B2BController::class, 'login'])->name('b2b.login');


Route::prefix('b2b')
    ->middleware(['b2b.auth'])
    ->name('b2b.')
    ->group(function () {
        Route::get('/dashboard', [B2BDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard-data', [B2BDashboardController::class, 'dashboard_data'])->name('dashboard_data');
         Route::get('/admin-dashboard', [B2BDashboardController::class, 'admin_dashboard'])->name('admin_dashboard');
        Route::post('/store/rider', [B2BVehicleController::class, 'store_rider'])->name('store_rider');
        Route::post('/vehicle_list/recovery-request', [B2BVehicleController::class, 'recovery_request_functionality'])->name('recovery-request_functionality');
        
        Route::get('/export/vehicle-request', [B2BVehicleController::class, 'export_vehicle_request'])->name('export_vehicle_request');
        Route::get('/vehicle_list',[B2BVehicleController::class,'vehicle_list'])->name('vehiclelist');
        Route::get('/rider/details', [B2BVehicleController::class, 'getRiderDetails'])->name('get_rider_details');
        Route::get('/rider/list', [B2BVehicleController::class, 'rider_list'])->name('rider_list');
        Route::get('/rider/view/{id}', [B2BVehicleController::class, 'rider_view'])->name('rider_view');
        Route::get('/riderlist/export', [B2BVehicleController::class, 'rider_export'])->name('rider_export');
        Route::get('/vehicle-list/export', [B2BVehicleController::class, 'export_vehicle_details'])->name('export_vehicle_details');
        Route::get('/settings/app-version-manage', [B2BController::class, 'app_version_manage_view'])->name('settings.app_version_manage');
        Route::get('/get-zones', [B2BController::class, 'get_zones'])->name('get_zones');
        
        Route::get('/returned-list',[B2BVehicleController::class,'returned_list'])->name('returned.list');
        Route::get('/returned-view/{id}',[B2BVehicleController::class,'returned_check_view'])->name('returned.checkview');
        Route::get('/returned-export',[B2BVehicleController::class,'returned_export'])->name('returned_export');
        
        Route::post(
                '/settings/app-version-manage/rider-update',
                [B2BController::class, 'updateRiderAppSettings']
            )->name('settings.app_version_manage.update_rider');
            
            // Agent App settings update
            Route::post(
                '/settings/app-version-manage/agent-update',
                [B2BController::class, 'updateAgentAppSettings']
            )->name('settings.app_version_manage.update_agent');
        
        
        Route::get('/vehicle_list/accident_report/{id}',[B2BVehicleController::class,'accident_report_view'])->name('accident_report');
        Route::post('/vehicle_list/service_request/submit', [B2BVehicleController::class, 'service_request_functionality'])->name('service_request_functionality');
        Route::post('/vehicle_list/return_request/submit', [B2BVehicleController::class, 'return_request_functionality'])->name('return_request_functionality');
        Route::post('/vehicle_list/accident-report', [B2BVehicleController::class, 'accident_report_functionallity'])->name('accident-report_functionality');
        Route::get('/vehicle_list/return_request/{id}',[B2BVehicleController::class,'vehicle_return_request'])->name('return_request');
        Route::post('/create/vehicle-request/',[B2BVehicleController::class,'create_vehicle_request'])->name('create_vehicle_request');
        
        Route::get('/vehicle_details/view/{id}',[B2BVehicleController::class,'vehicle_details_view'])->name('vehicle_details_view');//updated by Mugesh.B
        Route::get('/rider_details/view/{id}',[B2BVehicleController::class,'rider_details_view'])->name('rider_details_view');//updated by Mugesh.B
        Route::get('/vehicle_list/service_request/{id}',[B2BVehicleController::class,'vehicle_service_request'])->name('service_request');//updated by Mugesh.B
        Route::get('/vehicle_list/recovery_request/{id}',[B2BVehicleController::class,'vehicle_recovery_request'])->name('recovery_request');//updated by Mugesh.B
        
        
        Route::prefix('vehicle-request') //updated by Mugesh.B
        ->as('vehicle_request.')
        ->controller(B2BVehicleController::class)
        ->group(function () {
            Route::get('/vehicle-request/list', 'vehicle_request_list')->name('vehicle_request_list');
            Route::get('/vehicle-request/view/{id}', 'vehicle_request_view')->name('vehicle_request_view');
            Route::get('/add_rider','create')->name('add_rider');
        });
        
        
        Route::get('/help-support',[B2BController::class,'help'])->name('help');//updated by Mugesh.B
        
        
        
        Route::prefix('reports') //updated by Mugesh.B
        ->as('reports.')
        ->controller(B2BReportController::class)
        ->group(function () {
        Route::get('/index','index')->name('index');
        Route::get('/vehicle-usage','vehicle_usage')->name('vehicle_usage');
        });
    
    
        Route::get('/tracking', [B2BTrackingController::class, 'mobitra_tracking'])->name('tracking');
        
        Route::prefix('mobitra-api')->as('mobitra_api.')->group(function () {
         Route::get('/settings',[B2BTrackingController::class, 'mobitra_api_setting'])->name('mobitra_api_setting');
         Route::post('/setting-mode-update',[B2BTrackingController::class, 'mobitra_api_mode_update'] )->name('mobitra_api_mode_update');
         Route::post('/settings-update', [B2BTrackingController::class, 'mobitra_api_settings_update'])->name('mobitra_api_settings_update');
         Route::get('/authenticate', [B2BTrackingController::class, 'authenticate'])->name('authenticate');
         Route::get('/get-user-data', [B2BTrackingController::class, 'getUserData'])->name('get_user_data');
         Route::get('/get-user-devices', [B2BTrackingController::class, 'getUserDevicesJson'])->name('get_user_devices');
         Route::get('/get-role-based-imei-data', [B2BTrackingController::class, 'getRoleBasedImeiData'])->name('get_role_based_imei_data');
         Route::get('/get-vehicle-status-data', [B2BTrackingController::class, 'getVehicleStatusData'])->name('get_vehicle_status_data');
         Route::get('/get-notifications', [B2BTrackingController::class, 'getNotifications'])->name('get_notifications');
         Route::get('/update-vehicles-json', [B2BTrackingController::class, 'getVehicleStatusDataJson'])->name('update_vehicles_json');
         Route::get('/mobitra-tracking-json', [B2BTrackingController::class, 'mobitra_tracking_json'])->name('tracking_json');
        
    });


    });
    
    
    Route::get('/b2b-agent/password/reset/{token}', function ($token) {
    $email = request()->query('email');
        return view('b2b::auth.password-reset', [
        'token' => $token,
        'email' => $email
    ]);})->name('b2b-agent.password.reset');
    
     Route::post('reset-password', [B2BController::class, 'resetPassword'])->name('b2b.resetPassword');
     Route::get('forget-password',[B2BController::class, 'forgot_password'])->name('b2b.forgot_password');
     Route::post('/b2b-agent/forgot-password', [B2BController::class, 'forgotPasswordWeb'])->name('b2b.forgot_password.submit');
    Route::post('/b2b/password/reset', [B2BController::class, 'resetPasswordWeb'])->name('b2b.Customer_resetPassword');
    Route::get('/b2b-agent/password/reset/{token}', [B2BController::class, 'showResetForm'])->name('b2b.password.reset.form');
     Route::get('/b2b-agent/password/reset/success', [B2BController::class, 'successPage'])->name('b2b-agent.password.reset.success');
     
     
     
     
     
     

        
    


    






    
    

    