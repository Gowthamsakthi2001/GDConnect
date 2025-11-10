<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\DashboardController;

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
Route::prefix('admin/Green-Drive-Ev/delivery-man')
    ->as('admin.Green-Drive-Ev.delivery-man.')
    ->controller(DeliverymanController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('create', 'index')->name('create');
        Route::post('create', 'create')->name('store');
        Route::get('list', 'list')->name('list');
        Route::get('new-list', 'new_list');
        Route::get('filter-hublist', 'filter_hub_list')->name('filter_hub');
        Route::get('get-area', 'get_area')->name('get-area');
        Route::get('delete/{id}', 'delete_dm')->name('delete');
        Route::get('edit/{id}', 'edit_dm')->name('edit');
        Route::get('status/{id}/{status}', 'change_status')->name('status');
        Route::get('aadhar/{id}/{status}', 'aadhar_status')->name('aadhar');
        Route::get('pan/{id}/{status}', 'pan_status')->name('pan');
        Route::get('bank/{id}/{status}', 'bank_status')->name('bank');
        Route::get('lisence/{id}/{status}', 'lisence_status')->name('lisence');
        // Route::get('kyc_verify/{id}/{status}', 'kyc_verify')->name('kyc_verify');
        Route::post('kyc_verify/{id}', 'kyc_verify')->name('kyc_verify'); //updated by Gowtham.s
        Route::post('bgv-comments/update', 'bgv_comment_update')->name('bgv_comment_update');
        Route::get('approve/{id}', 'approve')->name('approve');
        Route::post('application-status/approve/{id}', 'application_status_approve')->name('application_status_approve');
        Route::post('application-status/reject', 'application_status_reject')->name('application_status_reject');
        Route::get('deny/{id}', 'deny')->name('deny');
        Route::post('whatsapp-message', 'whatsapp_message')->name('whatsapp-message');
        Route::get('preview/{id}', 'preview')->name('preview');
        Route::get('zone-asset/{id}', 'zone_asset')->name('zone-asset');
        Route::post('asset-assign/{id}', 'asset_assign')->name('asset-assign');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('login-logs/list', 'deliveryman_reports')->name('login-logs.list');
        Route::get('client-based/login-logs/list', 'client_based_deliveryman_reports')->name('client-based-dm-reports');//updated by Gowtham.s
        Route::get('login-logs/preview/{id}', 'show_deliveryman_report')->name('login-logs.preview');
        Route::get('login-logs/report-list/{id}', 'report_list_dm')->name('login-logs.report-list');
        Route::get('log/edit/{id}', 'single_log_edit')->name('single_log_edit');
        Route::post('log/update', 'single_log_update')->name('single_log_update');
        Route::get('verification/{id}/{status}/{column_name}', 'verification')->name('verification');
        Route::get('preview_navigation/{id}', 'preview')->name('preview_navigation');
        Route::get('export-leave-permissions', 'exportLeavePermissions')->name('exportLeavePermissions');
        Route::get('export-leave-days', 'export_leave_days')->name('exportLeaveDays');
        Route::get('export-leave-reject-list', 'export_leave_reject_list')->name('exportLeaveRejectList');
        Route::get('/verify-list/{type}', 'export_deliveryman_verify_list')->name('export_deliveryman_verify_list');
        Route::get('logs/export', 'export_dm_log_list')->name('export_dm_log_list');
        
         Route::get('Generate-GDMID/{id}', 'generate_GDMID')->name('generate_gdmid'); //updated by Mugesh.B
    });
    
    Route::get('/admin/today-appilcation-count',[DashboardController::class,'get_today_application_count'])->name('get_today_application_count');


