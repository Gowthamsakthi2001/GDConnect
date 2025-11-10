<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\VehicleManagement\DataTables\RTAOfficeDataTable;
use Modules\VehicleManagement\Entities\RTAOffice;

class RTAOfficeController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:vehicle_rta_office_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Vehicle office Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Vehicle office Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle.rta-office',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RTAOfficeDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of Vehicle office in Database.',
        ]);

        return $dataTable->render('vehiclemanagement::rta_office.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vehiclemanagement::rta_office.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:rta_offices,name',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $item = RTAOffice::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(RTAOffice $office)
    {
        return view('vehiclemanagement::rta_office.create_edit', ['item' => $office])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(RTAOffice $office, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:rta_offices,name,' . $office->id . ',id',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $office->update($data);

        return response()->success($office, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(RTAOffice $office)
    {

        $office->delete();
        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }
}
