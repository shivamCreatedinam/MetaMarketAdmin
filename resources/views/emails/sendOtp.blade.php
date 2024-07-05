<!DOCTYPE html>
<html>
<head>
    <title>Email OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #cc872a;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            line-height: 1.6;
        }
        .content h1 {
            color: #333333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .content p {
            color: #555555;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .otp {
            font-size: 20px;
            font-weight: bold;
            color: #333333;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            background-color: #cc872a;
            color: #ffffff;
            text-align: center;
            padding: 10px;
            font-size: 12px;
        }
        @media only screen and (max-width: 600px) {
            .container {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>OTP Verification</h1>
        </div>
        <div class="content">
            <h1>Hello, {{ $data['name'] }}!</h1>
            <p>Thank you for registering with us. Please use the following OTP codes to verify your account:</p>
            <div class="otp">
                <p>Mobile OTP: {{ $data['mobile_otp'] }}</p>
                <p>Email OTP: {{ $data['email_otp'] }}</p>
            </div>
            <p>These codes will expire at {{ $data['expire_at'] }}.</p>
            <p>If you did not request these OTP codes, please ignore this email.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Meta Market. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
