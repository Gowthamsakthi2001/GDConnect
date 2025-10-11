<?php

namespace Modules\City\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\EVState;
use App\Exports\EVStatesExport;
use Maatwebsite\Excel\Facades\Excel;


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
        
            // Save original formatting
            $state->update([
                'state_name' => $request->state_name,
                'state_code' => $request->state_code,
                'status'     => $request->status,
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
                $state->status = $status;
                $state->save();
        
                // Verify the update worked
                $updatedState = EVState::find($id);
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Status updated successfully',
                    'new_status' => $updatedState->status // Return the actual status from DB
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
                
                // Return Excel download with filters applied
                return Excel::download(new EVStatesExport($status, $fromDate, $toDate), $fileName);
            }

        
       
                            
        
        

      
 
    
}