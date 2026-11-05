<?php

namespace App\Services\Marketplace;

use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class OpeyemiPriceUpdater
{
    public const VENDOR_EMAIL = 'opeyemi@foodchain.ng';

    public function run(string $xlsxPath, bool $apply = false): array
    {
        $report = [
            'vendor' => null,
            'xlsx_path' => $xlsxPath,
            'apply' => $apply,
            'spreadsheet_entries' => 0,
            'duplicate_entries' => 0,
            'products_matched' => 0,
            'products_updated' => 0,
            'unmatched_entries' => 0,
            'ambiguous_matches' => 0,
            'failed_updates' => 0,
            'duplicates' => [],
            'ambiguous' => [],
            'unmatched' => [],
            'proposed' => [],
            'updated' => [],
            'failed' => [],
            'errors' => [],
        ];

        if (!is_file($xlsxPath)) {
            $report['errors'][] = "Excel file not found: {$xlsxPath}";
            return $report;
        }

        $vendor = Vendor::where('email', self::VENDOR_EMAIL)->first();
        if (!$vendor) {
            $report['errors'][] = 'Vendor not found: ' . self::VENDOR_EMAIL;
            return $report;
        }
        $report['vendor'] = ['id' => $vendor->id, 'name' => $vendor->business_name ?: $vendor->name, 'email' => $vendor->email];

        $rows = $this->readRows($xlsxPath);
        $entries = $this->extractEntries($rows, $report);
        $report['spreadsheet_entries'] = count($entries);

        $products = Product::where('vendor_id', $vendor->id)
            ->get(['id', 'name', 'sku', 'price', 'member_price', 'stock_quantity', 'low_stock_threshold',
                'description', 'category_id', 'approval_status', 'is_active', 'vendor_id', 'weight', 'weight_unit', 'unit', 'meta']);
        $dbIndex = $this->indexProducts($products);

        [$duplicates, $unique] = $this->splitDuplicates($entries);
        $report['duplicate_entries'] = count($duplicates);
        $report['duplicates'] = $duplicates;

        foreach ($unique as $entry) {
            $resolution = $this->resolve($entry, $dbIndex, $vendor->id);
            if ($resolution['status'] === 'ambiguous') {
                $report['ambiguous_matches']++;
                $report['ambiguous'][] = $resolution;
                continue;
            }
            if ($resolution['status'] === 'unmatched') {
                $report['unmatched_entries']++;
                $report['unmatched'][] = $resolution;
                continue;
            }

            $product = $resolution['product'];
            $newPrice = $this->formatPrice($entry['price']);
            $prevPrice = $this->formatPrice($product->price);
            $prevMember = $this->formatPrice($product->member_price);
            $change = [
                'product_name' => $product->name,
                'product_size' => $entry['size'],
                'excel_name' => $entry['name'],
                'sku' => $product->sku,
                'product_id' => $product->id,
                'previous_selling_price' => $prevPrice,
                'new_selling_price' => $newPrice,
                'previous_member_price' => $prevMember,
                'new_member_price' => $newPrice,
                'match' => $resolution['match'],
            ];
            $report['proposed'][] = $change;

            if (!$apply) continue;

            try {
                DB::transaction(function () use ($product, $vendor, $entry, $newPrice) {
                    $fresh = Product::where('id', $product->id)
                        ->where('vendor_id', $vendor->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if (!is_numeric($entry['price']) || (float)$entry['price'] <= 0) {
                        throw new \RuntimeException('Invalid price for SKU ' . $fresh->sku);
                    }

                    $fresh->price = $newPrice;
                    $fresh->member_price = $newPrice;
                    $fresh->save();
                });
                $report['products_updated']++;
                $report['updated'][] = $change;
            } catch (\Throwable $e) {
                $report['failed_updates']++;
                $report['failed'][] = $change + ['error' => $e->getMessage()];
            }
        }

        $report['products_matched'] = count($report['proposed']);
        return $report;
    }

    protected function readRows(string $xlsxPath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($xlsxPath) !== true) return [];
        $ss = [];
        $shared = $zip->getFromName('xl/sharedStrings.xml');
        if ($shared) {
            $x = @simplexml_load_string($shared);
            if ($x) foreach ($x->si as $si) {
                if (isset($si->t)) $ss[] = (string)$si->t;
                elseif (isset($si->r)) { $t=''; foreach ($si->r as $r) $t .= (string)$r->t; $ss[] = $t; }
                else $ss[] = '';
            }
        }
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if (!$sheetXml) return [];
        $x = @simplexml_load_string($sheetXml);
        if (!$x || !isset($x->sheetData)) return [];
        $rows = [];
        foreach ($x->sheetData->row as $r) {
            $row = [];
            foreach ($r->c as $c) {
                $col = preg_replace('/\d+/', '', (string)$c['r']);
                $t = (string)$c['t'];
                $v = isset($c->v) ? (string)$c->v : '';
                $val = ($t === 's' && isset($ss[(int)$v])) ? $ss[(int)$v] : $v;
                if (isset($c->is->t)) $val = (string)$c->is->t;
                $row[$col] = trim((string)$val);
            }
            $rows[] = $row;
        }
        return $rows;
    }

    protected function extractEntries(array $rows, array &$report): array
    {
        $entries = [];
        foreach ($rows as $i => $row) {
            $a = $row['A'] ?? '';
            $c = $row['C'] ?? '';
            $d = $row['D'] ?? ($row['B'] ?? ''); // fallback if layout differs
            if ($i === 0 && stripos($a, 'product') !== false) continue; // header
            if ($a === '' && $c === '' && $d === '') continue;
            $price = preg_replace('/[^\d.\-]/', '', str_replace(',', '', (string)$d));
            if ($a === '' || $price === '' || !is_numeric($price)) {
                if ($a !== '' || $c !== '' || $d !== '') {
                    $report['errors'][] = "Skipped row " . ($i + 1) . ": invalid/missing data " . json_encode($row, JSON_UNESCAPED_UNICODE);
                }
                continue;
            }
            if ((float)$price <= 0) {
                $report['errors'][] = "Skipped row " . ($i + 1) . ": price must be > 0";
                continue;
            }
            $entries[] = [
                'row' => $i + 1,
                'name' => trim($a),
                'size' => trim($c),
                'price' => (float)$price,
                'key' => $this->entryKey($a, $c),
            ];
        }
        return $entries;
    }

    protected function entryKey(string $name, string $size): string
    {
        return $this->norm($name) . '||' . $this->normSize($size);
    }

    protected function splitDuplicates(array $entries): array
    {
        $groups = [];
        foreach ($entries as $e) $groups[$e['key']][] = $e;
        $dups = []; $unique = [];
        foreach ($groups as $items) {
            if (count($items) > 1) {
                $dups[] = ['key' => $items[0]['key'], 'name' => $items[0]['name'], 'size' => $items[0]['size'], 'rows' => array_column($items, 'row'), 'prices' => array_values(array_unique(array_column($items, 'price')))];
            } else {
                $unique[] = $items[0];
            }
        }
        return [$dups, $unique];
    }

    protected function indexProducts($products): array
    {
        $index = [];
        foreach ($products as $p) {
            $index[] = [
                'product' => $p,
                'norm_name' => $this->norm($p->name),
                'name_tokens' => $this->tokens($p->name),
                'sig' => $this->productSig($p->name),
            ];
        }
        return $index;
    }

    protected function resolve(array $entry, array $dbIndex, int $vendorId): array
    {
        $excelNameNorm = $this->norm($entry['name']);
        $excelNameTokens = $this->tokens($entry['name']);
        $excelSig = $this->sizeSig($entry['size'], $entry['name']);
        $excelText = $this->norm(trim($entry['name'] . ' ' . $entry['size']));

        $candidates = [];
        foreach ($dbIndex as $item) {
            $p = $item['product'];
            $score = $this->score($excelNameNorm, $excelNameTokens, $excelSig, $item, $entry);
            if ($score['eligible']) {
                $candidates[] = ['product' => $p, 'score' => $score, 'sig' => $item['sig']];
            }
        }

        if (count($candidates) === 0) {
            return ['status' => 'unmatched', 'row' => $entry['row'], 'excel_name' => $entry['name'], 'size' => $entry['size'], 'price' => $entry['price'], 'reason' => 'No confident name+size match in vendor products'];
        }

        // disambiguation bonuses/penalties
        foreach ($candidates as &$c) {
            $dn = $this->norm($c['product']->name);
            // penalize extra line tokens in DB not implied by excel (zest/flavor lines) when excel is generic
            if (!str_contains($excelText, 'zest') && str_contains($dn, 'zest') && str_contains($excelText, 'active')) $c['score']['points'] -= 8;
            if (!str_contains($excelText, 'orange') && !str_contains($excelText, 'other') && (str_contains($dn,' orange') || str_contains($dn,' sb') || str_contains($dn,' plain') || str_contains($dn,' vanilla') || str_contains($dn,' strawberry')) && $excelSig['has_size']) {
                // flavor-split product but excel gives no flavor - keep as-is (ambiguous)
            }
            // bonus: can matches
            if (str_contains($excelText, 'can') && str_contains($dn, 'can')) $c['score']['points'] += 12;
            if (!str_contains($excelText, 'can') && str_contains($dn, 'can') && !str_contains($excelText,'330ml')) $c['score']['points'] -= 4;
            // bonus: evap explicit
            if (str_contains($excelText, 'evap') && str_contains($dn, 'evap')) $c['score']['points'] += 15;
            // bonus: promo/choco alignment
            if ((str_contains($excelText,'choco') || str_contains($excelText,'chocolate')) && (str_contains($dn,'choco') || str_contains($dn,'chocolate'))) $c['score']['points'] += 8;
            if (!(str_contains($excelText,'choco') || str_contains($excelText,'chocolate')) && (str_contains($dn,'+10pcs') || str_contains($dn,'+20') || str_contains($dn,' choco')) && str_contains($dn,'dano coolcow')) $c['score']['points'] -= 10;
            // refill/jar soft preference
            if (str_contains($excelText,'refill') && str_contains($dn,'refill')) $c['score']['points'] += 8;
            if (str_contains($excelText,'jar') && str_contains($dn,'jar')) $c['score']['points'] += 8;
            if (str_contains($excelText,'jar') && !str_contains($dn,'jar') && str_contains($dn,'refill')) $c['score']['points'] -= 6;
        }
        unset($c);

        usort($candidates, fn($a,$b) => $b['score']['points'] <=> $a['score']['points']);
        $best = $candidates[0];
        $second = $candidates[1] ?? null;

        $ambiguousReason = null;
        $gap = $second ? ($best['score']['points'] - $second['score']['points']) : 999;
        if ($best['score']['points'] < 70) $ambiguousReason = 'Low confidence score';
        if ($second && $gap < 12) $ambiguousReason = 'Multiple close candidates';
        if ($excelSig['has_size'] && !$best['score']['size_ok']) {
            $soft = $best['score']['soft'] ?? [];
            $amountExact = $excelSig['amount'] && $best['sig']['amount'] && ($excelSig['amount'] === $best['sig']['amount']);
            $onlyPackIssue = $amountExact && !empty($soft['pack_missing']) && empty($soft['pack_diff']) && empty($soft['db_size_unclear']) && empty($soft['amount_close']);
            $sameSizeOthers = 0;
            if ($onlyPackIssue) {
                foreach ($candidates as $c) {
                    if ($c['product']->id === $best['product']->id) continue;
                    if ($c['sig']['amount'] === $excelSig['amount']) $sameSizeOthers++;
                }
            }
            if (!($onlyPackIssue && $sameSizeOthers === 0 && $best['score']['points'] >= 75)) {
                $ambiguousReason = 'Size/packaging does not confidently match';
            }
        }
        if (!$excelSig['has_size'] && count($candidates) > 1) $ambiguousReason = 'No size in Excel and multiple name matches';

        // Safe overrides (review-resolve): sole candidate with exact size+pack, or clear choco/variant alignment
        if ($ambiguousReason === 'Low confidence score' && !$second && $best['score']['size_ok'] && $best['score']['points'] >= 65) {
            $ambiguousReason = null;
        }
        if ($ambiguousReason === 'Multiple close candidates' && $second && $gap >= 8 && $best['score']['size_ok']) {
            $excelHasChoco = (str_contains($excelText,'choco') || str_contains($excelText,'chocolate'));
            $bestHasChoco = str_contains($this->norm($best['product']->name), 'choco') || str_contains($this->norm($best['product']->name), 'chocolate');
            $secondHasChoco = str_contains($this->norm($second['product']->name), 'choco') || str_contains($this->norm($second['product']->name), 'chocolate');
            $chocoAligned = ($excelHasChoco === $bestHasChoco) && ($excelHasChoco !== $secondHasChoco);
            if ($chocoAligned) $ambiguousReason = null;
        }

        if ($ambiguousReason) {
            return [
                'status' => 'ambiguous',
                'row' => $entry['row'], 'excel_name' => $entry['name'], 'size' => $entry['size'], 'price' => $entry['price'],
                'reason' => $ambiguousReason,
                'candidates' => array_map(fn($c) => ['sku'=>$c['product']->sku,'name'=>$c['product']->name,'points'=>$c['score']['points'],'detail'=>$c['score']['detail']], array_slice($candidates,0,5)),
            ];
        }

        return ['status' => 'matched', 'product' => $best['product'], 'match' => $best['score']['detail'] . ' (score '.$best['score']['points'].')'];
    }

    protected function score(string $excelNameNorm, array $excelTokens, array $excelSig, array $item, array $entry): array
    {
        $p = $item['product'];
        $dbNorm = $item['norm_name'];
        $dbTokens = $item['name_tokens'];
        $dbSig = $item['sig'];

        // name core overlap
        $excelCore = $this->coreTokens($excelTokens);
        $dbCore = $this->coreTokens($dbTokens);
        if (empty($excelCore) || empty($dbCore)) return ['eligible'=>false,'points'=>0,'size_ok'=>false,'detail'=>'empty core'];
        $intersect = array_intersect($excelCore, $dbCore);
        $unionCount = count(array_unique(array_merge($excelCore, $dbCore)));
        $overlap = $unionCount ? count($intersect) / $unionCount : 0;
        $containsAllExcelCore = count(array_diff($excelCore, $dbCore)) === 0;

        $namePoints = 0;
        if ($containsAllExcelCore) $namePoints += 55;
        elseif ($overlap >= 0.6) $namePoints += 35;
        elseif ($overlap >= 0.4) $namePoints += 15;
        else return ['eligible'=>false,'points'=>0,'size_ok'=>false,'detail'=>'name mismatch'];

        // variant/flavor hints from size text (banana/vanilla/chocolate etc.)
        $variantOk = $this->variantOk($entry['size'].' '.$entry['name'], $p->name);
        if (!$variantOk) return ['eligible'=>false,'points'=>0,'size_ok'=>false,'detail'=>'variant mismatch'];

        $sizeOk = true; $sizePoints = 0; $detail = []; $soft = [];
        if ($excelSig['has_size']) {
            $cmp = $this->compareSigs($excelSig, $dbSig);
            $sizeOk = $cmp['ok'];
            $sizePoints = $cmp['points'];
            $detail[] = $cmp['detail'];
            $soft = $cmp['soft'] ?? [];
        } else {
            $detail[] = 'no excel size';
            $sizePoints = $containsAllExcelCore ? 10 : 0;
        }
        $detail[] = 'name overlap '.round($overlap*100).'%';

        $points = $namePoints + $sizePoints + ($variantOk ? 10 : 0);
        $eligible = $points >= 55;
        return ['eligible'=>$eligible,'points'=>$points,'size_ok'=>$sizeOk,'detail'=>implode('; ', $detail),'soft'=>$soft];
    }

    protected function norm(string $s): string
    {
        $s = mb_strtolower($s);
        $s = str_replace(['×', '*', "'", '’'], ' x ', $s);
        $s = preg_replace('/\bby\b/u', ' x ', $s);
        $s = preg_replace('/\bsatchet\b/u', 'sachet', $s);
        $s = preg_replace('/\bvanila\b/u', 'vanilla', $s);
        $s = preg_replace('/\b3\s*-\s*in\s*-\s*1\b|\b3in1\b/u', '3in1', $s);
        $s = preg_replace('/\bchoco\b/u', 'chocolate', $s);
        $s = preg_replace('/\bsupergetti\b/u', 'spaghetti', $s);
        $s = preg_replace('/\bspaghettini\b|\bspagettini\b/u', 'spaghettini', $s);
        $s = preg_replace('/\bspagetti\b|\bspag\b/u', 'spaghetti', $s);
        $s = preg_replace('/\bspaghettini\b/u', 'spaghettini', $s); // keep distinct
        $s = preg_replace('/\bsemolina\b/u', 'semo', $s);
        $s = preg_replace('/\bcavatto\b/u', 'cavetto', $s);
        $s = preg_replace('/\bfull\s*cream\b/u', 'fullcream', $s);
        $s = preg_replace('/\bcool\s*cow\b/u', 'coolcow', $s);
        $s = preg_replace('/\bvegetable\b/u', 'veg', $s);
        $s = preg_replace('/\bcapri\s+sun\b/u', 'caprisun', $s);
        $s = preg_replace('/\bhungry\s+man\b/u', 'hungryman', $s);
        $s = preg_replace('/\bsuper\s*cow\b/u', 'supercow', $s);
        // normalize units (handle attached forms like 1.4lt, 700ml, 10kg)
        $s = preg_replace('/(\d+(?:\.\d+)?)\s*(?:litres?|liters?|ltrs?|lt)\b/u', '$1l', $s);
        $s = preg_replace('/(\d+(?:\.\d+)?)\s*(?:millilit(?:re|er)s?|mls?)\b/u', '$1ml', $s);
        $s = preg_replace('/(\d+(?:\.\d+)?)\s*(?:kilograms?|kilos?|kgs?)\b/u', '$1kg', $s);
        $s = preg_replace('/(\d+(?:\.\d+)?)\s*(?:grams?|gms?|gr)\b/u', '$1g', $s);
        $s = preg_replace('/(\d+(?:\.\d+)?)\s*(cl)\b/u', '$1cl', $s); // keep cl (e.g. 75cl) for parsing
        $s = preg_replace('/(\d)\s+(ml|l|kg|g|cl)\b/u', '$1$2', $s);
        $s = preg_replace('/\b(\d+(?:\.\d+)?)\s*x\s*(\d+)\b/u', '$1 x$2', $s);
        $s = preg_replace('/[^a-z0-9\.\s]/u', ' ', $s);
        $s = preg_replace('/\s+/u', ' ', trim($s));
        return $s;
    }

    protected function normSize(string $s): string { return $this->norm($s); }

    protected function tokens(string $s): array
    {
        $n = $this->norm($s);
        return array_values(array_filter(explode(' ', $n), fn($t) => $t !== ''));
    }

    protected function coreTokens(array $tokens): array
    {
        $stop = array_flip(['oil','sachet','jar','refill','promo','by','x','the','and','with','new','pack','carton','pcs','pc','piece','pieces','bag','tin','can','bottle','pouch','powder','drink','milk','tea','flavour','flavored']);
        $core = [];
        foreach ($tokens as $t) {
            if (isset($stop[$t])) continue;
            if (preg_match('/^\d+(?:\.\d+)?(?:ml|l|kg|g)?$/', $t)) continue; // drop pure size/pack tokens from core name
            if (preg_match('/^x\d+$/', $t)) continue;
            $core[] = $t;
        }
        return array_values(array_unique($core));
    }

    protected function sizeSig(string $size, string $name = ''): array
    {
        $s = $this->norm(trim($size . ' ' . $name));
        $amount = null; $unit = null; $pack = null;
        if (preg_match('/(\d+(?:\.\d+)?)\s*(ml|cl|kg|g|l)\b/u', $s, $m)) { $amount = $this->normAmount($m[1], $m[2]); $unit = $m[2]; }
        if (preg_match('/\bx\s*(\d+)\b/u', $s, $m)) $pack = (int)$m[1];
        elseif (preg_match('/\b(\d{2,3})\b/u', $s, $m) && !$pack) { /* don't guess pack from random numbers */ }
        $has = $amount !== null || ($size !== '' && trim($size) !== '');
        return ['amount'=>$amount,'unit'=>$unit,'pack'=>$pack,'has_size'=>$has,'raw'=>$s];
    }

    protected function productSig(string $dbName): array
    {
        return $this->sizeSig('', $dbName);
    }

    protected function normAmount(string $n, string $unit): string
    {
        $v = (float)$n;
        // normalize to base for comparison: cl/l->ml, kg->g
        if ($unit === 'l') return (string)round($v * 1000, 3).'ml';
        if ($unit === 'cl') return (string)round($v * 10, 3).'ml';
        if ($unit === 'kg') return (string)round($v * 1000, 3).'g';
        return (string)round($v, 3).$unit;
    }

    protected function compareSigs(array $excel, array $db): array
    {
        $ok = true; $points = 0; $parts = []; $soft = [];
        if ($excel['amount'] && $db['amount']) {
            if ($excel['amount'] === $db['amount']) { $points += 25; $parts[]='size ok'; }
            elseif ($this->amountClose($excel['amount'], $db['amount'])) { $ok = false; $points += 18; $parts[]='size close (not exact) '.$excel['amount'].'~'.$db['amount']; $soft['amount_close']=true; }
            else { $ok = false; $parts[]='size diff '.$excel['amount'].' vs '.$db['amount']; }
        } elseif ($excel['amount'] && !$db['amount']) {
            $points += 5; $parts[]='db size unclear'; $ok = false; $soft['db_size_unclear']=true;
        } else {
            $parts[]='size n/a';
        }
        if ($excel['pack'] && $db['pack']) {
            if ($excel['pack'] === $db['pack']) { $points += 15; $parts[]='pack ok'; }
            else { $ok = false; $parts[]='pack diff x'.$excel['pack'].' vs x'.$db['pack']; $soft['pack_diff']=true; }
        } elseif ($excel['pack'] && !$db['pack']) {
            $ok = false; $points += 5; $parts[]='pack missing in db'; $soft['pack_missing']=true;
        }
        return ['ok'=>$ok,'points'=>$points,'detail'=>implode(', ',$parts),'soft'=>$soft];
    }

    protected function amountClose(string $a, string $b): bool
    {
        // e.g. 2700ml vs 2750ml (within ~5%)
        preg_match('/^([\d\.]+)(ml|g)$/', $a, $ma); preg_match('/^([\d\.]+)(ml|g)$/', $b, $mb);
        if (!$ma || !$mb || $ma[2] !== $mb[2]) return false;
        $va=(float)$ma[1]; $vb=(float)$mb[1]; $max=max($va,$vb);
        return $max > 0 && (abs($va-$vb)/$max) <= 0.05;
    }

    protected function variantOk(string $excelText, string $dbName): bool
    {
        $en = $this->norm($excelText); $dn = $this->norm($dbName);
        // flavor/variant conflicts
        $variants = ['banana','vanilla','chocolate','cocoa','strawberry','apple','plain','ginger','pepper','chicken','beef','onion','tomato','coconut','orange'];
        $eFound = []; $dFound = [];
        foreach ($variants as $v) { if (str_contains($en, $v)) $eFound[]=$v; if (str_contains($dn, $v)) $dFound[]=$v; }
        if (!empty($eFound) && !empty($dFound) && count(array_intersect($eFound, $dFound)) === 0) return false;

        // strong distinguishing tokens: if either side has one, other must not contradict
        $e3 = (bool)preg_match('/\b3in1\b/', $en); $d3 = (bool)preg_match('/\b3in1\b/', $dn);
        if ($e3 !== $d3 && ($e3 || $d3)) return false; // 3-in-1 vs non-3-in-1
        $eEvap = str_contains($en,'evap'); $dEvap = str_contains($dn,'evap');
        if ($eEvap && !$dEvap) return false;
        if ($dEvap && !$eEvap && str_contains($en,'dano')) return false; // evap product vs non-evap dano line
        $eCan = str_contains($en,' can') || preg_match('/\bcan\b/', $en); $dCan = str_contains($dn,' can') || preg_match('/\bcan\b/', $dn);
        // "can" is weak - don't hard fail, just don't conflict
        $e123 = (bool)preg_match('/\b123\b/', $en); $d123 = str_contains($dn,'123');
        $eFull = str_contains($en,'fullcream');
        $dFull = str_contains($dn,'fullcream');
        $eCool = str_contains($en,'coolcow');
        $dCool = str_contains($dn,'coolcow');
        $e123 = (bool)preg_match('/\b123\b/', $en);
        $d123 = (bool)preg_match('/\b123\b/', $dn);
        $eSabi = str_contains($en,'sabi'); $dSabi = str_contains($dn,'sabi');
        $eSlim = (bool)preg_match('/\bslim\b/', $en); $dSlim = (bool)preg_match('/\bslim\b/', $dn);
        $eGood = str_contains($en,'goodtime'); $dGood = str_contains($dn,'goodtime');
        // Dano dairy line conflicts
        $danoBoth = str_contains($en,'dano') || str_contains($dn,'dano');
        if ($danoBoth) {
            if ($e123 && ($dFull || $dCool || $dSabi)) return false;
            if ($d123 && ($eFull || $eCool || $eSabi)) return false;
            if ($eFull && ($dCool || $dSabi)) return false;
            if ($dFull && ($eCool || $eSabi)) return false;
            if ($eCool && ($dFull || $dSabi || $d123)) return false;
            if ($dCool && ($eFull || $eSabi || $e123)) return false;
            if ($eSabi !== $dSabi && ($eSabi || $dSabi)) return false;
            if ($eGood !== $dGood && ($eGood || $dGood)) return false;
            if ($eSlim && !$dSlim && str_contains($dn,'fullcream')) return false;
        }
        // refill vs jar vs generic - if excel specifies jar, prefer jar; if specifies refill, prefer refill
        $eJar = str_contains($en,'jar'); $dJar = str_contains($dn,'jar');
        $eRefill = str_contains($en,'refill'); $dRefill = str_contains($dn,'refill');
        // these are soft; don't hard fail on refill/jar alone except clear conflicts
        return true;
    }

    protected function formatPrice($v): string
    {
        $n = (float)$v;
        return number_format($n, 2, '.', '');
    }
}