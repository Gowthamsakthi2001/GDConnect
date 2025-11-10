<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\VehicleManagement\DataTables\VehicleOwnershipTypeDataTable;
use Modules\VehicleManagement\Entities\VehicleOwnershipType;

class VehicleOwnershipTypeController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:vehicle_ownership_type_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Vehicle Ownership Type Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Vehicle Ownership Type Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle.ownership.type',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(VehicleOwnershipTypeDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of Vehicle Ownership Type in Database.',
        ]);

        return $dataTable->render('vehiclemanagement::ownership.type.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vehiclemanagement::ownership.type.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_ownership_types,name',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $item = VehicleOwnershipType::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(VehicleOwnershipType $type)
    {
        return view('vehiclemanagement::ownership.type.create_edit', ['item' => $type])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleOwnershipType $type, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_ownership_types,name,' . $type->id . ',id',
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
    public function destroy(VehicleOwnershipType $type)
    {

        $type->delete();
        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }
}
