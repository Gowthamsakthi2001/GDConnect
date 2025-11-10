<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\DataTables\InventoryStockDataTable;
use Modules\Inventory\Entities\InventoryCategory;
use Modules\Inventory\Entities\InventoryParts;

class InventoryStockController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:inventory_stock_management']);
        \cs_set('theme', [
            'title' => 'Inventory stock Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Inventory stock Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.inventory.stock',
        ]);
    }

    public function index(InventoryStockDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of roles in Database.',
        ]);

        return $dataTable->render('inventory::stock.index');
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
}
