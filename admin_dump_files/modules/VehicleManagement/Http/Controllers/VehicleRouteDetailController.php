<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\VehicleManagement\DataTables\VehicleRouteDetailDataTable;
use Modules\VehicleManagement\Entities\VehicleRouteDetail;

class VehicleRouteDetailController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:vehicle_route_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Vehicle Route Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Vehicle Route Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle.route-detail',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(VehicleRouteDetailDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of Vehicle Route in Database.',
        ]);

        return $dataTable->render('vehiclemanagement::vehicle_route.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehiclemanagement::vehicle_route.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'route_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'starting_point' => 'required|string|max:255',
            'destination_point' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'create_pick_drop_point' => 'nullable|boolean',
        ]);

        $item = VehicleRouteDetail::create($data);

        return response()->success($item, localize('Vehicle Route Detail has been created Successfully'), 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleRouteDetail $route_detail)
    {

        return view('vehiclemanagement::vehicle_route.create_edit', [
            'item' => $route_detail,
        ])->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehicleRouteDetail $route_detail)
    {

        $data = $request->validate([
            'route_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'starting_point' => 'required|string|max:255',
            'destination_point' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'create_pick_drop_point' => 'nullable|boolean',
        ]);

        $route_detail->update($data);

        return response()->success($route_detail, localize('Vehicle Route Detail has been updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleRouteDetail $route_detail)
    {

        $route_detail->delete();
        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }

    /**
     * get Route detail by id
     */
    public function getRouteByID(VehicleRouteDetail $route_detail)
    {
        return response()->success($route_detail, localize('Vehicle Route Detail has been fetched Successfully'), 200);
    }
}
