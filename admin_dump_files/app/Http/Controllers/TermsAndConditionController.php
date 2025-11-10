<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Modules\B2B\Entities\B2BRider;//updated by Mugesh.B
use App\Helpers\CustomHandler;
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

    
}


