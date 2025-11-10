<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Vehicle Request</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f5f5f5; padding:20px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:auto;background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">

        <!-- Header -->
        <tr>
            <td style="background-color:#28a745; color:#ffffff; text-align:center; padding:20px; font-size:20px; font-weight:bold;">
                New Vehicle Request Created
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding:20px; color:#333333; font-size:16px; line-height:1.6;">

                {{-- Conditional Greetings --}}
                @if($mail_type === 'admin')
                    <p>Dear Admin,</p>
                    <p>A new <strong>Vehicle Request</strong> has been created by customer <strong>{{ $user->customer_relation->trade_name ?? '' }}</strong>. Please review the details below:</p>
                @elseif($mail_type === 'agent')
                    <p>Dear Agent,</p>
                    <p>A new <strong>Vehicle Request</strong> has been created under your assigned customer <strong>{{ $user->customer_relation->trade_name ?? '' }}</strong>. Rider details are as follows:</p>
                @else
                    <p>Dear {{ $user->customer_relation->trade_name ?? '' }},</p>
                    <p>You have successfully created a <strong>Vehicle Request</strong> for the rider mentioned below:</p>
                @endif

                <!-- Vehicle Request Details Table -->
                <table cellpadding="8" cellspacing="0" style="width:100%; border:1px solid #e0e0e0; margin:15px 0; border-radius:5px;">
                    <tr style="background:#f1f1f1;">
                        <td><strong>Request ID:</strong></td>
                        <td>{{ $vehicleRequest->req_id }}</td>
                    </tr>
                    <tr >
                        <td><strong>Rider Name:</strong></td>
                        <td>{{ $rider->name ?? 'N/A' }}</td>
                    </tr>
                    <tr style="background:#f1f1f1;">
                        <td><strong>Rider Mobile Number:</strong></td>
                        <td>{{ $rider->mobile_no ?? 'N/A' }}</td>
                    </tr>
                     <tr>
                        <td><strong>City:</strong></td>
                        <td>{{ $rider->city->city_name ?? '' }}</td>
                    </tr>
                    
                    <tr style="background:#f1f1f1;">
                        <td><strong>Zone:</strong></td>
                        <td>{{ $rider->zone->name ?? '' }}</td>
                    </tr>
                    
                    <tr>
                        <td><strong>Customer:</strong></td>
                        <td>{{ $user->customer_relation->trade_name ?? '' }} ({{ $user->customer_relation->email ?? '' }})</td>
                    </tr>
                </table>

                {{-- Conditional Closing --}}
                @if($mail_type === 'admin' || $mail_type === 'agent')
                    <p style="margin-top:20px;">Please review this request in your dashboard and take necessary action.</p>
                    <p>Regards,<br/>
                    <strong style="color:#28a745;">{{ $user->customer_relation->trade_name ?? '' }}</strong></p>
                @else
                    <p style="margin-top:20px;">Thank you for submitting the Vehicle Request. We will process it shortly.</p>
                    <p>Regards,<br/>
                    <strong style="color:#28a745;">Green Drive Mobility Team</strong></p>
                @endif

            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background:#f5f5f5; text-align:center; padding:15px; font-size:12px; color:#888888;">
                &copy; {{ date('Y') }} Green Drive Mobility. All rights reserved.
            </td>
        </tr>
    </table>
</body>
</html>
