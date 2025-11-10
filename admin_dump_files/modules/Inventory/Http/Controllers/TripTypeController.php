<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\DataTables\TripTypeDataTable;
use Modules\Inventory\Entities\TripType;

class TripTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:trip_type_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Trip Type Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Trip Type Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.inventory.trip-type',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TripTypeDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of expense types in Database.',
        ]);

        return $dataTable->render('inventory::trip_type.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('inventory::trip_type.create_edit')->render();
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

        $item = TripType::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TripType $trip_type)
    {
        return view('inventory::trip_type.create_edit', ['item' => $trip_type])->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TripType $trip_type)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $trip_type->update($data);

        return response()->success($trip_type, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TripType $trip_type)
    {


        $trip_type->delete();
        return response()->success($trip_type, localize('Item Deleted Successfully'), 200);
    }
}
