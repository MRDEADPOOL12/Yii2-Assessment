<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Cancelled</title>
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
            background: #e53e3e;
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
            border-left: 4px solid #e53e3e;
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
        <h1>Subscription Cancelled</h1>
    </div>
    <div class="content">
        <p>Hi {{ $userName }},</p>
        
        <p>We're sorry to see you go. Your subscription has been successfully cancelled.</p>
        
        <div class="plan-details">
            <h3 style="margin-top: 0;">Cancelled Plan</h3>
            <p><strong>{{ $planName }}</strong></p>
            <p>Cancelled on: {{ $subscription->ended_at?->format('M d, Y g:i A') }}</p>
        </div>
        
        <p><strong>What this means:</strong></p>
        <ul>
            <li>Your subscription is now inactive</li>
            <li>No further charges will be applied</li>
            <li>You can reactivate anytime</li>
        </ul>
        
        <p>We'd love to have you back! If you change your mind, you can create a new subscription at any time.</p>
        
        <a href="{{ url('/subscriptions/create') }}" class="button">Reactivate Subscription</a>
        
        <p style="margin-top: 30px;">Thank you for being with us!</p>
        
        <p>Best regards,<br>The Subscription Team</p>
    </div>
    <div class="footer">
        <p>We value your feedback. Please let us know how we can improve.</p>
        <p>&copy; {{ date('Y') }} Subscription Management. All rights reserved.</p>
    </div>
</body>
</html>