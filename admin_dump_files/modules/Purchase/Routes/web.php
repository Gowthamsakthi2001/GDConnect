<?php

use Illuminate\Support\Facades\Route;
use Modules\Purchase\Http\Controllers\PurchaseController;

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

Route::prefix('admin/purchase')->as('admin.purchase.')->middleware('auth')->group(function () {

    // type
    Route::get('/get-inventory-category', [PurchaseController::class, 'getInventoryCategory'])->name('get-inventory-category');
    Route::get('/get-parts', [PurchaseController::class, 'getParts'])->name('get-parts');
    Route::get('/get-vendor', [PurchaseController::class, 'getVendor'])->name('get-vendor');
    Route::resource('/', 'PurchaseController')->parameters(['' => 'purchase']);
    Route::post('{purchase}/status-update', [PurchaseController::class, 'statusUpdate'])->name('status-update');
});
