<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Membership Expiring</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #ef4444; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .alert-box { background: #fef2f2; border-left: 4px solid #ef4444; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .days-remaining { font-size: 48px; font-weight: bold; color: #ef4444; text-align: center; margin: 20px 0; }
        .btn { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 20px; }
        .btn-secondary { background: #6b7280; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Membership Expiring Soon</h1>
    </div>
    <div class="content">
        <p>Hi {{ $user->first_name }},</p>
        
        <div class="alert-box">
            <p><strong>Your FoodChain membership will expire in:</strong></p>
            <div class="days-remaining">{{ $daysRemaining }} {{ $daysRemaining == 1 ? 'Day' : 'Days' }}</div>
        </div>
        
        <p>Your current membership expires on <strong>{{ $user->membership_expires_at->format('M d, Y') }}</strong>.</p>
        
        <p>Don't lose access to your benefits! Renew now to continue enjoying:</p>
        <ul>
            <li>Your food package entitlements</li>
            <li>Referral rewards and commissions</li>
            <li>Union and loan benefits</li>
            <li>Delivery discounts</li>
        </ul>
        
        <a href="{{ route('membership.renew') }}" class="btn">Renew Now</a>
        
        <p>If you have any questions or need assistance, please contact our support team.</p>
        
        <p>Best regards,<br>The FoodChain Team</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} FoodChain. All rights reserved.</p>
    </div>
</body>
</html>
