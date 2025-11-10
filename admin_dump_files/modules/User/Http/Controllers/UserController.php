<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\Rules\Password;
use Modules\User\DataTables\UserDataTable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TelecallerOnboardExport;
use Illuminate\Support\Facades\DB;
use Modules\Role\Entities\Role;
use Carbon\Carbon;
use Modules\Zones\Entities\Zones; //updated by Gowtham.s - Zone Map
use App\Helpers\CustomHandler;
use App\Models\BusinessSetting;

class UserController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:user_management', 'status_check']);
        $this->middleware('request:ajax', ['only' => ['destroy', 'statusUpdate']]);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Staff List',
            'description' => 'Displaying all Staff.',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Staff List',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.user',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('user::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\view\View
     */
    public function create()
    {
        \cs_set('theme', [
            'title' => 'Create a Staff',
            'description' => 'Creating New Staff.',
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Staff List',
                    'link' => \route('admin.user.index'),
                ],
                [
                    'name' => 'User Create New Staff',
                    'link' => false,
                ],
            ],
        ]);

        return \view('user::create_edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
  public function store(Request $request) //updated by Gowtham.s - Zone Map
    {

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:255|unique:users,phone',
            'password' => ['required', 'string', new Password(), 'confirmed'], // Fixed placement of 'confirmed'
            'login_type'=>'required|in:1,2',
            'city' => 'required|integer|exists:ev_tbl_city,id',
            'zone' =>'nullable|exists:zones,id',
            'role' => 'required|integer|exists:roles,id',
            'status' => 'required|in:' . implode(',', array_keys(User::statusList())),
            'age' => 'nullable|integer',
            'address' => 'nullable|string|max:255',
            'gender' => 'required|in:' . implode(',', array_keys(User::genderList())),
            'permissions' => 'nullable|array',
        ]);
        
        
        if ($request->login_type == 2) {
            $request->validate([
                'zone' => 'required|exists:zones,id',
            ]);
        }
        
      $role = Role::where('id', $request->role)->first();
        if (!$role) {
            // return back()->with('error', 'Role not found.');
            return response()->json(['success'=>false,'message'=>'Role not found.']);
        }
        
        $get_users = DB::table('model_has_roles')
            ->join('users', 'model_has_roles.model_id', '=', 'users.id') 
            ->select('users.id as user_id', 'users.name as user_name')   
            ->where('model_has_roles.role_id', $request->role)
            ->where('users.status', 'Active')
            ->get();
        
        $get_users_count = count($get_users);
        preg_match('/^([A-Za-z]+)(\d+)$/', $role->user_id_name, $matches);
        
        $prefix = isset($matches[1]) ? $matches[1] : '';
        $numeric_part = isset($matches[2]) ? (int)$matches[2] : 0;
        if ($get_users_count === 0) {
            $staff_id = $role->user_id_name;
        } else {
            $new_numeric_part = str_pad($numeric_part + $get_users_count, 3, '0', STR_PAD_LEFT);
            $staff_id = $prefix . $new_numeric_part;
        }
        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:1024', // Add validation rules
            ]);
            $avatar = $request->file('avatar');
            $imageName = uniqid() . '.' . $avatar->getClientOriginalExtension();
            $destinationPath = public_path('uploads/users');
            $avatar->move($destinationPath, $imageName);
            $data['profile_photo_path'] =  $imageName;
        }
       
        $data['password'] = Hash::make($data['password']);
        $data['emp_id'] = $staff_id ?? null;
        $data['login_type'] = $request->login_type;
        $data['city_id'] = $request->city;
        $data['zone_id'] = $request->zone;
        $user = User::create($data)->assignRole($data['role']);
        $user->syncPermissions($data['permissions'] ?? []);
        
        if(!empty($request->phone) && $user){
            $this->StaffCreateSentWhatsMessage($request->phone,$user,$role->name);
            $this->StaffSentEmail($request->phone,$user,$role->name,'staff_create_notify');
        }
        
        if($user->role == 17){ //only for agent
            $this->AgentstoreNotification($user);
        }
        

        
        return response()->json(['success'=>true,'message'=>'New Staff account Created Successfully!']);

    }
    
    function AgentstoreNotification($user)
    {
        $title = "Welcome to GreenDrive Mobility!";
        $body  = "Hello {$user->name}, your agent account has been created successfully. "
                . "You can now log in and start exploring requests in your area.";
    
        $createModel = new \Modules\B2B\Entities\B2BAgentsNotification();
        $createModel->title = $title;
        $createModel->description = $body;
        $createModel->image = null; // optional image
        $createModel->status = 1;
        $createModel->agent_id = $user->id;
        $createModel->save();
    }

    function StaffSentEmail($phone, $user, $role_name, $forward_type,$account_status = null)
    {

        $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
        $footerContentText = $footerText ?? "For any assistance, please reach out to Admin Support.<br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
    
        // To and CC emails
        $toEmails = $user->email;
        $ccEmails = DB::table('roles')
            ->leftJoin('users', 'roles.id', '=', 'users.role')
            ->select('users.email')
            ->whereIn('users.role', [1, 13]) //Admins
            ->where('users.status','Active')
            ->pluck('users.email')
            ->filter()
            ->toArray(); // optional
    
        if ($forward_type == 'staff_create_notify') {
            $subject = "New Staff Account Created - Green Drive Connect";
           $body = "
                <html>
                <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #544e54;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #544e54;'>
                        <tr>
                            <td style='padding: 20px; text-align: center; background-color: #8b8b8b; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                                <h2>Welcome to Green Drive Connect</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding: 20px; color: #544e54;'>
                                <p>Hello <strong>{$user->name}</strong>,</p>
                                <p>Your staff account has been <strong>successfully created</strong>.</p>
                                
                                <table cellpadding='5' cellspacing='0' style='width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px; color: #544e54;'>
                                    <tr style='background-color: #f2f2f2;'>
                                        <td><strong>Email:</strong></td>
                                        <td>{$user->email}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{$phone}</td>
                                    </tr>
                                    <tr style='background-color: #f2f2f2;'>
                                        <td><strong>Role:</strong></td>
                                        <td>{$role_name}</td>
                                    </tr>
                                </table>
                
                                <p style='margin-top: 15px;'>🔑 Login credentials have been created for you. Please contact the Admin to receive your username and password.</p>
                                <p style='margin-top: 20px;'>{$footerContentText}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align: center; padding: 15px; font-size: 12px; color: #544e54;'>
                                &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                            </td>
                        </tr>
                    </table>
                </body>
                </html>
                ";

        }
        
        if ($forward_type == 'staff_password_update_notify') {
            $subject = "Your Account Password Has Been Updated - Green Drive Connect";
            $body = "
            <html>
            <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #544e54;'>
                <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #544e54;'>
                    <tr>
                        <td style='padding: 20px; text-align: center; background-color: #8b8b8b; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                            <h2>Password Updated</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding: 20px;'>
                            <p>Hello <strong>{$user->name}</strong>,</p>
                            <p>🔐 Your account password has been <strong>successfully updated</strong>.</p>
    
                            <table cellpadding='5' cellspacing='0' style='width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px; color: #544e54;'>
                                <tr style='background-color: #f2f2f2;'>
                                    <td><strong>Email:</strong></td>
                                    <td>{$user->email}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{$phone}</td>
                                </tr>
                                <tr style='background-color: #f2f2f2;'>
                                    <td><strong>Role:</strong></td>
                                    <td>{$role_name}</td>
                                </tr>
                            </table>
    
                            <p style='margin-top: 15px;'>⚠️ If you did not request this change, please contact Admin Support immediately.</p>
                            <p style='margin-top: 20px;'>{$footerContentText}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align: center; padding: 15px; font-size: 12px; color: #544e54;'>
                            &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                        </td>
                    </tr>
                </table>
            </body>
            </html>
            ";
        }
        
        if ($forward_type == 'staff_account_status_notify') {
            $subject = "Your Account Status Has Been Updated - Green Drive Connect";
    
            // Set message based on status
            switch (strtolower($account_status)) {
                case 'pending':
                    $statusMessage = "⏳ Your account is currently in <strong>Pending</strong> status. Please wait for Admin approval to get full access.";
                    break;
                case 'active':
                    $statusMessage = "🎉 Good news! Your account has been <strong>Activated</strong>. You can now log in and start using your account.";
                    break;
                case 'suspended':
                    $statusMessage = "⚠️ Your account has been <strong>Suspended</strong>. Please contact the Admin for more details and assistance.";
                    break;
                default:
                    $statusMessage = "ℹ️ Your account status has been updated to: <strong>" . ucfirst($account_status) . "</strong>.";
                    break;
            }
    
            $body = "
            <html>
            <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #544e54;'>
                <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #544e54;'>
                    <tr>
                        <td style='padding: 20px; text-align: center; background-color: #8b8b8b; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                            <h2>Account Status Update</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding: 20px; color: #544e54;'>
                            <p>Hello <strong>{$user->name}</strong>,</p>
                            <p>{$statusMessage}</p>
    
                            <table cellpadding='5' cellspacing='0' style='width: 100%; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px; color: #544e54;'>
                                <tr style='background-color: #f2f2f2;'>
                                    <td><strong>Email:</strong></td>
                                    <td>{$user->email}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{$phone}</td>
                                </tr>
                                <tr style='background-color: #f2f2f2;'>
                                    <td><strong>Role:</strong></td>
                                    <td>{$role_name}</td>
                                </tr>
                                <tr>
                                    <td><strong>Current Status:</strong></td>
                                    <td>{$account_status}</td>
                                </tr>
                            </table>
    
                            <p style='margin-top: 20px;'>{$footerContentText}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align: center; padding: 15px; font-size: 12px; color: #544e54;'>
                            &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                        </td>
                    </tr>
                </table>
            </body>
            </html>
            ";
        }

        CustomHandler::sendEmail($toEmails, $subject, $body, $ccEmails);
    }

    
    function StaffCreateSentWhatsMessage($phone, $user, $role_name)
    {
        $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
        
        // Admin message
        $Admin_message = "📢 *New Staff Account Created*\n\n" .
            "A new staff has been successfully created in the system.\n\n" .
            "Name: " . $user->name . "\n" .
            "Contact: " . $phone . "\n" .
            "Role: " . ($role_name ?? 'N/A') . "\n" .
            "Created At: " . (!empty($user->created_at) ? date('d-m-Y h:i A', strtotime($user->created_at)) : '') . "\n\n" .
            "Please verify the details in the Admin Panel.\n\n" .
            ($footerText ?? "Thank you,\nGreenDriveConnect Team");
    
        CustomHandler::admin_whatsapp_message($Admin_message);
    
        // Staff (user) message
        $message = "Hello " . $user->name . ",\n\n" .
            "Welcome to *GreenDrive Mobility*.\n\n" .
            "Your staff account has been created successfully with the following details:\n\n" .
            "Email: " . $user->email . "\n" .
            "Phone: " . $phone . "\n" .
            "Role: " . ($role_name ?? 'N/A') . "\n\n" .
            "🔑 Login credentials have been created for you. Please contact the Admin to receive your username and password.\n\n" .
            "Once you have the credentials, you can log in and start using your account.\n\n" .
            ($footerText ?? "Thank you,\nGreenDriveConnect Team");
    
        CustomHandler::user_whatsapp_message($phone, $message);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\view\View
     */
    public function show(User $user)
    {
        \cs_set('theme', [
            'title' => 'Staff Information',
            'description' => 'Display Single Staff Information.',
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Staff List',
                    'link' => \route('admin.user.index'),
                ],
                [
                    'name' => 'Staff Information',
                    'link' => false,
                ],
            ],
        ]);

        return \view('user::show', \compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\view\View
     */
    public function edit(User $user)
    {
        \cs_set('theme', [
            'title' => 'Edit a Staff',
            'description' => 'Edit existing Staff data.',
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Staff List',
                    'link' => \route('admin.user.index'),
                ],
                [
                    'name' => 'Edit Staff Information',
                    'link' => false,
                ],
            ],
        ]);

        $city_id = isset($user) && $user->city_id ? $user->city_id : 0;
        $zones = Zones::where('city_id', $city_id)->where('status',1)->get();
        return view('user::create_edit', ['item' => $user, 'zones' => $zones]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
       public function update(Request $request, User $user) //updated by Gowtham.s - Zone Map
    {

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id . ',id',
            'phone' => 'nullable|string|max:255|unique:users,phone,' . $user->id . ',id',
            'city' => 'required|integer|exists:ev_tbl_city,id',
            'zone' =>'nullable|exists:zones,id',
            'login_type'=>'required|in:1,2',
            'role' => 'required|integer|exists:roles,id',
            'status' => 'required|in:' . implode(',', array_keys(User::statusList())),
            'age' => 'nullable|integer',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|in:' . implode(',', array_keys(User::genderList())),
            'permissions' => 'nullable|array',
        ]);
        
        if ($request->login_type == 2) {
            $request->validate([
                'zone' => 'required|exists:zones,id',
            ]);
        }
        

        if ($request->password) {
            $request->validate([
                'password' => ['required', 'string', new Password(), 'confirmed'],
            ]);
           
            $data['password'] = Hash::make($request->password);
            $data['password_changed_at'] = now();
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:1024', 
            ]);
            $avatar = $request->file('avatar');
            $imageName = uniqid() . '.' . $avatar->getClientOriginalExtension();
            $destinationPath = public_path('uploads/users');
            $avatar->move($destinationPath, $imageName);
            $data['profile_photo_path'] = $imageName;
            if ($user->profile_photo_path) {
                $oldFilePath = public_path($user->profile_photo_path);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath); 
                }
            }
        }
        $data['city_id'] = $request->city;
        $data['login_type'] = $request->login_type;
        $data['zone_id'] = $request->zone ?? null;
        
        if($user->status != $request->status){
            $this->StaffStatusSentWhatsMessage($request->phone,$user,$user->get_role->name,$request->status);
           $this->StaffSentEmail($request->phone, $user, $user->get_role->name, 'staff_account_status_notify', $request->status);
        }
        
        $user->update($data);
        
        if (auth()->user()->id != $user->id) {
            $user->syncRoles($data['role']);

            $user->syncPermissions($data['permissions'] ?? []);
        } else {
            $user->status = 'Active';
            $user->save();
            // Session::flash('error', 'You Can\'t Updated Your User Account Status Or Role.');
            return response()->json(['success'=>false,'message'=>'You Can\'t Updated Your User Account Status Or Role.']);
        }

        if ($request->password) { //updated by Gowtham.s
            DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password_changed_at' => now(),
            ]);
          $this->StaffPasswordUpdateNotify($request->phone,$user,$user->get_role->name);
          $this->StaffSentEmail($request->phone,$user,$user->get_role->name,'staff_password_update_notify');
        }

        // flash message
        // Session::flash('success', 'Staff account updated successfully');

        // return \redirect()->route(config('theme.rprefix') . '.index');
        return response()->json(['success'=>true,'message'=>'Staff account updated successfully!']);
    }
    
    function StaffPasswordUpdateNotify($phone, $user, $role_name)
    {
        $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');

        // Admin message
        $Admin_message = "🔐 *Staff Account Password Updated*\n\n" .
            "A staff account password has been updated in the system.\n\n" .
            "Name: " . $user->name . "\n" .
            "Contact: " . $phone . "\n" .
            "Role: " . ($role_name ?? 'N/A') . "\n" .
            "Updated At: " . (!empty($user->updated_at) ? date('d-m-Y h:i A', strtotime($user->updated_at)) : '') . "\n\n" .
            "Please ensure this change was authorized.\n\n" .
            ($footerText ?? "Thank you,\nGreenDriveConnect Team");
    
        CustomHandler::admin_whatsapp_message($Admin_message);
    
        // Staff (user) message
        $message = "Hello " . $user->name . ",\n\n" .
            "🔐 Your account password has been *successfully updated*.\n\n" .
            "Email: " . $user->email . "\n" .
            "Phone: " . $phone . "\n" .
            "Role: " . ($role_name ?? 'N/A') . "\n\n" .
            "If you did not request this change, please contact Admin Support immediately.\n\n" .
            "You can now use your new password to log in and access your account securely.\n\n" .
            ($footerText ?? "Thank you,\nGreenDriveConnect Team");
    
        CustomHandler::user_whatsapp_message($phone, $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
     
     public function destroy(User $user)
    {
        if (auth()->user()->id == $user->id) {
            Session::flash('error', 'You can\'t delete your account.');

            return response()->error('', 'You can\'t delete your account.', 403);
        }
        
        // dd($user->phone,$user,$user->get_role->name);
      
        $user->delete_status = $user->delete_status == 1 ? 0 : 1;
        $user->status = $user->status == 'Suspended' ? 'Active' : 'Suspended';
        
        $user->save();
        
        $statusText = $user->status == 'Suspended' ? 'Active' : 'Suspended';
        
        $this->StaffStatusSentWhatsMessage($user->phone,$user,$user->get_role->name,$statusText);
        $this->StaffSentEmail($user->phone, $user, $user->get_role->name, 'staff_account_status_notify', $statusText);

        $message = $user->delete_status == 1 ? 'Staff account Removed successfully' : 'Staff account Restored successfully';
        
        Session::flash('success', $message);

        return response()->success('', $message, 200);
    }
    
    
    function StaffStatusSentWhatsMessage($phone, $user, $role_name,$account_status)
    {
        $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
    
        $Admin_message = "📢 *Staff Account Status Update*\n\n" .
            "A staff account status has been updated in the system.\n\n" .
            "Name: " . $user->name . "\n" .
            "Contact: " . $phone . "\n" .
            "Role: " . ($role_name ?? 'N/A') . "\n" .
            "Current Status: " . $account_status . "\n" .
            "Updated At: " . (!empty($user->updated_at) ? date('d-m-Y h:i A', strtotime($user->updated_at)) : '') . "\n\n" .
            "Please verify the details in the Admin Panel.\n\n" .
            ($footerText ?? "Thank you,\nGreenDriveConnect Team");
    
        CustomHandler::admin_whatsapp_message($Admin_message);

        $statusMessage = "";
        switch (strtolower($account_status)) {
             case 'pending':
                $statusMessage = "⏳ Your account is currently in *Pending* status. Please wait for Admin approval to get full access.";
                break;
            case 'active':
                $statusMessage = "🎉 Good news! Your account has been *Activated*. You can now log in and start using your account.";
                break;
    
            case 'suspended':
                $statusMessage = "⚠️ Your account has been *Suspended*. Please contact the Admin for more details and assistance.";
                break;
    
            default:
                $statusMessage = "ℹ️ Your account status has been updated to: *" . ucfirst($user->status) . "*.";
                break;
        }
    
        $message = "Hello " . $user->name . ",\n\n" .
            $statusMessage . "\n\n" .
            "Role: " . ($role_name ?? 'N/A') . "\n" .
            "Email: " . $user->email . "\n" .
            "Phone: " . $phone . "\n\n" .
            ($footerText ?? "Thank you,\nGreenDriveConnect Team");
    
        CustomHandler::user_whatsapp_message($phone, $message);
    }

    /**
     * View Use Profile
     *
     * @return \Illuminate\view\View
     */
    public function profile()
    {
        \cs_set('theme', [
            'title' => 'Profile',
            'description' => 'User Profile Information',
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Profile',
                    'link' => false,
                ],
            ],
        ]);

        return view('user::show', ['user' => auth()->user(), 'profile' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function statusUpdate(User $user, Request $request)
    {
        $status = $user->status;

        if (\auth()->user()->id == $user->id) {
            return \response()->error([], 'You can\'t update your account status.', 403);
        }

        $user->update(['status' => $request->status]);

        return \response()->success($user, 'Staff Account Status Updated Successfully.', 200);
    }
    
    
    public function user_data_export(Request $request, $id, $user_role)
    {
        if($id == 0 && $user_role == "not-assigned"){
            $filename = '-Onboard-Pending-List-' . now()->format('d-m-Y') . '.xlsx';
            return Excel::download(new TelecallerOnboardExport($id, $user_role), $filename);
        }else{
            $user = User::where('id', $id)->first();
            if (!$user) {
                return response()->json(['error' => 'Staff account not found.'], 404);
            }
            $filename = $user->name . '-Onboard-list-' . now()->format('d-m-Y') . '.xlsx';
            return Excel::download(new TelecallerOnboardExport($id, $user_role), $filename);
        }
    }

}
