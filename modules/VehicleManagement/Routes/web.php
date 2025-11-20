<?php

use Illuminate\Support\Facades\Route;
use Modules\VehicleManagement\Http\Controllers\PickupAndDropController;
use Modules\VehicleManagement\Http\Controllers\VehicleRequisitionController;
use Modules\VehicleManagement\Http\Controllers\VehicleRouteDetailController; 
use Modules\VehicleManagement\Http\Controllers\VehicleManageController;
use Modules\VehicleManagement\Http\Controllers\VehicleTypeController;
use Modules\VehicleManagement\Http\Controllers\MobitraApiController;
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

Route::prefix('admin/vehicle')->as('admin.vehicle.')->middleware('auth')->group(function () {

    // type
    Route::resource('/type', 'VehicleTypeController')->except(['show']);
    
     Route::post('/type/type-status-update', [VehicleTypeController::class, 'update_status'])->name('type.status_update');
     Route::post('/type/delete', [VehicleTypeController::class, 'destroy'])->name('type.destroy');
     Route::get('/type/export', [VehicleTypeController::class, 'export_vehicle_type_lists'])->name('type.export');

    //document type
    Route::resource('/document-type', 'DocumentTypeController')->except(['show'])->parameter('document-type', 'document_type');
    //division
    Route::resource('/division', 'VehicleDivisionController')->except(['show']);
    //vehicle routes
    Route::prefix('route-detail')->as('route-detail.')->group(function () {
        Route::resource('/', 'VehicleRouteDetailController')->except(['show'])->parameter('', 'route_detail');
        Route::get('/{route_detail}', [VehicleRouteDetailController::class, 'getRouteByID'])->name('detail');
    });
    //pickup and drop requisition
    Route::prefix('pick-drop')->as('pick-drop.')->group(function () {
        Route::resource('/', 'PickupAndDropController')->except(['show'])->parameter('', 'pick_drop');
        Route::post('{pick_drop}/status-update', [PickupAndDropController::class, 'statusUpdate'])->name('status-update');
    });
    // rta-office
    Route::resource('/rta-office', 'RTAOfficeController')->except(['show'])->parameters(['rta-office' => 'office']);
    // vehicle
    Route::resource('/', 'VehicleController')->except(['show'])->parameter('', 'vehicle');

    // legal-document
    Route::prefix('legal-document')->as('legal-document.')->group(function () {
        Route::resource('/', 'LegalDocumentationController')->except(['show'])->parameter('', 'legal_document');
    });

    // ownership
    Route::prefix('ownership')->as('ownership.')->group(function () {
        // type
        Route::resource('/type', 'VehicleOwnershipTypeController')->except(['show']);
    });

    // requisition
    Route::prefix('requisition')->as('requisition.')->group(function () {
        // type
        Route::resource('/type', 'VehicleRequisitionTypeController')->except(['show']);
        // purpose
        Route::resource('/purpose', 'VehicleRequisitionPurposeController')->except(['show']);
        //vehicle requisition
        Route::resource('/', 'VehicleRequisitionController')->except(['show'])->parameter('', 'requisition');
        Route::post('{requisition}/status-update', [VehicleRequisitionController::class, 'statusUpdate'])->name('status-update');
    });

    // insurance
    Route::prefix('insurance')->as('insurance.')->group(function () {
        // company
        Route::resource('/company', 'VehicleInsuranceCompanyController')->except(['show']);
        // recurring-period
        Route::resource('/recurring-period', 'VehicleInsuranceRecurringPeriodController')->except(['show']);
        // insurance
        Route::resource('/', 'InsuranceController')->except(['show'])->parameter('', 'insurance');
    });
});


Route::prefix('admin/mobitra-api')->as('admin.mobitra_api.')->group(function () {
     Route::get('/settings',[MobitraApiController::class, 'mobitra_api_setting'])->name('mobitra_api_setting');
     Route::post('/setting-mode-update',[MobitraApiController::class, 'mobitra_api_mode_update'] )->name('mobitra_api_mode_update');
     Route::post('/settings-update', [MobitraApiController::class, 'mobitra_api_settings_update'])->name('mobitra_api_settings_update');
     Route::get('/authenticate', [MobitraApiController::class, 'authenticate'])->name('authenticate');
     Route::get('/get-user-data', [MobitraApiController::class, 'getUserData'])->name('get_user_data');
     Route::get('/get-user-devices', [MobitraApiController::class, 'getUserDevicesJson'])->name('get_user_devices');
     Route::get('/get-role-based-imei-data', [MobitraApiController::class, 'getRoleBasedImeiData'])->name('get_role_based_imei_data');
     Route::get('/get-vehicle-status-data', [MobitraApiController::class, 'getVehicleStatusData'])->name('get_vehicle_status_data');
     Route::get('/get-notifications', [MobitraApiController::class, 'getNotifications'])->name('get_notifications');
      Route::get('/mobitra-tracking-json', [MobitraApiController::class, 'mobitra_tracking_json'])->name('tracking_json');
      Route::get('/update-vehicles-json', [MobitraApiController::class, 'getVehicleStatusDataJson'])->name('update_vehicles_json');
    
});

Route::prefix('admin/asset/vehicle-management')->as('admin.asset.vehicle_management.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [VehicleManageController::class, 'amv_dashboard'])->name('amv_dashboard');
    
    
});