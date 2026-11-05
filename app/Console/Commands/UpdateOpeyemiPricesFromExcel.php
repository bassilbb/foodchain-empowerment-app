<?php

namespace App\Console\Commands;

use App\Services\Marketplace\OpeyemiPriceUpdater;
use Illuminate\Console\Command;

class UpdateOpeyemiPricesFromExcel extends Command
{
    protected $signature = 'opeyemi:update-prices
        {--file= : Path to the Opeyemi prices .xlsx file}
        {--apply : Explicit production mode - apply matched price updates (default is dry-run/report only)}
        {--json : Output the full report as JSON (in addition to summary)}';

    protected $description = 'Update Opeyemi Foods (opeyemi@foodchain.ng) product price and member_price from the official Excel price list. Dry-run by default.';

    public function handle(OpeyemiPriceUpdater $updater): int
    {
        $file = $this->option('file') ?: $this->defaultFile();
        $apply = (bool) $this->option('apply');

        $this->info($apply ? 'MODE: APPLY (production) - matched, non-ambiguous updates will be written.' : 'MODE: DRY-RUN - no database changes will be made. Use --apply to execute.');
        $this->line('Source: ' . $file);
        $this->newLine();

        $report = $updater->run($file, $apply);

        if (!empty($report['errors']) && empty($report['vendor'])) {
            foreach ($report['errors'] as $e) $this->error($e);
            return self::FAILURE;
        }

        if ($report['vendor']) {
            $v = $report['vendor'];
            $this->line('Vendor: ' . $v['name'] . ' (ID ' . $v['id'] . ', ' . $v['email'] . ') - OPEYEMI ONLY');
        }

        // Required summary table
        $this->newLine();
        $this->table(['Metric', 'Result'], [
            ['Spreadsheet entries', $report['spreadsheet_entries']],
            ['Products matched', $report['products_matched']],
            ['Products updated', $report['products_updated']],
            ['Unmatched entries', $report['unmatched_entries']],
            ['Ambiguous matches', $report['ambiguous_matches']],
            ['Failed updates', $report['failed_updates']],
            ['Duplicate spreadsheet entries', $report['duplicate_entries']],
            ['Skipped/invalid rows (errors)', count($report['errors'])],
        ]);

        if (!empty($report['duplicates'])) {
            $this->newLine();
            $this->warn('Duplicate spreadsheet entries (not auto-updated):');
            foreach ($report['duplicates'] as $d) {
                $this->line('- Rows ' . implode(',', $d['rows']) . ' | ' . $d['name'] . ' | ' . $d['size'] . ' | prices: ' . implode(',', $d['prices']));
            }
        }

        if (!empty($report['ambiguous'])) {
            $this->newLine();
            $this->warn('Ambiguous matches (not auto-updated):');
            foreach ($report['ambiguous'] as $a) {
                $this->line('- Row ' . $a['row'] . ' | ' . $a['excel_name'] . ' | ' . $a['size'] . ' | ₦' . $a['price'] . ' | ' . $a['reason']);
                foreach (($a['candidates'] ?? []) as $c) {
                    $this->line('    candidate: ' . $c['sku'] . ' | ' . $c['name'] . ' (' . $c['detail'] . ')');
                }
            }
        }

        if (!empty($report['unmatched'])) {
            $this->newLine();
            $this->warn('Unmatched spreadsheet rows (not auto-updated):');
            foreach ($report['unmatched'] as $u) {
                $this->line('- Row ' . $u['row'] . ' | ' . $u['excel_name'] . ' | ' . $u['size'] . ' | ₦' . $u['price']);
            }
        }

        if (!empty($report['errors'])) {
            $this->newLine();
            $this->warn('Row/validation notes:');
            foreach ($report['errors'] as $e) $this->line('- ' . $e);
        }

        $list = $apply ? $report['updated'] : $report['proposed'];
        $this->newLine();
        $this->info(($apply ? 'Products updated' : 'Proposed price changes (dry-run)') . ' - showing ' . count($list) . ':');
        $this->line('Only `price` and `member_price` are changed; both set to Excel price. All other fields preserved. Opeyemi vendor only.');
        foreach ($list as $c) {
            $this->line(sprintf(
                '- %s | %s | SKU %s | sell %s -> %s | member %s -> %s | %s',
                $c['product_name'], $c['product_size'] ?: 'N/A', $c['sku'],
                $c['previous_selling_price'], $c['new_selling_price'],
                $c['previous_member_price'], $c['new_member_price'],
                $c['match'] ?? ''
            ));
        }

        if (!empty($report['failed'])) {
            $this->newLine();
            $this->error('Failed updates:');
            foreach ($report['failed'] as $f) {
                $this->line('- ' . $f['sku'] . ' | ' . $f['product_name'] . ' | ' . ($f['error'] ?? 'unknown'));
            }
        }

        if ($this->option('json')) {
            $this->newLine();
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        if (!$apply && $report['products_matched'] > 0) {
            $this->newLine();
            $this->comment('Dry-run complete. Re-run with --apply to execute only the matched, non-ambiguous updates above (inside a DB transaction per product).');
        }

        return ($report['failed_updates'] > 0 || (!empty($report['errors']) && empty($report['vendor']))) ? self::FAILURE : self::SUCCESS;
    }

    protected function defaultFile(): string
    {
        $candidates = [
            'C:/Users/Devops-Bassey/Downloads/Opyemi Foods - Prices list 1.xlsx',
            base_path('Opyemi Foods - Prices list 1.xlsx'),
            storage_path('app/Opyemi Foods - Prices list 1.xlsx'),
        ];
        foreach ($candidates as $c) if (is_file($c)) return $c;
        return $candidates[0];
    }
}