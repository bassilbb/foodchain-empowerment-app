<?php

namespace App\Console\Commands;

use App\Models\ProductImageResearch;
use App\Services\Images\CatalogueImageMatcher;
use App\Services\Images\CatalogueImageSearchProvider;
use App\Services\Images\ImageResearchService;
use Illuminate\Console\Command;

/**
 * Matches outstanding products against the synced supplier catalogue and, when
 * the name/size/variant evidence is strong enough, downloads and attaches the
 * genuine product image.
 *
 * Weak or ambiguous matches are never downloaded: they are stored as
 * "pending verification" with their confidence so an administrator can review
 * them in the existing image-research screen.
 */
class ImportProductImagesFromCatalogue extends Command
{
    protected $signature = 'images:import-from-site
        {--vendor= : Vendor id (defaults to config)}
        {--limit=0 : Maximum research rows to scan (0 = all)}
        {--sku= : Only process a specific SKU}
        {--min-score= : Minimum name-match score to auto-approve (defaults to config)}
        {--min-margin= : Minimum lead over the runner-up (defaults to config)}
        {--dry-run : Report what would happen without writing or downloading}
        {--no-download : Store and verify candidates but do not download images}';

    protected $description = 'Match outstanding products to a synced supplier catalogue and download genuine images';

    public function handle(
        ImageResearchService $service,
        CatalogueImageSearchProvider $provider,
        CatalogueImageMatcher $matcher,
    ): int {
        if (! $provider->isConfigured()) {
            $this->error('No synced catalogue found at '.$provider->path());
            $this->line('Run: php artisan images:sync-catalogue');

            return self::FAILURE;
        }

        $catalogue = $provider->catalogue();

        if ($catalogue === []) {
            $this->error('The synced catalogue is empty. Re-run images:sync-catalogue.');

            return self::FAILURE;
        }

        $vendorId = $this->option('vendor') !== null
            ? (int) $this->option('vendor')
            : $service->vendorId();

        $minScore = $this->option('min-score') !== null
            ? (float) $this->option('min-score')
            : (float) config('image_research.catalogue.min_score', 0.75);

        $minMargin = $this->option('min-margin') !== null
            ? (float) $this->option('min-margin')
            : (float) config('image_research.catalogue.min_margin', 0.05);

        // Below this a "candidate" is noise (e.g. a spaghetti pack for a
        // mayonnaise). Such rows stay "pending research" rather than filling
        // the administrator's review queue with nonsense.
        $reviewScore = (float) config('image_research.catalogue.review_score', 0.60);

        $dryRun = (bool) $this->option('dry-run');
        $download = ! $this->option('no-download');

        // The catalogue's pack shots are often smaller than the floor used for
        // manual uploads. The name/size/variant match has already proven what
        // the pack is, so allow the catalogue's own (lower) floor here.
        config([
            'image_research.min_width' => (int) config('image_research.catalogue.min_width', 100),
            'image_research.min_height' => (int) config('image_research.catalogue.min_height', 100),
        ]);

        $query = ProductImageResearch::query()
            ->where('vendor_id', $vendorId)
            ->whereIn('status', [
                ProductImageResearch::STATUS_PENDING_RESEARCH,
                ProductImageResearch::STATUS_CANDIDATE_FOUND,
                ProductImageResearch::STATUS_PENDING_VERIFICATION,
                ProductImageResearch::STATUS_NOT_FOUND,
                // Verified but not yet attached: a previous download failed or
                // the file was removed. Safe to retry because the query below
                // skips any product that already has a real image.
                ProductImageResearch::STATUS_VERIFIED,
                ProductImageResearch::STATUS_DOWNLOADED,
            ])
            ->whereHas('product', function ($product) {
                $product->whereDoesntHave('images', fn ($images) => $images->where('is_placeholder', false));
            })
            ->orderBy('sku');

        if ($sku = $this->option('sku')) {
            $query->where('sku', $sku);
        }

        $limit = (int) $this->option('limit');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            $this->info('No outstanding research rows to process.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            '%s %d product(s) against %d catalogue listings (score >= %.2f, margin >= %.2f)%s',
            $dryRun ? 'Would scan' : 'Scanning',
            $records->count(),
            count($catalogue),
            $minScore,
            $minMargin,
            $dryRun ? ' — dry run' : ''
        ));

        $accepted = 0;
        $downloaded = 0;
        $failed = 0;
        $pending = 0;
        $unmatched = 0;
        $rows = [];

        $bar = $this->output->createProgressBar($records->count());
        $bar->start();

        foreach ($records as $record) {
            $result = $matcher->best((string) $record->product_name, $catalogue, $minScore, $minMargin);
            $candidate = $result['candidate'];

            if ($candidate === null) {
                $unmatched++;
                $bar->advance();

                continue;
            }

            if (! $result['accepted']) {
                if ($result['score'] < $reviewScore) {
                    $unmatched++;
                    $bar->advance();

                    continue;
                }

                $pending++;

                $rows[] = [
                    $record->sku,
                    mb_strimwidth((string) $record->product_name, 0, 44, '…'),
                    mb_strimwidth((string) $candidate['name'], 0, 44, '…'),
                    number_format($result['score'], 3),
                    number_format($result['margin'], 3),
                    'review',
                ];

                if (! $dryRun) {
                    $record->fill([
                        'status' => ProductImageResearch::STATUS_PENDING_VERIFICATION,
                        'candidate_image_url' => $candidate['image_url'],
                        'candidate_source_url' => $candidate['source_url'],
                        'candidate_source_site' => $candidate['source_site'],
                        'candidate_title' => $candidate['title'],
                        'candidate_confidence' => $candidate['score'],
                        'candidate_match_method' => 'catalogue-name-match',
                        'candidate_query' => $record->product_name,
                        'rejection_reason' => null,
                    ])->save();
                }

                $bar->advance();

                continue;
            }

            $accepted++;

            $rows[] = [
                $record->sku,
                mb_strimwidth((string) $record->product_name, 0, 44, '…'),
                mb_strimwidth((string) $candidate['name'], 0, 44, '…'),
                number_format($result['score'], 3),
                number_format($result['margin'], 3),
                'yes',
            ];

            if ($dryRun) {
                $bar->advance();

                continue;
            }

            $record->fill([
                'status' => ProductImageResearch::STATUS_VERIFIED,
                'candidate_image_url' => $candidate['image_url'],
                'candidate_source_url' => $candidate['source_url'],
                'candidate_source_site' => $candidate['source_site'],
                'candidate_title' => $candidate['title'],
                'candidate_confidence' => $candidate['score'],
                'candidate_match_method' => 'catalogue-name-match',
                'candidate_query' => $record->product_name,
                'verification_status' => 'verified',
                'verified_at' => now(),
                'rejection_reason' => null,
                'reviewer_notes' => sprintf(
                    'Auto-matched to "%s" (score %.3f, margin %.3f).',
                    $candidate['name'],
                    $result['score'],
                    $result['margin']
                ),
            ])->save();

            if ($download) {
                $outcome = $service->download($record);

                if ($outcome['success']) {
                    $downloaded++;
                } else {
                    $failed++;
                    $this->newLine();
                    $this->warn("{$record->sku}: {$outcome['message']}");
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($rows !== []) {
            $this->table(
                ['SKU', 'Supplier product', 'Catalogue match', 'Score', 'Margin', 'Auto'],
                array_slice($rows, 0, 200)
            );
        }

        $this->info(sprintf(
            '%s: %d matched, %d downloaded, %d download failed, %d need review, %d no candidate.',
            $dryRun ? 'Dry run' : 'Done',
            $accepted,
            $downloaded,
            $failed,
            $pending,
            $unmatched
        ));

        if ($dryRun) {
            $this->line('Nothing was written. Re-run without --dry-run to apply.');
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
