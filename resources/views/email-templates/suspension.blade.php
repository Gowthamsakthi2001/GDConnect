<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px; border-radius: 5px; }
        .header { background: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background: white; padding: 20px; }
        .footer { background: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 5px 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>GreenDriveConnect</h2>
        </div>
        <div class="content">
            @if($userType === 'dm')
                <h3>Dear {{ $employee['first_name'] }} {{ $employee['last_name'] }},</h3>
                <p>This is to inform you that your account has been temporarily suspended due to 3 consecutive days of inactivity.</p>
                <p>Please contact HR to reactivate your account.</p>
                <!--<p><strong>Support Helpline:</strong> [Company Phone Number]</p>-->
            @else
                <h3>Dear HR Team,</h3>
                <p>A delivery partner has been automatically suspended.</p>
                <table style="width:100%; border-collapse:collapse;">
                    <tr><td><strong>Name:</strong></td><td>{{ $employee['first_name'] }} {{ $employee['last_name'] }}</td></tr>
                    <tr><td><strong>Contact:</strong></td><td>{{ $employee['mobile_number'] }}</td></tr>
                    <tr><td><strong>Employee ID:</strong></td><td>{{ $employee['emp_id'] ?? 'N/A' }}</td></tr>
                    <tr><td><strong>Reason:</strong></td><td>3 days of inactivity</td></tr>
                </table>
            @endif
        </div>
         @if($userType === 'dm')
        <div class="footer">
            <p>Best regards,<br><strong>GreenDriveConnect Team</strong></p>
        </div>
        @else
        <div class="footer">
            <p>Best regards,<br><strong>GreenDriveConnect System</strong></p>
        </div>
        @endif
    </div>
</body>
</html>
