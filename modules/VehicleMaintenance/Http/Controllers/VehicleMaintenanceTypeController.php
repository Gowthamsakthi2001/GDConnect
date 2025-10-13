<?php

namespace Modules\VehicleMaintenance\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\VehicleMaintenance\DataTables\VehicleMaintenanceTypeDataTable;
use Modules\VehicleMaintenance\Entities\VehicleMaintenanceType;

class VehicleMaintenanceTypeController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:vehicle_maintenance_type_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Vehicle Maintenance type Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Vehicle Maintenance type Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle.maintenance.type',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(VehicleMaintenanceTypeDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of Vehicle Maintenance types in Database.',
        ]);

        return $dataTable->render('vehiclemaintenance::type.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        cs_set('theme', [
            'title' => 'Create Vehicle Maintenance type',
        ]);

        return view('vehiclemaintenance::type.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_maintenance_types,name',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $item = VehicleMaintenanceType::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(VehicleMaintenanceType $type)
    {
        cs_set('theme', [
            'title' => 'Edit Item',
        ]);

        return view('vehiclemaintenance::type.create_edit', [
            'item' => $type,
        ])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleMaintenanceType $type, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_maintenance_types,name,' . $type->id . ',id',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $type->update($data);

        return response()->success($type, localize('Item Updated Successfully'), 200);
    }

    /**
     * Show the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(VehicleMaintenanceType $type)
    {

        cs_set('theme', [
            'title' => 'Inventory type Details',
        ]);

        return view('vehiclemaintenance::type.show', ['item' => $type])->render();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(VehicleMaintenanceType $type)
    {

        $type->delete();
        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }
}
