<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New User Registration</title>
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
        <p class="header">
            Hello {{ trim(($riderdata['first_name'] ?? '') . ' ' . ($riderdata['last_name'] ?? '')) ?: 'Rider' }},
        </p>
        <p>Thank you for registering with <strong>GREEN DRIVE CONNECT</strong>.</p>
        <p>We are currently reviewing your account. You will receive an update shortly regarding the status of your registration.</p>
        <p>Thank you and best regards,</p>
        <p><strong>GREEN DRIVE CONNECT</strong></p>
    </div>
</body>
</html>
