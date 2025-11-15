<?php

namespace Modules\LeaveManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\LeaveManagement\DataTables\HolidayDataTable;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth; //updated by Mugesh.B

class HolidayManagementController extends Controller
{
    public function index(HolidayDataTable $dataTable)
    {
        return $dataTable->render('leavemanagement::holiday_management.index');
    }
    
    public function dashboard()
    {
        return view('leavemanagement::dashboard');
    }
    
    public function manage(Request $request)
    {
        $existingHolidays = Holiday::active()->get()->map(function($holiday) {
            return [
                'id'=>$holiday->id,
                'date' => $holiday->date->format('Y-m-d'),
                'title' => $holiday->title,
                'type' => $holiday->type,
                'description' => $holiday->description,
                'is_recurring' => $holiday->is_recurring,
                'is_active' => $holiday->is_active
            ];
        });
        
        $holiday = null;
        if ($request->has('holiday_id')) {
            $holiday = Holiday::find($request->holiday_id);
        }
        


        return view('leavemanagement::holiday_management.manage', [
            'existingHolidays' => $existingHolidays,
            'holiday' => $holiday
        ]);
    }

    // public function save(Request $request)
    // {
    //     $validated = $request->validate([
    //         'id' => 'nullable|exists:holidays,id',
    //         'title' => 'required|string|max:255',
    //         'date' => 'required|date',
    //         'description' => 'nullable|string',
    //         'type' => 'required|in:national,state,regional,company',
    //         'is_recurring' => 'nullable',
    //         'is_active' => 'nullable',
    //         'apply_to_years' => 'nullable|array'
    //     ]);

    //     if ($request->filled('id')) {
    //         // Update existing holiday
    //         $holiday = Holiday::find($request->id);
    //         $holiday->update($validated);
    //         $message = 'Holiday updated successfully';
    //     } else {
    //         // Create new holiday
    //         $holiday = Holiday::create($validated);
    //         $message = 'Holiday created successfully';

    //         // Handle recurring holidays for future years
    //         if ($request->is_recurring && $request->has('apply_to_years')) {
    //             foreach ($request->apply_to_years as $year) {
    //                 $newDate = Carbon::parse($holiday->date)->setYear($year);
    //                 Holiday::create([
    //                     'title' => $holiday->title,
    //                     'date' => $newDate,
    //                     'description' => $holiday->description,
    //                     'type' => $holiday->type,
    //                     'is_recurring' => $holiday->is_recurring,
    //                     'is_active' => $holiday->is_active
    //                 ]);
    //             }
    //         }
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => $message,
    //         'redirect' => route('admin.Green-Drive-Ev.leavemanagement.holidays.index')
    //     ]);
    // }
    
    

    // public function destroy(Request $request)
    // {
    //     $holiday = Holiday::findOrFail($request->id);
    //     if(!$holiday){
    //      return response()->json([
    //         'success' => false,
    //         'message' => 'No Holiday data found'
    //     ]);   
    //     }
    //     $holiday->delete();
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Holiday deleted successfully'
    //     ]);
    // }
    
    
    public function save(Request $request)
{
    
    $validated = $request->validate([
        'id' => 'nullable|exists:ev_master_holidays,id',
        'title' => 'required|string|max:255',
        'date' => 'required|date',
        'description' => 'nullable|string',
        'type' => 'required|in:national,state,regional,company',
        'is_recurring' => 'required|boolean',
        'is_active' => 'nullable|boolean',
        // 'apply_to_years' => 'nullable|array'
    ]);

    $pageName = 'holiday.save';
    if ($request->filled('id')) {
        // Update existing holiday
        $holiday = Holiday::find($request->id);
        
        // Get the recurring group ID (or create new if not recurring before)
        $recurringGroupId = $holiday->recurring_group_id ?? ($request->is_recurring ? Str::uuid() : null);
        
        $holiday->update(array_merge($validated, ['recurring_group_id' => $recurringGroupId]));
        $message = 'Holiday updated successfully';

        // Update all holidays in the same recurring group (except date)
        if ($request->is_recurring && $recurringGroupId) {
            Holiday::where('recurring_group_id', $recurringGroupId)
                  ->where('id', '!=', $holiday->id)
                  ->update([
                      'title' => $holiday->title,
                      'description' => $holiday->description,
                      'type' => $holiday->type,
                      'is_active' => $holiday->is_active
                  ]);
        }
        
        $actionType = 'updated';
        $pageName = 'holiday.update';
    } else {
        // Create new holiday
        $recurringGroupId = $request->is_recurring ? Str::uuid() : null;
        $holiday = Holiday::create(array_merge($validated, ['recurring_group_id' => $recurringGroupId]));
        $message = 'Holiday created successfully';

        // Create recurring holidays if enabled
        if ($request->is_recurring) {
            $originalDate = Carbon::parse($holiday->date);
            $currentYear = $originalDate->year;
            
            $years = $request->apply_to_years ?: range($currentYear + 1, $currentYear + 10);
            
            foreach ($years as $year) {
                $newDate = $originalDate->copy()->setYear($year);
                
                if (!Holiday::where('date', $newDate->format('Y-m-d'))->exists()) {
                    Holiday::create([
                        'title' => $holiday->title,
                        'date' => $newDate,
                        'description' => $holiday->description,
                        'type' => $holiday->type,
                        'is_recurring' => 1,
                        'is_active' => $holiday->is_active,
                        'recurring_group_id' => $recurringGroupId
                    ]);
                }
            }
        }
        $actionType = 'created';
        $pageName = 'holiday.save';
    }
    
    
    
        
    $user = Auth::user();
    $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    $performedBy = $user->name ?? 'Unknown User';

    
    $shortDescription = "Holiday {$actionType} ({$holiday->title})";

    $longDescription = "The holiday titled '{$holiday->title}' dated "
        . Carbon::parse($holiday->date)->format('d-m-Y')
        . " was {$actionType} by {$performedBy} ({$roleName}). "
        . "Type: {$holiday->type}. "
        . ($holiday->is_recurring ? 'This holiday is set as recurring.' : 'This holiday is not recurring.');

    
    audit_log_after_commit([
        'module_id'         => 2, 
        'short_description' => $shortDescription,
        'long_description'  => $longDescription,
        'role'              => $roleName,
        'user_id'           => Auth::id(),
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => $pageName,
        'ip_address'        => request()->ip(),
        'user_device'       => request()->userAgent(),
    ]);

    return response()->json([
        'success' => true,
        'message' => $message,
        'redirect' => route('admin.Green-Drive-Ev.leavemanagement.holidays.index')
    ]);
}

// Add this delete method to your controller
public function destroy(Request $request)
{

    
    $id = $request->id;
    $holiday = Holiday::findOrFail($id);
    $recurringGroupId = $holiday->recurring_group_id;
    
    
    $user = Auth::user();
    $roleName    = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    $performedBy = $user->name ?? 'Unknown User';
    $pageName    = 'holiday.delete';
        
        
    // Delete all holidays in the same recurring group
    if ($recurringGroupId) {
        Holiday::where('recurring_group_id', $recurringGroupId)->delete();
    } else {
        $holiday->delete();
    }
    
    
    if ($recurringGroupId) {
        $shortDescription = "Recurring holiday group deleted ({$holiday->title})";
        $longDescription  = "The recurring holiday series '{$holiday->title}' starting from "
                          . Carbon::parse($holiday->date)->format('d M Y') . " was deleted by "
                          . "{$performedBy} ({$roleName}).";
    } else {
        $shortDescription = "Holiday deleted ({$holiday->title})";
        $longDescription  = "The holiday '{$holiday->title}' on "
                          . Carbon::parse($holiday->date)->format('d M Y') . " was deleted by "
                          . "{$performedBy} ({$roleName}). Type: {$holiday->type}.";
    }

        // Save audit log
        audit_log_after_commit([
            'module_id'         => 2, // HR / Leave Management module ID
            'short_description' => $shortDescription,
            'long_description'  => $longDescription,
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => $pageName,
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent(),
        ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Holiday(s) deleted successfully'
    ]);
}
}