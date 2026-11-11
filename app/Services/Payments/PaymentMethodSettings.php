<?php

namespace App\Services\Payments;

use App\Models\BankAccount;
use App\Models\Marketplace\Order;
use App\Models\Setting;
use InvalidArgumentException;

/**
 * Single source of truth for which payment methods are offered at checkout.
 *
 * Everything is persisted in the existing `settings` table under
 * `payment_methods.{method}` so administrators can change availability from
 * the dashboard without touching code. Nothing here is hard-coded into the
 * checkout controller or Blade templates.
 */
class PaymentMethodSettings
{
    public const SETTING_PREFIX = 'payment_methods.';

    public const BANK_TRANSFER = 'bank_transfer';

    /**
     * Friendly descriptions shown on the admin Payment Settings screen.
     *
     * @var array<string, string>
     */
    private const DESCRIPTIONS = [
        'bank_transfer' => 'Customer transfers the exact order amount to the configured company bank account.',
        'wallet' => 'Customer pays the full order total from their FoodChain wallet balance.',
        'wallet_cash' => 'Customer pays part of the order from their wallet and the balance on delivery.',
        'wallet_paystack' => 'Customer pays part of the order from their wallet and the balance by card.',
        'cash' => 'Cash on delivery — the order stays unpaid until the rider collects payment.',
        'paystack' => 'Card, USSD and transfer payments through the Paystack gateway.',
        'flutterwave' => 'Card, bank and USSD payments through the Flutterwave gateway.',
        'mono' => 'Direct bank debit through the Mono gateway.',
    ];

    /**
     * Methods that ship enabled. Bank Transfer is the only default because the
     * marketplace must not expose unconfigured gateways to customers.
     *
     * @var array<string, bool>
     */
    private const DEFAULTS = [
        self::BANK_TRANSFER => true,
    ];

    /**
     * @return array<string, string>
     */
    public function labels(): array
    {
        return Order::PAYMENT_METHODS;
    }

    /**
     * @return array<string, bool>
     */
    public function defaults(): array
    {
        return self::DEFAULTS;
    }

    public function isKnown(string $method): bool
    {
        return array_key_exists($method, $this->labels());
    }

    public function isEnabled(string $method): bool
    {
        if (! $this->isKnown($method)) {
            return false;
        }

        $default = self::DEFAULTS[$method] ?? false;

        return filter_var(
            Setting::get(self::SETTING_PREFIX.$method, $default),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    /**
     * A method is available when an administrator enabled it AND it has
     * everything it needs to work (bank transfer needs an active account).
     */
    public function isAvailable(string $method): bool
    {
        if (! $this->isEnabled($method)) {
            return false;
        }

        if ($method === self::BANK_TRANSFER) {
            return $this->bankTransferDetails() !== null;
        }

        return true;
    }

    /**
     * @return array<int, string>
     */
    public function enabledMethods(): array
    {
        return array_values(array_filter(
            array_keys($this->labels()),
            fn (string $method) => $this->isAvailable($method)
        ));
    }

    /**
     * Enabled methods in the key => label shape the checkout view expects.
     *
     * @return array<string, string>
     */
    public function enabledLabels(): array
    {
        $labels = $this->labels();

        return collect($this->enabledMethods())
            ->mapWithKeys(fn (string $method) => [$method => $labels[$method]])
            ->all();
    }

    /**
     * Everything an admin needs to render the settings screen.
     *
     * @return array<string, array{key: string, label: string, description: string, enabled: bool, available: bool, setting_key: string}>
     */
    public function all(): array
    {
        $out = [];

        foreach ($this->labels() as $key => $label) {
            $out[$key] = [
                'key' => $key,
                'label' => $label,
                'description' => self::DESCRIPTIONS[$key] ?? '',
                'enabled' => $this->isEnabled($key),
                'available' => $this->isAvailable($key),
                'setting_key' => self::SETTING_PREFIX.$key,
            ];
        }

        return $out;
    }

    public function setEnabled(string $method, bool $enabled): void
    {
        if (! $this->isKnown($method)) {
            throw new InvalidArgumentException('Unknown payment method: '.$method);
        }

        Setting::updateOrCreate(
            ['key' => self::SETTING_PREFIX.$method],
            [
                'value' => $enabled ? '1' : '0',
                'type' => 'boolean',
                'group' => 'payment',
                'description' => self::DESCRIPTIONS[$method] ?? null,
            ]
        );

        // Setting::set() flushes the memo, but updateOrCreate() does not.
        Setting::flushMemo();
    }

    /**
     * Make sure every known method has a persisted row. Idempotent — safe to
     * call from a migration, seeder or the admin screen.
     */
    public function syncDefaults(): void
    {
        foreach (array_keys($this->labels()) as $method) {
            $key = self::SETTING_PREFIX.$method;

            if (Setting::where('key', $key)->exists()) {
                continue;
            }

            Setting::create([
                'key' => $key,
                'value' => (self::DEFAULTS[$method] ?? false) ? '1' : '0',
                'type' => 'boolean',
                'group' => 'payment',
                'description' => self::DESCRIPTIONS[$method] ?? null,
            ]);
        }

        Setting::flushMemo();
    }

    /**
     * Bank details shown to customers. Returns null when no active account is
     * configured, which also makes Bank Transfer unavailable at checkout.
     *
     * @return array{bank_name: string, account_name: string, account_number: string, instructions: ?string, account_id: int}|null
     */
    public function bankTransferDetails(): ?array
    {
        $account = BankAccount::getActiveForPayment();

        if (! $account) {
            return null;
        }

        return [
            'account_id' => $account->id,
            'bank_name' => $account->bank_name,
            'account_name' => $account->account_name,
            'account_number' => $account->account_number,
            'instructions' => $account->instructions,
        ];
    }

    /**
     * Persist the single bank account used for transfers. Creates the default
     * account on first save and updates it afterwards, so there is exactly one
     * source of truth for the details customers see.
     *
     * @param  array{bank_name: string, account_name: string, account_number: string, instructions?: ?string, is_active?: bool}  $data
     */
    public function saveBankTransferAccount(array $data): BankAccount
    {
        $account = BankAccount::active()->default()->first()
            ?? BankAccount::orderBy('id')->first();

        if (! $account) {
            $account = new BankAccount;
        }

        $account->fill([
            'bank_name' => $data['bank_name'],
            'account_name' => $data['account_name'],
            'account_number' => $data['account_number'],
            'account_type' => $account->account_type ?: 'current',
            'instructions' => $data['instructions'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'is_default' => true,
        ]);

        $account->save();

        // Only one default account may exist.
        BankAccount::where('id', '!=', $account->id)
            ->where('is_default', true)
            ->update(['is_default' => false]);

        return $account->refresh();
    }
}
