<?php

namespace App\Console\Commands;

use App\Services\WalletService;
use Illuminate\Console\Command;

class ProcessRetryDefaultedDeductions extends Command
{
    protected $signature = 'accounts:retry-defaulted-deductions';

    protected $description = 'Retry weekly deductions for accounts that defaulted on Saturday';

    public function handle(): int
    {
        $this->info('Retrying defaulted deductions...');

        $result = app(WalletService::class)->retryDefaultedDeductions();
        \App\Helpers\CronLogger::logRun($this->signature);

        $this->info("Processed {$result['processed']} retry deductions, {$result['still_defaulted']} still lacking funds.");

        return Command::SUCCESS;
    }
}
