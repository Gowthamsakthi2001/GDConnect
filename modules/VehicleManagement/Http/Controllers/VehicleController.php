<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Employee\Entities\Department;
use Modules\Employee\Entities\Driver;
use Modules\Inventory\Entities\Vendor;
use Modules\VehicleManagement\DataTables\VehicleDataTable;
use Modules\VehicleManagement\Entities\RTAOffice;
use Modules\VehicleManagement\Entities\Vehicle;
use Modules\VehicleManagement\Entities\VehicleOwnershipType;
use Modules\VehicleManagement\Entities\VehicleType;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:vehicle_management', 'status_check']);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Vehicle List',
            'description' => 'Displaying all vehicle.',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Vehicle List',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(VehicleDataTable $dataTable)
    {

        return $dataTable->render('vehiclemanagement::vehicle.index', [
            'departments' => Department::all(),
            'vehicle_types' => VehicleType::where('is_active', true)->get(),
            'ownerships' => VehicleOwnershipType::where('is_active', true)->get(),
            'vendors' => Vendor::where('is_active', true)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehiclemanagement::vehicle.create_edit', [
            'departments' => Department::all(),
            'vehicle_types' => VehicleType::where('is_active', true)->get(),
            'ownerships' => VehicleOwnershipType::where('is_active', true)->get(),
            'vendors' => Vendor::where('is_active', true)->get(),
            'drivers' => Driver::where('is_active', true)->get(),
            'circle_offices' => RTAOffice::where('is_active', true)->get(),
        ])->render();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string',
            'department_id' => 'required|integer',
            'registration_date' => 'required',
            'license_plate' => 'required',
            'alert_cell_no' => 'required',
            'ownership_id' => 'nullable|integer',
            'vehicle_type_id' => 'nullable|integer',
            'vehicle_division_id' => 'nullable|integer',
            'rta_circle_office_id' => 'nullable|integer',
            'driver_id' => 'nullable|integer',
            'vendor_id' => 'nullable|integer',
            'seat_capacity' => 'nullable|integer',
        ]);

        $item = Vehicle::create($data);

        return response()->success($item, localize('Vehicle Created Successfully'), 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {

        return view('vehiclemanagement::vehicle.create_edit', [
            'departments' => Department::all(),
            'vehicle_types' => VehicleType::where('is_active', true)->get(),
            'ownerships' => VehicleOwnershipType::where('is_active', true)->get(),
            'vendors' => Vendor::where('is_active', true)->get(),
            'drivers' => Driver::where('is_active', true)->get(),
            'circle_offices' => RTAOffice::where('is_active', true)->get(),
            'item' => $vehicle,
        ])->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {

        $data = $request->validate([
            'name' => 'required|string',
            'department_id' => 'required|integer',
            'registration_date' => 'required',
            'license_plate' => 'required',
            'alert_cell_no' => 'required',
            'ownership_id' => 'nullable|integer',
            'vehicle_type_id' => 'nullable|integer',
            'vehicle_division_id' => 'nullable|integer',
            'rta_circle_office_id' => 'nullable|integer',
            'driver_id' => 'nullable|integer',
            'vendor_id' => 'nullable|integer',
            'seat_capacity' => 'nullable|integer',
        ]);

        $vehicle->update($data);

        return response()->success($vehicle, localize('Vehicle Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {


        $vehicle->delete();
        return response()->success(null, localize('Vehicle Deleted Successfully'), 200);
    }
}
