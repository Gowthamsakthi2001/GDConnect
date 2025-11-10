<?php

namespace Modules\LeadSource\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\LeadSource\Entities\LeadSource;
use Modules\LeadSource\DataTables\LeadSourceDataTable;

class LeadSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // List all LeadSources
        return view('leadsource::create');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Show form to create a new LeadSource
        return view('leadsource::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the request
        $request->validate([
            'source_name' => 'required|string|max:255',
        ]);
    
        // Check if the lead source already exists
        $existingLeadSource = LeadSource::where('source_name', $request->source_name)->first();
    
        if ($existingLeadSource) {
            // If it exists, return an error message
            return redirect()->back()->withErrors(['source_name' => 'This lead source already exists.']);
        }
    
        // If it doesn't exist, create a new LeadSource
        LeadSource::create($request->all());
    
        // Redirect with success message
        return redirect()->route('admin.Green-Drive-Ev.lead-source.list')
                         ->with('success', 'Lead source created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function list(LeadSourceDataTable $dataTable)
    {
        return $dataTable->render('leadsource::list');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit_city($id)
    {
        // Find LeadSource and show edit form
        $leadSource = LeadSource::findOrFail($id);
        return view('leadsource::edit', compact('leadSource'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // Validate and update LeadSource
        $request->validate([
            'source_name' => 'required|string|max:255',
        ]);

        $leadSource = LeadSource::findOrFail($id);
        $leadSource->update($request->all());

        return redirect()->route('admin.Green-Drive-Ev.lead-source.list')
                         ->with('success', 'Lead source updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete_city($id): RedirectResponse
    {
        // Find and delete LeadSource
        $leadSource = LeadSource::findOrFail($id);
        $leadSource->delete();

        return redirect()->route('admin.Green-Drive-Ev.lead-source.list')
                         ->with('success', 'Lead source deleted successfully.');
    }

    /**
     * Change the status of the specified resource.
     */
    public function change_status($id, $status): RedirectResponse
    {
        // Find LeadSource and update status
        $leadSource = LeadSource::findOrFail($id);
        $leadSource->status = $status;
        $leadSource->save();

        return redirect()->route('admin.Green-Drive-Ev.lead-source.list')
                         ->with('success', 'Lead source status updated successfully.');
    }
}
