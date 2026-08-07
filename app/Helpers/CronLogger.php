<?php

namespace App\Helpers;

use App\Models\Setting;

class CronLogger
{
    protected static $logKey = 'cron_run_log';

    public static function logRun(string $signature): void
    {
        $log = self::getRunLog();
        $log[$signature] = now()->toDateTimeString();
        Setting::updateOrCreate(['key' => self::$logKey], ['value' => json_encode($log)]);
    }

    public static function getRunLog(): array
    {
        $raw = Setting::where('key', self::$logKey)->value('value');

        return $raw ? json_decode($raw, true) : [];
    }

    public static function getLastRun(string $signature): ?string
    {
        $log = self::getRunLog();

        return $log[$signature] ?? null;
    }

    public static function getSummary(): array
    {
        $log = self::getRunLog();
        $definitions = [
            'accounts:process-weekly-deductions' => 'Weekly Deductions',
            'accounts:retry-defaulted-deductions' => 'Retry Deductions',
            'loans:check-overdue' => 'Overdue Loan Check',
            'membership:process-quarterly-benefits' => 'Quarterly Benefits',
            'daily:tier-upgrade' => 'Tier Upgrade Check',
            'daily:expiry-reminders' => 'Expiry Reminders',
        ];

        $result = [];
        foreach ($definitions as $sig => $label) {
            $result[] = [
                'command' => $sig,
                'label' => $label,
                'last_run' => $log[$sig] ?? null,
                'status' => isset($log[$sig])
                    ? (now()->diffInHours($log[$sig]) < 48 ? 'ok' : 'stale')
                    : 'never',
            ];
        }

        return $result;
    }
}
