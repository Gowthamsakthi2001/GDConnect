<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject ?? 'Application Status Update' }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 15px; text-align: center; }
        .status { 
            padding: 5px 10px; 
            border-radius: 4px; 
            font-weight: bold;
            color: white;
        }
        .approved { background-color: #28a745; }
        .bgv { background-color: #ffc107; color: #212529; }
        .hold { background-color: #17a2b8; }
        .rejected { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Application Status Update</h2>
        </div>
        
        <div class="content">
            <p>Hello,</p>
            
            <p>The status of application #{{ $application->id }} has been updated:</p>
            
            <table>
                <tr>
                    <td><strong>Candidate:</strong></td>
                    <td>{{ $application->first_name }} {{ $application->last_name }}</td>
                </tr>
                <tr>
                    <td><strong>Status:</strong></td>
                    <td>
                        <span class="status 
                            @if($status == 'approve_sent_to_hr02') approved
                            @elseif($status == 'sent_to_bgv') bgv
                            @elseif($status == 'on_hold') hold
                            @elseif($status == 'rejected') rejected
                            @endif">
                            {{ ucwords(str_replace('_', ' ', $status)) }}
                        </span>
                    </td>
                </tr>
                @if($remarks)
                <tr>
                    <td><strong>Remarks:</strong></td>
                    <td>{{ $remarks }}</td>
                </tr>
                @endif
                <tr>
                    <td><strong>Action By:</strong></td>
                    <td>{{ $actionBy }}</td>
                </tr>
            </table>
            
            <p>Thank you,<br>HR Team</p>
        </div>
    </div>
</body>
</html>