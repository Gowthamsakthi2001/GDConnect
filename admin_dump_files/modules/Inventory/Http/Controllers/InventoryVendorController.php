<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\DataTables\VendorDataTable;
use Modules\Inventory\Entities\Vendor;

class InventoryVendorController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:inventory_vendor_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Vendor Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Vendor Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.inventory.vendor',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(VendorDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of vendor in Database.',
        ]);

        return $dataTable->render('inventory::vendor.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('inventory::vendor.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);
        $item = Vendor::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(Vendor $vendor)
    {
        return view('inventory::vendor.create_edit', ['item' => $vendor])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Vendor $vendor, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);
        $vendor->update($data);

        return response()->success($vendor, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendor $vendor)
    {

        $vendor->delete();
        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }
}
