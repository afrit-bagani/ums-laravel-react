<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - UMS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-top: 40px;
            margin-bottom: 40px;
        }
        .header {
            background-color: #1a365d;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
            color: #333333;
            line-height: 1.6;
        }
        .content h2 {
            color: #1a365d;
            font-size: 20px;
            margin-top: 0;
        }
        .credentials-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .credentials-box p {
            margin: 5px 0;
            font-size: 16px;
        }
        .credentials-box strong {
            color: #1a365d;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .login-button {
            background-color: #e53e3e;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 20px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="email-container">
    <div class="header">
        <h1>UMS Password Reset</h1>
    </div>
    
    <div class="content">
        <h2>Hello, {{ $name }}!</h2>
        
        <p>We received a request to reset the password for your account.</p>
        
        <p>Your 6-digit One Time Password (OTP) has been generated for you. <strong>For your security, this code will expire in 15 minutes.</strong></p>

        <div class="credentials-box">
            <p><strong>Login ID:</strong> {{ $loginIdentifier }}</p>
            <p style="font-size: 24px; text-align: center; font-weight: bold; margin: 15px 0;"><strong>{{ $otp }}</strong></p>
        </div>

        <div class="button-container">
            <p>Please return to the application and enter this code to reset your password.</p>
        </div>
        
        <p>If you did not request a password reset, please contact the administration immediately.</p>
        
        <p>Best regards,<br>
        <strong>Admin Team</strong></p>
    </div>
    
    <div class="footer">
        &copy; {{ date('Y') }} UMS. All rights reserved.
    </div>
</div>

</body>
</html>
