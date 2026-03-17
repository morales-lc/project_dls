<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Verification Code</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .email-header {
            background: linear-gradient(135deg, #6c63ff, #554eea);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
            color: #333333;
            line-height: 1.6;
        }
        .email-body p {
            margin: 0 0 20px 0;
            font-size: 16px;
        }
        .otp-box {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 2px dashed #6c63ff;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 700;
            color: #6c63ff;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .otp-label {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }
        .email-footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🔐 Login Verification</h1>
        </div>
        <div class="email-body">
            <p>Hello <strong>{{ $userName }}</strong>,</p>
            <p>We received a login attempt for your Lourdes College Learning Commons account. To complete your login, please use the verification code below:</p>
            
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-label">Enter this code to verify your login</div>
            </div>

            <p>This code will expire in <strong>5 minutes</strong> for security purposes.</p>

            <div class="warning">
                <p><strong>⚠️ Security Notice:</strong> If you did not attempt to log in, please ignore this email and consider changing your password immediately.</p>
            </div>

            <p>For your security, never share this code with anyone. Our team will never ask for your verification code.</p>
        </div>
        <div class="email-footer">
            <p><strong>Lourdes College Learning Commons</strong></p>
            <p>This is an automated email. Please do not reply.</p>
        </div>
    </div>
</body>
</html>
