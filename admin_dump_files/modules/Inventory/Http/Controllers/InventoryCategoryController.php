<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\DataTables\InventoryCategoryDataTable;
use Modules\Inventory\Entities\InventoryCategory;

class InventoryCategoryController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:inventory_category_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Inventory Category Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Inventory Category Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.inventory.category',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InventoryCategoryDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of roles in Database.',
        ]);

        return $dataTable->render('inventory::category.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('inventory::category.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:inventory_categories,name',
            'is_active' => 'required|boolean',
        ]);
        $item = InventoryCategory::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(InventoryCategory $category)
    {
        return view('inventory::category.create_edit', ['item' => $category])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(InventoryCategory $category, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:inventory_categories,name,' . $category->id . ',id',
            'is_active' => 'required|boolean',
        ]);
        $category->update($data);

        return response()->success($category, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventoryCategory $category)
    {
        $category->delete();
        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }
}
