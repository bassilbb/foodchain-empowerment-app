<?php

namespace App\Console\Commands;

use App\Services\Images\ImageResearchService;
use Illuminate\Console\Command;

class AuditOpeyemiImages extends Command
{
    protected $signature = 'images:audit
        {--vendor= : Vendor id to audit (defaults to config)}';

    protected $description = 'Create/refresh image-research rows for products that have no real packaging image';

    public function handle(ImageResearchService $service): int
    {
        $vendorId = $this->option('vendor') !== null
            ? (int) $this->option('vendor')
            : $service->vendorId();

        $result = $service->audit($vendorId);

        $this->info("Vendor {$vendorId}: {$result['total']} products, {$result['with_images']} with real images, {$result['outstanding']} outstanding.");
        $this->info("Research rows created: {$result['created']}, refreshed: {$result['refreshed']}.");

        return self::SUCCESS;
    }
}
