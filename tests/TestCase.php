<?php

namespace Tests;

use App\Models\Marketplace\Order;
use App\Models\Setting;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->enableAllPaymentMethods();
    }

    /**
     * Feature tests exercise the payment flows themselves, so make every
     * supported method available by default. Tests that need to prove the
     * admin-controlled availability rules should set the specific methods
     * they want and assert the rest are rejected.
     */
    protected function enableAllPaymentMethods(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        Setting::flushMemo();

        foreach (array_keys(Order::PAYMENT_METHODS) as $method) {
            Setting::updateOrCreate(
                ['key' => 'payment_methods.'.$method],
                ['value' => '1', 'type' => 'boolean', 'group' => 'payment']
            );
        }

        Setting::flushMemo();
    }
}
