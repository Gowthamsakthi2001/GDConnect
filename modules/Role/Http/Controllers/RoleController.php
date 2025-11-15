<?php

namespace Modules\Role\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Modules\Role\DataTables\RoleDataTable;
use Modules\Role\Entities\Role;
use Modules\Permission\Entities\Permission;//updated by Gowtham.S
use App\Services\AuditHeader;//updated by Gowtham.S
use Illuminate\Support\Facades\Http;//updated by Gowtham.S

class RoleController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:role_management']);
        $this->middleware('request:ajax', ['only' => ['destroy']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Role Lists',
            'description' => 'Display a listing of roles in Database.',
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Role Lists',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.role',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RoleDataTable $dataTable)
    {
        return $dataTable->render('role::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\view\View
     */
    public function create()
    {
        \cs_set('theme', [
            'title' => 'Create New Role',
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Role List',
                    'link' => \route('admin.role.index'),
                ],

                [
                    'name' => 'Create New Role',
                    'link' => false,
                ],
            ],
            'description' => 'Create new role in a database.',
        ]);

        return view('role::create_edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,',
            'user_id_name'=>'required|max:8|unique:roles,user_id_name,'
        ]);
        $permissions = Permission::whereIn('id', $request->permissions)
                         ->pluck('name')
                         ->implode(', ');

        // dd($permissions,$request->permissions);
        $role = Role::create([
            'name' => \ucfirst($request->name),
            'user_id_name'=>$request->user_id_name
        ]);
        
        $role->syncPermissions($request->permissions ?? '');
        $user_id = auth()->user()->id;
        $roleName = auth()->user()->get_role->name ?? 'Unknown';
        
        $permissions = Permission::whereIn('id', $request->permissions)
            ->pluck('name')
            ->implode(', ');
        
        audit_log_after_commit([
            'module_id'         => 1,
            'In Role & Permission, new role "' . $role->name . '" was added by ' . auth()->user()->name,
            'long_description'  => 'A new role "' . $role->name . '" has been created by '
                                   . auth()->user()->name . ' (' . $roleName . '). '
                                   . 'Assigned permissions: ' . $permissions,
            'role'              => $roleName,
            'user_id'           => $user_id,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'role.store',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);


        
        // flash message
        Session::flash('success', 'Successfully Stored new role data.');

        return \redirect()->route(config('theme.rprefix') . '.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\view\View
     */
    public function edit(Role $role)
    {
        \cs_set('theme', [
            'title' => 'Edit Role Information',
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Role Table',
                    'link' => \route('admin.role.index'),
                ],

                [
                    'name' => 'Edit Role Information',
                    'link' => false,
                ],
            ],
            'description' => 'Edit existing role data.',
            'edit' => route(config('theme.rprefix') . '.update', $role->id),
        ]);

        return view('role::create_edit', ['item' => $role]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id . ',id',
            'user_id_name' => 'required|max:8|unique:roles,user_id_name,' . $role->id . ',id',
            'permission' => 'nullable|array',
        ]);
        
        $oldPermissions = $role->permissions->pluck('name')->implode(', ');

        $role->update([
            'name' => ucfirst($request->name),
            'user_id_name'=>$request->user_id_name
        ]);
        

        $newPermissions = Permission::whereIn('id', $request->permissions ?? [])
            ->pluck('name')
            ->implode(', ');
    
        $user_id = auth()->user()->id;
        $roleName = auth()->user()->get_role->name ?? 'Unknown';
    
        audit_log_after_commit([
            'module_id'         => 1,
            'In Role & Permission, the role "' . $role->name . '" was updated by ' . auth()->user()->name,
            'long_description'  => 'Role "' . $role->name . '" has been updated by ' 
                                    . auth()->user()->name . ' (' . $roleName . '). '
                                    . 'Previous permissions: ' . ($oldPermissions ?: 'None') . '. '
                                    . 'Updated permissions: ' . ($newPermissions ?: 'None'),
            'role'              => $roleName,
            'user_id'           => $user_id,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'role.update',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        
        $role->syncPermissions($request->permissions ?? '');
        // flash message
        Session::flash('success', 'Successfully Updated role data.');

        return \redirect()->route(config('theme.rprefix') . '.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $deletedRole = $role->name;
        $role->delete();        // flash message

        $user_id  = auth()->user()->id;
        $roleName = auth()->user()->get_role->name ?? 'Unknown';
    
        audit_log_after_commit([
            'module_id'         => 1,
            'short_description' => 'In Role & Permission, the role "' . $deletedRole . '" was deleted by ' . auth()->user()->name,
            'long_description'  => 'Role "' . $deletedRole . '" has been deleted by '
                                   . auth()->user()->name . ' (' . $roleName . ').',
            'role'              => $roleName,
            'user_id'           => $user_id,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'role.destroy',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        
        Session::flash('success', 'Successfully deleted role data.');

        return response()->success(null, 'Successfully deleted role data.');
    }
}
