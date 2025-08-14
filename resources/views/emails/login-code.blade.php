<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Login Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #4f46e5;
        }
        .code {
            text-align: center;
            font-size: 48px;
            font-weight: bold;
            color: #4f46e5;
            padding: 30px;
            margin: 20px 0;
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            letter-spacing: 8px;
        }
        .content {
            padding: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 14px;
        }
        .warning {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Your Login Code</h1>
    </div>
    
    <div class="content">
        <p>Here is your 4-digit login code:</p>
        
        <div class="code">{{ $code }}</div>
        
        <p>Enter this code on the login page to access your account.</p>
        
        <p>This code will expire in {{ config('magicpass.expiry', 15) }} minutes.</p>
        
        <div class="warning">
            <strong>Security Notice:</strong> If you did not request this code, please ignore this email and do not share this code with anyone.
        </div>
    </div>
    
    <div class="footer">
        <p>This is an automated message from {{ config('app.name', 'Laravel') }}</p>
        <p>Please do not reply to this email.</p>
    </div>
</body>
</html> 