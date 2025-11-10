<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\ExpenseController;
use Modules\Inventory\Http\Controllers\InventoryPartsUsageController;

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

Route::prefix('inventory')->as('admin.inventory.')->middleware('auth')->group(function () {
    Route::prefix('stock')->as('stock.')->group(function () {
        Route::get('/', 'InventoryStockController@index')->name('index');
        Route::get('/get-category', 'InventoryStockController@getInventoryCategory')->name('get-category');
        Route::get('/get-parts', 'InventoryStockController@getParts')->name('get-parts');
    });
    Route::prefix('trip-type')->as('trip-type.')->group(function () {
        Route::resource('/', 'TripTypeController')->except(['show'])->parameters(['' => 'trip_type']);
    });

    Route::prefix('category')->as('category.')->group(function () {
        Route::resource('/', 'InventoryCategoryController')->except(['show'])->parameters(['' => 'category']);
    });
    Route::prefix('location')->as('location.')->group(function () {
        Route::resource('/', 'InventoryLocationController')->except(['show'])->parameters(['' => 'location']);
    });
    Route::prefix('parts')->as('parts.')->group(function () {
        Route::prefix('usage')->as('usage.')->group(function () {
            Route::get('/get-vehicle', [InventoryPartsUsageController::class, 'getVehicle'])->name('get-vehicle');
            Route::get('/get-inventory-category', [InventoryPartsUsageController::class, 'getInventoryCategory'])->name('get-inventory-category');
            Route::get('/get-parts', [InventoryPartsUsageController::class, 'getParts'])->name('get-parts');
            Route::resource('/', 'InventoryPartsUsageController')->parameters(['' => 'parts_usage']);
            Route::post('{parts_usage}/status-update', [InventoryPartsUsageController::class, 'statusUpdate'])->name('status-update');
        });
        Route::resource('/', 'InventoryPartsController')->parameters(['' => 'parts']);
    });
    Route::prefix('vendor')->as('vendor.')->group(function () {
        Route::resource('/', 'InventoryVendorController')->parameters(['' => 'vendor']);
    });

    Route::prefix('expense')->as('expense.')->group(function () {
        Route::resource('/type', 'ExpenseTypeController')->parameters(['' => 'type']);
        //
        Route::get('/get-type', [ExpenseController::class, 'getType'])->name('get-type');
        Route::get('/get-employee', [ExpenseController::class, 'getEmployee'])->name('get-employee');
        Route::get('/get-vendor', [ExpenseController::class, 'getVendor'])->name('get-vendor');
        Route::get('/get-vehicle', [ExpenseController::class, 'getVehicle'])->name('get-vehicle');
        Route::get('/get-trip-type', [ExpenseController::class, 'getTripType'])->name('get-trip-type');
        Route::resource('/', 'ExpenseController')->parameters(['' => 'expense']);
        Route::post('{expense}/status-update', [ExpenseController::class, 'statusUpdate'])->name('status-update');
    });
});
