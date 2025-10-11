<?php

namespace Modules\VehicleRefueling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Employee\Entities\Driver;
use Modules\VehicleManagement\Entities\Vehicle;
use Modules\VehicleRefueling\DataTables\RefuelingDataTable;
use Modules\VehicleRefueling\Entities\FuelStation;
use Modules\VehicleRefueling\Entities\FuelType;
use Modules\VehicleRefueling\Entities\VehicleRefueling;

class VehicleRefuelingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:refueling_management', 'status_check']);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Refueling List',
            'description' => 'Displaying all Refuelings.',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Refueling List',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.refueling',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(RefuelingDataTable $dataTable)
    {
        return $dataTable->render('vehiclerefueling::refueling.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehiclerefueling::refueling.create_edit', [
            'vehicles' => Vehicle::all(),
            'drivers' => Driver::where('is_active', true)->get(),
            'fuel_types' => FuelType::where('is_active', true)->get(),
            'fuel_stations' => FuelStation::where('is_active', true)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'vehicle_id' => 'required|integer',
            'driver_id' => 'required|integer',
            'fuel_type_id' => 'required|integer',
            'fuel_station_id' => 'required|integer',
            'place' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'km_per_unit' => 'required|numeric|min:0',
            'last_reading' => 'required|numeric|min:0',
            'last_unit' => 'required|numeric|min:0',
            'refuel_limit' => 'required|numeric|min:0',
            'max_unit' => 'required|numeric|min:0',
            'unit_taken' => 'required|numeric|min:0',
            'odometer_day_end' => 'nullable|string',
            'odometer_refuel_time' => 'nullable|string',
            'consumption_percent' => 'required|numeric|min:0',
        ]);

        if ($request->hasFile('fuel_slip')) {
            $data['slip_path'] = upload_file($request, 'fuel_slip', 'refuel_slip');
        }

        $data['strict_policy'] = $request->has('strict_policy') ? 1 : 0;

        $item = VehicleRefueling::create($data);

        return response()->success($item, localize('Item Added Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleRefueling $vehicle_refueling)
    {
        return view('vehiclerefueling::refueling.create_edit', [
            'vehicles' => Vehicle::all(),
            'drivers' => Driver::where('is_active', true)->get(),
            'fuel_types' => FuelType::where('is_active', true)->get(),
            'fuel_stations' => FuelStation::where('is_active', true)->get(),
            'item' => $vehicle_refueling,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehicleRefueling $vehicle_refueling)
    {
        $data = $request->validate([
            'vehicle_id' => 'required|integer',
            'driver_id' => 'required|integer',
            'fuel_type_id' => 'required|integer',
            'fuel_station_id' => 'required|integer',
            'place' => 'required|string',
            'budget' => 'required|numeric',
            'km_per_unit' => 'required|numeric',
            'last_reading' => 'nullable|numeric',
            'last_unit' => 'nullable|numeric',
            'refuel_limit' => 'nullable|numeric',
            'max_unit' => 'required|numeric',
            'unit_taken' => 'nullable|numeric',
            'odometer_day_end' => 'nullable|string',
            'odometer_refuel_time' => 'nullable|string',
            'consumption_percent' => 'nullable|numeric',
        ]);

        if ($request->hasFile('fuel_slip')) {
            $data['slip_path'] = upload_file($request, 'fuel_slip', 'refuel_slip');

            if ($vehicle_refueling->slip_path) {
                delete_file($vehicle_refueling->slip_path);
            }
        }

        $data['strict_policy'] = $request->has('strict_policy') ? 1 : 0;

        $vehicle_refueling->update($data);

        return response()->success($vehicle_refueling, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleRefueling $vehicle_refueling)
    {



        if ($vehicle_refueling->slip_path) {
            delete_file($vehicle_refueling->slip_path);
        }

        $vehicle_refueling->delete();

        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }
}
