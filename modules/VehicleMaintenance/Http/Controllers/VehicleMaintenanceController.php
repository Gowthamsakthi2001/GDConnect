<?php

namespace Modules\VehicleMaintenance\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Entities\Employee;
use Modules\Inventory\Entities\InventoryCategory;
use Modules\Inventory\Entities\InventoryParts;
use Modules\VehicleMaintenance\DataTables\VehicleMaintenanceDataTable;
use Modules\VehicleMaintenance\Entities\VehicleMaintenance;
use Modules\VehicleMaintenance\Entities\VehicleMaintenanceType;
use Modules\VehicleManagement\Entities\Vehicle;

class VehicleMaintenanceController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:vehicle_maintenance_management']);
        $this->middleware('request:ajax', ['only' => ['destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update', 'statusUpdate']);
        \cs_set('theme', [
            'title' => 'Vehicle Maintenance Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Vehicle Maintenance Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle.maintenance',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(VehicleMaintenanceDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of Vehicle Maintenance in Database.',
        ]);

        return $dataTable->render('vehiclemaintenance::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        \cs_set('theme', [
            'title' => 'Create Vehicle Maintenance',
            'description' => 'Display a listing of Vehicle Maintenance in Database.',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Vehicle Maintenance Lists',
                    'link' => route(config('theme.rprefix').'.index'),
                ],
                [
                    'name' => 'Add New Vehicle Maintenance',
                    'link' => false,
                ],
            ],
        ]);

        return view('vehiclemaintenance::create_edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'type_id' => 'required|in:'.implode(',', array_keys(VehicleMaintenance::getTypes())),
            'priority' => 'required|in:'.implode(',', array_keys(VehicleMaintenance::getPriorities())),
            'employee_id' => 'required|exists:employees,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type_id' => 'required|exists:vehicle_maintenance_types,id',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'charge_bear_by' => 'nullable|string|max:255',
            'charge' => 'nullable|numeric',
            'remarks' => 'nullable|string',
            'collection.*.category_id' => 'required|exists:inventory_categories,id',
            'collection.*.parts_id' => 'required|exists:inventory_parts,id',
            'collection.*.qty' => 'required|numeric',
            'collection.*.price' => 'required|numeric',
        ]);
        try {
            DB::beginTransaction();
            $maintenance = VehicleMaintenance::create($data);
            $details = [];
            foreach ($request->collection as $d) {
                $details[] = [
                    'maintenance_id' => $maintenance->id,
                    'category_id' => $d['category_id'],
                    'parts_id' => $d['parts_id'],
                    'qty' => $d['qty'],
                    'price' => $d['price'],
                    'total' => $d['qty'] * $d['price'],
                ];
            }
            $maintenance->details()->createMany($details);
            $maintenance->update(['total' => $maintenance->details->sum('total')]);
            DB::commit();
        } catch (\Throwable $th) {
            // throw $th;
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong, please try again');
        }

        return redirect()->route(config('theme.rprefix').'.index')->with('success', localize('Item created successfully'));
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        //  'details.category:id.name', 'details.parts:id.name'
        $item = VehicleMaintenance::with('employee:id,name', 'vehicle:id,name', 'details', 'details.category:id,name', 'details.parts:id,name')->findOrFail($id);

        return view('vehiclemaintenance::show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        \cs_set('theme', [
            'title' => 'Edit Vehicle Maintenance',
            'description' => 'Display a listing of Vehicle Maintenance in Database.',
        ]);
        $item = VehicleMaintenance::with('employee:id,name', 'vehicle:id,name', 'details', 'details.category:id,name', 'details.parts:id,name')->findOrFail($id);

        return view('vehiclemaintenance::create_edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehicleMaintenance $maintenance)
    {
        $data = $request->validate([
            'type_id' => 'required|in:'.implode(',', array_keys(VehicleMaintenance::getTypes())),
            'priority' => 'required|in:'.implode(',', array_keys(VehicleMaintenance::getPriorities())),
            'employee_id' => 'required|exists:employees,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type_id' => 'required|exists:vehicle_maintenance_types,id',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'charge_bear_by' => 'nullable|string|max:255',
            'charge' => 'nullable|numeric',
            'remarks' => 'nullable|string',
            'collection.*.category_id' => 'required|exists:inventory_categories,id',
            'collection.*.parts_id' => 'required|exists:inventory_parts,id',
            'collection.*.qty' => 'required|numeric',
            'collection.*.price' => 'required|numeric',
        ]);

        if ($request->hasFile('req_img_path')) {
            $data['req_img_path'] = upload_file($request, 'req_img_path', 'VehicleMaintenance/requisition');
        }
        if ($request->hasFile('order_path')) {
            $data['order_path'] = upload_file($request, 'order_path', 'VehicleMaintenance/work-order');
        }
        try {
            DB::beginTransaction();
            $details = [];
            foreach ($request->collection as $d) {
                $details[] = [
                    'VehicleMaintenance_id' => $maintenance->id,
                    'category_id' => $d['category_id'],
                    'parts_id' => $d['parts_id'],
                    'qty' => $d['qty'],
                    'price' => $d['price'],
                    'total' => $d['qty'] * $d['price'],
                ];
            }
            $maintenance->details()->delete();
            $maintenance->details()->createMany($details);
            $data['total'] = $maintenance->details->sum('total');
            $maintenance->update($data);
            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong, please try again');
        }

        return redirect()->route(config('theme.rprefix').'.index')->with('success', 'VehicleMaintenance updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(VehicleMaintenance $maintenance)
    {
        // delete the VehicleMaintenance details
        $maintenance->details()->delete();
        // delete the VehicleMaintenance
        $maintenance->delete();

        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }

    /**
     * Get Inventory Category
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getInventoryCategory(Request $request)
    {
        // get all active Inventory Category id and name as text value pair paginated
        $items = InventoryCategory::where('is_active', true)
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', '%'.$search.'%');
            })
            ->select(['id', 'name as text'])->paginate(10);

        return response()->json($items);
    }

    /**
     * Get Inventory Parts
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getParts(Request $request)
    {
        // get all active Inventory Category id and name as text value pair paginated
        $items = InventoryParts::where('is_active', true)
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', '%'.$search.'%');
            })->when($request->category_id, function ($query, $category_id) {
                return $query->where('category_id', $category_id);
            })
            ->select(['id', 'name as text'])->paginate(10);

        return response()->json($items);
    }

    /**
     * Get Employee
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getEmployee(Request $request)
    {
        // get all active Inventory Category id and name as text value pair paginated
        $items = Employee::when($request->search, function ($query, $search) {
            return $query->where('name', 'like', '%'.$search.'%');
        })
            ->select(['id', 'name as text'])->paginate(10);

        return response()->json($items);
    }

    /**
     * Get Vehicle
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getVehicle(Request $request)
    {
        // get all active Inventory Category id and name as text value pair paginated
        $items = Vehicle::when($request->search, function ($query, $search) {
            return $query->where('name', 'like', '%'.$search.'%');
        })
            ->select(['id', 'name as text'])->paginate(10);

        return response()->json($items);
    }

    /**
     * Get Maintenance Type
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getMaintenanceType(Request $request)
    {
        $items = VehicleMaintenanceType::where('is_active', true)
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', '%'.$search.'%');
            })
            ->select(['id', 'name as text'])->paginate(10);

        return response()->json($items);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function statusUpdate(VehicleMaintenance $maintenance, Request $request)
    {
        $maintenance->update(['status' => $request->status]);

        return \response()->success($maintenance, localize('item Status Updated Successfully'), 200);
    }
}
