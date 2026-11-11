<?php

namespace App\Console\Commands;

use App\Models\ProductImageResearch;
use App\Services\Images\ImageResearchService;
use Illuminate\Console\Command;

class ImageResearchReport extends Command
{
    protected $signature = 'images:report
        {--vendor= : Vendor id (defaults to config)}
        {--json : Output machine-readable JSON}';

    protected $description = 'Show the packaging-image research progress report for a vendor';

    public function handle(ImageResearchService $service): int
    {
        $vendorId = $this->option('vendor') !== null
            ? (int) $this->option('vendor')
            : $service->vendorId();

        $report = $service->report($vendorId);

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $this->info("Image research report - vendor {$report['vendor_id']}");
        $this->line('Provider: '.$report['provider'].' ('.($report['provider_configured'] ? 'configured' : 'manual mode').')');
        $this->newLine();

        $this->table(['Metric', 'Count'], [
            ['Products in vendor', $report['total_products']],
            ['Already have real images', $report['with_real_images']],
            ['Outstanding (no real image)', $report['outstanding']],
            ['Verified (awaiting download)', $report['verified']],
            ['Downloaded', $report['downloaded']],
            ['Pending verification', $report['pending_verification']],
            ['Not found', $report['not_found']],
            ['Failed downloads', $report['failed_downloads']],
        ]);

        $this->newLine();
        $this->line('Status breakdown');

        $rows = [];
        foreach (ProductImageResearch::STATUSES as $status => $label) {
            $rows[] = [$label, (int) ($report['status_counts'][$status] ?? 0)];
        }

        $this->table(['Status', 'Count'], $rows);

        return self::SUCCESS;
    }
}
