<?php

namespace Modules\Employee\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Employee\DataTables\LicenseTypeDataTable;
use Modules\Employee\Entities\LicenseType;

class LicenseTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:employee_management', 'status_check']);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'License Type List',
            'description' => 'Displaying all Departments.',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'License Type List',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.license_type',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(LicenseTypeDataTable $dataTable)
    {
        return $dataTable->render('employee::license_type.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employee::license_type.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:license_types,name',
        ]);
        $item = LicenseType::create($data);

        return response()->success($item, localize('License Type Created Successfully'), 201);
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
    public function edit(LicenseType $license_type)
    {
        return view('employee::license_type.create_edit', ['item' => $license_type])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(Request $request, LicenseType $license_type)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:license_types,name,' . $license_type->id . ',id',
        ]);
        $license_type->update($data);

        return response()->success($license_type, localize('License Type Updated Successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(LicenseType $license_type)
    {
        $license_type->delete();
        return response()->success(null, localize('Position Deleted Successfully'), 200);
    }
}
