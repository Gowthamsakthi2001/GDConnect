<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Terms & Conditions Accepted</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                       style="background-color: #ffffff; border-radius: 8px; overflow: hidden; margin-top: 30px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color: #28a745; color: #ffffff; text-align: center; padding: 20px; font-size: 22px; font-weight: bold;">
                            Terms & Conditions Accepted
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 20px; color: #333333; font-size: 16px; line-height: 1.5;">

                            {{-- Conditional Greetings --}}
                            @if($mail_type === 'admin')
                                <p>Dear Admin,</p>
                                <p>The <strong>Terms & Conditions</strong> for the rider mentioned below have been accepted by the customer <strong>{{ $user->customer_relation->trade_name ?? '' }}</strong>.</p>
                            @elseif($mail_type === 'agent')
                                <p>Dear Agent,</p>
                                <p>One of the riders under your assigned customer <strong>{{ $user->customer_relation->trade_name ?? '' }}</strong> has had their <strong>Terms & Conditions</strong> accepted by the customer.</p>
                            @else
                                <p>Dear {{ $user->customer_relation->trade_name ?? '' }},</p>
                                <p>You have successfully accepted the <strong>Terms & Conditions</strong> on behalf of your rider mentioned below:</p>
                            @endif

                            <!-- Rider Details Table -->
                            <table cellpadding="8" cellspacing="0" style="width: 100%; border: 1px solid #e0e0e0; border-radius: 5px; margin-top: 10px;">
                                <tr style="background-color: #e8f5e9;">
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $rider->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Mobile:</strong></td>
                                    <td>{{ $rider->mobile_no }}</td>
                                </tr>
                                <tr style="background-color: #e8f5e9;">
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $rider->email ?? 'N/A' }}</td>
                                </tr>
                                
                                 <tr style="background-color: #e8f5e9;">
                                    <td><strong>City:</strong></td>
                                    <td>{{ $rider->city->city_name ?? 'N/A' }}</td>
                                </tr>
                                
                              <tr style="background-color: #e8f5e9;">
                                    <td><strong>Zone:</strong></td>
                                    <td>{{ $rider->zone->name ?? 'N/A' }}</td>
                                </tr>
                            </table>

                                <p style="margin-top: 20px; color: #d9534f;">
                                    This rider does not have a Driving Licence and LLR, hence Terms & Conditions were accepted.
                                </p>
 

                            @if($mail_type === 'admin' || $mail_type === 'agent')
                                <p>Regards,<br/>
                                <strong style="color: #28a745;">{{ $user->customer_relation->trade_name ?? '' }}</strong></p>
                            @else
                                <p>Regards,<br/>
                                <strong style="color: #28a745;">Green Drive Mobility</strong></p>
                            @endif
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f1f1f1; text-align: center; padding: 15px; font-size: 12px; color: #888888;">
                            &copy; {{ date('Y') }} Green Drive Mobility. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
