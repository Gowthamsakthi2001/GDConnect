<?php

namespace Modules\RiderType\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\RiderType\Entities\RiderType;
use Modules\RiderType\DataTables\RiderTypeDataTable;

class RiderTypeController extends Controller 
{
    /**
     * Display a listing of the resource.
     */
    public function index(RiderTypeDataTable $dataTable)
    {
        // Render the DataTable for listing RiderTypes
        return $dataTable->render('ridertype::list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ridertype::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate and store new RiderType
        $request->validate([
            'type' => 'required|string|max:255',
        ]);

        RiderType::create($request->all());

        return redirect()->route('admin.Green-Drive-Ev.rider-type.list')
                         ->with('success', 'Rider type created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit_rider_type($id)
    {
        // Find RiderType and show edit form
        $riderType = RiderType::findOrFail($id);
        return view('ridertype::edit', compact('riderType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // Validate and update RiderType
        $request->validate([
            'type' => 'required|string|max:255',
        ]);

        $riderType = RiderType::findOrFail($id);
        $riderType->update($request->all());

        return redirect()->route('admin.Green-Drive-Ev.rider-type.list')
                         ->with('success', 'Rider type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete_rider_type($id): RedirectResponse
    {
        // Find and delete RiderType
        $riderType = RiderType::findOrFail($id);
        $riderType->delete();

        return redirect()->route('admin.Green-Drive-Ev.rider-type.list')
                         ->with('success', 'Rider type deleted successfully.');
    }

    /**
     * Change the status of the specified resource.
     */
    public function change_status($id, $status): RedirectResponse
    {
        // Find RiderType and update status
        $riderType = RiderType::findOrFail($id);
        $riderType->status = $status;
        $riderType->save();

        return redirect()->route('admin.Green-Drive-Ev.rider-type.list')
                         ->with('success', 'Rider type status updated successfully.');
    }
}
