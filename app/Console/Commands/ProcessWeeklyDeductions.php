<?php

namespace App\Console\Commands;

use App\Services\WalletService;
use Illuminate\Console\Command;

class ProcessWeeklyDeductions extends Command
{
    protected $signature = 'accounts:process-weekly-deductions';

    protected $description = 'Process weekly contribution deductions from wallets for all active accounts';

    public function handle(): int
    {
        $this->info('Processing weekly deductions...');

        $result = app(WalletService::class)->processWeeklyDeductions();

        $this->info("Processed {$result['processed']} deductions, {$result['defaulted']} defaults recorded.");

        return Command::SUCCESS;
    }
}
