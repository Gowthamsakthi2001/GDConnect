<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title> Password Reset</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: Arial, sans-serif;
      line-height: 1.6;
      background-color: #f9fafb;
    }
    .email-container {
      max-width: 600px;
      margin: 40px auto;
      background: #ffffff;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      padding: 30px;
    }
    .header {
      border-bottom: 2px solid #f0f0f0;
      padding-bottom: 10px;
      margin-bottom: 20px;
      text-align: center;
    }
    .btn-reset {
      background-color: #171a42;
      color: #fff !important;
      font-weight: bold;
      padding: 12px 24px;
      border-radius: 6px;
      text-decoration: none;
      display: inline-block;
    }
    .footer {
      border-top: 1px solid #f0f0f0;
      font-size: 12px;
      color: #666;
      margin-top: 30px;
      padding-top: 15px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="email-container">
     
      <div class="header">
        <h2 class="mb-0">Green Drive Connect</h2>
      </div>

  
      <p class="fw-bold">Hello,</p>
      <p>You are receiving this email because we received a password reset request for your account.</p>

     
      <div class="text-center my-4">
        <a href="{{ $resetLink }}" class="btn-reset">Reset Password</a>
      </div>

      <p>This password reset link will expire in 60 minutes.</p>
      <p>If you did not request a password reset, no further action is required.</p>

    
      <p>Regards,<br>Green Drive Connect</p>
     
    </div>
  </div>
</body>
</html>
