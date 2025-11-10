<?php

namespace Modules\VehicleRefueling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\VehicleManagement\Entities\Vehicle;
use Modules\VehicleRefueling\DataTables\FuelRequisitionDataTable;
use Modules\VehicleRefueling\Entities\FuelRequisition;
use Modules\VehicleRefueling\Entities\FuelStation;
use Modules\VehicleRefueling\Entities\FuelType;

class FuelRequisitionController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:fuel_requisition_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy', 'vendorList']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Fuel requisition Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Fuel requisition Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.refueling.requisition',
        ]);
    }

    /**
     * Summary of index
     *
     * @return mixed
     */
    public function index(FuelRequisitionDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of fuel requisition in Database.',
        ]);

        return $dataTable->render('vehiclerefueling::requisition.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vehiclerefueling::requisition.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'station_id' => 'required|exists:fuel_stations,id',
            'type_id' => 'required|exists:fuel_types,id',
            'date' => 'required|date',
            'qty' => 'required|numeric',
            'current_qty' => 'required|numeric',
        ]);
        $item = FuelRequisition::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(FuelRequisition $requisition)
    {
        return view('vehiclerefueling::requisition.create_edit', ['item' => $requisition])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(FuelRequisition $requisition, Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'station_id' => 'required|exists:fuel_stations,id',
            'type_id' => 'required|exists:fuel_types,id',
            'date' => 'required|date',
            'qty' => 'required|numeric',
            'current_qty' => 'required|numeric',
        ]);
        $requisition->update($data);

        return response()->success($requisition, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(FuelRequisition $requisition)
    {

        $requisition->delete();
        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }

    /**
     * Get the vehicle.
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getVehicle(Request $request)
    {
        $items = Vehicle::when($request->search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        })
            ->select(['id', 'name as text'])->paginate(10);

        return response()->json($items);
    }

    /**
     * Get the station.
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getStation(Request $request)
    {
        $items = FuelStation::when($request->search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        })
            ->select(['id', 'name as text'])->paginate(10);

        return response()->json($items);
    }

    /**
     * Get the type.
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getType(Request $request)
    {
        $items = FuelType::when($request->search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        })
            ->select(['id', 'name as text'])->paginate(10);

        return response()->json($items);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function statusUpdate(FuelRequisition $requisition, Request $request)
    {
        $requisition->update(['status' => $request->status]);

        return \response()->success($requisition, localize('item Status Updated Successfully'), 200);
    }
}
