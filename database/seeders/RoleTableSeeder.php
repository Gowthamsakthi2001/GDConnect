<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Permission\Entities\Permission;
use Modules\Role\Entities\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define permissions grouped by categories
        $permissions = [
            'General' => [],
            'Dashboard' => [],
            'Employee' => [
                'employee_management',
                'employee_list',
                'employee_create',
            ],
            'Vehicle Management' => [
                'vehicle_management',
                'vehicle_type_management',
                'vehicle_division_management',
                'vehicle_rta_office_management',
                'vehicle_ownership_type_management',
                'document_type_management',
                'legal_document_management',
            ],
            'Vehicle Requisition' => [
                'vehicle_requisition_type_management',
                'vehicle_requisition_management',
                'vehicle_requisition_purpose_management',
                'vehicle_route_management',
                'pick_drop_requisition',
            ],
            'Vehicle Insurance' => [
                'vehicle_insurance_company_management',
                'vehicle_insurance_recurring_period_management',
                'insurance_management',
            ],
            'Refueling' => [
                'fuel_type_management',
                'fuel_station_management',
                'refueling_management',
                'refueling_requisition_management',
            ],
            'Inventory' => [
                'inventory_category_management',
                'inventory_location_management',
                'inventory_parts_management',
                'inventory_parts_usage_management',
                'inventory_vendor_management',
                'expense_management',
                'expense_type_management',
                'trip_type_management',
                'inventory_stock_management',
            ],
            'Vehicle Maintenance' => [
                'vehicle_maintenance_management',
                'vehicle_maintenance_type_management',
            ],
            'Purchase' => [
                'purchase_management',
            ],
            'Report' => [
                'report_management',
                'employee_report',
                'driver_report',
                'vehicle_report',
                'vehicle_requisition_report',
                'pickdrop_requisition_report',
                'refuel_requisition_report',
                'purchase_report',
                'expense_report',
                'maintenance_report',
            ],
            'User' => [
                'user_management',
                'role_management',
                'permission_management',
            ],
            'Setting' => [
                'setting_management',
                'mail_setting_management',
                'env_setting_management',
                'language_setting_management',
            ],
            'City' => [
                'city',
                'create',
                'list',
            ],
           'Leave Management' => [
                'Leave_Management',
                'types_of_leave',
                'new_leave_request',
                'approved_list',
                'leave_log',
            ],
            'Leads' => [
                'leads',
                'leads_list',
                'leads_add',
            ],
            'Area' => [
                'area',
                'area_create',
                'area_list',
            ],
            'Rider Category' => [
                'rider',
                'rider_create',
                'rider_list',
            ],
            'Green Rider' => [
                'Green_Rider',
                'Green_Rider_Create',
                'Green_Rider_list',
            ],
            'Asset Master' => [
                'asset_manage_dashboard',
                'asset_master',
                'modal_master_vechile',
                'modal_master_battery',
                'modal_master_charger',
                'manufacturer_master',
                'po_table',
                'ams_location_master',
                'asset_insurance_details',
                'asset_master_vechile',
                'asset_master_battery',
                'asset_master_charger',
                'quality_check',
                'brand_model_master',
                'vehicle_model_master',
                'location_master',
                'vehicle_transfer',
                'quality_check_list'
            ],
            'Master Management' => [
                'master_management' ,
                'sidebar_modules',
                'telemetric_master',
                'financing_type_master',
                'asset_ownership_master' ,
                'hypothecation_master' ,
                'insurer_name_master' ,
                'insurance_type_master' ,
                'registration_type_master',
                'customer_master',
                'inventory_location_master' ,
                'vehicle_types',
                'color_master',
                'state',
                'customer_type_master',
            ],
            'Zones' => [
                'zone',
            ],
            'Fleet Management' => [
                'fleet_management',
            ],
            'Clients' => [
                'Clients',
                'client_create',
                'client_list',
                'client_edit',
                'hub_create',
                'hub_list',
                'hub_edit'
            ],
            'API Club' => [
                'api_club_log',
                'api_club_log_settings',
                'adhaar_log',
                'license_log',
                'bank_detail_log',
                'pancard_log'
            ],
            'Adhoc Management' => [
                'adhoc_managment',
                'list_of_adhoc',
                'adhoc_log_list'
            ],
            'Background Verification' => [
                'background_verification',
                'bgt_recruiter_list'
            ],
             'Hr Status' => [
                'hr_status',
                'hr_dashboard_view',
                'recruiter_list',
                'hr_onboard_tap',
                'hr_level_one',
                'hr_level_two',
                'rider_onboard',
                'employee_categories',
                'onboarding_employee',
                'onboarding_rider',
                'onboarding_adhoc',
                'onboarding_helper'
            ],
            'BGV Vendor' => [
                'bgv_vendor',
                'bgv_verification_list'
            ],
            'B2B' => [
                'b2b',
                'b2b_dashboard',
                'b2b_add_rider',
                'b2b_vehicle_request_list',
                'b2b_vehicle_list',
                'b2b_live_tracking',
                'b2b_reports',
            ],
            'B2B Admin' => [
                'b2b_admin_dashboard',
                'b2b_admin_deployed_asset_list',
                'b2b_admin_rider_list',
                'b2b_admin_agent_list',
                'b2b_admin_dashboard_issue_ticket',
                'b2b_admin_deployment_request_list',
                'b2b_admin_service_list',
                'b2b_admin_return_list',
                'b2b_admin_recovery_list',
                'b2b_admin_accident_list',
                'b2b_admin_report_list',
                'b2b_admin_zone_list',
            ],
        ];
        
        $roles = [
            'User' => [],
        ];
        
        // when you add new permissions in table ,you should change firstOrCreate instead of create
        
        // Ensure the 'Administrator' role exists
        $administrator = Role::firstOrCreate(['name' => 'Administrator']);

        // Create permissions and assign to Administrator role
        foreach ($permissions as $group => $groupPermissions) {
            foreach ($groupPermissions as $permission) {
                $permissionModel = Permission::firstOrCreate([
                    'name' => $permission,
                    'group' => $group,
                ]);
                // Assign permission to Administrator role
                $permissionModel->assignRole($administrator);
            }
        }


        // Seed users
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => 'admin', 
                'role' => 'Administrator',
            ],
            [
                'name' => 'User',
                'email' => 'user@gmail.com',
                'password' => 'user', 
                'role' => 'User',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']], 
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now(),
                    'status' => 'Active',
                ]
            );

            // Assign role to the user
            $user->assignRole($userData['role']);
        }
    }
}
