<?php

namespace App\Console\Commands;

use App\Models\ProductImageResearch;
use App\Services\Images\ImageResearchService;
use Illuminate\Console\Command;

class DownloadProductImages extends Command
{
    protected $signature = 'images:download
        {--limit=25 : Maximum verified rows to download}
        {--sku= : Only process a specific SKU}
        {--vendor= : Vendor id (defaults to config)}
        {--replace : Replace an image the product already has}';

    protected $description = 'Download, validate, optimise and attach administrator-verified packaging images';

    public function handle(ImageResearchService $service): int
    {
        $vendorId = $this->option('vendor') !== null
            ? (int) $this->option('vendor')
            : $service->vendorId();

        $query = ProductImageResearch::where('vendor_id', $vendorId)
            ->where('status', ProductImageResearch::STATUS_VERIFIED)
            ->whereNotNull('candidate_image_url');

        if ($sku = $this->option('sku')) {
            $query->where('sku', $sku);
        }

        $records = $query->orderBy('id')->limit((int) $this->option('limit'))->get();

        if ($records->isEmpty()) {
            $this->info('No verified candidates are waiting to be downloaded.');

            return self::SUCCESS;
        }

        $ok = 0;
        $failed = 0;

        foreach ($records as $record) {
            $result = $service->download($record, (bool) $this->option('replace'));

            if ($result['success']) {
                $ok++;
                $this->line("  OK   {$record->sku} -> {$result['path']}");
            } else {
                $failed++;
                $this->warn("  FAIL {$record->sku}: {$result['message']}");
            }
        }

        $this->info("Downloaded {$ok} image(s); {$failed} failed. Failures never touch existing product images.");

        return self::SUCCESS;
    }
}
