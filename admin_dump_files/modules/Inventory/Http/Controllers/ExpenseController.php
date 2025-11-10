<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Entities\Employee;
use Modules\Inventory\DataTables\ExpenseDataTable;
use Modules\Inventory\Entities\Expense;
use Modules\Inventory\Entities\ExpenseType;
use Modules\Inventory\Entities\TripType;
use Modules\Inventory\Entities\Vendor;
use Modules\VehicleManagement\Entities\Vehicle;

class ExpenseController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:expense_management']);
        $this->middleware('request:ajax', ['only' => ['destroy', 'show', 'getType', 'getEmployee', 'getVendor', 'getVehicle', 'getTripType', 'getMaintenanceType', 'statusUpdate']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update', 'statusUpdate']);
        \cs_set('theme', [
            'title' => 'Expense Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Expense Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.inventory.expense',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ExpenseDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of Expense in Database.',
        ]);

        return $dataTable->render('inventory::expense.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        \cs_set('theme', [
            'title' => 'Create Expense',
            'description' => 'Display a listing of Expense in Database.',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Expense Lists',
                    'link' => route(config('theme.rprefix').'.index'),
                ],
                [
                    'name' => 'Add New Expense',
                    'link' => false,
                ],
            ],
        ]);

        return view('inventory::expense.create_edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'type_id' => 'required|in:'.implode(',', array_keys(Expense::getTypes())),
            'employee_id' => 'required|exists:employees,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'trip_type_id' => 'required|exists:trip_types,id',
            'trip_number' => 'required|numeric',
            'odometer_millage' => 'required|numeric',
            'vehicle_rent' => 'required|numeric',
            'date' => 'required|date',
            'remarks' => 'nullable|string',
            'collection.*.type_id' => 'required|exists:expense_types,id',
            'collection.*.qty' => 'required|numeric',
            'collection.*.price' => 'required|numeric',
        ]);
        try {
            DB::beginTransaction();
            $expense = Expense::create($data);
            $details = [];
            foreach ($request->collection as $d) {
                $details[] = [
                    'expense_id' => $expense->id,
                    'type_id' => $d['type_id'],
                    'qty' => $d['qty'],
                    'price' => $d['price'],
                    'total' => $d['qty'] * $d['price'],
                ];
            }
            $expense->details()->createMany($details);
            $expense->update(['total' => $expense->details->sum('total')]);
            DB::commit();
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong, please try again');
        }

        return redirect()->route(config('theme.rprefix').'.index')->with('success', localize('Item created successfully'));
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        //  'details.category:id.name', 'details.parts:id.name'
        $item =
            Expense::with('employee:id,name', 'vendor:id,name', 'vehicle:id,name', 'details', 'details.type:id,name')->findOrFail($id);

        return view('inventory::expense.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id)
    {
        \cs_set('theme', [
            'title' => 'Edit Expense',
            'description' => 'Display a listing of Expense in Database.',
        ]);
        $item = Expense::with('employee:id,name', 'vendor:id,name', 'vehicle:id,name', 'details', 'details.type:id,name')->findOrFail($id);

        return view('inventory::expense.create_edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'type_id' => 'required|in:'.implode(',', array_keys(Expense::getTypes())),
            'employee_id' => 'required|exists:employees,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'trip_type_id' => 'required|exists:trip_types,id',
            'trip_number' => 'required|numeric',
            'odometer_millage' => 'required|numeric',
            'vehicle_rent' => 'required|numeric',
            'date' => 'required|date',
            'remarks' => 'nullable|string',
            'collection.*.type_id' => 'required|exists:expense_types,id',
            'collection.*.qty' => 'required|numeric',
            'collection.*.price' => 'required|numeric',
        ]);
        try {
            DB::beginTransaction();
            $details = [];
            foreach ($request->collection as $d) {
                $details[] = [
                    'expense_id' => $expense->id,
                    'type_id' => $d['type_id'],
                    'qty' => $d['qty'],
                    'price' => $d['price'],
                    'total' => $d['qty'] * $d['price'],
                ];
            }
            $expense->details()->delete();
            $expense->details()->createMany($details);
            $data['total'] = $expense->details->sum('total');
            $expense->update($data);
            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong, please try again');
        }

        return redirect()->route(config('theme.rprefix').'.index')->with('success', 'Expense Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        // delete the Expense details
        $expense->details()->delete();
        // delete the Expense
        $expense->delete();

        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }

    /**
     * Get Type
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getType(Request $request)
    {
        $items = ExpenseType::where('is_active', true)
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', '%'.$search.'%');
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
     * Get getVendor
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getVendor(Request $request)
    {
        // get all active Inventory Category id and name as text value pair paginated
        $items = Vendor::when($request->search, function ($query, $search) {
            return $query->where('name', 'like', '%'.$search.'%');
        })
            ->where('is_active', true)
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
     * get Trip Type
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getTripType(Request $request)
    {
        // get all active Inventory Category id and name as text value pair paginated
        $items = TripType::when($request->search, function ($query, $search) {
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
        $items = ExpenseType::where('is_active', true)
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
    public function statusUpdate(Expense $expense, Request $request)
    {
        $expense->update(['status' => $request->status]);

        return \response()->success($expense, localize('item Status Updated Successfully'), 200);
    }
}
