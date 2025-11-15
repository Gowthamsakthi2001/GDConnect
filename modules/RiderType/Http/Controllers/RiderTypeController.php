<?php

namespace Modules\RiderType\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $statusText = $riderType->status == 1 ? 'Active' : 'Inactive';

        audit_log_after_commit([
            'module_id'         => 1,
            'short_description' => 'Rider Type Created',
            'long_description'  => "Rider Type '{$riderType->type}' created (ID: {$riderType->id}). Status: {$statusText}.",
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'rider_type.store',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
        
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
        $oldType = $riderType->type;
        $oldStatus = (int) $riderType->status;
        $oldStatusText = $oldStatus == 1 ? 'Active' : 'Inactive';
        $riderType->update($request->all());
        
        $newStatus = (int) $riderType->status;
        $newStatusText = $newStatus == 1 ? 'Active' : 'Inactive';
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

        audit_log_after_commit([
            'module_id'         => 6,
            'short_description' => 'Rider Type Updated',
            'long_description'  => "Rider Type updated (ID: {$riderType->id}). Type: '{$oldType}' → '{$riderType->type}'; Status: {$oldStatusText} → {$newStatusText}.",
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'rider_type.update',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
        
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
        $oldType = $riderType->type;
        $riderType->delete();
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

        audit_log_after_commit([
            'module_id'         => 6,
            'short_description' => 'Rider Type Deleted',
            'long_description'  => "Rider Type '{$oldType}' (ID: {$id}) was deleted.",
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'rider_type.delete',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
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
        $oldStatus = (int) $riderType->status;
        $newStatus = (int) $status;
        $riderType->status = $status;
        $riderType->save();
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $oldText = $oldStatus == 1 ? 'Active' : 'Inactive';
        $newText = $newStatus == 1 ? 'Active' : 'Inactive';

        audit_log_after_commit([
            'module_id'         => 6,
            'short_description' => 'Rider Type Status Updated',
            'long_description'  => "Rider Type '{$riderType->type}' (ID: {$riderType->id}) status changed: {$oldText} → {$newText}.",
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'rider_type.update_status',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
        
        return redirect()->route('admin.Green-Drive-Ev.rider-type.list')
                         ->with('success', 'Rider type status updated successfully.');
    }
}
