<?php

namespace Modules\VehicleManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\VehicleManagement\DataTables\DocumentTypeDataTable;
use Modules\VehicleManagement\Entities\DocumentType;

class DocumentTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:document_type_management']);
        $this->middleware('request:ajax', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Document Type Lists',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Document Type Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.vehicle.document-type',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DocumentTypeDataTable $dataTable)
    {
        \cs_set('theme', [
            'description' => 'Display a listing of Document Type in Database.',
        ]);

        return $dataTable->render('vehiclemanagement::document_type.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vehiclemanagement::document_type.create_edit')->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:document_types,name',
            'is_active' => 'required|boolean',
        ]);
        $item = DocumentType::create($data);

        return response()->success($item, localize('Item Created Successfully'), 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(DocumentType $document_type)
    {
        return view('vehiclemanagement::document_type.create_edit', ['item' => $document_type])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(DocumentType $document_type, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:document_types,name,' . $document_type->id . ',id',
            'is_active' => 'required|boolean',
        ]);
        $document_type->update($data);

        return response()->success($document_type, localize('Item Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentType $document_type)
    {

        $document_type->delete();
        return response()->success(null, localize('Item Deleted Successfully'), 200);
    }
}
