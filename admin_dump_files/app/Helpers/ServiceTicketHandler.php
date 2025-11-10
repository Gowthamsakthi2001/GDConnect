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

class ServiceTicketHandler
{
    
 
    public static function RiderstoreNotification($title, $body, $rider_id){
        
        $createModel = new \Modules\B2B\Entities\B2BRidersNotification();
        $createModel->title = $title;
        $createModel->description = $body;
        $createModel->image = null; // optional image
        $createModel->status = 1;
        $createModel->rider_id = $rider_id;
        $createModel->save();
    }
    
    public static function saveAgentNotification($agentId, $title, $body, $image = null){
        
        $createModel = new \Modules\B2B\Entities\B2BAgentsNotification();
        $createModel->title = $title;
        $createModel->description = $body;
        $createModel->image = $image;
        $createModel->status = 1;
        $createModel->agent_id = $agentId;
        $createModel->save();
    }
    
    
    
    public static function AutoSendServiceRequestEmail($serviceTicketId, $rider_id, $vehicle_id,$repairInfo, $forward_type,$tc_create_type)
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
        $riderPhone   = $rider->mobile_no;
        $riderEmail   = $rider->email;
        $service_TicketId    = $serviceTicketId ?? '';
        $customerID   = $rider->customerLogin->customer_relation->id ?? 'N/A';
        $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
        $customerEmail= $rider->customerLogin->customer_relation->email ?? 'N/A';
        $customerPhone = $rider->customerLogin->customer_relation->phone ?? 'N/A';
        $customerLoginEmail= $rider->customerLogin->email ?? 'N/A';

        $vehicleData = AssetMasterVehicle::with(['quality_check'])
            ->where('id', $vehicle_id)
            ->first();

        // Vehicle details
        $AssetvehicleId = $vehicleData->id ?? 'N/A';
        $vehicleNo      = $vehicleData->permanent_reg_number ?? 'N/A';
        $vehicleType    = $vehicleData->quality_check->vehicle_type_relation->name ?? 'N/A';
        $vehicleModel   = $vehicleData->quality_check->vehicle_model_relation->vehicle_model ?? 'N/A';
    
        $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
        $footerContentText = $footerText ?? "For any assistance, please reach out to Admin Support.<br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
        
        $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.
                <br>Email: {$customerEmail}<br>Thank you,<br>{$customerName}";
    
        if ($forward_type == 'customer_create_ticket') {
    
            // Rider email
            if (!empty($riderEmail)) {
                    if ($tc_create_type === 'create_by_rider') {
                        $subject = "Service Request Created - Ticket ID #{$service_TicketId}";
                        $intro = "Your service request has been successfully registered.";
                    } elseif ($tc_create_type === 'create_by_customer') {
                        $subject = "Service Request Notification - Ticket ID #{$service_TicketId}";
                        $intro = "A new service request has been created by your customer {$customerName} on your behalf.";
                    }
                
                    $body = "
                        <html>
                        <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; margin: 0; color: #544e54;'>
                            <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden;'>
                                
                                <tr>
                                    <td style='background-color: #4a90e2; color: #ffffff; text-align: center; padding: 25px 20px;'>
                                        <h2 style='margin: 0; font-size: 22px;'>Service Request Notification</h2>
                                    </td>
                                </tr>
                
                                <tr>
                                    <td style='padding: 25px 20px;'>
                                        <p style='font-size: 16px; margin-bottom: 15px;'>Hello <strong>{$riderName}</strong>,</p>
                                        <p style='font-size: 15px; margin-bottom: 20px;'>{$intro}</p>
                
                                        <table cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                            <tr style='background-color: #f2f2f2;'>
                                                <th style='text-align: left;'>Ticket ID</th>
                                                <td>{$service_TicketId}</td>
                                            </tr>
                                        </table>
                
                                        <h3 style='margin-top: 25px; font-size: 18px; color: #333;'>Reported Issue</h3>
                                        <p style='font-size: 15px; line-height: 1.5;'>
                                            {$repairInfo['issue_description']}<br>
                                            <strong>Location:</strong> {$repairInfo['address']}<br>
                                            <a href='https://maps.google.com/?q={$repairInfo['latitude']},{$repairInfo['longitude']}' target='_blank' style='color:#4a90e2;'>View on Map</a>
                                        </p>
                
                                        <p style='margin-top: 25px; font-size: 14px; line-height: 1.5;'>{$CustomerfooterContentText}</p>
                                    </td>
                                </tr>
                
                                <tr>
                                    <td style='background-color: #f2f2f2; text-align: center; padding: 15px; font-size: 12px; color: #777;'>
                                        &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                                    </td>
                                </tr>
                            </table>
                        </body>
                        </html>
                    ";
                    CustomHandler::sendEmail([$riderEmail], $subject, $body);
                }

            // Customer email
            $toCustomerEmails = array_filter([$customerLoginEmail, $customerEmail]);
            if (!empty($toCustomerEmails)) {
            
                if ($tc_create_type === 'create_by_rider') {
                    $introText = "Your rider <strong>{$riderName}</strong> has created a new service request (Ticket ID: {$service_TicketId}). Below are the details for your reference:";
                    $subject   = "Service Request Created by Rider - Ticket ID #{$service_TicketId}";
                } else { // customer_create_ticket
                    $introText = "We have received your service request. Below are the details of the assigned rider and vehicle for your reference:";
                    $subject   = "Service Request Confirmation - Ticket ID #{$service_TicketId}";
                }
            
                $body = "
                    <html>
                    <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; margin:0; color: #544e54;'>
                        <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 650px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden;'>
                            
                            <!-- Header -->
                            <tr>
                                <td style='background-color: #4a90e2; color: #ffffff; text-align: center; padding: 25px 20px;'>
                                    <h2 style='margin:0; font-size: 22px;'>Service Request</h2>
                                    <p style='margin:5px 0 0; font-size: 14px;'>Ticket ID: {$service_TicketId}</p>
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
                                        <tr>
                                            <td style='width: 40%; font-weight:bold; border-bottom: 1px solid #ddd;'>Name</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$riderName}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>Phone</td>
                                            <td style='background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>{$riderPhone}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; border-bottom: 1px solid #ddd;'>Email</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$riderEmail}</td>
                                        </tr>
                                    </table>
            
                                    <!-- Vehicle Details -->
                                    <table cellpadding='10' cellspacing='0' style='width: 100%; margin-top:20px; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                        <tr style='background-color: #f2f2f2;'>
                                            <th colspan='2' style='text-align:center; font-size:16px; padding:10px;'>Vehicle Details</th>
                                        </tr>
                                        <tr>
                                            <td style='width: 40%; font-weight:bold; border-bottom: 1px solid #ddd;'>Vehicle ID</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$AssetvehicleId}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>Vehicle No</td>
                                            <td style='background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>{$vehicleNo}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; border-bottom: 1px solid #ddd;'>Type</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$vehicleType}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; background-color: #f9f9f9;'>Model</td>
                                            <td style='background-color: #f9f9f9;'>{$vehicleModel}</td>
                                        </tr>
                                    </table>
            
                                    <!-- Repair Info -->
                                    <h3 style='margin-top: 25px; font-size: 18px; color: #333;'>Reported Issue</h3>
                                    <p style='font-size: 15px; line-height: 1.5;'>
                                        {$repairInfo['issue_description']}<br>
                                        <strong>Location:</strong> {$repairInfo['address']}<br>
                                        <a href='https://maps.google.com/?q={$repairInfo['latitude']},{$repairInfo['longitude']}' target='_blank' style='color:#4a90e2; text-decoration:none;'>Repair Location</a>
                                    </p>
            
                                    <p style='margin-top: 25px; font-size: 14px; line-height: 1.5;'>{$footerContentText}</p>
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
                    </html>
                ";
               
                CustomHandler::sendEmail($toCustomerEmails, $subject, $body);
            }

            // Admin email
            $adminEmails = DB::table('roles')
                ->leftJoin('users', 'roles.id', '=', 'users.role')
                ->whereIn('users.role', [1, 13]) // Admins
                ->where('users.status','Active')
                ->pluck('users.email')
                ->filter()
                ->toArray();
            
            if (!empty($adminEmails)) {
            
                $subject = "New Service Request Assigned - Ticket ID #{$service_TicketId}";
            
                // Determine who created the ticket
                if ($tc_create_type == 'create_by_rider') {
                    $creatorText = "This ticket was created by the rider <strong>{$riderName}</strong>.";
                } else {
                    $creatorText = "This ticket was created by the customer <strong>{$customerName}</strong>.";
                }
            
                $body = "
                <html>
                  <body style='font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 30px; color: #333;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 650px; margin: auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08);'>
            
                      <!-- Header -->
                      <tr>
                        <td style='background-color: #4a90e2; color: #ffffff; text-align: center; padding: 25px 20px;'>
                          <h2 style='margin:0; font-size: 22px;'>New Service Request Created</h2>
                          <p style='margin:5px 0 0; font-size: 14px;'>Ticket ID: {$service_TicketId}</p>
                        </td>
                      </tr>
            
                      <!-- Body Content -->
                      <tr>
                        <td style='padding: 25px; line-height: 1.6; font-size: 15px;'>
                          <p style='margin: 0 0 15px;'>Dear Admin,</p>
                          <p style='margin: 0 0 20px;'>A new service request has been created and assigned. {$creatorText}</p>
            
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
                          <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                            <tr style='background-color: #f7f7f7;'>
                              <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Rider Details</td>
                            </tr>
                            <tr><td><strong>Name:</strong></td><td>{$riderName}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>Phone:</strong></td><td>{$riderPhone}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>{$riderEmail}</td></tr>
                          </table>
            
                          <!-- Vehicle Details -->
                          <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                            <tr style='background-color: #f7f7f7;'>
                              <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Vehicle Details</td>
                            </tr>
                            <tr><td><strong>Vehicle ID:</strong></td><td>{$AssetvehicleId}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>Vehicle No:</strong></td><td>{$vehicleNo}</td></tr>
                            <tr><td><strong>Type:</strong></td><td>{$vehicleType}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>Model:</strong></td><td>{$vehicleModel}</td></tr>
                          </table>
            
                          <!-- Reported Issue -->
                          <h3 style='margin-top: 25px; font-size: 16px; color: #333;'>Reported Issue</h3>
                          <p style='font-size: 14px; line-height: 1.5;'>
                            {$repairInfo['issue_description']}<br>
                            <strong>Location:</strong> {$repairInfo['address']}<br>
                            <a href='https://maps.google.com/?q={$repairInfo['latitude']},{$repairInfo['longitude']}' target='_blank' style='color:#4a90e2; text-decoration:none;'>View on Map</a>
                          </p>
            
                          <p style='margin-top: 20px; font-size: 14px; color: #555;'>{$footerContentText}</p>
                        </td>
                      </tr>
            
                      <!-- Footer -->
                      <tr>
                        <td style='background-color: #f7f7f7; text-align: center; padding: 15px; font-size: 12px; color: #666;'>
                          &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                        </td>
                      </tr>
            
                    </table>
                  </body>
                </html>
                ";

                CustomHandler::sendEmail($adminEmails, $subject, $body);
            }
        }
    }
    
    public static function AutoSendServiceRequestWhatsApp($serviceTicketId, $rider_id, $vehicle_id,$repairInfo, $forward_type,$tc_create_type)
    {
        
        $rider = B2BRider::with('customerLogin.customer_relation')
            ->where('id', $rider_id)
            ->first();

        if (!$rider || !$rider->mobile_no) {
            Log::info('Whatsapp Notify : Rider or mobile number not found');
            return false;
        }
    
        // Rider details
        $riderName    = $rider->name ?? 'Rider';
        $riderPhone   = $rider->mobile_no;
        $riderEmail   = $rider->email;
        $service_TicketId    = $serviceTicketId ?? '';
        $customerID   = $rider->customerLogin->customer_relation->id ?? 'N/A';
        $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
        $customerEmail= $rider->customerLogin->customer_relation->email ?? 'N/A';
        $customerPhone = $rider->customerLogin->customer_relation->phone ?? 'N/A';
        $customerLoginEmail= $rider->customerLogin->email ?? 'N/A';

    
        $vehicleData = AssetMasterVehicle::with(['quality_check'])
            ->where('id', $vehicle_id)
            ->first();

        // Vehicle details
        $AssetvehicleId = $vehicleData->id ?? 'N/A';
        $vehicleNo      = $vehicleData->permanent_reg_number ?? 'N/A';
        $vehicleType    = $vehicleData->quality_check->vehicle_type_relation->name ?? 'N/A';
        $vehicleModel   = $vehicleData->quality_check->vehicle_model_relation->vehicle_model ?? 'N/A';
    
        $api_key = BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
        $url = 'https://whatshub.in/api/whatsapp/send';

         $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
         $footerContentText = $footerText ??
            "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
            
        $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.\n" .
            "Email: {$customerEmail}\n" .
            "Thank you,\n" .
            "{$customerName}";
    
        if ($forward_type == 'customer_create_ticket') {
    
            // Rider phone
        if (!empty($riderPhone)) {

            if ($tc_create_type === 'create_by_rider') {
                $subject = "Service Request Created - Ticket ID #{$service_TicketId}";
                $intro = "Your service request has been successfully registered.";
            } elseif ($tc_create_type === 'create_by_customer') {
                $subject = "Service Request Notification - Ticket ID #{$service_TicketId}";
                $intro = "A new service request has been created by your customer {$customerName} on your behalf.";
            }
        
            $mapLink = "https://maps.google.com/?q={$repairInfo['latitude']},{$repairInfo['longitude']}";
        
            $rider_message = 
                "Hello {$riderName},\n\n" .
                "{$intro}\n\n" .
                "Request Details:\n" .
                "• Ticket ID: {$service_TicketId}\n" .
                "Reported Issue:\n" .
                "{$repairInfo['issue_description']}\n" .
                "📍 Location:\n" .
                "{$repairInfo['address']}\n" .
                "🔗 View on Map: {$mapLink}\n" .
                $CustomerfooterContentText;

            CustomHandler::user_whatsapp_message($riderPhone, $rider_message);
        }

        if (!empty($customerPhone)) {
        
            if ($tc_create_type === 'create_by_rider') {
                $introText = "Your rider {$riderName} has created a new service request (Ticket ID: {$service_TicketId}). Below are the details for your reference:";
                $subject   = "Service Request Created by Rider - Ticket ID #{$service_TicketId}";
            } else { 
                $introText = "We have received your service request. Below are the details of the assigned rider and vehicle for your reference:";
                $subject   = "Service Request Confirmation - Ticket ID #{$service_TicketId}";
            }
        
            $mapLink = "https://maps.google.com/?q={$repairInfo['latitude']},{$repairInfo['longitude']}";
        
            $customer_message = 
                "Hello {$customerName},\n\n" .
                "{$introText}\n\n" .
                "*Rider Details:*\n" .
                "• Name: {$riderName}\n" .
                "• Phone: {$riderPhone}\n" .
                "• Email: {$riderEmail}\n" .
                "*Vehicle Details:*\n" .
                "• Vehicle ID: {$AssetvehicleId}\n" .
                "• Vehicle No: {$vehicleNo}\n" .
                "• Type: {$vehicleType}\n" .
                "• Model: {$vehicleModel}\n" .
                "*Reported Issue:*\n" .
                "{$repairInfo['issue_description']}\n" .
                "📍 Location:\n" .
                "{$repairInfo['address']}\n" .
                "🔗 View on Map: {$mapLink}\n" .
                $footerContentText;
        
            CustomHandler::user_whatsapp_message($customerPhone, $customer_message);
        }

        // Admin phone
        $adminPhone = BusinessSetting::where('key_name', 'admin_whatsapp_no')->value('value');
        if (!empty($adminPhone)) {
        
            $subject = "New Service Request Assigned - Ticket ID #{$service_TicketId}";
        
            if ($tc_create_type == 'create_by_rider') {
                $creatorText = "This ticket was created by the rider {$riderName}.";
            } else {
                $creatorText = "This ticket was created by the customer {$customerName}.";
            }
        
            $mapLink = "https://maps.google.com/?q={$repairInfo['latitude']},{$repairInfo['longitude']}";
        
            $admin_message = 
                "Hello Admin,\n\n" .
                "A new service request has been created and assigned.\n" .
                "{$creatorText}\n\n" .
                "*Customer Details:*\n" .
                "• Name: {$customerName}\n" .
                "• Customer ID: {$customerID}\n" .
                "• Email: {$customerEmail}\n" .
                "• Phone: {$customerPhone}\n" .
                "*Rider Details:*\n" .
                "• Name: {$riderName}\n" .
                "• Phone: {$riderPhone}\n" .
                "• Email: {$riderEmail}\n\n" .
                "*Vehicle Details:*\n" .
                "• Vehicle ID: {$AssetvehicleId}\n" .
                "• Vehicle No: {$vehicleNo}\n" .
                "• Type: {$vehicleType}\n" .
                "• Model: {$vehicleModel}\n" .
                "*Reported Issue:*\n" .
                "{$repairInfo['issue_description']}\n" .
                "📍 Location:\n" .
                "{$repairInfo['address']}\n" .
                "🔗 View on Map: {$mapLink}\n" .
                $footerContentText;
            // Send WhatsApp message
            CustomHandler::admin_whatsapp_message($admin_message);
        }

        }
    }
    
    public static function pushRiderServiceTicketNotification($rider, $serviceTicketId, $repairInfo, $tc_create_type, $customerName = null)
    {
        $svc     = new FirebaseNotificationService();
        $image   = null;
        $icon    = null;
        $riderId = $rider->id;
        $token   = $rider->fcm_token;
    
        try {
            if ($tc_create_type === 'create_by_rider') {
                $title = "Service Request Created - Ticket ID #{$serviceTicketId}";
                $body  = "Your service request has been successfully registered.\n\n"
                       . "Reported Issue: {$repairInfo['issue_description']}\n"
                       . "📍 Location: {$repairInfo['address']}\n"
                       . "🔗 View on Map: https://maps.google.com/?q={$repairInfo['latitude']},{$repairInfo['longitude']}";
            } elseif ($tc_create_type == 'create_by_customer') {
                $title = "Service Request Notification - Ticket ID #{$serviceTicketId}";
                $body  = "A new service request has been created by your customer {$customerName}.\n\n"
                       . "Reported Issue: {$repairInfo['issue_description']}\n"
                       . "📍 Location: {$repairInfo['address']}\n"
                       . "🔗 View on Map: https://maps.google.com/?q={$repairInfo['latitude']},{$repairInfo['longitude']}";
            }
    
            // Store in DB
            $createModel = new B2BRidersNotification();
            $createModel->title       = $title;
            $createModel->description = $body;
            $createModel->image       = $image;
            $createModel->status      = 1;
            $createModel->rider_id    = $riderId;
            $createModel->save();
            
            // Prepare optional data payload
            $data = [
                'ticket_id' => $serviceTicketId,
                'issue'     => $repairInfo['issue_description'],
                'latitude'  => $repairInfo['latitude'],
                'longitude' => $repairInfo['longitude'],
            ];
    
            // Send FCM
            if ($token) {
                $svc->sendToToken($token, $title, $body, $data, $image, $icon, $riderId);
            }
    
            return true;
    
        } catch (\Exception $e) {
            \Log::error("pushRiderServiceTicketNotification failed: " . $e->getMessage());
            return false;
        }
    }



}