<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Referral</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #8b5cf6; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .referral-card { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center; }
        .referral-name { font-size: 20px; font-weight: bold; color: #333; }
        .stats { display: flex; justify-content: center; gap: 30px; margin: 20px 0; }
        .stat { text-align: center; }
        .stat-number { font-size: 32px; font-weight: bold; color: #8b5cf6; }
        .stat-label { font-size: 12px; color: #6b7280; }
        .btn { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 20px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Referral!</h1>
    </div>
    <div class="content">
        <p>Hi {{ $referrer->first_name }},</p>
        
        <p>Great news! Someone just joined using your referral code!</p>
        
        <div class="referral-card">
            <div class="referral-name">{{ $referred->full_name }}</div>
            <p>{{ $referred->email }}</p>
            <p>Joined: {{ $referred->created_at->format('M d, Y') }}</p>
        </div>
        
        <div class="stats">
            <div class="stat">
                <div class="stat-number">{{ $referrer->direct_referrals_count }}</div>
                <div class="stat-label">Direct Referrals</div>
            </div>
            <div class="stat">
                <div class="stat-number">{{ $referrer->total_community_count ?? 0 }}</div>
                <div class="stat-label">Total Community</div>
            </div>
        </div>
        
        <p>Keep sharing your referral code to grow your community and unlock more benefits!</p>
        
        <a href="{{ route('referrals.index') }}" class="btn">View Referrals</a>
        
        <p>Best regards,<br>The FoodChain Team</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} FoodChain. All rights reserved.</p>
    </div>
</body>
</html>
