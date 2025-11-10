<?php

use Illuminate\Support\Facades\Route;
use Modules\RecoveryManager\Http\Controllers\DashboardController;
use Modules\RecoveryManager\Http\Controllers\RecoveryManagerController;
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


Route::prefix('admin/recovery-management')
    ->name('admin.recovery_management.')
    ->group(function () {
  
   Route::get('/dashboard',[DashboardController::class, 'dashboard'])->name('dashboard');
   Route::get('/dashboard/filter',[DashboardController::class, 'filter'] )->name('dashboard.filter');
   Route::post('/dashboard/recovery-filter', [DashboardController::class, 'recoveryFilter'])->name('dashboard.recoveryFilter');
   Route::get('/request-list/{type}',[RecoveryManagerController::class, 'list'])->name('list');
   Route::get('/request-list/view/{id}',[RecoveryManagerController::class, 'view'])->name('view');
   Route::get('/request-list-export',[RecoveryManagerController::class, 'export'])->name('request_list_export');
   Route::get('/agent-list/{type}',[RecoveryManagerController::class, 'agentList'])->name('agent_list');
   Route::get('/agent-view/{id}',[RecoveryManagerController::class, 'agentView'])->name('agent_view');
   Route::get('/agent-list-export',[RecoveryManagerController::class, 'agentExport'])->name('agent_list_export');

   
   
   Route::post('/assign-agent',[RecoveryManagerController::class, 'assignAgent'])->name('assign_agent'); 
   Route::get('/get-agents',[RecoveryManagerController::class, 'getAgent'])->name('get_agents_by_zone');
   
   Route::post('/update-status',[RecoveryManagerController::class, 'updateStatus'])->name('update_status'); 
   Route::get('/get-agent-comments/{id}', [RecoveryManagerController::class, 'getAgentComments'])->name('get_agent_comments');
   Route::post('/add-comment', [RecoveryManagerController::class, 'addComment'])->name('add_comment');
    
    Route::get('/get-vehicle-tracking', [RecoveryManagerController::class, 'getVehicleStatusDataJson'])->name('get_vehicle_tracking');

});
