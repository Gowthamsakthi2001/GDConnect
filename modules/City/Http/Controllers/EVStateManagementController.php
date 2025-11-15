<?php

namespace Modules\City\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\EVState;
use App\Exports\EVStatesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth; 

class EVStateManagementController extends Controller
{
 
    //   public function state_index()
    //     {
    //         $states =EVState::orderBy('id','DESC')->get();
    //         return view('city::State.index', compact('states'));
    //     }
    
        public function state_index(Request $request)
        {
            // Get filter parameters from the request
            $status = $request->query('status', 'all');
            $fromDate = $request->query('from_date');
            $toDate = $request->query('to_date');
            
            // Start with base query
            $query = EVState::orderBy('id', 'DESC');
            
            // Apply status filter
            if ($status !== 'all') {
                $query->where('status', $status);
            }
            
            // Apply date range filter
            if ($fromDate && $toDate) {
                $query->whereBetween('created_at', [
                    \Carbon\Carbon::parse($fromDate)->startOfDay(),
                    \Carbon\Carbon::parse($toDate)->endOfDay()
                ]);
            } elseif ($fromDate) {
                $query->where('created_at', '>=', \Carbon\Carbon::parse($fromDate)->startOfDay());
            } elseif ($toDate) {
                $query->where('created_at', '<=', \Carbon\Carbon::parse($toDate)->endOfDay());
            }
           
           
            $states = $query->get();
            
           
            return view('city::State.index', compact('states', 'status', 'fromDate', 'toDate'));
        }
        
        // Create new state
        public function state_create(Request $request)
        {
            $request->validate([
                'state_name' => 'required|string|max:255',
                'state_code' => 'required|string|max:10',
                'status'     => 'required|boolean',
            ]);
        
            // Normalize input for duplicate check: lowercase + remove all spaces
            $normalizedName = strtolower(str_replace(' ', '', $request->state_name));
            $normalizedCode = strtolower(str_replace(' ', '', $request->state_code));
        
            // Check DB for duplicates
            $exists =EVState::whereRaw('LOWER(REPLACE(state_name, " ", "")) = ?', [$normalizedName])
                ->orWhereRaw('LOWER(REPLACE(state_code, " ", "")) = ?', [$normalizedCode])
                ->exists();
        
            if ($exists) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'State with same name or code already exists.'
                    ], 422);
                }
                return back()->with('error', 'State with same name or code already exists.');
            }
        
            // Save original formatting
            $state =EVState::create([
                'state_name' => $request->state_name,
                'state_code' => $request->state_code,
                'status'     => $request->status,
            ]);
        
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'State created successfully.',
                    'state' => $state
                ]);
            }
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
            $statusText = $state->status == 1 ? 'Active' : 'Inactive';
            
            audit_log_after_commit([
                'module_id'         => 1, // set proper module ID for State module
                'short_description' => 'State Created',
                'long_description'  => "State '{$state->state_name}' created (ID: {$state->id}). Code: {$state->state_code}. Status: {$statusText}.",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'state_master.store',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
            return redirect()->route('admin.Green-Drive-Ev.State.create')
                             ->with('success', 'State created successfully.');
        }
        
        
        // Update existing state
        public function state_update(Request $request, $id)
        {
            $state =EVState::findOrFail($id);

            $request->validate([
                'state_name' => 'required|string|max:255',
                'state_code' => 'required|string|max:10',
                'status'     => 'required|boolean',
            ]);
        
            // Normalize input for duplicate check: lowercase + remove all spaces
            $normalizedName = strtolower(str_replace(' ', '', $request->state_name));
            $normalizedCode = strtolower(str_replace(' ', '', $request->state_code));
        
            // Check DB for duplicates excluding the current record
            $exists =EVState::where(function($query) use ($normalizedName, $normalizedCode) {
                        $query->whereRaw('LOWER(REPLACE(state_name, " ", "")) = ?', [$normalizedName])
                              ->orWhereRaw('LOWER(REPLACE(state_code, " ", "")) = ?', [$normalizedCode]);
                    })
                    ->where('id', '!=', $state->id)
                    ->exists();
        
            if ($exists) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Another state with same name or code already exists.'
                    ], 422);
                }
                return back()->with('error', 'Another state with same name or code already exists.');
            }
            
            $oldName = $state->state_name;
            $oldCode = $state->state_code;
            $oldStatus = (int) $state->status;
            // Save original formatting
            $state->update([
                'state_name' => $request->state_name,
                'state_code' => $request->state_code,
                'status'     => $request->status,
            ]);
            
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
            $newStatus = (int) $state->status;
            $oldStatusText = $oldStatus == 1 ? 'Active' : 'Inactive';
            $newStatusText = $newStatus == 1 ? 'Active' : 'Inactive';
            
            audit_log_after_commit([
                'module_id'         => 1,
                'short_description' => 'State Updated',
                'long_description'  => "State updated (ID: {$state->id}). Name: '{$oldName}' → '{$state->state_name}'; Code: '{$oldCode}' → '{$state->state_code}'; Status: {$oldStatusText} → {$newStatusText}.",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'state_master.update',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'State updated successfully.',
                    'state' => $state
                ]);
            }
        
            return redirect()->route('admin.Green-Drive-Ev.State.create')
                             ->with('success', 'State updated successfully.');
        }
        
        public function state_change_status($id, $status)
        {
            try {
                $state = EVState::findOrFail($id);
                $oldStatus = (int) $state->status;
        
                $state->status = $status;
                $state->save();
        
                $user = Auth::user();
                $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
                $oldText = $oldStatus == 1 ? 'Active' : 'Inactive';
                $newText = $status == 1 ? 'Active' : 'Inactive';
        
                audit_log_after_commit([
                    'module_id'         => 1, // replace with real module id for State
                    'short_description' => 'State Status Updated',
                    'long_description'  => "State '{$state->state_name}' (ID: {$state->id}) status changed: {$oldText} → {$newText}.",
                    'role'              => $roleName,
                    'user_id'           => $user->id ?? null,
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'state_master.update_status',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                ]);
        
                // Verify the update worked
                $updatedState = EVState::find($id);
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Status updated successfully',
                    'new_status' => $oldStatus // Return the actual status from DB
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error updating status: ' . $e->getMessage()
                ]);
            }
        }
        
        // Delete a specific state
        // public function state_delete($id)
        // {
        //     try {
        //         $state = \App\Models\EVState::findOrFail($id);
        //         $state->delete();
        
        //         // Return JSON for AJAX requests
        //         if (request()->ajax()) {
        //             return response()->json([
        //                 'success' => true,
        //                 'message' => 'State deleted successfully.'
        //             ]);
        //         }
        
        //         return redirect()->route('admin.Green-Drive-Ev.State.index')
        //                          ->with('success', 'State deleted successfully.');
        //     } catch (\Exception $e) {
        //         if (request()->ajax()) {
        //             return response()->json([
        //                 'success' => false,
        //                 'message' => 'Error deleting state: ' . $e->getMessage()
        //             ]);
        //         }
        
        //         return redirect()->route('admin.Green-Drive-Ev.State.index')
        //                          ->with('error', 'Error deleting state: ' . $e->getMessage());
        //     }
        // }
        
          public function state_export(Request $request)
            {
                // Get filter parameters from the request
                $status = $request->query('status', 'all');
                $fromDate = $request->query('from_date');
                $toDate = $request->query('to_date');
                
                // Check if any filters are actually provided
                $hasFilters = ($status !== 'all') || $fromDate || $toDate;
                
                // Generate filename with timestamp and filters
                $fileName = 'states_export_' . now()->format('Y_m_d');
                
                $fileName .= '.xlsx';
                
                $user = Auth::user();
                $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
            
                $longDescription = sprintf(
                    "State Master export initiated. Filters → Status: %s | From: %s | To: %s",
                    $status ?? '-',
                    $fromDate ?? '-',
                    $toDate ?? '-'
                );
            
                audit_log_after_commit([
                    'module_id'         => 1, // replace with the real module id for State Master
                    'short_description' => 'State Master Export Triggered',
                    'long_description'  => $longDescription,
                    'role'              => $roleName,
                    'user_id'           => $user->id ?? null,
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'state_master.export',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                ]);
                // Return Excel download with filters applied
                return Excel::download(new EVStatesExport($status, $fromDate, $toDate), $fileName);
            }

        
       
                            
        
        

      
 
    
}