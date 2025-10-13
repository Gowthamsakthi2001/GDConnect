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

Route::prefix('admin/Green-Drive-Ev/leads')->as('admin.Green-Drive-Ev.leads.')->controller(LeadsController::class)->middleware('auth')->group(function () {
    Route::get('/', 'index')->name('leads');
    Route::get('/development', 'lead_dev_index')->name('lead_dev_index');
    Route::post('add', 'store')->name('add');
    Route::get('list', 'list')->name('list');
    Route::get('update', 'update')->name('update');
    Route::get('excel', 'downloadExcel')->name('excel');
    Route::get('lead-import-verify', 'lead_import_verify')->name('lead_import_verify');
    Route::get('leads-excel-download', 'leadsExcel_download')->name('Excel_download');
    Route::post('uploadLeads', 'importExcel')->name('uploadLeads');
    Route::post('addComment', 'addComment')->name('addComment');
    Route::post('deleteComment', 'deleteComment')->name('deleteComment');
    Route::get('getComment', 'getComment')->name('getComment');
    Route::post('assignTelecaller', 'assignTelecaller')->name('assignTelecaller');
    Route::get('/popup-detail/{id}', 'get_popup_data')->name('get_popup_data');
    Route::get('/fetch-data','fetchLeads')->name('fetch');
    Route::get('/autoload-leaddata','load_more_leaddata')->name('auload_lead_data');
    Route::get('/append-leaddata','append_leaddata')->name('append_lead_data');
    Route::get('/search-leaddata','search_leaddata')->name('search_lead_data');

});
