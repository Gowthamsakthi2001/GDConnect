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

// Route::group([], function () {
//     Route::resource('b2badmin', B2BAdminController::class)->names('b2badmin');
// });


Route::prefix('b2b/admin') //updated by Mugesh.B
    ->as('b2b.admin.')
    ->controller(DeployedAssetController::class)
    ->group(function () {
        
        Route::get('/deployed-asset/list', 'list')->name('deployed_asset.list');
        Route::get('/deployed-asset/view/{id}', 'deployed_asset_view')->name('deployed_asset.deployed_asset_view');
        Route::get('/deployed-asset/recovery_request/{id}','deployed_asset_recovery_request')->name('deployed_asset.recovery_request');//updated by Gowtham.S
        Route::post('/deployed-asset/store-recovery-request','store_recovery_request')->name('deployed_asset.store_recovery_request');//updated by Gowtham.S
        Route::get('/deployment-request/list', 'deployment_list')->name('deployment_request.list');
        Route::get('/deployment-request/view/{id}', 'deployment_view')->name('deployment_request.deployment_view');
        Route::get('/deployment-request/export', 'export_deploymet_request')->name('deployment_request.export');
        Route::get('/deployed-asset/export', 'export_deployed_list')->name('deployed_asset.export');
        Route::get('/autoload-servicedata','load_more_servicedata')->name('deployed_asset.auload_service_data');
        Route::get('/accident/view/{id}', 'accident_view')->name('deployed_asset.accident_view');
        Route::get('/accident/list/{id}', 'accident_list')->name('deployed_asset.accident_list');
        
        Route::get('/activity-logs/{id}', 'autoload_activity_logs')->name('deployed_asset.activity_logs');
        
    });

    Route::prefix('b2b/admin') //updated by Logesh
        ->as('b2b.admin.')
        ->controller(DashboardController::class)
        ->group(function () {
            Route::get('/dashboard', 'index')->name('dashboard');
            Route::get('/dashboard/filter', 'filter')->name('dashboard.filter');
            Route::post('/dashboard/recovery-filter', 'recoveryFilter')->name('dashboard.recoveryFilter');
            Route::post('/dashboard/service-filter', 'serviceFilter')->name('dashboard.serviceFilter');
            Route::post('/dashboard/accident-filter', 'accidentFilter')->name('dashboard.accidentFilter');
            Route::post('/dashboard/return-filter', 'returnFilter')->name('dashboard.returnFilter');
        });
    
    
    Route::prefix('b2b/admin/zone') //updated by Mugesh
        ->as('b2b.admin.zone.')
        ->controller(B2BZoneController::class)
        ->group(function () {
            Route::get('/list', 'zone_list')->name('zone_list');
            Route::get('/view/{id}', 'zone_view')->name('zone_view');
            Route::get('/export', 'export')->name('export');
            
        });
    
    
    
    Route::prefix('b2b/admin/dashboard-issue-tickets') //updated by Logesh
        ->as('b2b.admin.dashboard_ticket.')
        ->controller(B2BDashboardTicketController::class)
        ->group(function () {
            
            Route::get('/list', 'list')->name('list');
            
        });
    
    
    Route::prefix('b2b/admin/rider') //updated by Logesh
        ->as('b2b.admin.rider.')
        ->controller(B2BRiderController::class)
        ->group(function () {
            
            Route::get('/list', 'list')->name('list');
            Route::get('/view/{id}', 'rider_view')->name('rider_view');
            Route::get('/rider_export', 'rider_export')->name('rider_export');
            
        });
    Route::prefix('b2b/admin/agent') //updated by Logesh
        ->as('b2b.admin.agent.')
        ->controller(B2BAgentController::class)
        ->group(function () {
            
            Route::get('/list', 'list')->name('list');
            Route::get('/view/{id}', 'agent_view')->name('agent_view');
            Route::get('/agent-export', 'agent_export')->name('agent_export');
            Route::post('/update-status','updateStatus')->name('updateStatus');
            
        });   

    Route::prefix('b2b/admin/ticket') //updated by Logesh
        ->as('b2b.admin.ticket.')
        ->controller(B2BTicketController::class)
        ->group(function () {
            
            Route::get('/list', 'list')->name('list');
            Route::get('/view', 'ticket_view')->name('ticket_view');
            Route::post('/update-status', 'update_ticket_status')->name('update_ticket_status');
            
        });
    
    Route::prefix('b2b/admin/service-request') //updated by Logesh
        ->as('b2b.admin.service_request.')
        ->controller(B2BServiceController::class)
        ->group(function () {
            
            Route::get('/list', 'list')->name('list');
            Route::get('/view/{id}', 'view')->name('view');
            Route::get('/export', 'export')->name('export');
            
        });
    
    Route::prefix('b2b/admin/return-request') //updated by Logesh
        ->as('b2b.admin.return_request.')
        ->controller(B2BReturnController::class)
        ->group(function () {
            
            Route::get('/list', 'list')->name('list');
            Route::get('/view/{id}', 'view')->name('view');
            Route::get('/export', 'export')->name('export');
        });
    
    Route::prefix('b2b/admin/recovery-request') //updated by Logesh
        ->as('b2b.admin.recovery_request.')
        ->controller(B2BRecoveryController::class)
        ->group(function () {
            
            Route::get('/list', 'list')->name('list');
            Route::get('/view/{id}', 'view')->name('view');
            Route::get('/export', 'export')->name('export');
            Route::get('/get-recovery-logs/{req_id}','recovery_logs'); // Logesh Updated
        });
    
    Route::prefix('b2b/admin/accident-report') //updated by Logesh
        ->as('b2b.admin.accident_report.')
        ->controller(B2BAccidentController::class)
        ->group(function () {
            
            Route::get('/list', 'list')->name('list');
            Route::get('/view/{id}', 'view')->name('view');
            Route::post('/update-status', 'updateStatus')->name('updateStatus');
            Route::get('/export', 'export')->name('export');
        });
    
    Route::prefix('b2b/admin/report') //updated by Logesh
        ->as('b2b.admin.report.')
        ->controller(B2BReportController::class)
        ->group(function () {
            Route::get('/list', 'list')->name('list');
            
            Route::get('/deployment-report', 'deployment_report')->name('deployment_report');//Updated by Mugesh.B
            Route::get('/export-deployment-report', 'export_deployment_report')->name('export_deployment_report');//Updated by Mugesh.B
            
            Route::get('/service-report', 'service_report')->name('service_report');//Updated by Mugesh.B
            Route::get('/export-service-report', 'export_service_report')->name('export_service_report');//Updated by Mugesh.B
             
             
            Route::get('/accident-report', 'accident_report')->name('accident_report');//Updated by Mugesh.B
            Route::get('/export-accident-report', 'export_accident_report')->name('export_accident_report');//Updated by Mugesh.B
            
            Route::get('/return-report', 'return_report')->name('return_report');//Updated by Mugesh.B
            Route::get('/export-return-report', 'export_return_report')->name('export_return_report');//Updated by Mugesh.B
            
            Route::get('/recovery-report', 'recovery_report')->name('recovery_report'); //Updated by Mugesh.B
            Route::get('/export-recovery-report', 'export_recovery_report')->name('export_recovery_report'); //Updated by Mugesh.B
            
            
        });
        
        Route::get('get-deployment-vehicles', 'B2BReportController@get_deployment_vehicles')->name('b2badmin.get_deployment_vehicles');//Updated by Mugesh.B
    