<?php

use Illuminate\Support\Facades\Route;
use MasterManagement\MasterManagement\MasterManagement\MasterManagementController;

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
//     Route::resource('mastermanagement', MasterManagementController::class)->names('mastermanagement');
// });


Route::prefix('admin/master-management/telematric-oem-master') //created by Mugesh.B
    ->as('admin.Green-Drive-Ev.master_management.telematric_oem_master.')
    ->controller(TelemetricOEMMasterController::class)
    ->middleware('auth')
    ->group(function () {

        Route::get('/list', 'telematric_master_list')->name('list');
        Route::post('/store', 'store')->name('store');
        Route::post('/get-data/{id}', 'get_data')->name('get_data');
        Route::post('/update', 'update')->name('update');
        Route::post('/status-update', 'status_update')->name('status_update');
        Route::get('/export', 'export_telemetric_master')->name('export_telemetric_master');
});
    
    
    
Route::prefix('admin/master-management/financing-type-master') //created by Mugesh.B 
    ->as('admin.Green-Drive-Ev.master_management.financing_type_master.')
    ->controller(FinancingTypeMasterController::class)
    ->middleware('auth')
    ->group(function () {

        Route::get('/list', 'financing_type_master_list')->name('list');
        Route::post('/store', 'store')->name('store');
        Route::post('/get-data/{id}', 'get_data')->name('get_data');
        Route::post('/update', 'update')->name('update');
        Route::post('/status-update', 'status_update')->name('status_update');
        Route::get('/export', 'export_financing_type_master')->name('export_financing_type_master');
});
    
    
    
Route::prefix('admin/master-management/asset-ownership-master') //created by Mugesh.B
    ->as('admin.Green-Drive-Ev.master_management.asset_ownership_master.')
    ->controller(AssetOwnershipMasterController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/list', 'asset_ownership_master_list')->name('list');
        Route::post('/store', 'store')->name('store');
        Route::post('/get-data/{id}', 'get_data')->name('get_data');
        Route::post('/update', 'update')->name('update');
        Route::post('/status-update', 'status_update')->name('status_update');
        Route::get('/export', 'export_asset_ownership_master')->name('export_asset_ownership_master');
});
    
Route::prefix('admin/master-management/hypothecations') //created by Gowtham.S
    ->as('admin.Green-Drive-Ev.master_management.hypothecation.')
    ->controller(HypothecationMasterController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/status-update', 'status_update')->name('status_update');
        Route::post('/delete', 'destroy')->name('destroy');
        Route::get('/export', 'export_hypthecation')->name('export_hypthecation');
});
    
Route::prefix('admin/master-management/insurer-names') //created by Gowtham.S
    ->as('admin.Green-Drive-Ev.master_management.insurer_name.')
    ->controller(InsurerNameMasterController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/get-data/{id}', 'get_data')->name('get_data');
        Route::post('/update', 'update')->name('update');
        Route::post('/status-update', 'status_update')->name('status_update');
        Route::get('/export', 'export_insurer_name_master')->name('export_insurer_name_master');
});
    
Route::prefix('admin/master-management/insurance-types') //created by Gowtham.S
    ->as('admin.Green-Drive-Ev.master_management.insurance_type.')
    ->controller(InsuranceTypeMasterController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/status-update', 'status_update')->name('status_update');
        Route::get('/export', 'export_insurence_type')->name('export_insurence_type');
});


Route::prefix('admin/master-management/registration-types') //created by Gowtham.S
    ->as('admin.Green-Drive-Ev.master_management.registration_type.')
    ->controller(RegistrationTypeMasterController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/status-update', 'status_update')->name('status_update');
        Route::get('/export', 'export_registration_type')->name('export_registration_type');

});


Route::prefix('admin/master-management/inventory-location-master') //updated by Mugesh.B
->as('admin.Green-Drive-Ev.master_management.inventory_location_master.')
->controller(InventoryLocationMasterController::class)
->middleware('auth')
->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/store', 'store')->name('store');
    Route::post('/delete', 'destroy')->name('destroy');
    Route::post('/update-status', 'update_status')->name('status_update');
    Route::get('/export', 'export_inventory_location')->name('export');
});

Route::prefix('admin/master-management/color-master') //updated by Mugesh.B
->as('admin.Green-Drive-Ev.master_management.color_master.')
->controller(ColorMasterController::class)
->middleware('auth')
->group(function () {
    Route::get('/', 'index')->name('index');
     Route::post('/store', 'store')->name('store');
    Route::post('/update', 'update')->name('update');
    Route::post('/update-status', 'update_status')->name('status_update');
    Route::get('/export', 'export_color_master')->name('export');
});




Route::prefix('admin/master-management/customer-type-master') //updated by Mugesh.B
->as('admin.Green-Drive-Ev.master_management.customer_type_master.')
->controller(CustomerTypeMasterController::class)
->middleware('auth')
->group(function () {
    Route::get('/', 'index')->name('index');
     Route::post('/store', 'store')->name('store');
     Route::post('/update', 'update')->name('update');
     Route::post('/update-status', 'update_status')->name('status_update');
     Route::get('/export', 'export_customer_type_master')->name('export');
});



Route::prefix('admin/master-management/customer-master') //created by Gowtham.S
    ->as('admin.Green-Drive-Ev.master_management.customer_master.')
    ->controller(CustomerMasterController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/list', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/status-update', 'status_update')->name('status_update');
        Route::post('/delete', 'destroy')->name('destroy');
        
        Route::get('/login-credential/}{id}', 'login_credential')->name('login_credential'); //updated by Mugesh.B
        Route::post('/create/customer-login/', 'create_login')->name('create_login'); //updated by Mugesh.B
        Route::post('/edit/customer-login/', 'login_update')->name('login_update'); //updated by Mugesh.B
        Route::post('/login-status-update/', 'login_status_update')->name('login_status_update'); //updated by Mugesh.B
        Route::post('/get-customer-logins', 'get_customer_logins')->name('get_customer_logins'); //updated by Mugesh.B
        
        Route::get('/export', 'export_customer_master')->name('export_customer_master');
});

Route::prefix('admin/master-management/sidebar-modules') //created by Gowtham.S
    ->as('admin.Green-Drive-Ev.master_management.sidebar_module.')
    ->controller(SidebarModuleController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/render-data', 'module_render_data')->name('render.data');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('data.update');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/status-update', 'status_update')->name('status_update');
        // Route::post('/delete', 'destroy')->name('destroy');
});
    