<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\VehicleManagement\DataTables\VehicleDivisionDataTable;
use Modules\VehicleManagement\Entities\VehicleDivision;

class VehicleDivisionController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:vehicle_division_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Vehicle Division Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Vehicle Division Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle.division',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(VehicleDivisionDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of Vehicle Type in Database.',
        ]);

        return $dataTable->render('vehiclemanagement::division.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vehiclemanagement::division.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_divisions,name',
            'is_active' => 'required|boolean',
        ]);
        $item = VehicleDivision::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(VehicleDivision $division)
    {
        return view('vehiclemanagement::division.create_edit', ['item' => $division])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleDivision $division, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_divisions,name,' . $division->id . ',id',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $division->update($data);

        return response()->success($division, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(VehicleDivision $division)
    {

        $division->delete();
        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }
}
