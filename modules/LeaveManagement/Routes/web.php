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

Route::prefix('admin/Green-Drive-Ev/leavemanagement')->as('admin.Green-Drive-Ev.leavemanagement.')->controller(LeaveManagementController::class)->middleware('auth')->group(function () {
    Route::get('/', 'list')->name('index');
    Route::post('store', 'add_orupdate')->name('add_or_update');
    Route::get('edit/{id}', 'edit')->name('edit'); 
    Route::post('update', 'update')->name('update');
    Route::get('delete/{id}', 'delete_leave')->name('delete');
    
    Route::get('new-leave-request', 'new_leave_request_list')->name('new_leave_request');
    Route::get('list-of-requests', 'approved_leave_request_list')->name('approved_leave_request');
    Route::get('permission-request', 'new_permission_request_list')->name('new_permission_request');
    Route::post('approve-or-reject', 'leave_approve_or_reject')->name('leave_approve_or_reject');
    Route::get('leave-logs', 'leave_log_report')->name('leave_log_report');
    Route::get('get-leave-count', 'get_leave_count')->name('get-leave-count');
});

Route::prefix('admin/Green-Drive-Ev/leavemanagement')->as('admin.Green-Drive-Ev.leavemanagement.')->controller(HolidayManagementController::class)->middleware('auth')->group(function () {
            Route::get('holidays', 'index')->name('holidays.index');
            Route::get('holidays/manage', 'manage')->name('holidays.manage');
            Route::get('holidays/manage/{holiday}', 'manage')->name('holidays.manage.edit');
            Route::post('holidays/save', 'save')->name('holidays.save');
            Route::post('holidays/delete', 'destroy')->name('holidays.destroy');
          
     
});