<?php

use Illuminate\Support\Facades\Route;
use Modules\HRStatus\Http\Controllers\HRStatusController;
use Modules\HRStatus\Http\Controllers\HRLevelOneController;

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
//     Route::resource('hrstatus', HRStatusController::class)->names('hrstatus');
// });

 Route::get('/admin/recruiter-filter-data/{type}',[HRStatusController::class,'dashboard_filter_data'])->name('admin.Green-Drive-Ev.hr_status.dashboard_filter_data')->middleware('auth');
 
Route::prefix('admin/Green-Drive-Ev/hr-status')->as('admin.Green-Drive-Ev.hr_status.')->controller(HRStatusController::class)->middleware('auth')->group(function () {
    Route::get('/dashboard', 'hr_dashboard_show')->name('dashboard');
    Route::get('/recruiters', 'recruiter_list')->name('index');
    Route::get('/recruiter/preview/{id}', 'recruiter_preview')->name('recruiter.preview');
    Route::get('/recruiter/bgv-comments-view/{id}', 'recruiter_bgv_comment_view')->name('recruiter.bgv_comment_view');
    Route::get('/recruiter/bgv-documents-view/{id}', 'recruiter_bgv_document_view')->name('recruiter.bgv_documnet_view');
    Route::post('/recruiter/query-add', 'recruiter_query_add')->name('recruiter.query_add');
    Route::get('/add-candidate', 'add_candidate')->name('add_candidate');
    Route::get('/edit-candidate/{id}', 'edit_candidate')->name('edit_candidate');
    Route::post('/store-candidate', 'store_candidate')->name('store_candidate');
    Route::post('/update-candidate', 'update_candidate')->name('update_candidate');
    Route::get('/reinitiate-candidate/{id}', 'reinitiate_candidate')->name('reinitiate_candidate');
    Route::post('/approve-candidate/{id}', 'update_approve_candidate')->name('update_approve_candidate');

});

Route::prefix('admin/hr-level-one')->as('admin.Green-Drive-Ev.hr_level_one.')->controller(HRLevelOneController::class)->middleware('auth')->group(function () {
    Route::get('/dashboard', 'hr_levelone_dashboard')->name('dashboard');
    Route::get('/list/{type}', 'levelone_application_list')->name('app_list');
    Route::get('/application/{id}', 'levelone_application_view')->name('app_preview');
    Route::post('/application-kyc-update/{id}', 'candidate_kyc_update')->name('candidate_kyc_update');
    Route::post('/update-candidate-status','updateCandidateStatus')->name('updateCandidateStatus');
});

Route::prefix('admin/hr-level-two')->as('admin.Green-Drive-Ev.hr_level_two.')->controller(HRLevelTwoController::class)->middleware('auth')->group(function () {
    Route::get('/dashboard', 'hr_leveltwo_dashboard')->name('dashboard');
    Route::get('/list/{type}', 'leveltwo_application_list')->name('app_list');
    Route::get('/application/{id}', 'leveltwo_application_view')->name('app_preview');
    Route::post('/update-status/', 'update')->name('candidate');
    Route::post('/update-data/', 'update_details')->name('update_data');
    Route::post('/get-areas/', 'get_area_by_id')->name('get_areas');
    Route::get('verification/{id}/{status}/{column_name}', 'verification')->name('verification');
    
    Route::post('/admin/hr-filtered-stats','fetchFilteredStats')->name('admin.hr-filtered-stats');
    Route::post('/comment-store/','comment_store')->name('comment_store');
        Route::post('/delete', 'destroy')->name('destroy');

    Route::get('/export-table','export_data')->name('export_data');
});



Route::prefix('admin/rider-onboard')->as('admin.Green-Drive-Ev.rider_onboard.')->controller(RiderOnboardController::class)->middleware('auth')->group(function () {
    Route::get('/list', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::post('/delete/{id}', 'destroy')->name('delete');
    Route::get('/view', 'rider_onboard_view')->name('view');
    Route::get('/fetch-riderdetail', 'fetch_riderdetail')->name('fetch_riderdetail');
    Route::get('/export-rider-onboard', 'export_rider_onboarding')->name('export_rider_onboard');
    Route::get('/logs', 'onboard_log')->name('onboard_log');
    Route::get('/export-rider-onboard-log', 'export_rider_onboard_log')->name('export_rider_onboard_log');
    Route::get('/view-log', 'rider_onboard_view_log')->name('view_log');
      Route::get('/fetch-hubs' , 'fetch_hubsdetail')->name('fetch_hubsdetail');
});


Route::prefix('admin/employee-categories')->as('admin.Green-Drive-Ev.employee_categories.')->controller(EmployeeCategoryController::class)->middleware('auth')->group(function () {  //updated by Mugesh.B
    Route::get('/employee/list', 'employee_list')->name('employee_list');
    Route::get('/rider/list', 'rider_list')->name('rider_list');
    Route::get('/adhoc/list', 'adhoc_list')->name('adhoc_list');
    Route::get('/helper/list', 'helper_list')->name('helper_list');
    
    Route::get('/employee/view/{id}', 'employee_view')->name('employee_view');
    Route::get('/rider/view/{id}', 'rider_view')->name('rider_view');
    Route::get('/adhoc/view/{id}', 'adhoc_view')->name('adhoc_view');
    Route::get('/helper/view/{id}', 'helper_view')->name('helper_view');
    
    
     Route::post('/employee/update/{id}', 'employee_update')->name('employee_update');
     Route::post('/rider/update/{id}', 'rider_update')->name('rider_update');
    Route::post('/adhoc/update/{id}', 'adhoc_update')->name('adhoc_update');
     Route::post('/helper/update/{id}', 'helper_update')->name('helper_update');
    
});