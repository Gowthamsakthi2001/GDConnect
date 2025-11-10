<?php

namespace Modules\Employee\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Employee\DataTables\PositionDataTable;
use Modules\Employee\Entities\Position;

class PositionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:employee_management', 'status_check']);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Position List',
            'description' => 'Displaying all Positions.',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Positions List',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.position',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(PositionDataTable $dataTable)
    {
        return $dataTable->render('employee::position.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employee::position.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name',
            'description' => 'nullable|string',
        ]);
        $item = Position::create($data);

        return response()->success($item, localize('Position Created Successfully'), 201);
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        return view('employee::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit(Position $position)
    {
        return view('employee::position.create_edit', ['item' => $position])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(Position $position, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $position->id . ',id',
            'description' => 'nullable|string',
        ]);
        $position->update($data);

        return response()->success($position, 'Position Updated Successfully.', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(Position $position)
    {
        $position->delete();

        return response()->success(null, 'Position Deleted Successfully.', 200);
    }
}
