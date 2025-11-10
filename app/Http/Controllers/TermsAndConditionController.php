<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Modules\B2B\Entities\B2BRider;//updated by Mugesh.B
use Modules\B2B\Entities\B2BRecoveryRequest;
use Modules\B2B\Entities\B2BVehicleAssignmentLog;
use Modules\RecoveryManager\Entities\RecoveryComment;
use App\Models\User;
use App\Helpers\CustomHandler;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use App\Models\BusinessSetting;

class TermsAndConditionController extends Controller
{
    
    public function index(Request $request)
    {
        $rider = null;

        if ($request->has('id')) {
            try {
                $riderId = decrypt($request->query('id'));
                $rider = B2BRider::find($riderId);
            } catch (\Exception $e) {
                $rider = null;
            }
        }

        return view('terms-condition.index', compact('rider'));
    }
    
    
    public function respond(Request $request)
    {
        $request->validate([
            'rider_id' => 'required',
            'response' => 'required|in:accept,reject'
        ]);

        try {
            $riderId = decrypt($request->rider_id);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Invalid link.'], 400);
        }

        $rider = B2BRider::find($riderId);
        if (!$rider) {
            return response()->json(['status' => 'error', 'message' => 'Rider not found.'], 404);
        }

        if ($rider->terms_condition_status == 1) {
            return response()->json(['status' => 'info', 'message' => 'Terms & Conditions already accepted.']);
        }

        $statusText = $request->response === 'accept' ? 'accepted' : 'rejected';
        $rider->terms_condition_status = $request->response === 'accept' ? 1 : 2;
        $rider->save();

        // Send email notifications
        $this->sendEmailNotify($rider, $request->response);

        return response()->json([
            'status' => 'success',
            'message' => $request->response === 'accept' 
                        ? 'You have successfully accepted the Terms & Conditions.' 
                        : 'You have rejected the Terms & Conditions.'
        ]);
    }
    
    
    private function sendEmailNotify($rider, $response)
    {
        $riderPhone = $rider->mobile_no;
        $riderEmail = $rider->email;
        $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
        $customerEmail = $rider->customerLogin->customer_relation->email ?? 'N/A';
        $customerLoginEmail = $rider->customerLogin->email ?? 'N/A';
        $toAdmins = DB::table('roles')
            ->leftJoin('users', 'roles.id', '=', 'users.role')
            ->whereIn('users.role', [1, 13])
            ->where('users.status', 'Active')
            ->pluck('users.email')
            ->filter()
            ->toArray();
    
        $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
        $footerContentText = $footerText ?? "For assistance, contact support@greendrivemobility.com";
    
        // Make first letter capital, rest lowercase
        $statusText = ucfirst(strtolower($response === 'accept' ? 'accepted' : 'rejected'));
        $responsibilityText = $response === 'accept' 
            ? 'Customer has accepted responsibility for this rider without DL/LLR.' 
            : 'Customer has rejected the responsibility for this rider.';
    
        // Rider email
        if ($riderEmail) {
            $subject = "Terms & Conditions {$statusText} by Customer";
            $body = "
            <html>
            <body style='font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px; color:#544e54;'>
                <table width='100%' cellpadding='0' cellspacing='0' style='max-width:600px; margin:auto; background:#fff; border-radius:8px;'>
                    <tr>
                        <td style='padding:20px; text-align:center; background:#4CAF50; color:#fff; border-radius:8px 8px 0 0;'>
                            <h2>Terms & Conditions {$statusText}</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding:20px;'>
                            <p>Hello <strong>{$rider->name}</strong>,</p>
                            <p>{$responsibilityText}</p>
                            <p>{$footerContentText}</p>
                        </td>
                    </tr>
                </table>
            </body>
            </html>";
            CustomHandler::sendEmail($riderEmail, $subject, $body);
        }
    
        // Customer & Customer login email
        $customerRecipients = array_filter([$customerEmail, $customerLoginEmail]);
        if ($customerRecipients) {
            $subject = "You have {$statusText} Terms & Conditions for Rider {$rider->name}";
            $body = "
            <html>
            <body style='font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px; color:#544e54;'>
                <table width='100%' cellpadding='0' cellspacing='0' style='max-width:600px; margin:auto; background:#fff; border-radius:8px;'>
                    <tr>
                        <td style='padding:20px; text-align:center; background:#2196F3; color:#fff; border-radius:8px 8px 0 0;'>
                            <h2>Terms & Conditions {$statusText}</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding:20px;'>
                            <p>Hello <strong>{$customerName}</strong>,</p>
                            <p>You have <strong>{$statusText}</strong> the Terms & Conditions for the rider <strong>{$rider->name}</strong> who does not possess a Driving License or LLR.</p>
                            <p>{$footerContentText}</p>
                        </td>
                    </tr>
                </table>
            </body>
            </html>";
            CustomHandler::sendEmail($customerRecipients, $subject, $body);
        }
    
        // Admin email
        if (!empty($toAdmins)) {
            $subject = "Rider {$rider->name} Terms & Conditions {$statusText} by Customer";
            $body = "
            <html>
            <body style='font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px; color:#544e54;'>
                <table width='100%' cellpadding='0' cellspacing='0' style='max-width:600px; margin:auto; background:#fff; border-radius:8px;'>
                    <tr>
                        <td style='padding:20px; text-align:center; background:#8b8b8b; color:#fff; border-radius:8px 8px 0 0;'>
                            <h2>Rider Terms & Conditions {$statusText}</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding:20px;'>
                            <p>Rider <strong>{$rider->name}</strong> ({$riderPhone}) Terms & Conditions have been <strong>{$statusText}</strong> by customer <strong>{$customerName}</strong>.</p>
                            <p>{$footerContentText}</p>
                        </td>
                    </tr>
                </table>
            </body>
            </html>";
            CustomHandler::sendEmail($toAdmins, $subject, $body);
        }
    }
    
    public function recoveryRequest(Request $request)
    {
        if ($request->has('recovery_id')) {
                $requestId = decrypt($request->query('recovery_id'));
                // $requestId = $request->query('id');
                $recovery = B2BRecoveryRequest::with('rider','recovery_agent','assignment')->find($requestId);
        }

        return view('terms-condition.recovery', compact('recovery'));
    }
    
    public function closeRecoveryRequest(Request $request)
    {
        $recoveryId = decrypt($request->recovery_id); 

        if (!$recoveryId) {
            return response()->json([
                'status'  => false,
                'message' => 'Recovery ID is required.'
            ], 400);
        }

        $recovery = B2BRecoveryRequest::with('rider','recovery_agent','assignment')->find($recoveryId);

        if (!$recovery) {
            return response()->json([
                'status'  => false,
                'message' => 'Recovery request not found.'
            ], 404);
        }

        if ($recovery->status === 'closed' || $recovery->faq_id != 4) {
            return response()->json([
                'status'  => false,
                'message' => 'This recovery request is already closed.'
            ], 200);
        }

        // Update status
        $recovery->status = 'closed';
        $recovery->agent_status = 'closed';
        $recovery->closed_by = $recovery->created_by; 
        $recovery->closed_by_type = 'b2b-customer';
        $recovery->closed_at = now();
        $recovery->faq_id = null;
        if ($recovery->assignment) {
        $recovery->assignment->status = 'running';
        $recovery->assignment->save();
        }
        $recovery->save();
        
        B2BVehicleAssignmentLog::create([
            'assignment_id'   => $recovery->assign_id,
            'status'          => 'closed',
            'remarks'         => 'Closed by customer via email link',
            'action_by'       => $recovery->created_by ?? null,
            'type'            => 'b2b-customer' ,
            'request_type'    => 'recovery_request',
            'request_type_id' => $recovery->id,
            'location_lat'    => null,
            'location_lng'    => null
            ]);
        
        $remark = RecoveryComment::create([
            'req_id'    => $recovery->id,
            'status'    => 'closed',
            'comments'  => 'Closed by customer via email link',
            'user_id'   => $recovery->created_by ?? null,
            'user_type' => 'b2b-customer',
            'location_lat'    => null,
            'location_lng'    => null
        ]);
        Log::info("Recovery request #{$recovery->id} closed by customer.");
        
        $admins = User::whereIn('role', [1,13])
            ->where('status', 'Active')
            ->pluck('email')
            ->toArray();
        
        $manager = '';
    
        if($recovery->city_manager_id){
            $manager =  User::where('id',$recovery->city_manager_id)
                ->where('status', 'Active')
                ->pluck('email');
        }
        
        $customerEmail = $recovery->rider->customerLogin->customer_relation->email;
        $recipients = [
            [
                'to'  => $customerEmail,
                'cc'  => [$manager],
                'bcc' => $admins
            ]
        ];
        
        // $recipients = [
        //     [
        //         'to'  => 'logeshmudaliyar2802@gmail.com',
        //         'cc'  => ['mudaliyarlogesh@gmail.com'],
        //         'bcc' => array_merge(['pratheesh@alabtechnology.com'],['gowtham@alabtechnology.com'])
        //     ]
        // ];
        
        $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
        $footerContent = $footerText ?? "For any assistance, please reach out to Admin Support.<br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
        
        $faqSubject = 'Thank You – Your Recovery Request Has Been Closed (ID: #'.$recovery->assignment->req_id.')';
        $customer = $recovery->rider->customerLogin->customer_relation->trade_name ?? $recovery->client_name;
        $reasonList = [
            1 => 'Breakdown',
            2 => 'Battery Drain',
            3 => 'Accident',
            4 => 'Rider Unavailable',
            5 => 'Other',
        ];
        $reasonText = $reasonList[$recovery->reason ?? 0] ?? 'Unknown';
        $faqBody = '
        <html>
        <body style="font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px;">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:auto; background:#fff; border-radius:8px;">
                <tr>
                    <td style="padding:20px; text-align:center; background:#2196F3; color:#fff;">
                        <h2>Recovery Request Closed</h2>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px;">
                        <p>Dear <strong>'.$customer.'</strong>,</p>
                        <p>Thank you for confirming the closure of your recovery request <strong>#'.$recovery->assignment->req_id.'</strong>.</p>
                        <p>We appreciate your prompt response and cooperation throughout the recovery process.</p>
                        
                        <p><strong>Summary of Request</strong></p>
                        <table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; max-width: 600px;">
                            <tr style="background:#f2f2f2;">
                                <td><strong>Request Id</strong></td>
                                <td>'.($recovery->assignment->req_id ?? 'N/A').'</td>
                            </tr>
                            <tr>
                                <td><strong>Vehicle Number</strong></td>
                                <td>'.($recovery->vehicle_number ?? 'N/A').'</td>
                            </tr>
                            <tr style="background:#f2f2f2;">
                                <td><strong>Chassis Number</strong></td>
                                <td>'.($recovery->chassis_number ?? 'N/A').'</td>
                            </tr>
                            <tr>
                                <td><strong>Reason</strong></td>
                                <td>'.($reasonText ?? 'N/A').'</td>
                            </tr>
                            <tr style="background:#f2f2f2;">
                                <td><strong>Description </strong></td>
                                <td>'.($recovery->description ?? 'N/A').'</td>
                            </tr>
                            <tr >
                                <td><strong>Closed On</strong></td>
                                <td>'.now()->format('d M Y, h:i A').'</td>
                            </tr>
                        </table>

                        <p>'.$footerContent.'</p>
                    </td>
                </tr>
            </table>
        </body>
        </html>';
        
        // $this->sendDynamicEmailNotify($recipients,$faqSubject,$faqBody,false);
        $recoveryData = [
            'recovery_reason'=>$reasonText,
            'recovery_description'=>$recovery->description
            ];
        $this->AutoSendRecoveryRequestWhatsApp($recovery->assignment->req_id,$recovery->rider->id,$recovery,$recoveryData,$recovery->created_by_type);
        return response()->json([
            'status'  => true,
            'message' => 'The recovery request has been successfully closed.',
            // 'message' => $request->response === 'accept' 
            //             ? 'The recovery request has been successfully closed.' 
            //             : 'You have rejected to close recovery request.',
        ], 200);
    }
    
    public function sendDynamicEmailNotify(array $recipients, string $subject, string $body, bool $footer = false)
        {
            // Add footer content dynamically if needed
            if ($footer) {
                $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
                $footerContent = $footerText ?? "For any assistance, please reach out to Admin Support.<br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
                $body .= "<p style='margin-top:20px;'>{$footerContent}</p>";
            }
        
            foreach ($recipients as $recipient) {
                $to  = $recipient['to'] ?? null;
                $cc  = (array) ($recipient['cc'] ?? []);
                $bcc = (array) ($recipient['bcc'] ?? []);
        
                if (!empty($to)) {
                    CustomHandler::updatedSendEmail($to, $subject, $body, $cc, $bcc);
                }
            }
        
            return true;
        }
    public static function AutoSendRecoveryRequestWhatsApp($requestID, $rider_id, $recovery, $recoveryInfo, $tc_create_type)
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
        $vehicleData = $recovery->assignment->vehicle;
        $AssetvehicleId = $recovery->assignment->vehicle->id ?? 'N/A';
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
        $footerText = BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
            $footerContentText = $footerText ??
                "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
             $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.\n" .
                 "Email: {$customerEmail}\n" .
                 "Thank you,\n" .
                 "{$customerName}";
        $selectedfooterTextContent = ($tc_create_type == 'b2b-web-dashboard') ? $CustomerfooterContentText : $footerContentText;

 
        $customer_message = "Dear {$customerName},\n\n"
        . "*Thank you for confirming the closure of your recovery request <strong>#{$recovery->assignment->req_id}</strong>.*\n"
        . "We appreciate your prompt response and cooperation throughout the recovery process.\n\n"
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
        . "{$footerContentText}\n\n";
        $manager_message = "Dear Manager,\n\n"
        . "Customer {$customerName} has closed Recovery Request #{$service_TicketId}.\n"
        . "Kindly verify and confirm the closure on your dashboard.\n\n"
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
        . "{$CustomerfooterContentText}\n\n";
        $admin_message = "Dear Admin,\n\n"
        . "Customer {$customerName} has closed Recovery Request #{$service_TicketId}.\n"
        . "Kindly verify and confirm the closure on your dashboard.\n\n"
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
        . "{$CustomerfooterContentText}\n\n";

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
 
            // $managerphones = ['+917812880655','+919360992327'];
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
}


