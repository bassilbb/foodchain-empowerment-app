<?php

namespace App\Services\Marketplace\PriceImport;

use App\Models\Marketplace\Category;
use App\Models\Marketplace\PriceImportBatch;
use App\Models\Marketplace\PriceImportItem;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;

/**
 * Supplier-scoped product matching.
 *
 * Priority: SKU -> supplier product code -> barcode -> name (+ category).
 * Ambiguous or cross-supplier records are never auto-matched.
 */
class ProductMatchingService
{
    /**
     * @param  array<int, array{row_number: int, raw: array<string, mixed>, fields: array<string, string|null>}>  $rows
     * @param  array<string, mixed>  $options
     * @return array{items: array<int, array<string, mixed>>, missing: array<int, array<string, mixed>>, summary: array<string, int>}
     */
    public function analyse(Vendor $vendor, array $rows, array $options = []): array
    {
        $products = Product::with('category')
            ->withCount('statePrices')
            ->where('vendor_id', $vendor->id)
            ->get();

        $categories = Category::query()->get(['id', 'name', 'slug']);
        $categoryByName = [];
        foreach ($categories as $category) {
            $categoryByName[$this->normalizeName($category->name)] = (int) $category->id;
            $categoryByName[$this->normalizeName($category->slug)] = (int) $category->id;
        }

        $defaultCategoryId = $this->resolveDefaultCategory($options['default_category_id'] ?? null, $categories);

        $index = $this->buildIndexes($products, $rows);

        $duplicates = $this->detectFileDuplicates($rows);

        $items = [];
        $matchedProductIds = [];

        foreach ($rows as $row) {
            $fields = $row['fields'];
            $rowNumber = (int) $row['row_number'];

            $errors = [];
            $warnings = [];

            $sku = $this->clean($fields['sku'] ?? null);
            $code = $this->clean($fields['supplier_product_code'] ?? null);
            $barcode = $this->clean($fields['barcode'] ?? null);
            $name = $this->clean($fields['product_name'] ?? null);
            $categoryName = $this->clean($fields['category_name'] ?? null);
            $unit = $this->clean($fields['unit'] ?? null);
            $priceRaw = $fields['price'] ?? null;

            if ($name === null) {
                $errors[] = 'Product name is missing.';
            }

            [$price, $priceError] = $this->parsePrice($priceRaw);
            if ($priceError !== null) {
                $errors[] = $priceError;
            }

            foreach ($duplicates as $type => $groups) {
                $value = match ($type) {
                    'sku' => $sku,
                    'supplier_product_code' => $code,
                    'barcode' => $barcode,
                    default => null,
                };

                if ($value === null) {
                    continue;
                }

                $key = $this->normalizeIdentifier($value);
                if (isset($groups[$key]) && count($groups[$key]) > 1) {
                    $errors[] = sprintf(
                        'Duplicate %s "%s" appears on rows %s.',
                        str_replace('_', ' ', $type),
                        $value,
                        implode(', ', $groups[$key]),
                    );
                }
            }

            if ($errors !== []) {
                $items[] = $this->item($rowNumber, $fields, [
                    'product_id' => null,
                    'current_price' => null,
                    'price' => $price,
                    'new_price' => $price,
                    'difference' => null,
                    'percent_change' => null,
                    'status' => PriceImportItem::STATUS_INVALID,
                    'match_method' => null,
                    'change_type' => PriceImportItem::CHANGE_INVALID,
                    'errors' => array_values(array_unique($errors)),
                    'warnings' => $warnings,
                    'requires_manual_completion' => false,
                ]);

                continue;
            }

            $candidate = null;
            $method = null;
            $ambiguousReason = null;
            $wrongSupplier = null;

            // Identifier lookups are global so we can detect another supplier's SKU.
            $identifierCandidates = [];

            if ($sku !== null) {
                $global = $index['global']['sku'][$this->normalizeIdentifier($sku)] ?? null;
                if ($global !== null) {
                    $identifierCandidates['sku'] = $global;
                }
            }

            if ($code !== null) {
                $global = $index['global']['supplier_product_code'][$this->normalizeIdentifier($code)] ?? null;
                if ($global !== null) {
                    $identifierCandidates['supplier_product_code'] = $global;
                }
            }

            if ($barcode !== null) {
                $global = $index['global']['barcode'][$this->normalizeIdentifier($barcode)] ?? null;
                if ($global !== null) {
                    $identifierCandidates['barcode'] = $global;
                }
            }

            foreach ($identifierCandidates as $type => $product) {
                if ((int) $product->vendor_id !== (int) $vendor->id) {
                    $wrongSupplier = sprintf(
                        'The %s "%s" belongs to another supplier (%s). This record was not matched.',
                        str_replace('_', ' ', $type),
                        match ($type) {
                            'sku' => $sku,
                            'supplier_product_code' => $code,
                            default => $barcode,
                        },
                        $product->vendor?->name ?? 'supplier #'.$product->vendor_id,
                    );
                    break;
                }
            }

            if ($wrongSupplier !== null) {
                $items[] = $this->item($rowNumber, $fields, [
                    'product_id' => null,
                    'current_price' => null,
                    'price' => $price,
                    'new_price' => $price,
                    'difference' => null,
                    'percent_change' => null,
                    'status' => PriceImportItem::STATUS_INVALID,
                    'match_method' => null,
                    'change_type' => PriceImportItem::CHANGE_INVALID,
                    'errors' => [$wrongSupplier],
                    'warnings' => $warnings,
                    'requires_manual_completion' => false,
                ]);

                continue;
            }

            $vendorCandidates = [];
            foreach ($identifierCandidates as $type => $product) {
                $vendorCandidates[(int) $product->id] = ['product' => $product, 'method' => $type];
            }

            if (count($vendorCandidates) > 1) {
                $ambiguousReason = 'The SKU, supplier code and barcode point to different products.';
            } elseif (count($vendorCandidates) === 1) {
                $entry = array_values($vendorCandidates)[0];
                $candidate = $entry['product'];
                $method = $entry['method'];
            }

            if ($candidate === null && $ambiguousReason === null && $name !== null) {
                $nameKey = $this->normalizeName($name);
                $nameMatches = $index['vendor']['name'][$nameKey] ?? [];

                if (count($nameMatches) > 1) {
                    $ambiguousReason = 'Multiple products share this name. Select the correct product manually.';
                } elseif (count($nameMatches) === 1) {
                    $possible = $nameMatches[0];

                    if ($categoryName !== null && $possible->category !== null) {
                        if ($this->normalizeName($possible->category->name) !== $this->normalizeName($categoryName)) {
                            $ambiguousReason = sprintf(
                                'The name matches "%s" but its category is "%s", not "%s".',
                                $possible->name,
                                $possible->category->name,
                                $categoryName,
                            );
                        }
                    }

                    if ($ambiguousReason === null) {
                        $candidate = $possible;
                        $method = 'name';
                    }
                }
            }

            if ($ambiguousReason !== null) {
                $items[] = $this->item($rowNumber, $fields, [
                    'product_id' => null,
                    'current_price' => null,
                    'price' => $price,
                    'new_price' => $price,
                    'difference' => null,
                    'percent_change' => null,
                    'status' => PriceImportItem::STATUS_AMBIGUOUS,
                    'match_method' => null,
                    'change_type' => PriceImportItem::CHANGE_AMBIGUOUS,
                    'errors' => [$ambiguousReason],
                    'warnings' => $warnings,
                    'requires_manual_completion' => false,
                ]);

                continue;
            }

            if ($candidate === null) {
                $resolvedCategoryId = $categoryName !== null
                    ? ($categoryByName[$this->normalizeName($categoryName)] ?? null)
                    : null;

                if ($resolvedCategoryId === null) {
                    $resolvedCategoryId = $defaultCategoryId;
                }

                $manual = $resolvedCategoryId === null;
                if ($manual) {
                    $warnings[] = 'A category is required before this product can be created. Choose a default category or complete it manually.';
                }

                if ($sku === null) {
                    $warnings[] = 'No SKU supplied; a FoodChain SKU will be generated when the product is created.';
                }

                $raw = $row['raw'];
                $raw['_resolved_category_id'] = $resolvedCategoryId;
                $raw['_generate_sku'] = $sku === null;

                $items[] = $this->item($rowNumber, $fields, [
                    'product_id' => null,
                    'current_price' => null,
                    'price' => $price,
                    'new_price' => $price,
                    'difference' => null,
                    'percent_change' => null,
                    'status' => PriceImportItem::STATUS_NEW,
                    'match_method' => 'none',
                    'change_type' => PriceImportItem::CHANGE_NEW,
                    'errors' => [],
                    'warnings' => $warnings,
                    'requires_manual_completion' => $manual,
                    'raw' => $raw,
                ]);

                continue;
            }

            $matchedProductIds[] = (int) $candidate->id;

            $currentPrice = round((float) $candidate->price, 2);
            $newPrice = round((float) $price, 2);
            $difference = round($newPrice - $currentPrice, 2);
            $percent = $currentPrice > 0 ? round(($difference / $currentPrice) * 100, 2) : null;

            if (abs($difference) < 0.01) {
                $change = PriceImportItem::CHANGE_UNCHANGED;
            } elseif ($difference > 0) {
                $change = PriceImportItem::CHANGE_INCREASE;
            } else {
                $change = PriceImportItem::CHANGE_DECREASE;
            }

            if ((int) ($candidate->state_prices_count ?? 0) > 0) {
                $warnings[] = 'This product has state-specific prices that override the base price in those states.';
            }

            if ($candidate->discount_price !== null && (float) $candidate->discount_price < $currentPrice) {
                $warnings[] = 'This product has a discount price; the storefront may keep showing the discounted price.';
            }

            if ($candidate->member_price !== null) {
                $warnings[] = 'This product has a member price; member pricing was left unchanged.';
            }

            if (! $candidate->is_active) {
                $warnings[] = 'This product is currently inactive. Its price will change but it stays hidden from the storefront.';
            }

            if ($candidate->approval_status !== 'approved') {
                $warnings[] = 'This product is not approved; the price will change but it is not visible on the storefront.';
            }

            $items[] = $this->item($rowNumber, $fields, [
                'product_id' => (int) $candidate->id,
                'current_price' => $currentPrice,
                'price' => $price,
                'new_price' => $newPrice,
                'difference' => $difference,
                'percent_change' => $percent,
                'status' => PriceImportItem::STATUS_MATCHED,
                'match_method' => $method,
                'change_type' => $change,
                'errors' => [],
                'warnings' => $warnings,
                'requires_manual_completion' => false,
            ]);
        }

        $missing = $this->findMissingProducts($vendor, $products, array_values(array_unique($matchedProductIds)), $options);

        $summary = [
            'total_records' => count($items),
            'matched_count' => count(array_filter($items, fn ($i) => $i['status'] === PriceImportItem::STATUS_MATCHED)),
            'increased_count' => count(array_filter($items, fn ($i) => $i['change_type'] === PriceImportItem::CHANGE_INCREASE)),
            'reduced_count' => count(array_filter($items, fn ($i) => $i['change_type'] === PriceImportItem::CHANGE_DECREASE)),
            'unchanged_count' => count(array_filter($items, fn ($i) => $i['change_type'] === PriceImportItem::CHANGE_UNCHANGED)),
            'new_count' => count(array_filter($items, fn ($i) => $i['status'] === PriceImportItem::STATUS_NEW)),
            'invalid_count' => count(array_filter($items, fn ($i) => $i['status'] === PriceImportItem::STATUS_INVALID)),
            'ambiguous_count' => count(array_filter($items, fn ($i) => $i['status'] === PriceImportItem::STATUS_AMBIGUOUS)),
            'manual_count' => count(array_filter($items, fn ($i) => $i['status'] === PriceImportItem::STATUS_NEW && $i['requires_manual_completion'])),
            'missing_count' => count($missing),
        ];

        return ['items' => $items, 'missing' => $missing, 'summary' => $summary];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Product>  $products
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<string, mixed>
     */
    private function buildIndexes($products, array $rows): array
    {
        $index = [
            'vendor' => ['sku' => [], 'supplier_product_code' => [], 'barcode' => [], 'name' => []],
            'global' => ['sku' => [], 'supplier_product_code' => [], 'barcode' => []],
        ];

        foreach ($products as $product) {
            if ($product->sku) {
                $index['vendor']['sku'][$this->normalizeIdentifier($product->sku)] = $product;
            }
            if ($product->supplier_product_code) {
                $index['vendor']['supplier_product_code'][$this->normalizeIdentifier($product->supplier_product_code)][] = $product;
            }
            if ($product->barcode) {
                $index['vendor']['barcode'][$this->normalizeIdentifier($product->barcode)][] = $product;
            }

            $index['vendor']['name'][$this->normalizeName($product->name)][] = $product;
        }

        // Flatten single-candidate identifier arrays.
        foreach (['supplier_product_code', 'barcode'] as $type) {
            foreach ($index['vendor'][$type] as $key => $list) {
                $index['vendor'][$type][$key] = count($list) === 1 ? $list[0] : $list;
            }
        }

        $skus = [];
        $codes = [];
        $barcodes = [];
        foreach ($rows as $row) {
            foreach (['sku' => 'sku', 'supplier_product_code' => 'supplier_product_code', 'barcode' => 'barcode'] as $field => $type) {
                $value = $this->clean($row['fields'][$field] ?? null);
                if ($value === null) {
                    continue;
                }
                $key = $this->normalizeIdentifier($value);
                match ($type) {
                    'sku' => $skus[$key] = $value,
                    'supplier_product_code' => $codes[$key] = $value,
                    'barcode' => $barcodes[$key] = $value,
                };
            }
        }

        if ($skus === [] && $codes === [] && $barcodes === []) {
            $globals = collect();
        } else {
            $globals = Product::with('vendor:id,name')
                ->where(function ($query) use ($skus, $codes, $barcodes) {
                    if ($skus !== []) {
                        $query->orWhereIn('sku', array_values($skus));
                    }
                    if ($codes !== []) {
                        $query->orWhereIn('supplier_product_code', array_values($codes));
                    }
                    if ($barcodes !== []) {
                        $query->orWhereIn('barcode', array_values($barcodes));
                    }
                })
                ->get(['id', 'vendor_id', 'name', 'sku', 'supplier_product_code', 'barcode', 'price', 'category_id']);
        }

        foreach ($globals as $product) {
            if ($product->sku) {
                $index['global']['sku'][$this->normalizeIdentifier($product->sku)] ??= $product;
            }
            if ($product->supplier_product_code) {
                $index['global']['supplier_product_code'][$this->normalizeIdentifier($product->supplier_product_code)] ??= $product;
            }
            if ($product->barcode) {
                $index['global']['barcode'][$this->normalizeIdentifier($product->barcode)] ??= $product;
            }
        }

        return $index;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<string, array<string, array<int, int>>>
     */
    private function detectFileDuplicates(array $rows): array
    {
        $groups = ['sku' => [], 'supplier_product_code' => [], 'barcode' => []];

        foreach ($rows as $row) {
            foreach ($groups as $type => $_) {
                $value = $this->clean($row['fields'][$type] ?? null);
                if ($value === null) {
                    continue;
                }
                $groups[$type][$this->normalizeIdentifier($value)][] = (int) $row['row_number'];
            }
        }

        foreach ($groups as $type => $entries) {
            foreach ($entries as $key => $rowNumbers) {
                if (count($rowNumbers) < 2) {
                    unset($groups[$type][$key]);
                }
            }
        }

        return $groups;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Product>  $vendorProducts
     * @param  array<int, int>  $matchedProductIds
     * @param  array<string, mixed>  $options
     * @return array<int, array<string, mixed>>
     */
    private function findMissingProducts(Vendor $vendor, $vendorProducts, array $matchedProductIds, array $options): array
    {
        $previous = PriceImportBatch::query()
            ->where('vendor_id', $vendor->id)
            ->whereIn('status', [PriceImportBatch::STATUS_COMPLETED, PriceImportBatch::STATUS_PARTIALLY_APPLIED])
            ->when($options['exclude_batch_id'] ?? null, fn ($q, $id) => $q->where('id', '!=', $id))
            ->latest('id')
            ->first();

        if ($previous !== null) {
            $baselineIds = $previous->items()
                ->whereNotNull('product_id')
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->all();
        } else {
            $baselineIds = $vendorProducts->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        $missingIds = array_values(array_diff($baselineIds, $matchedProductIds));

        if ($missingIds === []) {
            return [];
        }

        return Product::query()
            ->whereIn('id', $missingIds)
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'price'])
            ->map(fn (Product $product) => [
                'product_id' => (int) $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku,
                'current_price' => round((float) $product->price, 2),
            ])
            ->all();
    }

    private function resolveDefaultCategory(mixed $value, $categories): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        $id = (int) $value;

        return $categories->contains(fn ($category) => (int) $category->id === $id) ? $id : null;
    }

    /**
     * @param  array<string, string|null>  $fields
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function item(int $rowNumber, array $fields, array $overrides): array
    {
        return array_merge([
            'row_number' => $rowNumber,
            'product_id' => null,
            'sku' => $this->clean($fields['sku'] ?? null),
            'supplier_product_code' => $this->clean($fields['supplier_product_code'] ?? null),
            'barcode' => $this->clean($fields['barcode'] ?? null),
            'product_name' => $this->clean($fields['product_name'] ?? null),
            'category_name' => $this->clean($fields['category_name'] ?? null),
            'unit' => $this->clean($fields['unit'] ?? null),
            'price' => null,
            'current_price' => null,
            'new_price' => null,
            'difference' => null,
            'percent_change' => null,
            'status' => PriceImportItem::STATUS_INVALID,
            'match_method' => null,
            'change_type' => PriceImportItem::CHANGE_INVALID,
            'errors' => [],
            'warnings' => [],
            'raw' => null,
            'requires_manual_completion' => false,
        ], $overrides);
    }

    private function clean(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * @return array{0: float|null, 1: string|null}
     */
    private function parsePrice(mixed $value): array
    {
        if ($value === null || trim((string) $value) === '') {
            return [null, 'Price is missing.'];
        }

        $raw = str_replace(["\xC2\xA0", ' ', ','], '', trim((string) $value));
        $clean = preg_replace('/[^0-9.\-]/', '', $raw) ?? '';

        if ($clean === '' || $clean === '-' || ! is_numeric($clean)) {
            return [null, 'Price "'.trim((string) $value).'" is not a valid number.'];
        }

        $price = round((float) $clean, 2);

        if ($price < 0) {
            return [null, 'Price cannot be negative.'];
        }

        if ($price == 0.0) {
            return [null, 'Price must be greater than zero.'];
        }

        return [$price, null];
    }

    private function normalizeIdentifier(string $value): string
    {
        return strtoupper(preg_replace('/\s+/', '', trim($value)) ?? trim($value));
    }

    private function normalizeName(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?? $value;

        return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
    }
}
