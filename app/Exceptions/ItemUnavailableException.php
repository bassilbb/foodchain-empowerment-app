<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Raised when a checkout cannot be honoured because a line on the cart is no
 * longer buyable: it was delisted, rejected by moderation, or someone else
 * bought the remaining stock first.
 *
 * Thrown from inside the order transaction so the whole attempt rolls back —
 * no order row, no wallet debit, no loyalty points, no negative stock.
 */
class ItemUnavailableException extends RuntimeException
{
    public static function delisted(string $productName): self
    {
        return new self("{$productName} is no longer available. Please remove it from your cart to continue.");
    }

    public static function insufficientStock(string $productName, int $requested, int $available): self
    {
        if ($available <= 0) {
            return new self("{$productName} just sold out. Please remove it from your cart to continue.");
        }

        return new self("Only {$available} left of {$productName} — please reduce the quantity in your cart to continue.");
    }
}
