<?php

namespace Modules\Employee\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Employee\DataTables\DepartmentDataTable;
use Modules\Employee\Entities\Department;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:employee_management', 'status_check']);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Department List',
            'description' => 'Displaying all Departments.',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Departments List',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.department',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(DepartmentDataTable $dataTable)
    {
        return $dataTable->render('employee::department.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employee::department.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
        ]);
        $item = Department::create($data);

        return response()->success($item, localize('Department Created Successfully'), 201);
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
    public function edit(Department $department)
    {
        return view('employee::department.create_edit', ['item' => $department])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id . ',id',
        ]);
        $department->update($data);

        return response()->success($department, 'Department Updated Successfully.', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(Department $department)
    {
        $department->delete();
        return response()->success(null, 'Department Deleted Successfully.', 200);
    }
}
