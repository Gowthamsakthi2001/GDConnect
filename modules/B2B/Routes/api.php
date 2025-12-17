<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\B2B\Http\Controllers\Api\V1\B2BRider\B2BRiderAuthController;
use Modules\B2B\Http\Controllers\Api\V1\B2BRider\B2BRiderController;
use Modules\B2B\Http\Controllers\Api\V1\B2BAgent\B2BAgentAuthController;
use Modules\B2B\Http\Controllers\Api\V1\B2BAgent\B2BAgentController;
use Modules\B2B\Http\Controllers\B2BController;
use App\Services\FirebaseNotificationService;
/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::prefix('v1/b2bagent')->name('api.b2bagent')->group(function () {
    
    Route::middleware('b2b.guard:agent')->group(function () { 
    
        Route::get('deployment/request-list', [B2BAgentController::class, 'request_list'])->name('request_list');// 
        Route::get('deployment/request-view/{id}', [B2BAgentController::class, 'request_view'])->name('request_view');// 
        Route::post('update-request', [B2BAgentController::class, 'update_request'])->name('update_request');
        Route::post('update-vehicle', [B2BAgentController::class, 'vehicle_update'])->name('vehicle_update');//updated by logesh
        Route::post('assign-vehicle', [B2BAgentController::class, 'assign_vehicle'])->name('assign_vehicle');
        Route::get('get-return-request-list', [B2BAgentController::class, 'get_return_request_list'])->name('get_return_request_list');// 
        Route::post('update-return-request', [B2BAgentController::class, 'update_return_request'])->name('update_return_request');
    
        Route::post('/fcm-token/update', [B2BAgentController::class, 'fcm_token_update'])->name('fcm_token_update');
        Route::post('agent-logout', [B2BAgentAuthController::class, 'logout'])->name('agent_logout');
        
        Route::get('get-dashboard-data', [B2BAgentController::class, 'get_dashboard_data'])->name('get_dashboard_data');// 
        Route::get('get-vehicle-list', [B2BAgentController::class, 'get_vehicle_list'])->name('get_vehicle_list');// 
        Route::get('get-vehicle-data', [B2BAgentController::class, 'get_vehicle_data'])->name('get_vehicle_data');// 
        
        Route::get('get-notifications', [B2BAgentController::class, 'get_notification_data'])->name('get_notification_data');
        Route::get('notification/status-update/{notification_id}/{status}', [B2BAgentController::class, 'notification_status_update'])->name('notification_status_update');
          Route::get('get-agent-logs', [B2BAgentController::class, 'get_agent_logs'])->name('get_agent_logs');
    });
    
    
    Route::post('agent-login', [B2BAgentAuthController::class, 'agent_login'])->name('agent_login');
    Route::post('forgot-password', [B2BAgentAuthController::class, 'forgotPassword'])->name('forgotPassword');
    Route::post('reset-password', [B2BAgentAuthController::class, 'resetPassword'])->name('resetPassword');
    
     Route::post('agent-send-otp', [B2BAgentAuthController::class, 'agent_send_otp'])->name('agent_send_otp');
     Route::post('otp-verification', [B2BAgentAuthController::class, 'otp_verification'])->name('otp-verification');
});

    Route::post('/send-qr', [B2BController::class, 'sendQrCodeWhatsApp'])->name('sendQrCodeWhatsApp');


Route::prefix('v1/b2brider')->name('api.b2brider')->group(function () {
    Route::middleware('b2b.guard:rider')->group(function () {
    
        Route::post('/fcm-token/update', [B2BRiderAuthController::class, 'update_fcm_token'])->name('update_fcm_token');
        Route::post('/push-notification-test', [B2BRiderAuthController::class, 'push_notification_test'])->name('update_fcm_token');
        
        Route::post('rider-logout', [B2BRiderAuthController::class, 'logout'])->name('rider_logout');
        Route::get('rider-profile', [B2BRiderController::class, 'rider_profile'])->name('rider_profile');
        Route::post('update-rider-profile', [B2BRiderController::class, 'update_rider_profile'])->name('update_rider_profile');
        Route::post('update-rider-kyc', [B2BRiderController::class, 'update_rider_kyc'])->name('update_rider_kyc');
        Route::post('update-profile-image', [B2BRiderController::class, 'update_profile_image'])->name('update_profile_image');
        Route::get('get-rider-qr', [B2BRiderController::class, 'get_rider_qr'])->name('get_rider_qr');
        Route::get('get-categories/{id?}', [B2BRiderController::class, 'get_categories'])->name('get_categories');
        Route::post('add-ticket', [B2BRiderController::class, 'store_ticket'])->name('store_ticket');
        Route::get('get-ticket-history', [B2BRiderController::class, 'get_ticket_history'])->name('get_ticket_history');
        Route::get('get-vehicle-details', [B2BRiderController::class, 'get_vehicle_details'])->name('get_vehicle_details');
        
        Route::get('get-ticket-status', [B2BRiderController::class, 'get_ticket_status'])->name('get_ticket_status');
        
        Route::get('get-notifications', [B2BRiderController::class, 'get_notification_list'])->name('get_notification_list');
        Route::get('notification/status-update/{notification_id}/{status}', [B2BRiderController::class, 'notification_status_update'])->name('notification_status_update');
         Route::get('get-rider-logs', [B2BRiderController::class, 'get_rider_logs'])->name('get_rider_logs');
    });
    
    
     Route::post('rider-send-otp', [B2BRiderAuthController::class, 'rider_send_otp'])->name('rider_send_otp');
     Route::post('otp-verification', [B2BRiderAuthController::class, 'otp_verification'])->name('otp-verification');
});


Route::get('v1/b2b/app-validation/{user_type}/{app_mode}/{app_version}', [B2BAgentAuthController::class, 'user_app_validation']);

Route::middleware('b2b.guard:agent')->get('/test-middleware', function() {
    print_r('hello');exit;
});

Route::get('proxy', function (Request $request) {
    $url = $request->query('url'); // e.g. b2b/aadhar_images/xxx.png
    
    if (!$url) {
        return response()->json(['error' => 'No URL provided'], 400);
    }

    // Force it inside public/
    $filePath = public_path($url);

    if (!file_exists($filePath)) {
        return response()->json(['error' => 'File not found'], 404);
    }

    $mime = mime_content_type($filePath);

    return response()->file($filePath, [
        'Access-Control-Allow-Origin' => '*',
        'Content-Type' => $mime,
    ]);
});

