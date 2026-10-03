<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1f2937; background: #f3f4f6; font-size: 13px; }
        .invoice { max-width: 800px; margin: 20px auto; background: #fff; border-radius: 12px; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #059669; padding-bottom: 20px; }
        .logo { font-size: 22px; font-weight: 800; color: #059669; }
        .meta { text-align: right; }
        .meta h1 { font-size: 20px; color: #111827; }
        .meta p { color: #6b7280; margin-top: 2px; }
        .billing { display: flex; justify-content: space-between; margin: 24px 0; }
        .box h3 { font-size: 12px; text-transform: uppercase; color: #059669; margin-bottom: 6px; }
        .box p { color: #374151; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        thead th { background: #ecfdf5; color: #065f46; text-align: left; padding: 10px; font-size: 11px; text-transform: uppercase; }
        tbody td { padding: 12px 10px; border-bottom: 1px solid #f3f4f6; }
        .num { text-align: right; }
        .totals { margin-left: auto; width: 300px; }
        .totals div { display: flex; justify-content: space-between; padding: 6px 0; }
        .totals .grand { font-weight: 800; font-size: 16px; color: #059669; border-top: 2px solid #059669; margin-top: 8px; padding-top: 10px; }
        .footer { margin-top: 30px; text-align: center; color: #9ca3af; font-size: 11px; }
        .status { display: inline-block; padding: 3px 12px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .status-paid { background: #d1fae5; color: #047857; }
        .status-unpaid { background: #fef3c7; color: #b45309; }
        @media print {
            body { background: #fff; }
            .invoice { margin: 0; border-radius: 0; padding: 24px; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="invoice">
        <div class="header">
            <div>
                <div class="logo">{{ app_name() }} <span style="color:#d97706;">Marketplace</span></div>
                <p style="color:#6b7280; margin-top:4px;">Invoice for online food marketplace purchase</p>
            </div>
            <div class="meta">
                <h1>INVOICE</h1>
                <p>{{ $order->created_at->format('d M Y') }}</p>
                <p><strong>{{ $order->order_number }}</strong></p>
                <div style="margin-top:6px;">
                    <span class="status {{ $order->payment_status === 'paid' ? 'status-paid' : 'status-unpaid' }}">{{ strtoupper(str_replace('_', ' ', $order->payment_status)) }}</span>
                </div>
            </div>
        </div>

        <div class="billing">
            <div class="box">
                <h3>Billed To</h3>
                <p><strong>{{ $order->user->name ?? 'Customer' }}</strong><br>
                   {{ $order->user->email ?? '' }}<br>
                   {{ $order->user->phone ?? '' }}</p>
            </div>
            <div class="box">
                <h3>Delivery</h3>
                <p>{{ ucwords(str_replace('_', ' ', $order->delivery_type)) }}<br>
                   @if ($order->delivery_address){{ $order->delivery_address }}<br>@endif
                   @if ($order->delivery_city){{ $order->delivery_city }}, @endif{{ $order->delivery_state ?? '' }}<br>
                   @if ($order->pickup_location)Pickup: {{ $order->pickup_location }}@endif
                   @if ($order->estimated_delivery_date)Est: {{ $order->estimated_delivery_date->format('d M Y') }}@endif
                </p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>SKU</th>
                    <th class="num">Qty</th>
                    <th class="num">Unit Price</th>
                    <th class="num">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td style="color:#6b7280;">{{ $item->product_sku }}</td>
                        <td class="num">{{ $item->quantity }}</td>
                        <td class="num">₦{{ number_format($item->unit_price, 2) }}</td>
                        <td class="num">₦{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div><span>Subtotal</span><span>₦{{ number_format($order->subtotal, 2) }}</span></div>
            @if ($order->coupon_discount > 0)
                <div><span>Coupon ({{ $order->coupon_code }})</span><span>-₦{{ number_format($order->coupon_discount, 2) }}</span></div>
            @endif
            @if ($order->discount_amount > 0)
                <div><span>Discount</span><span>-₦{{ number_format($order->discount_amount, 2) }}</span></div>
            @endif
            <div><span>Delivery</span><span>₦{{ number_format($order->shipping_fee, 2) }}</span></div>
            <div class="grand"><span>Total</span><span>₦{{ number_format($order->total, 2) }}</span></div>

            @if ($order->wallet_amount > 0)
                <div style="font-size:12px; color:#6b7280;"><span>Paid from wallet</span><span>₦{{ number_format($order->wallet_amount, 2) }}</span></div>
            @endif
            @if ($order->cash_amount > 0)
                <div style="font-size:12px; color:#b45309;"><span>Payable in cash</span><span>₦{{ number_format($order->cash_amount, 2) }}</span></div>
            @endif
            @if ($order->refund_amount > 0)
                <div style="font-size:12px; color:#6b7280;"><span>Refunded</span><span>₦{{ number_format($order->refund_amount, 2) }}</span></div>
            @endif

            <div style="font-size:11px; color:#6b7280; border-top:1px solid #e5e7eb; margin-top:6px; padding-top:6px;">
                <span>Payment method</span>
                <span>{{ \App\Models\Marketplace\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</span>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for shopping with {{ app_name() }} Marketplace!</p>
            <p style="margin-top:4px;">For support contact: {{ setting('support_email') ?: 'support@foodchain.ng' }}</p>
        </div>
    </div>
</body>
</html>
