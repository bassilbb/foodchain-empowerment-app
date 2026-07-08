<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tier Upgrade</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .tier-badge { display: inline-block; background: #f59e0b; color: white; padding: 8px 20px; border-radius: 20px; font-weight: bold; font-size: 18px; margin: 10px 0; }
        .benefits { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .benefit-item { padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .benefit-item:last-child { border-bottom: none; }
        .btn { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 20px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Congratulations!</h1>
        <p>You've been upgraded to a new tier!</p>
    </div>
    <div class="content">
        <p>Hi {{ $user->first_name }},</p>
        
        <p>Amazing news! You've reached <strong>{{ $newTier->name }}</strong> tier!</p>
        
        <div style="text-align: center;">
            <div class="tier-badge">{{ $newTier->name }}</div>
        </div>
        
        <p>With {{ $user->direct_referrals_count }} referrals, you now qualify for enhanced benefits:</p>
        
        <div class="benefits">
            @if($newTier->annual_fee == 0)
            <div class="benefit-item">✓ Free Annual Membership</div>
            @endif
            <div class="benefit-item">✓ Food Package Value: ₦{{ number_format($newTier->food_package_value) }}</div>
            @if($newTier->has_delivery)
            <div class="benefit-item">✓ Delivery Available</div>
            @if($newTier->free_delivery)
            <div class="benefit-item">✓ Free Delivery</div>
            @endif
            @endif
            @if($newTier->community_min_members > 0)
            <div class="benefit-item">✓ Community Range: {{ $newTier->community_min_members }} - {{ $newTier->community_max_members }} members</div>
            <div class="benefit-item">✓ Free Food Pack Type: {{ ucfirst($newTier->free_food_pack_type) }}</div>
            @endif
        </div>
        
        <a href="{{ route('dashboard') }}" class="btn">View Your Benefits</a>
        
        <p>Keep referring more members to unlock even higher tiers!</p>
        
        <p>Best regards,<br>The FoodChain Team</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} FoodChain. All rights reserved.</p>
    </div>
</body>
</html>
