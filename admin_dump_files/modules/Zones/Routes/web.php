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

Route::group([], function () {
    Route::resource('zones', ZonesController::class)->names('zones');
})->middleware('auth');

Route::prefix('admin/Green-Drive-Ev/zone')->as('admin.Green-Drive-Ev.zone.')->controller(ZonesController::class)->middleware('auth')->group(function () {
    Route::get('zone', 'index')->name('zone'); 
    Route::get('render-zones', 'render_zone_list')->name('render.list'); 
    Route::get('list', 'list')->name('list'); // Displays the zone page with map and list of zones
    Route::post('save-zones', 'store')->name('save-zones');  // Saves a new zone
    Route::post('toggle-status/{id}', 'toggleStatus')->name('toggle-status');  // Toggles the active/inactive status of a zone
    Route::get('delete/{id}', 'destroy')->name('delete');  // Deletes a zone
    Route::get('edit/{id}', 'edit')->name('edit');
    Route::post('update/{id}', 'update')->name('update');
    Route::post('/check-zone-exist','check_exist_zone')->name('check-exist');
});

