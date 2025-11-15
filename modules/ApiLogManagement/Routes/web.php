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

Route::prefix('admin/Green-Drive-Ev/api-log')->as('admin.Green-Drive-Ev.apilogmanagement.')->controller(ApiLogManagementController::class)->middleware('auth')->group(function () {
    Route::get('/settings', 'api_log_settings')->name('api_log_settings');
    Route::post('/settings-update', 'api_log_settings_update')->name('setting_api_log_update');
    Route::post('/log-mode-update', 'api_log_mode_update')->name('api_log_mode_update');
    Route::get('/adhaar-logs', 'adhaar_api_logs')->name('adhaar_api_log');
    Route::get('/license-logs', 'license_api_logs')->name('license_api_log');
    Route::get('/bank-detail-logs', 'bank_detail_api_logs')->name('bankdetail_api_log');
    Route::get('/pancard-logs', 'pancard_api_logs')->name('pancard_api_log');
    Route::get('/activity-logs', 'user_activity_api_logs')->name('user_activity_api_log');
    Route::get('/get-user-activity', 'get_user_activity_logs')->name('get_user_activity_log');
   
});
