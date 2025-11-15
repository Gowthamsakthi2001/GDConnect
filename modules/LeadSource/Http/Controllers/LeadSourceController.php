<?php

namespace Modules\LeadSource\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\LeadSource\Entities\LeadSource;
use Modules\LeadSource\DataTables\LeadSourceDataTable;
use Illuminate\Support\Facades\Auth; //updated by Mugesh.B


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
        $leadSource  = LeadSource::create($request->all());
    
    
    
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    


        $shortDescription = 'Lead Source Created';
        $longDescription  = "Created a new Lead Source: '{$leadSource->source_name}'.";
    
        audit_log_after_commit([
            'module_id'         => 2,
            'short_description' => $shortDescription,
            'long_description'  => $longDescription,
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'lead_source.create',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent(),
        ]);
    
    
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
    
        // Store original values
        $originalName = $leadSource->source_name;
        $originalStatus = $leadSource->status == 1 ? 'Active' : 'Inactive';
    
        // Update with new data
        $leadSource->update($request->all());
    
        // New values
        $updatedName = $leadSource->source_name;
        $updatedStatus = $leadSource->status == 1 ? 'Active' : 'Inactive';
    
        // User details
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    
        // Detect changes
        $changes = [];
    
        if ($originalName !== $updatedName) {
            $changes[] = "name changed from '{$originalName}' to '{$updatedName}'";
        }
    
        if ($originalStatus !== $updatedStatus) {
            $changes[] = "status changed from '{$originalStatus}' to '{$updatedStatus}'";
        }
    
        // Build log message
        $shortDescription = 'Lead Source Updated';
    
        if (!empty($changes)) {
            $longDescription = "Lead Source details updated — " . implode(' and ', $changes) . ".";
        } else {
            $longDescription = "Lead Source details updated without any field changes.";
        }
    
        // Store audit log
        audit_log_after_commit([
            'module_id'         => 2,
            'short_description' => $shortDescription,
            'long_description'  => $longDescription,
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'lead_source.update',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent(),
        ]);
    
        return redirect()
            ->route('admin.Green-Drive-Ev.lead-source.list')
            ->with('success', 'Lead source updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function delete_city($id): RedirectResponse
    {
        // Find and delete LeadSource
        $leadSource = LeadSource::findOrFail($id);
        $originalName = $leadSource->source_name;
        
        $leadSource->delete();
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    
        $shortDescription = 'Lead Source Deleted';
        $longDescription  = "Deleted the Lead Source named '{$originalName}'.";
        
        audit_log_after_commit([
            'module_id'         => 2,
            'short_description' => $shortDescription,
            'long_description'  => $longDescription,
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'lead_source.delete',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent(),
        ]);

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
        
        $originalStatus = $leadSource->status;
         
        $leadSource->status = $status;
        $leadSource->save();
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    

        $statusText = [
            0 => 'Inactive',
            1 => 'Active',
        ];
    
        $fromStatus = $statusText[$originalStatus] ?? $originalStatus;
        $toStatus   = $statusText[$status] ?? $status;
    
        $shortDescription = 'Lead Source Status Updated';
        $longDescription  = "Changed the status of Lead Source '{$leadSource->source_name}' from '{$fromStatus}' to '{$toStatus}'.";
    


        audit_log_after_commit([
            'module_id'         => 2,
            'short_description' => $shortDescription,
            'long_description'  => $longDescription,
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'lead_source.change_status',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent(),
        ]);


        return redirect()->route('admin.Green-Drive-Ev.lead-source.list')
                         ->with('success', 'Lead source status updated successfully.');
    }
}
