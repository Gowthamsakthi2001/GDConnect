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

Route::prefix('admin/Green-Drive-Ev/rider-type')->as('admin.Green-Drive-Ev.rider-type.')->controller(RiderTypeController::class)->middleware('auth')->group(function () {
    Route::get('create', 'create')->name('create');
    Route::post('store', 'store')->name('store');
    Route::get('list', 'index')->name('list');
    Route::get('delete/{id}', 'delete_rider_type')->name('delete');
    Route::get('edit/{id}', 'edit_rider_type')->name('edit');
    Route::get('status/{id}/{status}', 'change_status')->name('status');
    Route::post('update/{id}', 'update')->name('update');
});
