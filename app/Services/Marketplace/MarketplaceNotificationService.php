<?php

namespace App\Services\Marketplace;

use App\Mail\MarketplaceOrderMail;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\BulkOrder;
use App\Models\Notification;
use App\Models\User;
use App\Models\Marketplace\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MarketplaceNotificationService
{
    private bool $emailEnabled = true;

    public function orderPlaced(Order $order): void
    {
        $this->notify($order->user, 'order_placed', 'Order Placed Successfully',
            'Your order '.$order->order_number.' of ₦'.number_format($order->total, 2).' has been placed.',
            route('marketplace.orders.show', $order->id), $order);

        $this->mail($order->user, $order, 'Order Confirmation');
    }

    public function paymentSuccessful(Order $order): void
    {
        $this->notify($order->user, 'payment_successful', 'Payment Successful',
            'Payment of ₦'.number_format($order->total, 2).' for order '.$order->order_number.' was successful.',
            route('marketplace.orders.show', $order->id), $order);
    }

    public function orderStatusChanged(Order $order, string $status): void
    {
        $titles = [
            'confirmed' => 'Order Confirmed',
            'processing' => 'Order Processing',
            'packed' => 'Order Packed',
            'shipped' => 'Order Shipped',
            'delivered' => 'Order Delivered',
            'cancelled' => 'Order Cancelled',
            'refunded' => 'Order Refunded',
        ];

        $title = $titles[$status] ?? 'Order Update';

        $this->notify($order->user, 'order_'.$status, $title,
            'Your order '.$order->order_number.' is now '.strtolower($title).'.',
            route('marketplace.orders.show', $order->id), $order);

        if (in_array($status, ['shipped', 'delivered', 'cancelled', 'refunded'])) {
            $this->mail($order->user, $order, $title);
        }
    }

    public function orderShipped(Order $order): void
    {
        $this->orderStatusChanged($order, 'shipped');
    }

    public function orderDelivered(Order $order): void
    {
        $this->orderStatusChanged($order, 'delivered');
    }

    public function orderCancelled(Order $order): void
    {
        $this->orderStatusChanged($order, 'cancelled');
    }

    public function refundApproved(Order $order): void
    {
        $this->notify($order->user, 'refund_approved', 'Refund Approved',
            'Your refund of ₦'.number_format($order->refund_amount, 2).' for order '.$order->order_number.' has been approved.',
            route('marketplace.orders.show', $order->id), $order);
    }

    public function walletCredited(User $user, float $amount, string $description): void
    {
        $this->notify($user, 'wallet_credited', 'Wallet Credited',
            'Your wallet was credited with ₦'.number_format($amount, 2).'. '.$description,
            route('marketplace.wallet.index'), null);
    }

    public function returnRequested(Order $order): void
    {
        $this->notify($order->user, 'return_requested', 'Return Request Submitted',
            'Your return request for order '.$order->order_number.' has been submitted. We will review it shortly.',
            route('marketplace.orders.show', $order->id), $order);
    }

    public function lowStockAlert(Product $product): void
    {
        $vendors = $product->vendor_id ? collect([$product->vendor]) : collect();
        $admins = \App\Models\AdminUser::where('is_active', true)->get();

        foreach ($admins->merge($vendors) as $recipient) {
            $this->notify($recipient, 'low_stock', 'Low Stock Alert',
                'Product "'.$product->name.'" is low on stock ('.$product->stock_quantity.' left).',
                route('admin.marketplace.products.index', ['search' => $product->name]), null);
        }
    }

    public function bulkOrderCreated(BulkOrder $bulkOrder): void
    {
        $this->notify($bulkOrder->creator, 'bulk_order_created', 'Community Bulk Order Created',
            'Your community bulk order "'.$bulkOrder->title.'" has been created.',
            route('marketplace.bulk.show', $bulkOrder->id), null);
    }

    public function bulkOrderCompleted(BulkOrder $bulkOrder): void
    {
        foreach ($bulkOrder->contributions->pluck('user_id')->unique() as $userId) {
            $user = User::find($userId);
            if ($user) {
                $this->notify($user, 'bulk_order_completed', 'Community Bulk Order Completed',
                    'The bulk order "'.$bulkOrder->title.'" you contributed to is now complete.',
                    route('marketplace.bulk.show', $bulkOrder->id), null);
            }
        }
    }

    private function notify($user, string $type, string $title, string $message, ?string $actionUrl, ?Order $order): void
    {
        if (! $user) {
            return;
        }

        Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
        ]);
    }

    private function mail(User $user, Order $order, string $subject): void
    {
        if (! $this->emailEnabled) {
            return;
        }

        try {
            Mail::to($user->email)->send(new MarketplaceOrderMail($order, $subject));
        } catch (\Throwable $e) {
            Log::error('Marketplace order mail failed: '.$e->getMessage());
        }
    }

    public function setEmailEnabled(bool $enabled): void
    {
        $this->emailEnabled = $enabled;
    }
}
