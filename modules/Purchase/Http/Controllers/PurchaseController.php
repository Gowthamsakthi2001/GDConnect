<?php

namespace Modules\Purchase\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\InventoryCategory;
use Modules\Inventory\Entities\InventoryParts;
use Modules\Inventory\Entities\Vendor;
use Modules\Purchase\DataTables\PurchaseDataTable;
use Modules\Purchase\Entities\Purchase;

class PurchaseController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:purchase_management']);
        $this->middleware('request:ajax', ['only' => ['destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Purchase Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Purchase Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.purchase',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(PurchaseDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of purchase in Database.',
        ]);

        return $dataTable->render('purchase::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        \cs_set('theme', [
            'title' => 'Create Purchase',
            'description' => 'Display a listing of roles in Database.',
        ]);

        return view('purchase::create_edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'date' => 'required|date',
            'req_img_path' => 'nullable|image',
            'order_path' => 'nullable|image',
            'collection.*.category_id' => 'required|exists:inventory_categories,id',
            'collection.*.parts_id' => 'required|exists:inventory_parts,id',
            'collection.*.qty' => 'required|numeric',
            'collection.*.price' => 'required|numeric',
        ]);

        if ($request->hasFile('req_img_path')) {
            $data['req_img_path'] = upload_file($request, 'req_img_path', 'purchase/requisition');
        }
        if ($request->hasFile('order_path')) {
            $data['order_path'] = upload_file($request, 'order_path', 'purchase/work-order');
        }
        try {
            DB::beginTransaction();
            $purchase = Purchase::create($data);
            $details = [];
            foreach ($request->collection as $d) {
                $details[] = [
                    'purchase_id' => $purchase->id,
                    'category_id' => $d['category_id'],
                    'parts_id' => $d['parts_id'],
                    'qty' => $d['qty'],
                    'price' => $d['price'],
                    'total' => $d['qty'] * $d['price'],
                ];
            }
            $purchase->details()->createMany($details);
            $purchase->update(['total' => $purchase->details->sum('total')]);
            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong, please try again');
        }

        return redirect()->route(config('theme.rprefix').'.index')->with('success', 'Purchase created successfully');
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        //  'details.category:id.name', 'details.parts:id.name'
        $item = Purchase::with('vendor:id,name', 'details', 'details.category:id,name', 'details.parts:id,name')->findOrFail($id);

        return view('purchase::show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id)
    {
        $item = Purchase::with('vendor:id,name', 'details', 'details.category:id,name', 'details.parts:id,name')->findOrFail($id);

        return view('purchase::create_edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $data = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'date' => 'required|date',
            'req_img_path' => 'nullable|image',
            'order_path' => 'nullable|image',
            'collection.*.category_id' => 'required|exists:inventory_categories,id',
            'collection.*.parts_id' => 'required|exists:inventory_parts,id',
            'collection.*.qty' => 'required|numeric',
            'collection.*.price' => 'required|numeric',
        ]);

        if ($request->hasFile('req_img_path')) {
            $data['req_img_path'] = upload_file($request, 'req_img_path', 'purchase/requisition');
        }
        if ($request->hasFile('order_path')) {
            $data['order_path'] = upload_file($request, 'order_path', 'purchase/work-order');
        }
        try {
            DB::beginTransaction();
            $details = [];
            foreach ($request->collection as $d) {
                $details[] = [
                    'purchase_id' => $purchase->id,
                    'category_id' => $d['category_id'],
                    'parts_id' => $d['parts_id'],
                    'qty' => $d['qty'],
                    'price' => $d['price'],
                    'total' => $d['qty'] * $d['price'],
                ];
            }
            $purchase->details()->delete();
            $purchase->details()->createMany($details);
            $data['total'] = $purchase->details->sum('total');
            $purchase->update($data);
            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong, please try again');
        }

        return redirect()->route(config('theme.rprefix').'.index')->with('success', 'Purchase updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        // delete the purchase details
        $purchase->details()->delete();
        // delete the purchase
        $purchase->delete();

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
    public function getVendor(Request $request)
    {
        // get all active Inventory Category id and name as text value pair paginated
        $items = Vendor::where('is_active', true)
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
    public function statusUpdate(Purchase $purchase, Request $request)
    {
        $purchase->update(['status' => $request->status]);

        return \response()->success($purchase, localize('item Status Updated Successfully'), 200);
    }
}
