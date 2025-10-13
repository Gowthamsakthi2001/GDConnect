<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\VehicleManagement\DataTables\VehicleRequisitionTypeDataTable;
use Modules\VehicleManagement\Entities\VehicleRequisitionType;

class VehicleRequisitionTypeController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:vehicle_requisition_type_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Vehicle requisition Type Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Vehicle requisition Type Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle.requisition.type',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(VehicleRequisitionTypeDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of Vehicle requisition Type in Database.',
        ]);

        return $dataTable->render('vehiclemanagement::requisition.type.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vehiclemanagement::requisition.type.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_requisition_types,name',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $item = VehicleRequisitionType::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(VehicleRequisitionType $type)
    {
        return view('vehiclemanagement::requisition.type.create_edit', ['item' => $type])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleRequisitionType $type, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_requisition_types,name,' . $type->id . ',id',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $type->update($data);

        return response()->success($type, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(VehicleRequisitionType $type)
    {

        $type->delete();
        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }
}
