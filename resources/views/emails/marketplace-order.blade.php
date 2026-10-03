<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subjectText }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10b981; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .order-number { font-size: 22px; font-weight: bold; text-align: center; margin: 10px 0 5px; }
        .amount { font-size: 32px; font-weight: bold; color: #10b981; text-align: center; margin: 10px 0 20px; }
        .details, .items { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .detail-row:last-child { border-bottom: none; }
        .item-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .item-row:last-child { border-bottom: none; }
        .item-name { flex: 1; padding-right: 10px; }
        .status { display: inline-block; padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; background: #d1fae5; color: #047857; }
        .btn { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 20px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>FoodChain Marketplace</h1>
    </div>
    <div class="content">
        <p>Hi {{ $order->user?->first_name ?? $order->user?->name ?? 'there' }},</p>

        <p>{{ $subjectText }}</p>

        <div class="order-number">Order {{ $order->order_number }}</div>
        <div class="amount">₦{{ number_format($order->total, 2) }}</div>

        <div style="text-align:center; margin-bottom: 10px;">
            <span class="status">{{ $order->status }}</span>
        </div>

        <div class="details">
            <div class="detail-row">
                <span><strong>Payment Method</strong></span>
                <span>{{ \App\Models\Marketplace\Order::PAYMENT_METHODS[$order->payment_method] ?? ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
            </div>
            <div class="detail-row">
                <span><strong>Delivery Type</strong></span>
                <span>{{ ucfirst(str_replace('_', ' ', $order->delivery_type)) }}</span>
            </div>
            @if ($order->delivery_type === 'delivery')
                <div class="detail-row">
                    <span><strong>Deliver To</strong></span>
                    <span>{{ $order->delivery_address }}, {{ $order->delivery_city }}, {{ $order->delivery_state }}</span>
                </div>
            @elseif ($order->delivery_type === 'pickup')
                <div class="detail-row">
                    <span><strong>Pickup Location</strong></span>
                    <span>{{ $order->pickup_location }}</span>
                </div>
            @endif
            @if ($order->estimated_delivery_date)
                <div class="detail-row">
                    <span><strong>Estimated Delivery</strong></span>
                    <span>{{ $order->estimated_delivery_date->format('M d, Y') }}</span>
                </div>
            @endif
            <div class="detail-row">
                <span><strong>Order Date</strong></span>
                <span>{{ $order->created_at->format('M d, Y h:i A') }}</span>
            </div>
        </div>

        @if ($order->items->count())
            <div class="items">
                <h3 style="margin-top:0;">Items</h3>
                @foreach ($order->items as $item)
                    <div class="item-row">
                        <span class="item-name">{{ $item->product_name }} <span style="color:#6b7280;">x{{ $item->quantity }}</span></span>
                        <span>₦{{ number_format($item->total_price, 2) }}</span>
                    </div>
                @endforeach
                <div class="detail-row">
                    <span><strong>Subtotal</strong></span>
                    <span>₦{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if ((float) $order->discount_amount > 0)
                    <div class="detail-row">
                        <span><strong>Discount</strong></span>
                        <span style="color:#059669;">-₦{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="detail-row">
                    <span><strong>Shipping</strong></span>
                    <span>₦{{ number_format($order->shipping_fee, 2) }}</span>
                </div>
                <div class="detail-row" style="border-top: 2px solid #e5e7eb; font-size: 16px;">
                    <span><strong>Total</strong></span>
                    <span><strong>₦{{ number_format($order->total, 2) }}</strong></span>
                </div>
            </div>
        @endif

        <div style="text-align:center;">
            <a href="{{ route('marketplace.orders.show', $order) }}" class="btn">View Your Order</a>
        </div>

        <p>Thank you for shopping with FoodChain!</p>

        <p>Best regards,<br>The FoodChain Team</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} FoodChain. All rights reserved.</p>
    </div>
</body>
</html>
