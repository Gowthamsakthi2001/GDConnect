<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\VehicleManagement\DataTables\RequisitionPurposeDataTable;
use Modules\VehicleManagement\Entities\VehicleRequisitionPurpose;

class VehicleRequisitionPurposeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:vehicle_requisition_type_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Vehicle requisition purpose Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Vehicle requisition purpose Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle.requisition.purpose',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RequisitionPurposeDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of Vehicle requisition Purpose in Database.',
        ]);

        return $dataTable->render('vehiclemanagement::requisition.purpose.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehiclemanagement::requisition.purpose.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_requisition_purposes,name',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $item = VehicleRequisitionPurpose::create($data);

        return response()->success($item, localize('Purpose Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(VehicleRequisitionPurpose $purpose)
    {
        return view('vehiclemanagement::requisition.purpose.create_edit', ['item' => $purpose])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleRequisitionPurpose $purpose, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_requisition_purposes,name,' . $purpose->id . ',id',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $purpose->update($data);

        return response()->success($purpose, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(VehicleRequisitionPurpose $purpose)
    {

        $purpose->delete();
        return response()->success(null, localize('Purpose Deleted Successfully'), 200);
    }
}
