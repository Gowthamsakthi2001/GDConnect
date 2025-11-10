<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\DataTables\InventoryLocationDataTable;
use Modules\Inventory\Entities\InventoryLocation;

class InventoryLocationController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:inventory_location_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Inventory location Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Inventory location Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.inventory.location',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InventoryLocationDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of roles in Database.',
        ]);

        return $dataTable->render('inventory::location.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('inventory::location.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:inventory_locations,name',
            'room' => 'integer|min:0',
            'self' => 'integer|min:0',
            'drawer' => 'integer|min:0',
            'capacity' => 'integer|min:0',
            'dimension' => 'integer|min:0',
            'is_active' => 'required|boolean',
        ]);
        $item = InventoryLocation::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(InventoryLocation $location)
    {
        return view('inventory::location.create_edit', ['item' => $location])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(InventoryLocation $location, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:inventory_categories,name,' . $location->id . ',id',
            'room' => 'integer|min:0',
            'self' => 'integer|min:0',
            'drawer' => 'integer|min:0',
            'capacity' => 'integer|min:0',
            'dimension' => 'integer|min:0',
            'is_active' => 'required|boolean',
        ]);
        $location->update($data);

        return response()->success($location, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventoryLocation $location)
    {
        $location->delete();

        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }
}
