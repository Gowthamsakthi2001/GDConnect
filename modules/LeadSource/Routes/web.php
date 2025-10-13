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

Route::prefix('admin/Green-Drive-Ev/lead-source')->as('admin.Green-Drive-Ev.lead-source.')->controller(LeadSourceController::class)->middleware('auth')->group(function () {
    Route::get('create', 'index')->name('create');           // Shows form to create a city
    Route::post('store', 'store')->name('store');          // Stores the new city
    Route::get('list', 'list')->name('list');                // Lists all cities
    Route::get('delete/{id}', 'delete_city')->name('delete'); // Deletes a specific city
    Route::get('edit/{id}', 'edit_city')->name('edit');      // Shows form to edit a city
    Route::get('status/{id}/{status}', 'change_status')->name('status'); // Changes status of a city
    Route::post('update/{id}', 'update')->name('update');    // Updates a specific city
});
