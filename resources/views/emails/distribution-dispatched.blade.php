<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Distribution Dispatched</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3b82f6; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .tracking-box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center; }
        .tracking-number { font-size: 24px; font-weight: bold; color: #3b82f6; }
        .details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .detail-row { padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .detail-row:last-child { border-bottom: none; }
        .btn { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 20px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Your Package is On Its Way!</h1>
    </div>
    <div class="content">
        <p>Hi {{ $distribution->user->first_name }},</p>
        
        <p>Your food package has been dispatched and is on its way to you!</p>
        
        @if($distribution->tracking_number)
        <div class="tracking-box">
            <p><strong>Tracking Number:</strong></p>
            <div class="tracking-number">{{ $distribution->tracking_number }}</div>
            @if($distribution->courier_name)
            <p><strong>Courier:</strong> {{ $distribution->courier_name }}</p>
            @endif
        </div>
        @endif
        
        <div class="details">
            <div class="detail-row">
                <strong>Distribution Number:</strong> {{ $distribution->distribution_number }}
            </div>
            <div class="detail-row">
                <strong>Package Value:</strong> ₦{{ number_format($distribution->package_value, 2) }}
            </div>
            <div class="detail-row">
                <strong>Delivery Method:</strong> {{ ucfirst($distribution->delivery_method ?? 'delivery') }}
            </div>
        </div>
        
        <p>Track your delivery status in your dashboard.</p>
        
        <a href="{{ route('distributions.show', $distribution->id) }}" class="btn">Track Delivery</a>
        
        <p>Thank you for being a part of FoodChain!</p>
        
        <p>Best regards,<br>The FoodChain Team</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} FoodChain. All rights reserved.</p>
    </div>
</body>
</html>
