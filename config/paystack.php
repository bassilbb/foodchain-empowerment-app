<?php

/*
 * This file is part of the Laravel Paystack package.
 *
 * (c) Prosper Otemuyiwa <prosperotemuyiwa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /**
     * Public Key From Paystack Dashboard
     *
     */
    'public_key' => env('PAYSTACK_PUBLIC_KEY'),

    /**
     * Secret Key From Paystack Dashboard
     *
     */
    'secret_key' => env('PAYSTACK_SECRET_KEY'),

    /**
     * Paystack Payment URL
     *
     */
    'payment_url' => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),

    /**
     * Optional email address of the merchant
     *
     */
    'merchant_email' => env('MERCHANT_EMAIL'),

];
