<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Rider Registration</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h2>New Rider Registration</h2>
        <p><strong>Reg Application No:</strong> {{ $riderdata['reg_application_id'] ?? 'N/A' }}</p>
        <p><strong>Name:</strong> {{ ($riderdata['first_name'] ?? '') . ' ' . ($riderdata['last_name'] ?? '') }}</p>
        <p><strong>Email:</strong> {{ $riderdata->email ?? 'N/A' }}</p>
        <p><strong>Phone:</strong> {{ $riderdata->mobile_number ?? 'N/A' }}</p>
        <p><strong>City:</strong> {{ $riderdata->current_city->city_name ?? 'N/A' }}</p>
        <p>A new rider has registered and submitted KYC. Please review their account in the admin panel.</p>
        <br>
        <p>Thanks,<br><strong>GREEN DRIVE CONNECT</strong></p>
    </div>
</body>
</html>
