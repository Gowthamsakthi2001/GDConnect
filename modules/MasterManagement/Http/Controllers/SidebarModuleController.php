<?php

namespace Modules\MasterManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SidebarModule;
use Modules\Role\Entities\Role;
use App\Helpers\CustomHandler;

class SidebarModuleController extends Controller
{
   
  public function index(Request $request)
  {
    if ($request->ajax()) {
        $query = SidebarModule::query();

        $status  = $request->input('status');
        $search  = $request->input('search.value');
        $start   = $request->input('start', 0);
        $length  = $request->input('length', 15);


        if ($status !== null && $status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('module_name', 'like', "%$search%");
            });
        }

        $totalRecords = $query->count();

        if ($length == -1) {
            $length = $totalRecords;
        }

        $data = $query->orderBy('id', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $sno = $start + 1;

        $formattedData = $data->map(function ($item) use (&$sno) {
              $statusHtml = $item->status == 1
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';


            $imageHtml = $item->image
                ? '<img src="' . asset('admin-assets/sidebar_icon/' . $item->image) . '" alt="Module Image" class="img-thumbnail" style="width:50px;height:50px;">'
                : '<span class="text-muted">No Image</span>';
            // $imageHtml = $item->image
            //     ? '<img src="https://admin.greendrivemobility.in/uploads/users/68185a7edb134.png" alt="Module Image" class="img-thumbnail" style="width:50px;height:50px;">'
            //     : '<span class="text-muted">No Image</span>';

            
            $rolesHtml = '<span class="text-muted">N/A</span>';

            if (!empty($item->view_roles_id)) {
                $roleIds = $item->view_roles_id;
                if (is_array($roleIds) && count($roleIds) > 0) {
                    $roles = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
                    $rolesHtml = implode(', ', $roles);
                }
            }

            $editURL = route('admin.Green-Drive-Ev.master_management.sidebar_module.edit', ['id' => $item->id]);

            $actionsHtml = '
                <a href="'.$editURL.'" class="dropdown-item d-flex align-items-center justify-content-center">
                    <i class="bi bi-pencil-square me-2"></i> Edit
                </a>
            ';

            return [
                'checkbox' => $sno++,
                'image' => $imageHtml,
                'module_name' => e($item->module_name),
                'view_roles_id' => $rolesHtml,
                'status' => $item->status, 
                'actions' => $actionsHtml,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $formattedData,
        ]);
    }

        return view('mastermanagement::sidebar_module.index');
    }
    
      public function create(Request $request){
        $roles = Role::all();
        return view('mastermanagement::sidebar_module.create',compact('roles'));
    }
    
    
     public function store(Request $request)
    {
        $request->validate([
            'module_name'     => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $normalized = strtolower(preg_replace('/\s+/', '', $value)); 
        
                    $exists = DB::table('sidebar_modules')
                        ->whereRaw("REPLACE(LOWER(module_name), ' ', '') = ?", [$normalized])
                        ->exists();
        
                    if ($exists) {
                        $fail('The module name has already been taken.');
                    }
                },
            ],
            'route_name'  => 'required|string|max:255',
            'status'      => 'required|in:0,1',
            'assign_roles' => 'required|array',
            'image'       => 'required|image|max:1024',
        ]);
        
        
        $createModel = new SidebarModule();
        
        if ($request->hasFile('image')) {
            $createModel->image = CustomHandler::uploadFileImage(
                $request->file('image'),
                'admin-assets/sidebar_icon'
            );
        }
        $createModel->view_roles_id = $request->assign_roles ?? []; 
        $createModel->module_name = $request->module_name;
        $createModel->route_name = $request->route_name;
        $createModel->status = $request->status;
        $createModel->save();
        
         $rolesSummary = is_array($createModel->view_roles_id)
        ? implode(',', $createModel->view_roles_id)
        : (string) $createModel->view_roles_id;

        audit_log_after_commit([
            'module_id'         => 1, // keep your module_id mapping; change if you have a constant or dynamic value
            'short_description' => 'Sidebar Module Created',
            'long_description'  => "Sidebar module '{$createModel->module_name}' created (DB ID: {$createModel->id}). Route: {$createModel->route_name}. Status: {$createModel->status}. Roles: {$rolesSummary}.",
            'role'              => optional(Auth::user())->role ?? 'admin',
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'sidebar_module.create',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent(),
        ]);

        return response()->json(['success' => true, 'message' => 'Module created successfully']);
    }

    // ✅ Edit (fetch single)
    public function edit($id)
    {
        $module = SidebarModule::findOrFail($id);
        if(!$module){
            return back()->with('error','Module Not Found');
        }
        $roles = Role::all();
        return view('mastermanagement::sidebar_module.edit',compact('roles','module'));
    }

    // ✅ Update
    public function update(Request $request, $id)
    {

        $request->validate([
            'module_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($id) {
                    $normalized = strtolower(preg_replace('/\s+/', '', $value)); 
            
                    $exists = DB::table('sidebar_modules')
                        ->where('id', '!=', $id)
                        ->whereRaw("REPLACE(LOWER(module_name), ' ', '') = ?", [$normalized])
                        ->exists();
            
                    if ($exists) {
                        $fail('The module name has already been taken.');
                    }
                },
            ],
            'route_name'  => 'required|string|max:255',
            'status'      => 'required|in:0,1',
            'assign_roles' => 'required|array',
            'image'       => 'nullable|image|max:1024',
        ]);
        
        
        $updateModel = SidebarModule::where('id',$id)->first();
        if(!$updateModel){
            return response()->json(['success' => false, 'message' => 'Module Not Found!']);
        }
        $old = [
            'module_name' => $updateModel->module_name,
            'route_name'  => $updateModel->route_name,
            'status'      => (string) $updateModel->status,
            'view_roles'  => is_array($updateModel->view_roles_id) ? $updateModel->view_roles_id : json_decode($updateModel->view_roles_id, true) ?? [],
            'image'       => $updateModel->image,
        ];
        if (isset($request->image) && $request->hasFile('image')) {
            $old_file = $updateModel->image;
            $updateModel->image = CustomHandler::uploadFileImage(
                $request->file('image'),
                'admin-assets/sidebar_icon'
            );
            if (!empty($old_file)) {
                CustomHandler::GlobalFileDelete($old_file, 'admin-assets/sidebar_icon/');
            }
        }
        $updateModel->view_roles_id = $request->assign_roles ?? []; 
        $updateModel->module_name = $request->module_name;
        $updateModel->route_name = $request->route_name;
        $updateModel->status = $request->status;
        $updateModel->save();
        
        $oldRolesSummary = is_array($old['view_roles']) ? implode(',', $old['view_roles']) : (string) $old['view_roles'];
    $newRolesSummary = is_array($updateModel->view_roles_id) ? implode(',', $updateModel->view_roles_id) : (string) $updateModel->view_roles_id;

    // Determine what changed (simple diff)
        $changes = [];
        if ($old['module_name'] !== $updateModel->module_name) {
            $changes[] = "module_name: '{$old['module_name']}' -> '{$updateModel->module_name}'";
        }
        if ($old['route_name'] !== $updateModel->route_name) {
            $changes[] = "route_name: '{$old['route_name']}' -> '{$updateModel->route_name}'";
        }
        if ((string)$old['status'] !== (string)$updateModel->status) {
            $changes[] = "status: '{$old['status']}' -> '{$updateModel->status}'";
        }
        if ($oldRolesSummary !== $newRolesSummary) {
            $changes[] = "roles: '{$oldRolesSummary}' -> '{$newRolesSummary}'";
        }
        if ($old['image'] !== $updateModel->image) {
            $changes[] = "image: '{$old['image']}' -> '{$updateModel->image}'";
        }

        $changesText = empty($changes) ? 'No visible changes (values remained the same).' : implode('; ', $changes);
        audit_log_after_commit([
            'module_id'         => 1, // change if you want dynamic module mapping
            'short_description' => 'Sidebar Module Updated',
            'long_description'  => "Sidebar module (DB ID: {$updateModel->id}) updated. Changes: {$changesText}",
            'role'              => optional(Auth::user())->role ?? 'admin',
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'sidebar_module.update',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent(),
        ]);

        return response()->json(['success' => true, 'message' => 'Module updated successfully']);
    }


    // public function destroy($id)
    // {
    //     $module = SidebarModule::findOrFail($id);
    //     $module->delete();

    //     return response()->json(['success' => true, 'message' => 'Module deleted successfully']);
    // }

    // // ✅ Bulk Delete
    // public function bulkDelete(Request $request)
    // {
    //     $ids = $request->input('ids', []);
    //     SidebarModule::whereIn('id', $ids)->delete();
    //     return response()->json(['success' => true, 'message' => 'Selected modules deleted successfully']);
    // }
   
}