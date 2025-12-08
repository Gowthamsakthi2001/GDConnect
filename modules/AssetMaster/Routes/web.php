<?php

use Illuminate\Support\Facades\Route;

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
//     Route::resource('assetmaster', AssetMasterController::class)->names('assetmaster');
// });

Route::prefix('admin/Green-Drive-Ev/asset-master')
    ->as('admin.Green-Drive-Ev.asset-master.')
    ->controller(AssetMasterController::class)
    ->middleware('auth')
    ->group(function () {
        //modal master vechile
        Route::get('create', 'index')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('list', 'list')->name('list'); 
        Route::get('status/{id}/{status}', 'change_status')->name('status'); 
        Route::post('update/{id}', 'update')->name('update');
        Route::get('delete/{id}', 'delete_ModalMasterVechile')->name('delete'); 
        Route::get('edit/{id}', 'edit_ModalMasterVechile')->name('edit');  
        
        //completed
    
        //modal master battery
        Route::get('modal-master-battery-index', 'modal_master_battery_index')->name('modal_master_battery_index');
        Route::get('modal-master-battery-list', 'modal_master_battery_list')->name('modal_master_battery_list'); 
        Route::post('modal-master-battery-store', 'modal_master_battery_store')->name('modal_master_battery_store');
        Route::post('modal-master-battery-update/{id}', 'modal_master_battery_update')->name('modal_master_battery_update');
        Route::get('modal-master-battery-delete/{id}', 'modal_master_battery_delete')->name('modal_master_battery_delete'); 
        Route::get('modal-master-battery-edit/{id}', 'modal_master_battery_edit')->name('modal_master_battery_edit');  
        Route::get('modal-master-battery-status/{id}/{status}', 'modal_master_battery_change_status')->name('modal_master_battery_change_status'); 
        
        //modal master charger
        Route::get('model-master-charger-index', 'model_master_charger_index')->name('model_master_charger_index');
        Route::get('model-master-charger-list', 'model_master_charger_list')->name('model_master_charger_list');
        Route::post('modal-master-charger-store', 'model_master_charger_store')->name('model_master_charger_store');
        Route::post('modal-master-charger-update/{id}', 'model_master_charger_update')->name('model_master_charger_update');
        Route::get('modal-master-charger-delete/{id}', 'model_master_charger_delete')->name('model_master_charger_delete'); 
        Route::get('modal-master-charger-edit/{id}', 'model_master_charger_edit')->name('model_master_charger_edit');  
        Route::get('modal-master-charger-status/{id}/{status}', 'model_master_charger_change_status')->name('model_master_charger_change_status'); 
        
        //modal master manufacturer
        Route::get('manufacturer-master-index', 'manufacturer_master_index')->name('manufacturer_master_index');
        Route::get('manufacturer-master-list', 'manufacturer_master_list')->name('manufacturer_master_list');
        Route::post('manufacturer-master-store', 'manufacturer_master_store')->name('manufacturer_master_store');
        Route::post('manufacturer-master-update/{id}', 'manufacturer_master_update')->name('manufacturer_master_update');
        Route::get('manufacturer-master-delete/{id}', 'manufacturer_master_delete')->name('manufacturer_master_delete'); 
        Route::get('manufacturer-master-edit/{id}', 'manufacturer_master_edit')->name('manufacturer_master_edit');  
        Route::get('manufacturer-master-status/{id}/{status}', 'manufacturer_master_change_status')->name('manufacturer_master_change_status'); 
        
        //potable
        Route::get('po-table-index', 'po_table_index')->name('po_table_index');
        Route::get('po-table-list', 'po_table_list')->name('po_table_list');
        Route::post('po-table-store', 'po_table_store')->name('po_table_store');
        Route::post('po-table-update/{id}', 'po_table_update')->name('po_table_update');
        Route::get('po-table-delete/{id}', 'po_table_delete')->name('po_table_delete'); 
        Route::get('po-table-edit/{id}', 'po_table_edit')->name('po_table_edit');  
        Route::get('po-table-status/{id}/{status}', 'po_table_change_status')->name('po_table_change_status'); 
        
        //ams_location_master
        Route::get('ams-location-master-index', 'ams_location_master_index')->name('ams_location_master_index');
        Route::get('ams-location-master-list', 'ams_location_master_list')->name('ams_location_master_list');
        Route::post('ams-location-master-store', 'ams_location_master_store')->name('ams_location_master_store');
        Route::post('ams-location-master-update/{id}', 'ams_location_master_update')->name('ams_location_master_update');
        Route::get('ams-location-master-delete/{id}', 'ams_location_master_delete')->name('ams_location_master_delete'); 
        Route::get('ams-location-master-edit/{id}', 'ams_location_master_edit')->name('ams_location_master_edit'); 
        
       //asset_insurance_details
        Route::get('asset-insurance-details', 'asset_insurance_details_index')->name('asset_insurance_details_index');
        Route::get('asset-insurance-details-list', 'asset_insurance_details_list')->name('asset_insurance_details_list');
        Route::get('asset-insurance-details-delete/{id}', 'asset_insurance_details_delete')->name('asset_insurance_details_delete'); 
        Route::get('asset-insurance-details-edit/{id}', 'asset_insurance_details_edit')->name('asset_insurance_details_edit'); 
        Route::post('asset-insurance-details-store', 'asset_insurance_details_store')->name('asset_insurance_details_store');
        Route::post('asset-insurance-details-update/{id}', 'asset_insurance_details_update')->name('asset_insurance_details_update');
        
        //asset status 
        Route::get('asset-master-vehicle/status', 'asset_status_list_handle')->name('asset_status_list_handle');
        Route::post('asset-master-vehicle/status/store', 'asset_status_store')->name('asset_status_store');
        Route::get('asset-master-vehicle/delete/{id}', 'asset_status_delete')->name('asset_status_delete'); 
        Route::get('asset-master-vehicle/get-status/{id}', 'asset_get_status')->name('asset_get_status'); 
        Route::get('asset-master-vehicle/update-status/{id}/{status}', 'asset_update_status')->name('asset_update_status'); 
         
        //asset_master_vehicle
        Route::get('asset-master-vehicle', 'asset_master_vehicle_index')->name('asset_master_vehicle_index');
        Route::get('asset-master-vehicle-list', 'asset_master_vehicle_list')->name('asset_master_vehicle_list');
        Route::post('asset-master-vehicle-store', 'asset_master_vehicle_store')->name('asset_master_vehicle_store');
        Route::post('asset-master-vehicle-update/{id}', 'asset_master_vehicle_update')->name('asset_master_vehicle_update');
        Route::get('asset-master-vehicle-delete/{id}', 'asset_master_vehicle_delete')->name('asset_master_vehicle_delete'); 
        Route::get('asset-master-vehicle-edit/{id}', 'asset_master_vehicle_edit')->name('asset_master_vehicle_edit');  
        Route::get('asset-master-vehicle-status/{id}/{status}', 'asset_master_vehicle_change_status')->name('asset_master_vehicle_change_status'); 
        Route::get('asset-master-vehicle/import-verify', 'asset_master_vehicle_import_verify')->name('asset_master_vehicle_importverify');
        Route::post('import-excel', 'importExcel')->name('import-excel');
        
        //asset_master_charger
        Route::get('asset-master-charger', 'asset_master_charger_index')->name('asset_master_charger_index');
        Route::get('asset-master-charger-list', 'asset_master_charger_list')->name('asset_master_charger_list');
        Route::post('asset-master-charger-store', 'asset_master_charger_store')->name('asset_master_charger_store');
        Route::post('asset-master-charger-update/{id}', 'asset_master_charger_update')->name('asset_master_charger_update');
        Route::get('asset-master-charger-delete/{id}', 'asset_master_charger_delete')->name('asset_master_charger_delete'); 
        Route::get('asset-master-charger-edit/{id}', 'asset_master_charger_edit')->name('asset_master_charger_edit');  
        Route::get('asset-master-charger-status/{id}/{status}', 'asset_master_charger_change_status')->name('asset_master_charger_change_status'); 
        
        //asset_master_battery
        Route::get('asset-master-battery', 'asset_master_battery_index')->name('asset_master_battery_index');
        Route::get('asset-master-battery-list', 'asset_master_battery_list')->name('asset_master_battery_list');
        Route::post('asset-master-battery-store', 'asset_master_battery_store')->name('asset_master_battery_store');
        Route::post('asset-master-battery-update/{id}', 'asset_master_battery_update')->name('asset_master_battery_update');
        Route::get('asset-master-battery-delete/{id}', 'asset_master_battery_delete')->name('asset_master_battery_delete'); 
        Route::get('asset-master-battery-edit/{id}', 'asset_master_battery_edit')->name('asset_master_battery_edit');  
        Route::get('asset-master-battery-status/{id}/{status}', 'asset_master_battery_change_status')->name('asset_master_battery_change_status'); 
        
        Route::get('/vehicle-management', 'vehicle_manage_dashboard')->name('vehicle_manage_dashboard');
        
    });
    
    
    
Route::prefix('admin/Green-Drive-Ev/asset-master/vehicle-management') //updated by Gowtham.s
    ->as('admin.Green-Drive-Ev.asset_master.vehicle_management.')
    ->controller(VehicleManagementController::class)
    ->middleware('auth')
    ->group(function () {
        //vehicle management
        Route::get('/dashboard', 'asset_manage_dashboard')->name('dashboard');
    });
    
Route::prefix('admin/asset-management/') //updated by Gowtham.s
->as('admin.asset_management.asset_master.')
->controller(AssetMasterController::class)
->middleware('auth')
->group(function () {
    Route::get('/dashboard', 'asset_manage_dashboard')->name('dashboard');
    Route::get('/inventory-summary-filter', 'inventory_summary_filter')->name('inventory_summary.filter');
    Route::get('/get-customer-name', 'get_customer_name')->name('inventory_summary.get_name');
    Route::get('/dashboard-overall-data', 'get_dashboard_overall_data')->name('dashboard.get_overall_data');
    Route::get('/citywize-fetch-data', 'fetch_city_wise_data')->name('dashboard.fetch_city_wise_data');
});
    
Route::prefix('admin/asset-management/asset-master') //updated by Gowtham.s
->as('admin.asset_management.asset_master.')
->controller(AssetMasterController::class)
->middleware('auth')
->group(function () {
    
    Route::get('/list', 'asset_master_list')->name('list');
    Route::get('/create', 'add_vehicle')->name('add_vehicle');
    Route::post('/store', 'store_vehicle')->name('store_vehicle');
    Route::post('/vehicle-status-update', 'vehicle_status_update')->name('vehicle_status_update');
    Route::post('/bulk/vehicle-status-update', 'bulk_vehicle_status_update')->name('bulk.vehicle_status_update');
    Route::get('/view', 'view_asset_master')->name('view_asset_master');
    Route::get('/vehicle-data/reupload', 'reupload_vehicle_data')->name('reupload_vehicle_data');
    Route::post('/vehicle-data/reupdate', 'reupdate_vehicle_data')->name('reupdate_vehicle_data');
    Route::get('/logs-history', 'logs_history')->name('logs_history');
    Route::get('/get-qc-data', 'get_qc_data')->name('get_qc_data');
    // Route::get('/log-history/preview', 'log_history_preview')->name('log_history.preview');
           
    Route::get('log_history/preview/{log_id}/{asset_vehicle_id}', 'log_history_preview')  //updated by Mugesh.B
        ->name('log_history.preview');
        
        
    Route::post('/export/log_history', 'export_vehicle_log_and_history')->name('export.vehicle_log_history');
    Route::get('/bulk-upload', 'vehicle_bulk_upload')->name('bulk_upload');
    Route::get('/demo-excel-export', 'demo_excel_export')->name('demo_excel_export');
    Route::get('/bulk-upload-form', 'vehicle_bulk_upload_form')->name('bulk_upload_form');
    Route::post('/bulk-upload-form/import', 'vehicle_bulk_upload_form_import')->name('bulk_upload_form.import');
    Route::get('/bulk-upload-preview', 'bulk_upload_preview')->name('bulk_upload_preview');
    Route::get('/export-pending-assets', 'export_pending_assets')->name('export.pending_asset');
    Route::post('/export/vehicle-detail', 'export_vehicle_detail')->name('export.vehicle_detail');
    Route::post('/delete', 'destroy')->name('destroy');//updated by Mugesh.B
    
    Route::post('/update', 'update_data')->name('update');//updated by Mugesh.B
    
});

Route::prefix('admin/asset-management/asset-master')
    ->as('admin.asset_management.asset_master.')
    ->controller(InventoryController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/inventory', 'inventory_list')->name('inventory.list');
        Route::get('/inventory-view/{id}', 'inventory_view')->name('inventory.view');
        Route::post('/export/inventory-detail', 'export_inventory_detail')->name('export.inventory_detail');
        Route::get('/inventory-edit/{id}', 'edit')->name('inventory.edit');
        Route::post('/inventory-update', 'update')->name('inventory.update');
        Route::get('/inventory/bulk-upload-form', 'inventory_bulk_upload_form')->name('inventory.view.bulk_upload');
        Route::post('/inventory/bulk-upload-data', 'bulk_upload_data')->name('inventory.bulk_upload_data');
        Route::get('/download/import-error-file/{filename}', 'downloadErrorFile')->name('inventory.import.error.file');
        
    });



Route::prefix('admin/asset-management/quality-check') //updated by Mugesh.B
->as('admin.asset_management.quality_check.')
->controller(QualityCheckController::class)
->middleware('auth')
->group(function () {
    Route::get('/list', 'quality_check_list')->name('list');
    Route::get('/create', 'add_quality_check')->name('add_quality_check');
    Route::get('/view/{id}', 'view_quality_check')->name('view_quality_check');
    Route::get('/bulk-upload', 'quality_check_bulk_upload')->name('bulk_upload');
    Route::get('/bulk-upload-form', 'quality_check_bulk_upload_form')->name('bulk_upload_form');
    Route::get('/total-qc-list', 'total_qc_list')->name('total_qc_list');
    Route::get('/qc-list-view', 'qc_list_view')->name('qc_list_view');
    Route::post('/store', 'store')->name('store');
    Route::post('/reinitiate', 'reinitiate')->name('reinitiate');
    Route::post('/bulk-upload-data', 'bulk_upload_data')->name('bulk_upload_data');
    Route::get('/get-qc-checklist', 'get_qc_checklist')->name('get_qc_checklist');
    Route::get('/export-quality-check', 'export_quality_check')->name('export_quality_check');
    Route::get('Quality-check-excel-download', 'Quality_Check_Excel_download')->name('Excel_download');
    Route::get('quality-check-import-verify', 'quality_check_import_verify')->name('quality_check_import_verify');
    Route::post('/delete', 'destroy')->name('destroy');
    Route::get('/bypass', 'qc_bulk_pass');
});


Route::prefix('admin/asset-management/brand-model-master') //updated by Mugesh.B
->as('admin.asset_management.brand_model_master.')
->controller(BrandModelMasterController::class)
->middleware('auth')
->group(function () {
    Route::get('/list', 'brand_model_mater_list')->name('list');
    Route::get('/create', 'create_brand_model_master')->name('create_brand_model_master');
    Route::get('/update/{id}', 'update_brand_model_master')->name('update_brand_model_master');
    Route::post('/store', 'store')->name('store');
    Route::post('/update-data', 'update_data')->name('update_data');
    Route::post('/update-status', 'update_status')->name('status_update');
    Route::get('/export-brand-model-master', 'export_brand_model_master')->name('export_brand_model_master');
});



Route::prefix('admin/asset-management/vehicle-model-master') //updated by Mugesh.B
->as('admin.asset_management.vehicle_model_master.')
->controller(VehicleModelMasterController::class)
->middleware('auth')
->group(function () {
    Route::get('/list', 'vehicle_model_mater_list')->name('list');
    Route::get('/create', 'create_vehicle_model_master')->name('create_vehicle_model_master');
    Route::get('/update/{id}', 'update_vehicle_model_master')->name('update_vehicle_model_master');
    Route::post('/store', 'store')->name('store');
    Route::post('/update-data', 'update_data')->name('update_data');
    Route::post('/update-status', 'update_status')->name('status_update');
    Route::get('/export-vehicle-model-master', 'export_vehicle_model_master')->name('export_vehicle_model_master');
});


Route::prefix('admin/asset-management/vehicle-transfer') //updated by Gowtham.S
->as('admin.asset_management.vehicle_transfer.')
->controller(VehicleTransferController::class)
->middleware('auth')
->group(function () {
    Route::get('/', 'vehicle_transfer_show')->name('show');
    Route::post('/', 'vehicle_transfer_initiate_form')->name('initiate_form');
    Route::get('/get-chassis-details', 'get_chassis_details')->name('get_chassis_detail');
    Route::get('/get-rider-details', 'get_rider_details')->name('get_rider_detail');
    Route::post('/get-bulk-details', 'get_bulk_details')->name('get_bulk_detail');
    Route::get('/return-vehicles', 'return_transfer_vehicle_view')->name('return_vehicle_view');
    Route::post('/return-vehicle-form', 'vehicle_transfer_return_form')->name('return_form');
    Route::get('/logs-and-history', 'log_and_history_view')->name('log_and_history');
    Route::get('/log-preview', 'log_preview')->name('log_preview');
    Route::post('/export-detail', 'export_detail')->name('export_detail');
    
    Route::get('/render-internal-table/{id}', 'getInterTransferTable')->name('getInterTransferTable');
    Route::get('/render-rider-table/{id}', 'getRiderTransferTable')->name('getRiderTransferTable');
    
    Route::get('/logs/render-internal-table/{id}', 'getInterTransferTablelist')->name('getLogInterTransferTablelist');
    Route::get('/logs/render-rider-table/{id}', 'getLogRiderTransferTablelist')->name('getLogRiderTransferTablelist');
});

Route::prefix('admin/asset-management/location-master') //updated by Mugesh.B
->as('admin.asset_management.location_master.')
->controller(LocationMasterController::class)
->middleware('auth')
->group(function () {
    Route::get('/list', 'location_mater_list')->name('list');
    Route::get('/create', 'create_location_master')->name('create_location_master');
    Route::post('/store', 'store_location_master')->name('store');
    Route::get('/update', 'update_location_master')->name('update_location_master');
    Route::post('/edit', 'edit_location_master')->name('update');
    Route::get('/view', 'view_location_master')->name('view_location_master');
    Route::post('/update-status', 'update_status')->name('status_update');
    Route::get('/export-location-master', 'export_location_master')->name('export_location_master');
});

Route::prefix('admin/asset-management/qc-check-list') //updated by Gowtham.s
->as('admin.asset_management.quality_check_list.')
->controller(QualityCheckListController::class)
->middleware('auth')
->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/store', 'store')->name('store');
    Route::post('/delete', 'destroy')->name('destroy');
     Route::post('/create', 'create')->name('create');
    Route::post('/update-status', 'update_status')->name('status_update');
    Route::get('/export', 'export_qc_check_lists')->name('export');
    
});



