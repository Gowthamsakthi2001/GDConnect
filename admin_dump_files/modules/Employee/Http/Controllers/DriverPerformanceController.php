<?php

namespace Modules\Employee\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Employee\DataTables\DriverPerformanceDataTable;
use Modules\Employee\Entities\Driver;
use Modules\Employee\Entities\DriverPerformance;

class DriverPerformanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:employee_management', 'status_check']);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Driver Performance List',
            'description' => 'Displaying all Drivers.',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Driver Performance List',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.driver.performance',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(DriverPerformanceDataTable $dataTable)
    {
        return $dataTable->render('employee::driver_performance.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $drivers = Driver::all();

        return view('employee::driver_performance.create_edit', [
            'drivers' => $drivers,
        ])->render();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'driver_id' => 'required|integer',
            'over_time_status' => 'required|boolean',
            'salary_status' => 'required|boolean',
            'ot_payment' => 'required|numeric',
            'performance_bonus' => 'required|numeric',
            'penalty_amount' => 'required|numeric',
            'penalty_reason' => 'required|string|max:255',
            'penalty_date' => 'nullable',
        ]);

        $item = DriverPerformance::create($data);

        return response()->json(['success' => 'Driver created successfully.']);
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        return view('employee::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit(DriverPerformance $performance)
    {

        $drivers = Driver::all();

        return view('employee::driver_performance.create_edit', [
            'item' => $performance,
            'drivers' => $drivers,
        ])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(Request $request, DriverPerformance $performance)
    {
        $data = $request->validate([
            'driver_id' => 'required|integer',
            'over_time_status' => 'required|boolean',
            'salary_status' => 'required|boolean',
            'ot_payment' => 'required|numeric',
            'performance_bonus' => 'required|numeric',
            'penalty_amount' => 'required|numeric',
            'penalty_reason' => 'required|string|max:255',
            'penalty_date' => 'nullable',
        ]);

        $performance->update($data);

        return response()->json(['success' => 'Driver Performance updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(DriverPerformance $performance)
    {
        $performance->delete();
        return response()->success(null, localize('Driver Performance Deleted Successfully'), 200);
    }
}
