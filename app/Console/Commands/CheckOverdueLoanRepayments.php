<?php

namespace App\Console\Commands;

use App\Models\FoodLoan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOverdueLoanRepayments extends Command
{
    protected $signature = 'loans:check-overdue 
                            {--dry-run : Run without making changes}';

    protected $description = 'Check and mark overdue loan repayments';

    public function handle(): int
    {
        $this->info('Checking for overdue loan repayments...');
        
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('Running in DRY-RUN mode - no changes will be made');
        }

        $results = [
            'loans_checked' => 0,
            'overdue_installments_found' => 0,
            'loans_defaulted' => 0,
        ];

        $activeLoans = FoodLoan::whereIn('status', ['disbursed', 'repaying'])->get();

        foreach ($activeLoans as $loan) {
            $results['loans_checked']++;

            if (!$dryRun) {
                $loan->checkOverdue();
            }

            $overdueCount = $loan->repayments()
                ->where('status', 'overdue')
                ->count();

            $results['overdue_installments_found'] += $overdueCount;

            if (!$dryRun && $loan->status === 'defaulted') {
                $results['loans_defaulted']++;
            }
        }

        $this->info("Check complete!");
        $this->info("Loans checked: {$results['loans_checked']}");
        $this->info("Overdue installments: {$results['overdue_installments_found']}");
        $this->info("Loans defaulted: {$results['loans_defaulted']}");

        Log::info('Loan overdue check executed', [
            'dry_run' => $dryRun,
            'results' => $results,
        ]);

        return Command::SUCCESS;
    }
}
