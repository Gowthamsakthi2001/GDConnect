<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\Entities\Vendor;
use Modules\VehicleManagement\DataTables\LegalDocumentationDataTable;
use Modules\VehicleManagement\Entities\DocumentType;
use Modules\VehicleManagement\Entities\LegalDocumentation;
use Modules\VehicleManagement\Entities\Vehicle;

class LegalDocumentationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:legal_document_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Legal Documentation Details',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Legal Documentation Details',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle.legal-document',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LegalDocumentationDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of Insurance in Database.',
        ]);

        return $dataTable->render('vehiclemanagement::legal_documentation.index', [
            'document_types' => DocumentType::where('is_active', 1)->get(),
            'vehicles' => Vehicle::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehiclemanagement::legal_documentation.create_edit', [
            'document_types' => DocumentType::where('is_active', 1)->get(),
            'vehicles' => Vehicle::all(),
            'vendors' => Vendor::where('is_active', 1)->get(),
        ])->render();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'document_type_id' => 'required|integer',
            'vehicle_id' => 'required|integer',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'charge_paid' => 'nullable|numeric',
            'vendor_id' => 'nullable|integer',
            'commission' => 'nullable|numeric',
            'notify_before' => 'nullable|integer',
            'email' => 'nullable|email',
        ]);

        if ($request->hasFile('legal_document_file')) {
            $data['document_file_path'] = upload_file($request, 'legal_document_file', 'legal_documents');
        }

        $item = LegalDocumentation::create($data);

        return response()->success($item, localize('Legal Documentation Added Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LegalDocumentation $legal_document)
    {
        return view('vehiclemanagement::legal_documentation.create_edit', [
            'document_types' => DocumentType::where('is_active', 1)->get(),
            'vehicles' => Vehicle::all(),
            'vendors' => Vendor::where('is_active', 1)->get(),
            'item' => $legal_document,
        ])->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LegalDocumentation $legal_document)
    {

        $data = $request->validate([
            'document_type_id' => 'required|integer',
            'vehicle_id' => 'required|integer',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'charge_paid' => 'nullable|numeric',
            'vendor_id' => 'nullable|integer',
            'commission' => 'nullable|numeric',
            'notify_before' => 'nullable|integer',
            'email' => 'nullable|email',
        ]);

        if ($request->hasFile('legal_document_file')) {
            $data['document_file_path'] = upload_file($request, 'legal_document_file', 'legal_documents');

            if ($legal_document->document_file_path) {
                delete_file($legal_document->document_file_path);
            }
        }

        $legal_document->update($data);

        return response()->success($legal_document, localize('Legal Documentation Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LegalDocumentation $legal_document)
    {



        if ($legal_document->document_file_path) {
            delete_file($legal_document->document_file_path);
        }

        $legal_document->delete();
        return response()->success($legal_document, localize('Legal Documentation Deleted Successfully'), 200);
    }
}
