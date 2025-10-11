<?php

namespace Modules\VehicleRefueling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\Entities\Vendor;
use Modules\VehicleRefueling\DataTables\FuelStationDataTable;
use Modules\VehicleRefueling\Entities\FuelStation;

class FuelStationController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:fuel_station_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy', 'vendorList']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Fuel Station Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Fuel station Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.refueling.station',
        ]);
    }

    /**
     * Summary of index
     *
     * @return mixed
     */
    public function index(FuelStationDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of fuel station in Database.',
        ]);

        return $dataTable->render('vehiclerefueling::station.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vehiclerefueling::station.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255|unique:fuel_stations,name',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $item = FuelStation::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(FuelStation $station)
    {
        return view('vehiclerefueling::station.create_edit', ['item' => $station])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(FuelStation $station, Request $request)
    {
        $data = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255|unique:fuel_stations,name,' . $station->id . ',id',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $station->update($data);

        return response()->success($station, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(FuelStation $station)
    {

        $station->delete();
        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }

    /**
     * Get all active fuel stations id and name as text value pair paginated
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function vendorList(Request $request)
    {
        // get all active fuel stations id and name as text value pair paginated
        $items = Vendor::where('is_active', true)
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->select(['id', 'name as text'])->paginate(10);

        return response()->json($items);
    }
}
