<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Successful</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10b981; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .amount { font-size: 32px; font-weight: bold; color: #10b981; text-align: center; margin: 20px 0; }
        .details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .detail-row:last-child { border-bottom: none; }
        .btn { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 20px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Successful!</h1>
    </div>
    <div class="content">
        <p>Hi {{ $user->first_name }},</p>
        
        <p>Great news! Your payment has been processed successfully.</p>
        
        <div class="amount">₦{{ number_format($payment->amount, 2) }}</div>
        
        <div class="details">
            <div class="detail-row">
                <span><strong>Payment Type</strong></span>
                <span>{{ $payment->type === 'membership' ? 'Membership' : 'Starter Pack' }}</span>
            </div>
            <div class="detail-row">
                <span><strong>Reference</strong></span>
                <span>{{ $payment->reference }}</span>
            </div>
            <div class="detail-row">
                <span><strong>Date</strong></span>
                <span>{{ $payment->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div class="detail-row">
                <span><strong>Status</strong></span>
                <span style="color: #10b981; font-weight: bold;">{{ ucfirst($payment->status) }}</span>
            </div>
        </div>
        
        <a href="{{ route('payments.index') }}" class="btn">View Payment History</a>
        
        <p>Thank you for your continued support!</p>
        
        <p>Best regards,<br>The FoodChain Team</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} FoodChain. All rights reserved.</p>
    </div>
</body>
</html>
