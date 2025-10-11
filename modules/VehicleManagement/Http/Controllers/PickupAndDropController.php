<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Employee\Entities\Employee;
use Modules\VehicleManagement\DataTables\PickAndDropDataTable;
use Modules\VehicleManagement\Entities\PickupAndDrop;
use Modules\VehicleManagement\Entities\VehicleRouteDetail;

class PickupAndDropController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:pick_drop_requisition']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Pickup and Drop Requisition Details',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Pickup and Drop Requisition Details',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle.pick-drop',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PickAndDropDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of Pick and Drop requisition in Database.',
        ]);

        return $dataTable->render('vehiclemanagement::pick_drop.index', [
            'vehicle_routes' => VehicleRouteDetail::where('is_active', 1)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehiclemanagement::pick_drop.create_edit', [
            'vehicle_routes' => VehicleRouteDetail::where('is_active', 1)->get(),
            'employees' => Employee::all(),
        ])->render();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'route_id' => 'required',
            'start_point' => 'required',
            'end_point' => 'required',
            'employee_id' => 'required',
            'request_type' => 'required',
            'type' => 'required',
            'effective_date' => 'nullable',
        ]);

        $item = PickupAndDrop::create($request->all());

        return response()->success($item, localize('Pickup and Drop Requisition has been created Successfully'), 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PickupAndDrop $pick_drop)
    {
        return view('vehiclemanagement::pick_drop.create_edit', [
            'vehicle_routes' => VehicleRouteDetail::where('is_active', 1)->get(),
            'employees' => Employee::all(),
            'item' => $pick_drop,
        ])->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PickupAndDrop $pick_drop)
    {
        $request->validate([
            'route_id' => 'required',
            'start_point' => 'required',
            'end_point' => 'required',
            'employee_id' => 'required',
            'request_type' => 'required',
            'type' => 'required',
            'effective_date' => 'nullable',
        ]);

        $pick_drop->update($request->all());

        return response()->success($pick_drop, localize('Pickup and Drop Requisition has been updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PickupAndDrop $pick_drop)
    {

        $pick_drop->delete();
        return response()->success($pick_drop, localize('Pickup and Drop Requisition has been deleted Successfully'), 200);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function statusUpdate(PickupAndDrop $pick_drop, Request $request)
    {
        $pick_drop->update(['status' => $request->status]);

        return \response()->success($pick_drop, localize('item Status Updated Successfully'), 200);
    }
}
