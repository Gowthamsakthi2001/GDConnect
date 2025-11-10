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

Route::prefix('admin/Green-Drive-Ev/City')->as('admin.Green-Drive-Ev.City.')->controller(CityController::class)->middleware('auth')->group(function () {
    Route::get('create', 'index')->name('create');           // Shows form to create a city
    Route::post('create', 'create')->name('store');          // Stores the new city
    Route::get('list', 'list')->name('list');                // Lists all cities
    Route::get('delete/{id}', 'delete_city')->name('delete'); // Deletes a specific city
    Route::get('edit/{id}', 'edit_city')->name('edit');      // Shows form to edit a city
    Route::get('status/{id}/{status}', 'change_status')->name('status'); // Changes status of a city
    Route::post('update/{id}', 'update')->name('update');    // Updates a specific city
});

Route::prefix('admin/Green-Drive-Ev/Area')->as('admin.Green-Drive-Ev.Area.')->controller(CityController::class)->middleware('auth')->group(function () {
    Route::get('create', 'area_index')->name('create');           // Shows form to create a city
    Route::post('create', 'area_create')->name('store');          // Stores the new city
    Route::get('list', 'area_list')->name('list');                // Lists all cities
    Route::get('delete/{id}', 'area_delete')->name('delete'); // Deletes a specific city
    Route::get('edit/{id}', 'area_edit')->name('edit');      // Shows form to edit a city
    Route::get('status/{id}/{status}', 'area_change_status')->name('status'); // Changes status of a city
    Route::post('update/{id}', 'area_update')->name('update');    // Updates a specific city
});

    Route::prefix('admin/Green-Drive-Ev/State')->as('admin.Green-Drive-Ev.State.')->controller(EVStateManagementController::class)->group(function () {
        Route::get('create', 'state_index')->name('create');          
        Route::post('create', 'state_create')->name('store');         
        Route::put('update/{id}', 'state_update')->name('update');   
        Route::get('list', 'state_index')->name('list');               
        Route::get('delete/{id}', 'state_delete')->name('delete');    
        Route::get('status/{id}/{status}', 'state_change_status')->name('status'); 
        Route::get('export','state_export')->name('export');
    });