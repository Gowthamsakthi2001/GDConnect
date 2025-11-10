<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Employee\Entities\Driver;
use Modules\Employee\Entities\Employee;
use Modules\VehicleManagement\DataTables\VehicleRequisitionDataTable;
use Modules\VehicleManagement\Entities\VehicleRequisition;
use Modules\VehicleManagement\Entities\VehicleRequisitionPurpose;
use Modules\VehicleManagement\Entities\VehicleType;

class VehicleRequisitionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:vehicle_requisition_management', 'status_check']);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Vehicle Requisition List',
            'description' => 'Displaying all Requisition.',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Vehicle Requisition List',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle.requisition',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(VehicleRequisitionDataTable $dataTable)
    {
        $employees = Employee::all();
        $vehicle_types = VehicleType::all();

        return $dataTable->render('vehiclemanagement::requisition.vehicle_requisition.index', [
            'employees' => $employees,
            'vehicle_types' => $vehicle_types,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('vehiclemanagement::requisition.vehicle_requisition.create_edit', [
            'employees' => Employee::all(),
            'vehicle_types' => VehicleType::where('is_active', true)->get(),
            'drivers' => Driver::where('is_active', true)->get(),
            'purposes' => VehicleRequisitionPurpose::where('is_active', true)->get(),
        ])->render();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'employee_id' => 'required|integer',
            'vehicle_type_id' => 'required|integer',
            'where_from' => 'required|string',
            'where_to' => 'required|string',
            'pickup' => 'nullable|string',
            'requisition_date' => 'nullable|date',
            'time_from' => 'nullable',
            'time_to' => 'nullable',
            'tolerance' => 'required',
            'number_of_passenger' => 'nullable|integer',
            'purpose' => 'required|integer',
            'driver_id' => 'required|integer',
            'details' => 'required',
        ]);

        $item = VehicleRequisition::create($data);

        return response()->success($item, localize('Requisition Created Successfully'), 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleRequisition $requisition)
    {

        return view('vehiclemanagement::requisition.vehicle_requisition.create_edit', [
            'item' => $requisition,
            'employees' => Employee::all(),
            'vehicle_types' => VehicleType::where('is_active', true)->get(),
            'drivers' => Driver::where('is_active', true)->get(),
            'purposes' => VehicleRequisitionPurpose::where('is_active', true)->get(),
        ])->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehicleRequisition $requisition)
    {

        $data = $request->validate([
            'employee_id' => 'required|integer',
            'vehicle_type_id' => 'required|integer',
            'where_from' => 'required|string',
            'where_to' => 'required|string',
            'pickup' => 'nullable|string',
            'requisition_date' => 'nullable|date',
            'time_from' => 'nullable',
            'time_to' => 'nullable',
            'tolerance' => 'required',
            'number_of_passenger' => 'nullable|integer',
            'purpose' => 'required|integer',
            'driver_id' => 'required|integer',
            'details' => 'required',
        ]);

        $item = $requisition->update($data);

        return response()->success($item, localize('Requisition Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleRequisition $requisition)
    {


        $requisition->delete();
        return response()->success(null, localize('Requisition Deleted Successfully'), 200);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function statusUpdate(VehicleRequisition $requisition, Request $request)
    {
        $requisition->update(['status' => $request->status]);

        return \response()->success($requisition, localize('item Status Updated Successfully'), 200);
    }
}
