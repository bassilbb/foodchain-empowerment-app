<?php

namespace App\Jobs;

use App\Models\AdminUser;
use App\Models\Marketplace\PriceImportBatch;
use App\Services\Marketplace\PriceImport\ProductPriceImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Throwable;

class ApplyPriceImportBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 900;

    /**
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        public int $batchId,
        public int $adminUserId,
        public array $options = [],
    ) {
    }

    public function handle(ProductPriceImportService $service): void
    {
        $batch = PriceImportBatch::query()->find($this->batchId);
        $admin = AdminUser::query()->find($this->adminUserId);

        if ($batch === null || $admin === null) {
            return;
        }

        try {
            $service->apply($batch, $admin, $this->options);
        } catch (Throwable $exception) {
            $batch->update([
                'status' => PriceImportBatch::STATUS_FAILED,
                'last_error' => Str::limit($exception->getMessage(), 2000),
            ]);

            throw $exception;
        }
    }
}
