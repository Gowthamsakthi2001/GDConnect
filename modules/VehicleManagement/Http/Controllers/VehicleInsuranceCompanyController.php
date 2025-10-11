<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\VehicleManagement\DataTables\VehicleInsuranceCompanyDataTable;
use Modules\VehicleManagement\Entities\VehicleInsuranceCompany;

class VehicleInsuranceCompanyController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:vehicle_insurance_company_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Vehicle insurance company Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Vehicle insurance company Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle.insurance.company',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(VehicleInsuranceCompanyDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of Vehicle insurance company in Database.',
        ]);

        return $dataTable->render('vehiclemanagement::insurance.company.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vehiclemanagement::insurance.company.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_insurance_companies,name',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $item = VehicleInsuranceCompany::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(VehicleInsuranceCompany $company)
    {
        return view('vehiclemanagement::insurance.company.create_edit', ['item' => $company])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleInsuranceCompany $company, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_insurance_companies,name,' . $company->id . ',id',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $company->update($data);

        return response()->success($company, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(VehicleInsuranceCompany $company)
    {

        $company->delete();
        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }
}
