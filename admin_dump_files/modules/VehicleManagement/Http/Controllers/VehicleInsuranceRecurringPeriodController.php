<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\VehicleManagement\DataTables\VehicleInsuranceRecurringPeriodDataTable;
use Modules\VehicleManagement\Entities\VehicleInsuranceRecurringPeriod;

class VehicleInsuranceRecurringPeriodController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:vehicle_insurance_recurring_period_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Vehicle insurance recurring-period Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Vehicle insurance recurring-period Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle.insurance.recurring-period',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(VehicleInsuranceRecurringPeriodDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of Vehicle insurance recurring-period in Database.',
        ]);

        return $dataTable->render('vehiclemanagement::insurance.recurring-period.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vehiclemanagement::insurance.recurring-period.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_insurance_recurring_periods,name',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $item = VehicleInsuranceRecurringPeriod::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(VehicleInsuranceRecurringPeriod $recurring_period)
    {
        return view('vehiclemanagement::insurance.recurring-period.create_edit', ['item' => $recurring_period])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleInsuranceRecurringPeriod $recurring_period, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_insurance_recurring_periods,name,' . $recurring_period->id . ',id',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $recurring_period->update($data);

        return response()->success($recurring_period, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(VehicleInsuranceRecurringPeriod $recurring_period)
    {

        $recurring_period->delete();
        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }
}
