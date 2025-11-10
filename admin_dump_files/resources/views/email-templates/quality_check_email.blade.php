<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quality Check Created</title>
    <style>
        body {
            background-color: #f4f4f4;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            max-width: 600px;
            margin: 40px auto;
            padding: 30px 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            border-top: 4px solid #2E7D32;
        }
        p {
            font-size: 16px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            font-size: 20px;
            color: #2E7D32;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">QC Created Successfully</div>

        <p>Hello {{ $qcData['technician_name'] ?? 'Technician' }},</p>

        <p>Your quality check has been created successfully.</p>

        <p><strong>QC ID:</strong> {{ $qcData['id'] }}</p>
        <p><strong>Vehicle Model:</strong> {{ $qcData['vehicle_model'] }}</p>
        <p><strong>Status:</strong> {{ ucfirst($qcData['status']) }}</p>
        <p><strong>Date & Time:</strong> {{ \Carbon\Carbon::parse($qcData['datetime'])->format('d M Y, h:i A') }}</p>

        <p>Regards,<br><strong>Green Drive Connect</strong></p>
    </div>
</body>
</html>
