<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\City\Entities\City;
use Modules\VehicleServiceTicket\Entities\VehicleTicket;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Modules\B2B\Entities\B2BVehicleRequests;
use Modules\Zones\Entities\Zones;
use Modules\B2B\Entities\B2BReturnRequest;
use Modules\B2B\Entities\B2BAgent;
use Modules\B2B\Entities\B2BVehicleAssignment;
use Modules\B2B\Entities\B2BServiceRequest;
use Modules\AssetMaster\Entities\AssetVehicleInventory;
use Modules\B2B\Entities\B2BRider;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\B2B\Entities\B2BAgentsNotification;
use App\Services\FirebaseNotificationService;
use Modules\B2B\Entities\B2BRidersNotification;
use App\Helpers\CustomHandler;
use App\Jobs\SendEmailJob;

class RecoveryNotifyHandler
{
    
 
    // public static function RiderstoreNotification($title, $body, $rider_id){
        
    //     $createModel = new \Modules\B2B\Entities\B2BRidersNotification();
    //     $createModel->title = $title;
    //     $createModel->description = $body;
    //     $createModel->image = null; // optional image
    //     $createModel->status = 1;
    //     $createModel->rider_id = $rider_id;
    //     $createModel->save();
    // }
    
    // public static function saveAgentNotification($agentId, $title, $body, $image = null){
        
    //     $createModel = new \Modules\B2B\Entities\B2BAgentsNotification();
    //     $createModel->title = $title;
    //     $createModel->description = $body;
    //     $createModel->image = $image;
    //     $createModel->status = 1;
    //     $createModel->agent_id = $agentId;
    //     $createModel->save();
    // }
    
    
    
    public static function AutoSendRecoveryRequestEmail($requestID, $rider_id, $vehicle_id, $recoveryInfo, $tc_create_type)
    {
        $rider = B2BRider::with('customerLogin.customer_relation')
            ->where('id', $rider_id)
            ->first();
    
        if (!$rider || !$rider->mobile_no) {
            Log::info('Email Notify : Rider or mobile number not found');
            return false;
        }
    
        // Rider details
        $riderName    = $rider->name ?? 'Rider';
        $riderPhone   = $rider->mobile_no ?? 'N/A';
        $riderEmail   = $rider->email ?? 'N/A';
        $service_TicketId = $requestID ?? 'N/A';
        $customerID   = $rider->customerLogin->customer_relation->id ?? 'N/A';
        $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
        $customerEmail = $rider->customerLogin->customer_relation->email ?? 'N/A';
        $customerPhone = $rider->customerLogin->customer_relation->phone ?? 'N/A';
        $customerLoginEmail = $rider->customerLogin->email ?? 'N/A';
    
        $vehicleData = AssetMasterVehicle::with(['quality_check','quality_check.vehicle_type_relation', 'quality_check.vehicle_model_relation'])
            ->find($vehicle_id);
    
        $AssetvehicleId = $vehicleData->id ?? 'N/A';
        $cno = $vehicleData->quality_check->chassis_number ?? 'N/A';
        $vehicleNo      = $vehicleData->permanent_reg_number ?? 'N/A';
        $vehicleType    = $vehicleData->quality_check->vehicle_type_relation->name ?? 'N/A';
        $vehicleModel   = $vehicleData->quality_check->vehicle_model_relation->vehicle_model ?? 'N/A';
        $vehicleMake   = $vehicleData->quality_check->vehicle_model_relation->make ?? 'N/A';
        $city_id = $vehicleData->quality_check->location;
        $cityName = $vehicleData->quality_check->location_relation->city_name ?? 'N/A';
        $zoneName = $vehicleData->quality_check->zone->name ?? 'N/A';
        // Who created the request
        $createdByName = 'System';
        $requestedByText = '';
        $roleName = 'N/A';
        
        if ($tc_create_type == 'b2b-admin-dashboard') {
            $user_id = auth()->user()->id;
            $Request_User = \App\Models\User::find($user_id ?? null);
            if ($Request_User) {
                $createdByName = $Request_User->name ?? 'N/A';
                $roleName = $Request_User->get_role->name ?? 'Admin';
                $requestedByText = "{$createdByName}";
            }
        } elseif ($tc_create_type == 'b2b-web-dashboard') {
            $Request_User = \Modules\MasterManagement\Entities\CustomerLogin::find($rider->created_by ?? null);
            if ($Request_User) {
                $createdBy = $Request_User->email ?? 'N/A';
                $createdByName = $Request_User->customer_relation->trade_name ?? 'N/A';
                $roleName = 'Client';
                $requestedByText = "{$createdByName}";
            }
        }

        $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
        $footerContentText = $footerText ?? "For any assistance, please reach out to Admin Support.<br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
    
       $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.
       <br>Email: {$customerEmail}<br>Thank you,<br>{$customerName}";
       $selectedfooterTextContent = ($tc_create_type == 'b2b-web-dashboard') ? $CustomerfooterContentText : $footerContentText;

        /** ------------------ CUSTOMER EMAIL ------------------ **/
        $toCustomerEmails = array_filter([$customerLoginEmail, $customerEmail]);
       
        if (!empty($toCustomerEmails)) {
    
            $subject = "Recovery Request Confirmation - Request ID #{$service_TicketId}";
            $introText = "We have received your recovery request. Below are the details of the assigned rider and vehicle for your reference:";
    
            $customerBody = "
            <html>
            <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; margin:0; color: #544e54;'>
                <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 650px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden;'>
                    
                    <!-- Header -->
                    <tr>
                        <td style='background-color: #4a90e2; color: #ffffff; text-align: center; padding: 25px 20px;'>
                            <h2 style='margin:0; font-size: 22px;'>Recovery Request</h2>
                            <p style='margin:5px 0 0; font-size: 14px;'>Request ID: {$service_TicketId}</p>
                        </td>
                    </tr>
            
                    <!-- Body -->
                    <tr>
                        <td style='padding: 25px 20px;'>
                            <p style='font-size:16px; margin-bottom:15px;'>Dear <strong>{$customerName}</strong>,</p>
                            <p style='font-size:15px; margin-bottom:20px;'>{$introText}</p>
    
                            <!-- Rider Details -->
                            <table cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                <tr style='background-color: #f2f2f2;'>
                                    <th colspan='2' style='text-align:center; font-size:16px; padding:10px;'>Rider Details</th>
                                </tr>
                                <tr><td style='font-weight:bold;'>Name</td><td>{$riderName}</td></tr>
                                <tr><td style='font-weight:bold;'>Phone</td><td>{$riderPhone}</td></tr>
                                <tr><td style='font-weight:bold;'>Email</td><td>{$riderEmail}</td></tr>
                            </table>
    
                            <!-- Vehicle Details -->
                            <table cellpadding='10' cellspacing='0' style='width: 100%; margin-top:20px; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                <tr style='background-color: #f2f2f2;'>
                                    <th colspan='2' style='text-align:center; font-size:16px; padding:10px;'>Vehicle Details</th>
                                </tr>
                                <tr><td style='font-weight:bold;'>Chassis No</td><td>{$cno}</td></tr>
                                <tr><td style='font-weight:bold;'>Vehicle No</td><td>{$vehicleNo}</td></tr>
                                <tr><td style='font-weight:bold;'>Type</td><td>{$vehicleType}</td></tr>
                                <tr><td style='font-weight:bold;'>Model</td><td>{$vehicleModel}</td></tr>
                                <tr><td style='font-weight:bold;'>Make</td><td>{$vehicleMake}</td></tr>
                                <tr><td style='font-weight:bold;'>City</td><td>{$cityName}</td></tr>
                                <tr><td style='font-weight:bold;'>Zone/Hub</td><td>{$zoneName}</td></tr>
                            </table>
    
                            <!-- Requested By -->
                            <table cellpadding='10' cellspacing='0' style='width: 100%; margin-top:20px; border-collapse: collapse; border: 1px solid #ddd;'>
                                <tr style='background-color: #f2f2f2;'>
                                    <th colspan='2' style='text-align:center; font-size:16px;'>Requested By</th>
                                </tr>
                                <tr>
                                    <td style='font-weight:bold;'>Role</td>
                                    <td>{$roleName}</td>
                                </tr>
                                <tr>
                                    <td style='font-weight:bold;'>Name</td>
                                    <td>{$requestedByText}</td>
                                </tr>
                            </table>

    
                            <!-- Reported Issue -->
                            <h3 style='margin-top: 25px; font-size: 18px; color: #333;'>Reported Issue</h3>
                            <p style='font-size: 15px; line-height: 1.5;'>
                                <strong>Reason:</strong> {$recoveryInfo['recovery_reason']}<br>
                                {$recoveryInfo['recovery_description']}
                            </p>
    
                            <p style='margin-top: 25px; font-size: 14px; line-height: 1.5;'>{$selectedfooterTextContent}</p>
                        </td>
                    </tr>
    
                    <!-- Footer -->
                    <tr>
                        <td style='background-color: #f2f2f2; text-align: center; padding: 15px; font-size: 12px; color: #777;'>
                            &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                        </td>
                    </tr>
                </table>
            </body>
            </html>";
    
            CustomHandler::sendEmail($toCustomerEmails, $subject, $customerBody);
        }
    
        /** ------------------ MANAGER EMAIL (CC ADMINS) ------------------ **/
        $managerEmails = DB::table('roles')
            ->leftJoin('users', 'roles.id', '=', 'users.role')
            ->whereIn('users.role', [16]) // Managers
            ->where('users.status', 'Active')
            ->when($city_id != "",function($query) use ($city_id){
                $query->where('city_id',$city_id);
            })
            ->pluck('users.email')
            ->filter()
            ->toArray();
    
        $adminEmails = DB::table('roles')
            ->leftJoin('users', 'roles.id', '=', 'users.role')
            ->whereIn('users.role', [1, 13]) // Admins
            ->where('users.status', 'Active')
            ->pluck('users.email')
            ->filter()
            ->toArray();

        if (!empty($managerEmails)) {
            $subject = "New Recovery Requested - Request ID #{$service_TicketId}";
            $introText = "A new recovery request has been received. Below are the details for your reference.";
    
             $managerBody = "
                <html>
                <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; margin:0; color: #544e54;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 650px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden;'>
                        
                        <!-- Header -->
                        <tr>
                            <td style='background-color: #4a90e2; color: #ffffff; text-align: center; padding: 25px 20px;'>
                                <h2 style='margin:0; font-size: 22px;'>Recovery Request</h2>
                                <p style='margin:5px 0 0; font-size: 14px;'>Request ID: {$service_TicketId}</p>
                            </td>
                        </tr>
                
                        <!-- Body -->
                        <tr>
                            <td style='padding: 25px 20px;'>
                                <p style='font-size:16px; margin-bottom:15px;'>Dear Manager,</p>
                                <p style='font-size:15px; margin-bottom:20px;'>{$introText}</p>
        
                                <!-- Customer Details -->
                                <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                                    <tr style='background-color: #f7f7f7;'>
                                      <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Customer Details</td>
                                    </tr>
                                    <tr><td width='40%'><strong>Name:</strong></td><td>{$customerName}</td></tr>
                                    <tr style='background-color:#fafafa;'><td><strong>Customer ID:</strong></td><td>{$customerID}</td></tr>
                                    <tr><td><strong>Email:</strong></td><td>{$customerEmail}</td></tr>
                                    <tr style='background-color:#fafafa;'><td><strong>Phone:</strong></td><td>{$customerPhone}</td></tr>
                                </table>
                          
                                <!-- Rider Details -->
                                <table cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                    <tr style='background-color: #f2f2f2;'>
                                        <th colspan='2' style='text-align:center; font-size:16px; padding:10px;'>Rider Details</th>
                                    </tr>
                                    <tr><td style='font-weight:bold;'>Name</td><td>{$riderName}</td></tr>
                                    <tr><td style='font-weight:bold;'>Phone</td><td>{$riderPhone}</td></tr>
                                    <tr><td style='font-weight:bold;'>Email</td><td>{$riderEmail}</td></tr>
                                </table>
        
                                <!-- Vehicle Details -->
                                <table cellpadding='10' cellspacing='0' style='width: 100%; margin-top:20px; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                    <tr style='background-color: #f2f2f2;'>
                                        <th colspan='2' style='text-align:center; font-size:16px; padding:10px;'>Vehicle Details</th>
                                    </tr>
                                    <tr><td style='font-weight:bold;'>Chassis No</td><td>{$cno}</td></tr>
                                    <tr><td style='font-weight:bold;'>Vehicle No</td><td>{$vehicleNo}</td></tr>
                                    <tr><td style='font-weight:bold;'>Type</td><td>{$vehicleType}</td></tr>
                                    <tr><td style='font-weight:bold;'>Model</td><td>{$vehicleModel}</td></tr>
                                    <tr><td style='font-weight:bold;'>Make</td><td>{$vehicleMake}</td></tr>
                                    <tr><td style='font-weight:bold;'>City</td><td>{$cityName}</td></tr>
                                    <tr><td style='font-weight:bold;'>Zone/Hub</td><td>{$zoneName}</td></tr>
                                </table>
        
                                <!-- Requested By -->
                                <table cellpadding='10' cellspacing='0' style='width: 100%; margin-top:20px; border-collapse: collapse; border: 1px solid #ddd;'>
                                    <tr style='background-color: #f2f2f2;'>
                                        <th colspan='2' style='text-align:center; font-size:16px;'>Requested By</th>
                                    </tr>
                                    <tr>
                                        <td style='font-weight:bold;'>Role</td>
                                        <td>{$roleName}</td>
                                    </tr>
                                    <tr>
                                        <td style='font-weight:bold;'>Name</td>
                                        <td>{$requestedByText}</td>
                                    </tr>
                                </table>
        
                                <!-- Reported Issue -->
                                <h3 style='margin-top: 25px; font-size: 18px; color: #333;'>Reported Issue</h3>
                                <p style='font-size: 15px; line-height: 1.5;'>
                                    <strong>Reason:</strong> {$recoveryInfo['recovery_reason']}<br>
                                    {$recoveryInfo['recovery_description']}
                                </p>
        
                                <p style='margin-top: 25px; font-size: 14px; line-height: 1.5;'>{$selectedfooterTextContent}</p>
                            </td>
                        </tr>
        
                        <!-- Footer -->
                        <tr>
                            <td style='background-color: #f2f2f2; text-align: center; padding: 15px; font-size: 12px; color: #777;'>
                                &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                            </td>
                        </tr>
                    </table>
                </body>
                </html>";
            
       
            CustomHandler::sendEmail($managerEmails, $subject, $managerBody, $adminEmails);
        }
    }
    
        public static function AutoSendRecoveryRequestClosedWhatsApp($requestID, $rider_id, $vehicle_id, $recoveryInfo, $tc_create_type,$recovery)
    {
        $rider = B2BRider::with('customerLogin.customer_relation')
            ->where('id', $rider_id)
            ->first();
    
        if (!$rider || !$rider->mobile_no) {
            Log::info('Email Notify : Rider or mobile number not found');
            return false;
        }
    
        // Rider details
        $riderName    = $rider->name ?? 'Rider';
        $riderPhone   = $rider->mobile_no ?? 'N/A';
        $riderEmail   = $rider->email ?? 'N/A';
        $service_TicketId = $requestID ?? 'N/A';
        $customerID   = $rider->customerLogin->customer_relation->id ?? 'N/A';
        $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
        $customerEmail = $rider->customerLogin->customer_relation->email ?? 'N/A';
        $customerPhone = $rider->customerLogin->customer_relation->phone ?? 'N/A';
        $customerLoginEmail = $rider->customerLogin->email ?? 'N/A';
    
        $vehicleData = AssetMasterVehicle::with(['quality_check','quality_check.vehicle_type_relation', 'quality_check.vehicle_model_relation'])
            ->find($vehicle_id);
    
        $AssetvehicleId = $vehicleData->id ?? 'N/A';
        $cno = $vehicleData->quality_check->chassis_number ?? 'N/A';
        $vehicleNo      = $vehicleData->permanent_reg_number ?? 'N/A';
        $vehicleType    = $vehicleData->quality_check->vehicle_type_relation->name ?? 'N/A';
        $vehicleModel   = $vehicleData->quality_check->vehicle_model_relation->vehicle_model ?? 'N/A';
        $vehicleMake   = $vehicleData->quality_check->vehicle_model_relation->make ?? 'N/A';
        $city_id = $vehicleData->quality_check->location;
        $cityName = $vehicleData->quality_check->location_relation->city_name ?? 'N/A';
        $zoneName = $vehicleData->quality_check->zone->name ?? 'N/A';
        // Who created the request
        $createdByName = 'System';
        $requestedByText = '';
        $roleName = 'N/A';
        
        if ($tc_create_type == 'b2b-admin-dashboard') {
            $user_id = $recovery->created_by;
            $Request_User = \App\Models\User::find($user_id ?? null);
            if ($Request_User) {
                $createdByName = $Request_User->name ?? 'N/A';
                $roleName = $Request_User->get_role->name ?? 'Admin';
                $requestedByText = "{$createdByName}";
            }
        } elseif ($tc_create_type == 'b2b-web-dashboard') {
            $Request_User = \Modules\MasterManagement\Entities\CustomerLogin::find($rider->created_by ?? null);
            if ($Request_User) {
                $createdBy = $Request_User->email ?? 'N/A';
                $createdByName = $Request_User->customer_relation->trade_name ?? 'N/A';
                $roleName = 'Client';
                $requestedByText = "{$createdByName}";
            }
        }
        
        $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
            $footerContentText = $footerText ??
                "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
            
             $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.\n" .
                 "Email: {$customerEmail}\n" .
                 "Thank you,\n" .
                 "{$customerName}";
                 
        $selectedfooterTextContent = ($tc_create_type == 'b2b-web-dashboard') ? $CustomerfooterContentText : $footerContentText;
        

$customer_message = "Dear {$customerName},\n\n"
    . "*Recovery Request Closed*\n"
    . "Request ID: #{$service_TicketId}\n\n"
    . "We’re pleased to inform you that your recovery request has been successfully *closed*.\n\n"
    
    . "*Rider Details:*\n"
    . "• Name: {$riderName}\n"
    . "• Phone: {$riderPhone}\n"
    . "• Email: {$riderEmail}\n\n"
    
    . "*Vehicle Details:*\n"
    . "• Chassis No: {$cno}\n"
    . "• Vehicle No: {$vehicleNo}\n"
    . "• Type: {$vehicleType}\n"
    . "• Model: {$vehicleModel}\n"
    . "• Make: {$vehicleMake}\n"
    . "• City: {$cityName}\n"
    . "• Zone/Hub: {$zoneName}\n\n"
    
    . "*Reported Issue (at creation):*\n"
    . "• Reason: {$recoveryInfo['recovery_reason']}\n"
    . "• Description: {$recoveryInfo['recovery_description']}\n\n"
    
    . "{$selectedfooterTextContent}\n\n";


$manager_message = "Dear Manager,\n\n"
    . "*Recovery Request Closed*\n"
    . "Request ID: #{$service_TicketId}\n\n"
    . "The recovery request has been *successfully closed*. Below are the closure details:\n\n"
    
    . "*Customer Details:*\n"
    . "• Name: {$customerName}\n"
    . "• Customer ID: {$customerID}\n"
    . "• Email: {$customerEmail}\n"
    . "• Phone: {$customerPhone}\n\n"
    
    . "*Rider Details:*\n"
    . "• Name: {$riderName}\n"
    . "• Phone: {$riderPhone}\n"
    . "• Email: {$riderEmail}\n\n"
    
    . "*Vehicle Details:*\n"
    . "• Chassis No: {$cno}\n"
    . "• Vehicle No: {$vehicleNo}\n"
    . "• Type: {$vehicleType}\n"
    . "• Model: {$vehicleModel}\n"
    . "• Make: {$vehicleMake}\n"
    . "• City: {$cityName}\n"
    . "• Zone/Hub: {$zoneName}\n\n"
    
    . "*Original Reported Issue:*\n"
    . "• Reason: {$recoveryInfo['recovery_reason']}\n"
    . "• Description: {$recoveryInfo['recovery_description']}\n\n"
    
    . "{$selectedfooterTextContent}\n\n";


$admin_message = "Dear Admin,\n\n"
    . "*Recovery Request Closed*\n"
    . "Request ID: #{$service_TicketId}\n\n"
    . "The recovery request has been *closed successfully*. Below are the complete details:\n\n"
    
    . "*Customer Details:*\n"
    . "• Name: {$customerName}\n"
    . "• Customer ID: {$customerID}\n"
    . "• Email: {$customerEmail}\n"
    . "• Phone: {$customerPhone}\n\n"
    
    . "*Rider Details:*\n"
    . "• Name: {$riderName}\n"
    . "• Phone: {$riderPhone}\n"
    . "• Email: {$riderEmail}\n\n"
    
    . "*Vehicle Details:*\n"
    . "• Chassis No: {$cno}\n"
    . "• Vehicle No: {$vehicleNo}\n"
    . "• Type: {$vehicleType}\n"
    . "• Model: {$vehicleModel}\n"
    . "• Make: {$vehicleMake}\n"
    . "• City: {$cityName}\n"
    . "• Zone/Hub: {$zoneName}\n\n"
    
    . "*Original Reported Issue:*\n"
    . "• Reason: {$recoveryInfo['recovery_reason']}\n"
    . "• Description: {$recoveryInfo['recovery_description']}\n\n"
    
    . "{$selectedfooterTextContent}\n\n";
        
        
        $managerphones = DB::table('roles')
            ->leftJoin('users', 'roles.id', '=', 'users.role')
            ->whereIn('users.role', [24]) // Managers
            ->where('users.status', 'Active')
            ->when($city_id != "",function($query) use ($city_id){
                $query->where('city_id',$city_id);
            })
            ->pluck('users.phone')
            ->filter()
            ->toArray();
        
        $managerphones = ['+919360992327','+917812880655'];
        if (!empty($managerphones)) {
                CustomHandler::multi_user_whatsapp_message($managerphones, $manager_message);
        }
        
        if (!empty($customerPhone)) {
                // CustomHandler::user_whatsapp_message($customerPhone, $customer_message);
        }
        
        $adminPhone = BusinessSetting::where('key_name', 'admin_whatsapp_no')->value('value');
        if (!empty($adminPhone)) {
            // CustomHandler::admin_whatsapp_message($admin_message);
        }
        

    }
    
     public static function AutoSendRecoveryRequestWhatsApp($requestID, $rider_id, $vehicle_id, $recoveryInfo, $tc_create_type)
    {
        $rider = B2BRider::with('customerLogin.customer_relation')
            ->where('id', $rider_id)
            ->first();
    
        if (!$rider || !$rider->mobile_no) {
            Log::info('Email Notify : Rider or mobile number not found');
            return false;
        }
    
        // Rider details
        $riderName    = $rider->name ?? 'Rider';
        $riderPhone   = $rider->mobile_no ?? 'N/A';
        $riderEmail   = $rider->email ?? 'N/A';
        $service_TicketId = $requestID ?? 'N/A';
        $customerID   = $rider->customerLogin->customer_relation->id ?? 'N/A';
        $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
        $customerEmail = $rider->customerLogin->customer_relation->email ?? 'N/A';
        $customerPhone = $rider->customerLogin->customer_relation->phone ?? 'N/A';
        $customerLoginEmail = $rider->customerLogin->email ?? 'N/A';
    
        $vehicleData = AssetMasterVehicle::with(['quality_check','quality_check.vehicle_type_relation', 'quality_check.vehicle_model_relation'])
            ->find($vehicle_id);
    
        $AssetvehicleId = $vehicleData->id ?? 'N/A';
        $cno = $vehicleData->quality_check->chassis_number ?? 'N/A';
        $vehicleNo      = $vehicleData->permanent_reg_number ?? 'N/A';
        $vehicleType    = $vehicleData->quality_check->vehicle_type_relation->name ?? 'N/A';
        $vehicleModel   = $vehicleData->quality_check->vehicle_model_relation->vehicle_model ?? 'N/A';
        $vehicleMake   = $vehicleData->quality_check->vehicle_model_relation->make ?? 'N/A';
        $city_id = $vehicleData->quality_check->location;
        $cityName = $vehicleData->quality_check->location_relation->city_name ?? 'N/A';
        $zoneName = $vehicleData->quality_check->zone->name ?? 'N/A';
        // Who created the request
        $createdByName = 'System';
        $requestedByText = '';
        $roleName = 'N/A';
        
        if ($tc_create_type == 'b2b-admin-dashboard') {
            $user_id = auth()->user()->id;
            $Request_User = \App\Models\User::find($user_id ?? null);
            if ($Request_User) {
                $createdByName = $Request_User->name ?? 'N/A';
                $roleName = $Request_User->get_role->name ?? 'Admin';
                $requestedByText = "{$createdByName}";
            }
        } elseif ($tc_create_type == 'b2b-web-dashboard') {
            $Request_User = \Modules\MasterManagement\Entities\CustomerLogin::find($rider->created_by ?? null);
            if ($Request_User) {
                $createdBy = $Request_User->email ?? 'N/A';
                $createdByName = $Request_User->customer_relation->trade_name ?? 'N/A';
                $roleName = 'Client';
                $requestedByText = "{$createdByName}";
            }
        }
        
        $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
            $footerContentText = $footerText ??
                "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
            
             $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.\n" .
                 "Email: {$customerEmail}\n" .
                 "Thank you,\n" .
                 "{$customerName}";
                 
        $selectedfooterTextContent = ($tc_create_type == 'b2b-web-dashboard') ? $CustomerfooterContentText : $footerContentText;
        

        $customer_message = "Dear {$customerName},\n\n"
        . "*New Recovery Request Received*\n"
        . "Request ID: #{$service_TicketId}\n\n"
        . "A new recovery request has been generated. Please find the details below:\n\n"
    
        . " *Rider Details:*\n"
        . "• Name: {$riderName}\n"
        . "• Phone: {$riderPhone}\n"
        . "• Email: {$riderEmail}\n\n"
    
        . "*Vehicle Details:*\n"
        . "• Chassis No: {$cno}\n"
        . "• Vehicle No: {$vehicleNo}\n"
        . "• Type: {$vehicleType}\n"
        . "• Model: {$vehicleModel}\n"
        . "• Make: {$vehicleMake}\n"
        . "• City: {$cityName}\n"
        . "• Zone/Hub: {$zoneName}\n\n"
    
        . "*Requested By:*\n"
        . "• Role: {$roleName}\n"
        . "• Name: {$requestedByText}\n\n"
    
        . " *Reported Issue:*\n"
        . "• Reason: {$recoveryInfo['recovery_reason']}\n"
        . "• Description: {$recoveryInfo['recovery_description']}\n\n"
    
        . "{$selectedfooterTextContent}\n\n";
        
        $manager_message = "Dear Manager,\n\n"
        . "*New Recovery Request Received*\n"
        . "Request ID: #{$service_TicketId}\n\n"
        . "A new recovery request has been generated. Please find the details below:\n\n"
    
        . "*Customer Details:*\n"
        . "• Name: {$customerName}\n"
        . "• Customer ID: {$customerID}\n"
        . "• Email: {$customerEmail}\n"
        . "• Phone: {$customerPhone}\n\n"
    
        . " *Rider Details:*\n"
        . "• Name: {$riderName}\n"
        . "• Phone: {$riderPhone}\n"
        . "• Email: {$riderEmail}\n\n"
    
        . "*Vehicle Details:*\n"
        . "• Chassis No: {$cno}\n"
        . "• Vehicle No: {$vehicleNo}\n"
        . "• Type: {$vehicleType}\n"
        . "• Model: {$vehicleModel}\n"
        . "• Make: {$vehicleMake}\n"
        . "• City: {$cityName}\n"
        . "• Zone/Hub: {$zoneName}\n\n"
    
        . "*Requested By:*\n"
        . "• Role: {$roleName}\n"
        . "• Name: {$requestedByText}\n\n"
    
        . " *Reported Issue:*\n"
        . "• Reason: {$recoveryInfo['recovery_reason']}\n"
        . "• Description: {$recoveryInfo['recovery_description']}\n\n"
    
        . "{$selectedfooterTextContent}\n\n";
        
        $admin_message = "Dear Admin,\n\n"
        . "*New Recovery Request Received*\n"
        . "Request ID: #{$service_TicketId}\n\n"
        . "A new recovery request has been generated. Please find the details below:\n\n"
    
        . "*Customer Details:*\n"
        . "• Name: {$customerName}\n"
        . "• Customer ID: {$customerID}\n"
        . "• Email: {$customerEmail}\n"
        . "• Phone: {$customerPhone}\n\n"
    
        . " *Rider Details:*\n"
        . "• Name: {$riderName}\n"
        . "• Phone: {$riderPhone}\n"
        . "• Email: {$riderEmail}\n\n"
    
        . "*Vehicle Details:*\n"
        . "• Chassis No: {$cno}\n"
        . "• Vehicle No: {$vehicleNo}\n"
        . "• Type: {$vehicleType}\n"
        . "• Model: {$vehicleModel}\n"
        . "• Make: {$vehicleMake}\n"
        . "• City: {$cityName}\n"
        . "• Zone/Hub: {$zoneName}\n\n"
    
        . "*Requested By:*\n"
        . "• Role: {$roleName}\n"
        . "• Name: {$requestedByText}\n\n"
    
        . " *Reported Issue:*\n"
        . "• Reason: {$recoveryInfo['recovery_reason']}\n"
        . "• Description: {$recoveryInfo['recovery_description']}\n\n"
    
        . "{$selectedfooterTextContent}\n\n";
        
        
        $managerphones = DB::table('roles')
            ->leftJoin('users', 'roles.id', '=', 'users.role')
            ->whereIn('users.role', [16]) // Managers
            ->where('users.status', 'Active')
            ->when($city_id != "",function($query) use ($city_id){
                $query->where('city_id',$city_id);
            })
            ->pluck('users.phone')
            ->filter()
            ->toArray();

        if (!empty($managerphones)) {
                CustomHandler::multi_user_whatsapp_message($managerphones, $manager_message);
        }
        
        if (!empty($customerPhone)) {
                CustomHandler::user_whatsapp_message($customerPhone, $customer_message);
        }
        
        $adminPhone = BusinessSetting::where('key_name', 'admin_whatsapp_no')->value('value');
        if (!empty($adminPhone)) {
            CustomHandler::admin_whatsapp_message($admin_message);
        }
        
        

    }
    
    private function formatPhoneNumber($phone)
    {
        $cleanedPhone = preg_replace('/\D+/', '', $phone);
    
        if (substr($cleanedPhone, 0, 1) === '0') {
            $cleanedPhone = substr($cleanedPhone, 1);
        }
    
        if (substr($cleanedPhone, 0, 2) === '91' && strlen($cleanedPhone) === 12) {
            return $cleanedPhone;
        } elseif (strlen($cleanedPhone) === 10) {
            return '91' . $cleanedPhone;
        }
    
        return null;
    }

    
    // public static function AutoSendRecoveryRequestWhatsApp($recoveryId)
    // {
    //     $recovery = \Modules\B2BModule\Entities\B2BRecoveryRequest::with(['created_by_user', 'created_by_customer.customer_relation'])
    //         ->find($recoveryId);
    
    //     if (!$recovery) {
    //         \Log::info('WhatsApp Notify: Recovery Request not found');
    //         return false;
    //     }
    
    //     // Detect who created the request
    //     $creatorType = $recovery->created_by_type ?? 'unknown';
    //     $creatorName = 'N/A';
    //     $creatorRole = 'N/A';
    //     $clientTradeName = 'N/A';
    //     $clientPhone = null;
    
    //     if ($creatorType === 'b2b-admin-dashboard') {
    //         $creator = \App\Models\User::find($recovery->created_by);
    //         $creatorName = $creator->name ?? 'Admin';
    //         $creatorRole = $creator->get_role->name ?? 'Administrator';
    //     } elseif ($creatorType === 'b2b-web-dashboard') {
    //         $creator = \Modules\MasterManagement\Entities\CustomerLogin::find($recovery->created_by);
    //         $creatorName = $creator->name ?? 'Client';
    //         $creatorRole = 'Client';
    //         $clientTradeName = $creator->customer_relation->trade_name ?? 'N/A';
    //         $clientPhone = $creator->customer_relation->phone ?? null;
    //     }
    
    //     $vehicleNo = $recovery->vehicle_number ?? 'N/A';
    //     $reason = $recovery->reason ?? 'N/A';
    //     $contactPerson = $recovery->contact_person_name ?? 'N/A';
    //     $contactNo = $recovery->contact_no ?? 'N/A';
    //     $contactEmail = $recovery->contact_email ?? 'N/A';
    //     $desc = $recovery->description ?? '-';
    
    //     $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value') ??
    //         "For any assistance, please contact Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
    
    //     // 🔹 Message for Customer
    //     if (!empty($clientPhone)) {
    //         $customer_message =
    //             "🟢 *Vehicle Recovery Request Received*\n\n" .
    //             "Hello *{$clientTradeName}*,\n\n" .
    //             "Your vehicle recovery request has been submitted successfully.\n\n" .
    //             "*Recovery Details:*\n" .
    //             "• Vehicle No: {$vehicleNo}\n" .
    //             "• Reason: {$reason}\n" .
    //             "• Contact: {$contactPerson}\n" .
    //             "• Phone: {$contactNo}\n" .
    //             "• Email: {$contactEmail}\n" .
    //             "• Description: {$desc}\n\n" .
    //             "*Requested By:*\n" .
    //             "{$creatorName} ({$creatorRole})\n\n" .
    //             "{$footerText}";
    
    //         CustomHandler::user_whatsapp_message($clientPhone, $customer_message);
    //     }
    
    //     // 🔹 Message for Admin (from BusinessSetting)
    //     $adminPhone = \App\Models\BusinessSetting::where('key_name', 'admin_whatsapp_no')->value('value');
    //     if (!empty($adminPhone)) {
    //         $admin_message =
    //             "🚨 *New Recovery Request Alert*\n\n" .
    //             "A new vehicle recovery request has been created.\n\n" .
    //             "*Vehicle Details:*\n" .
    //             "• Vehicle No: {$vehicleNo}\n" .
    //             "• Reason: {$reason}\n" .
    //             "• Contact Person: {$contactPerson}\n" .
    //             "• Phone: {$contactNo}\n" .
    //             "• Email: {$contactEmail}\n" .
    //             "• Description: {$desc}\n\n" .
    //             "*Requested By:*\n" .
    //             "{$creatorName} ({$creatorRole})\n" .
    //             ($clientTradeName !== 'N/A' ? "Client: {$clientTradeName}\n" : '') . "\n" .
    //             "{$footerText}";
    
    //         CustomHandler::admin_whatsapp_message($admin_message);
    //     }
    
    //     return true;
    // }

    
    // public static function pushRiderServiceTicketNotification($rider, $serviceTicketId, $repairInfo, $tc_create_type, $customerName = null)
    // {
    //     $svc     = new FirebaseNotificationService();
    //     $image   = null;
    //     $icon    = null;
    //     $riderId = $rider->id;
    //     $token   = $rider->fcm_token;
    
    //     try {
    //         if ($tc_create_type === 'create_by_rider') {
    //             $title = "Service Request Created - Ticket ID #{$serviceTicketId}";
    //             $body  = "Your service request has been successfully registered.\n\n"
    //                   . "Reported Issue: {$repairInfo['issue_description']}\n"
    //                   . "📍 Location: {$repairInfo['address']}\n"
    //                   . "🔗 View on Map: https://maps.google.com/?q={$repairInfo['latitude']},{$repairInfo['longitude']}";
    //         } elseif ($tc_create_type == 'create_by_customer') {
    //             $title = "Service Request Notification - Ticket ID #{$serviceTicketId}";
    //             $body  = "A new service request has been created by your customer {$customerName}.\n\n"
    //                   . "Reported Issue: {$repairInfo['issue_description']}\n"
    //                   . "📍 Location: {$repairInfo['address']}\n"
    //                   . "🔗 View on Map: https://maps.google.com/?q={$repairInfo['latitude']},{$repairInfo['longitude']}";
    //         }
    
    //         // Store in DB
    //         $createModel = new B2BRidersNotification();
    //         $createModel->title       = $title;
    //         $createModel->description = $body;
    //         $createModel->image       = $image;
    //         $createModel->status      = 1;
    //         $createModel->rider_id    = $riderId;
    //         $createModel->save();
            
    //         // Prepare optional data payload
    //         $data = [
    //             'ticket_id' => $serviceTicketId,
    //             'issue'     => $repairInfo['issue_description'],
    //             'latitude'  => $repairInfo['latitude'],
    //             'longitude' => $repairInfo['longitude'],
    //         ];
    
    //         // Send FCM
    //         if ($token) {
    //             $svc->sendToToken($token, $title, $body, $data, $image, $icon, $riderId);
    //         }
    
    //         return true;
    
    //     } catch (\Exception $e) {
    //         \Log::error("pushRiderServiceTicketNotification failed: " . $e->getMessage());
    //         return false;
    //     }
    // }



}