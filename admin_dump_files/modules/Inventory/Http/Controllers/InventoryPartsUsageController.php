<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\DataTables\InventoryPartsUsageDataTable;
use Modules\Inventory\Entities\InventoryCategory;
use Modules\Inventory\Entities\InventoryParts;
use Modules\Inventory\Entities\InventoryPartsUsage;
use Modules\VehicleManagement\Entities\Vehicle;

class InventoryPartsUsageController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:inventory_parts_usage_management']);
        $this->middleware('request:ajax', ['only' => ['destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update', 'statusUpdate']);
        \cs_set('theme', [
            'title' => 'Parts Usage Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Parts Usage Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.inventory.parts.usage',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(InventoryPartsUsageDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of Parts Usage in Database.',
        ]);

        return $dataTable->render('inventory::parts-usage.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        \cs_set('theme', [
            'title' => 'Create Parts Usage',
            'description' => 'Display a listing of Parts Usage in Database.',
        ]);

        return view('inventory::parts-usage.create_edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'date' => 'required|date',
            'remarks' => 'nullable|string',
            'collection.*.category_id' => 'required|exists:inventory_categories,id',
            'collection.*.parts_id' => 'required|exists:inventory_parts,id',
            'collection.*.qty' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();
            $data['created_by'] = auth()->id();
            $parts_usage = InventoryPartsUsage::create($data);
            $details = [];
            foreach ($request->collection as $d) {
                $details[] = [
                    'parts_usage_id' => $parts_usage->id,
                    'category_id' => $d['category_id'],
                    'parts_id' => $d['parts_id'],
                    'qty' => $d['qty'],
                ];
            }
            $parts_usage->details()->createMany($details);
            DB::commit();
        } catch (\Throwable $th) {
            // throw $th;
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong, please try again');
        }

        return redirect()->route(config('theme.rprefix').'.index')->with('success', 'Item created successfully');
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        $item = InventoryPartsUsage::with('vehicle:id,name', 'details', 'details.category:id,name', 'details.parts:id,name')->findOrFail($id);

        return view('inventory::parts-usage.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id)
    {
        $item = InventoryPartsUsage::with('vehicle:id,name', 'details', 'details.category:id,name', 'details.parts:id,name')->findOrFail($id);

        return view('inventory::parts-usage.create_edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventoryPartsUsage $parts_usage)
    {
        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'date' => 'required|date',
            'remarks' => 'nullable|string',
            'collection.*.category_id' => 'required|exists:inventory_categories,id',
            'collection.*.parts_id' => 'required|exists:inventory_parts,id',
            'collection.*.qty' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();
            $details = [];
            foreach ($request->collection as $d) {
                $details[] = [
                    'parts_usage_id' => $parts_usage->id,
                    'category_id' => $d['category_id'],
                    'parts_id' => $d['parts_id'],
                    'qty' => $d['qty'],
                ];
            }
            $parts_usage->details()->delete();
            $parts_usage->details()->createMany($details);
            $parts_usage->update($data);
            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong, please try again');
        }

        return redirect()->route(config('theme.rprefix').'.index')->with('success', 'Item updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventoryPartsUsage $parts_usage)
    {
        // delete the parts_usage details
        $parts_usage->details()->delete();
        // delete the parts_usage
        $parts_usage->delete();

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
     * Get Vendor
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
     * Display the specified resource.
     *
     * @param  \Modules\Purchase\Entities\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function statusUpdate(InventoryPartsUsage $parts_usage, Request $request)
    {
        $parts_usage->update(['status' => $request->status]);

        return \response()->success($parts_usage, localize('item Status Updated Successfully'), 200);
    }
}
