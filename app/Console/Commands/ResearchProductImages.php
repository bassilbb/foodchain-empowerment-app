<?php

namespace App\Console\Commands;

use App\Models\ProductImageResearch;
use App\Services\Images\ImageResearchService;
use Illuminate\Console\Command;

class ResearchProductImages extends Command
{
    protected $signature = 'images:research
        {--limit=25 : Maximum rows to process}
        {--status= : Only process a specific status}
        {--vendor= : Vendor id (defaults to config)}';

    protected $description = 'Ask the approved image-search API for candidate packaging images (never auto-approves)';

    public function handle(ImageResearchService $service): int
    {
        if (! $service->providerConfigured()) {
            $this->warn('No image-search provider is configured (IMAGE_SEARCH_PROVIDER / SERPAPI_KEY).');
            $this->warn('Products stay in "Pending Research"; no images were invented or scraped.');

            return self::SUCCESS;
        }

        $vendorId = $this->option('vendor') !== null
            ? (int) $this->option('vendor')
            : $service->vendorId();

        $query = ProductImageResearch::where('vendor_id', $vendorId);

        if ($status = $this->option('status')) {
            $query->where('status', $status);
        } else {
            $query->whereIn('status', [
                ProductImageResearch::STATUS_PENDING_RESEARCH,
                ProductImageResearch::STATUS_NOT_FOUND,
            ]);
        }

        $records = $query->orderBy('id')->limit((int) $this->option('limit'))->get();

        $found = 0;
        $missing = 0;

        foreach ($records as $record) {
            $service->research($record);

            if ($record->status === ProductImageResearch::STATUS_PENDING_VERIFICATION) {
                $found++;
            } else {
                $missing++;
            }
        }

        $this->info("Processed {$records->count()} product(s): {$found} candidate(s) found, {$missing} not found.");
        $this->line('Candidates still require administrator verification before download.');

        return self::SUCCESS;
    }
}
