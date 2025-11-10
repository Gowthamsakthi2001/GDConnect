<?php

use Illuminate\Support\Facades\Route;
use Modules\VehicleMaintenance\Http\Controllers\VehicleMaintenanceController;
use Modules\VehicleMaintenance\Http\Controllers\VehicleMaintenanceTypeController;

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

Route::prefix('admin/vehicle/maintenance')->as('admin.vehicle.maintenance.')->middleware('auth')->group(function () {

    Route::resource('/type', VehicleMaintenanceTypeController::class);
    Route::get('/get-inventory-category', [VehicleMaintenanceController::class, 'getInventoryCategory'])->name('get-inventory-category');
    Route::get('/get-parts', [VehicleMaintenanceController::class, 'getParts'])->name('get-parts');
    Route::get('/get-employee', [VehicleMaintenanceController::class, 'getEmployee'])->name('get-employee');
    Route::get('/get-vehicle', [VehicleMaintenanceController::class, 'getVehicle'])->name('get-vehicle');
    Route::get('/get-maintenance-type', [VehicleMaintenanceController::class, 'getMaintenanceType'])->name('get-maintenance-type');
    Route::resource('/', 'VehicleMaintenanceController')->parameters(['' => 'maintenance']);
    Route::post('{maintenance}/status-update', [VehicleMaintenanceController::class, 'statusUpdate'])->name('status-update');
});
