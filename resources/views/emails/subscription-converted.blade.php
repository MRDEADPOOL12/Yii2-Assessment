<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trial Converted</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .plan-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .plan-name {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        .plan-price {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Trial Converted!</h1>
    </div>
    <div class="content">
        <p>Hi {{ $userName }},</p>
        
        <p>Great news! Your trial period has ended and your subscription has been successfully converted to a paid plan.</p>
        
        <div class="plan-details">
            <div class="plan-name">{{ $planName }}</div>
            <div class="plan-price">${{ number_format($planPrice, 2) }}<span style="font-size: 16px; color: #666;">/month</span></div>
        </div>
        
        <p><strong>What happens next?</strong></p>
        <ul>
            <li>Your subscription is now active</li>
            <li>You'll be billed monthly at ${{ number_format($planPrice, 2) }}</li>
            <li>Full access to all {{ $planName }} features</li>
        </ul>
        
        <a href="{{ url('/subscriptions/' . $subscription->id) }}" class="button">View Subscription Details</a>
        
        <p style="margin-top: 30px;">Thank you for choosing us!</p>
        
        <p>Best regards,<br>The Subscription Team</p>
    </div>
    <div class="footer">
        <p>If you have any questions, please contact our support team.</p>
        <p>&copy; {{ date('Y') }} Subscription Management. All rights reserved.</p>
    </div>
</body>
</html>