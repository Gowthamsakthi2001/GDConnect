<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\DataTables\ExpenseTypeDataTable;
use Modules\Inventory\Entities\ExpenseType;

class ExpenseTypeController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:expense_type_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Expense Type Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Expense Type Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.inventory.expense.type',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ExpenseTypeDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of expense types in Database.',
        ]);

        return $dataTable->render('inventory::expense.type.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('inventory::expense.type.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $item = ExpenseType::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExpenseType $type)
    {
        return view('inventory::expense.type.create_edit', ['item' => $type])->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExpenseType $type)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $type->update($data);

        return response()->success($type, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseType $type)
    {
        $type->delete();
        return response()->success($type, localize('Item Deleted Successfully'), 200);
    }
}
