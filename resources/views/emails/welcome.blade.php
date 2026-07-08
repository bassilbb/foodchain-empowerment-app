<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to FoodChain</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10b981; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .referral-box { background: white; border: 2px dashed #10b981; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px; }
        .referral-code { font-size: 24px; font-weight: bold; color: #10b981; letter-spacing: 2px; }
        .btn { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 20px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to FoodChain Community!</h1>
    </div>
    <div class="content">
        <p>Hi {{ $user->first_name }},</p>
        
        <p>Welcome to FoodChain! We're thrilled to have you as part of our community. Together, we're building a network that provides sustainable food access and financial growth for everyone.</p>
        
        <div class="referral-box">
            <p>Your Referral Code:</p>
            <div class="referral-code">{{ $user->referral_code }}</div>
            <p>Share this code with friends and family to grow your community!</p>
        </div>
        
        <p><strong>What you can do:</strong></p>
        <ul>
            <li>Refer friends and earn rewards</li>
            <li>Get access to food packages based on your membership tier</li>
            <li>Join or create a union with other members</li>
            <li>Apply for food loans through your union</li>
        </ul>
        
        <a href="{{ route('dashboard') }}" class="btn">Go to Dashboard</a>
        
        <p>If you have any questions, feel free to reach out to our support team.</p>
        
        <p>Best regards,<br>The FoodChain Team</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} FoodChain. All rights reserved.</p>
    </div>
</body>
</html>
