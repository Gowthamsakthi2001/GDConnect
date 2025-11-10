<?php

use Illuminate\Support\Facades\Route;
use Modules\BgvVendor\Http\Controllers\BgvVendorController;
use App\Http\Controllers\DashboardController;

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
//     Route::resource('bgvvendor', BgvVendorController::class)->names('bgvvendor');
// });

 Route::get('/admin/filter-data/{type}',[BgvVendorController::class,'dashboard_filter_data'])->name('admin.Green-Drive-Ev.bgvvendor.dashboard_filter_data')->middleware('auth');
 Route::get('/admin/get-pending-bgv/count',[DashboardController::class,'get_today_pending_application_count'])->name('admin.Green-Drive-Ev.bgvvendor.get_today_pending_application_count')->middleware('auth');

Route::prefix('admin/Green-Drive-Ev/bgvvendor')->as('admin.Green-Drive-Ev.bgvvendor.')->controller(BgvVendorController::class)->middleware('auth')->group(function () {
    Route::get('/dashbaord', 'show_bgv_dashboard')->name('dashboard');
    Route::get('/list/{type}', 'bgv_verification_list')->name('bgv_list');
    Route::get('/document-verify/{id}', 'bgv_document_verify')->name('bgv_doc_verify');
    Route::post('bgv-comments/store', 'bgv_comment_store')->name('bgv_comment_store');
    Route::post('bgv-documents/store', 'bgv_document_store')->name('bgv_document_store');
     Route::get('/recruiter/queries/{id}', 'recruiter_query_list')->name('recruiter_query_list');
     
     Route::get('/summary', 'summary')->name('summary'); //updated by Mugesh.B
});