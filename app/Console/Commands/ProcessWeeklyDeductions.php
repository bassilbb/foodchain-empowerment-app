<?php

namespace App\Console\Commands;

use App\Services\WalletService;
use Illuminate\Console\Command;

class ProcessWeeklyDeductions extends Command
{
    protected $signature = 'accounts:process-weekly-deductions';

    protected $description = 'Process weekly contribution deductions from user wallets';

    public function handle()
    {
        $this->info('Starting weekly deduction processing...');

        try {
            app(WalletService::class)->processWeeklyDeductions();
            \App\Helpers\CronLogger::logRun($this->signature);
            $this->info('Weekly deductions processed successfully.');
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());
        }
    }
}
