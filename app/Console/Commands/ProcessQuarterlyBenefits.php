<?php

namespace App\Console\Commands;

use App\Services\MembershipGradeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessQuarterlyBenefits extends Command
{
    protected $signature = 'membership:process-quarterly-benefits 
                            {--dry-run : Run without making changes}';

    protected $description = 'Process quarterly benefit eligibility for all active members';

    public function __construct(
        private MembershipGradeService $membershipService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting quarterly benefits processing...');
        
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('Running in DRY-RUN mode - no changes will be made');
        }

        try {
            if ($dryRun) {
                $results = $this->simulateProcessing();
            } else {
                $results = $this->membershipService->processQuarterlyBenefitsForAllUsers();
            }

            $this->info("Processing complete!");
            $this->info("Total evaluated: {$results['total_evaluated']}");
            $this->info("Total qualified: {$results['total_qualified']}");
            
            if (!empty($results['errors'])) {
                $this->warn('Errors encountered: ' . count($results['errors']));
                foreach ($results['errors'] as $error) {
                    $this->error("User {$error['user_id']}: {$error['error']}");
                }
            }

            Log::info('Quarterly benefits command executed', [
                'dry_run' => $dryRun,
                'results' => $results,
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
            Log::error('Quarterly benefits processing failed', [
                'error' => $e->getMessage(),
            ]);
            return Command::FAILURE;
        }
    }

    private function simulateProcessing(): array
    {
        $results = [
            'total_evaluated' => 0,
            'total_qualified' => 0,
            'errors' => [],
            'simulated' => true,
        ];

        $users = \App\Models\User::active()
            ->whereNotNull('current_tier_id')
            ->limit(100)
            ->get();

        foreach ($users as $user) {
            $tier = $user->currentTier;
            if ($tier) {
                $communityCount = $this->membershipService->calculateUserCommunityCount($user);
                $qualifies = $tier->qualifiesForFreeFoodPack($communityCount);
                
                $results['total_evaluated']++;
                
                if ($qualifies) {
                    $results['total_qualified']++;
                }
            }
        }

        return $results;
    }
}
