<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\DataTables\InventoryPartsDataTable;
use Modules\Inventory\Entities\InventoryCategory;
use Modules\Inventory\Entities\InventoryLocation;
use Modules\Inventory\Entities\InventoryParts;

class InventoryPartsController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:inventory_parts_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Inventory parts Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Inventory parts Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.inventory.parts',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(InventoryPartsDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of roles in Database.',
        ]);

        return $dataTable->render('inventory::parts.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        cs_set('theme', [
            'title' => 'Create Inventory parts',
        ]);
        $categories = InventoryCategory::where('is_active', 1)->get();
        $locations = InventoryLocation::where('is_active', 1)->get();

        return view('inventory::parts.create_edit', compact('categories', 'locations'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|integer|exists:inventory_categories,id',
            'location_id' => 'required|integer|exists:inventory_locations,id',
            'name' => 'required|string|max:255|unique:inventory_parts,name',
            'description' => 'nullable|string',
            'qty' => 'integer|min:0',
            'remarks' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $item = InventoryParts::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(InventoryParts $parts)
    {
        cs_set('theme', [
            'title' => 'Edit Inventory parts',
        ]);

        $categories = InventoryCategory::where('is_active', 1)->get();
        $locations = InventoryLocation::where('is_active', 1)->get();

        return view('inventory::parts.create_edit', [
            'item' => $parts,
            'categories' => $categories,
            'locations' => $locations,
        ])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(InventoryParts $parts, Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|integer|exists:inventory_categories,id',
            'location_id' => 'required|integer|exists:inventory_locations,id',
            'name' => 'required|string|max:255|unique:inventory_parts,name,' . $parts->id . ',id',
            'description' => 'nullable|string',
            'qty' => 'integer|min:0',
            'remarks' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $parts->update($data);

        return response()->success($parts, localize('Item Updated Successfully'), 200);
    }

    /**
     * Show the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(InventoryParts $parts)
    {

        cs_set('theme', [
            'title' => 'Inventory parts Details',
        ]);

        return view('inventory::parts.show', ['item' => $parts])->render();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventoryParts $parts)
    {
        $parts->delete();
        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }
}
