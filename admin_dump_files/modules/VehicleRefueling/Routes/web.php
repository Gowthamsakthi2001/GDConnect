<?php

use Illuminate\Support\Facades\Route;
use Modules\VehicleRefueling\Http\Controllers\FuelRequisitionController;

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

Route::prefix('admin/refueling')->as('admin.refueling.')->middleware('auth')->group(function () {

    // type
    Route::resource('/type', 'FuelTypeController')->except(['show']);

    // fuel-station
    Route::resource('/station', 'FuelStationController')->except(['show']);
    Route::get(
        '/station/vendor-list',
        'FuelStationController@vendorList'
    )->name('station.vendor-list');
    //
    Route::prefix('requisition')->as('requisition.')->group(function () {
        Route::get('/get-vehicle', 'FuelRequisitionController@getVehicle')->name('get-vehicle');
        Route::get('/get-station', 'FuelRequisitionController@getStation')->name('get-station');
        Route::get('/get-type', 'FuelRequisitionController@getType')->name('get-type');
        Route::post('{requisition}/status-update', [FuelRequisitionController::class, 'statusUpdate'])->name('status-update');
    });
    Route::resource('/requisition', 'FuelRequisitionController');
    // refueling
    Route::resource('/', 'VehicleRefuelingController')->except(['show'])->parameter('', 'vehicle_refueling');
});
