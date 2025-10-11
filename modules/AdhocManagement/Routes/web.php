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
//     Route::resource('adhocmanagement', AdhocManagementController::class)->names('adhocmanagement');
// });

Route::prefix('admin/Green-Drive-Ev/adhoc-management')->as('admin.Green-Drive-Ev.adhocmanagement.')->controller(AdhocManagementController::class)->middleware('auth')->group(function () {
    Route::get('/', 'list_of_adhoc')->name('list_of_adhoc');
    Route::get('/create', 'create')->name('create_adhoc');
    Route::get('/edit/{id}', 'edit_adhoc')->name('edit_adhoc');
    Route::post('update-work-status', 'update_work_status')->name('update_work_status');
    Route::post('update-active-date', 'update_active_date')->name('update_active_date');
    Route::post('approve-status', 'adhoc_approve_status')->name('approve_status');
    Route::post('deny-status', 'adhoc_deny_status')->name('adhoc_deny_status');
    Route::get('/asset-assign/{id}', 'sp_asset_assign')->name('sp_asset_assign');
    Route::post('asset-assign-store/{id}', 'sp_asset_assign_store')->name('sp_asset_assign_store');
    Route::get('/verify-list/{type}', 'export_adhoc_verify_list')->name('export_adhoc_verify_list');
    Route::get('/log-list', 'log_list')->name('log_list');
    Route::get('/adhoc/log-preview/{id}', 'show_adhoc_log_report')->name('show_adhoc_log_report');
    // Route::get('edit/{id}', 'edit')->name('edit'); 
    // Route::post('update', 'update')->name('update');
    // Route::get('delete/{id}', 'delete_leave')->name('delete');
    
 
});