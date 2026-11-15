<?php

namespace App\Services\Marketplace\PriceImport;

/**
 * Resolves supplier column/element names onto the canonical import fields.
 *
 * Suppliers rarely agree on header names ("SKU", "Product Code", "Item Code"),
 * so uploads are auto-detected and can still be overridden by the admin.
 */
class FieldMapping
{
    public const FIELDS = [
        'sku' => 'SKU',
        'supplier_product_code' => 'Supplier product code',
        'barcode' => 'Barcode / product identifier',
        'product_name' => 'Product name',
        'category_name' => 'Category',
        'unit' => 'Unit / pack size',
        'price' => 'Price',
    ];

    public const REQUIRED = ['product_name', 'price'];

    private const ALIASES = [
        'sku' => ['sku', 'product_sku', 'item_sku', 'stock_code', 'stock_keeping_unit', 'sku_code'],
        'supplier_product_code' => [
            'supplier_product_code', 'supplier_code', 'supplier_sku', 'vendor_code', 'vendor_sku',
            'product_code', 'item_code', 'catalogue_code', 'catalog_code', 'manufacturer_code',
        ],
        'barcode' => ['barcode', 'bar_code', 'ean', 'ean13', 'upc', 'gtin', 'product_identifier', 'identifier'],
        'product_name' => ['product_name', 'name', 'product', 'item_name', 'title', 'description', 'item'],
        'category_name' => ['category', 'category_name', 'product_category', 'department', 'group'],
        'unit' => ['unit', 'uom', 'unit_of_measure', 'pack_size', 'size', 'packaging'],
        'price' => [
            'price', 'selling_price', 'new_price', 'unit_price', 'amount', 'cost',
            'list_price', 'retail_price', 'price_ngn',
        ],
    ];

    public static function normalizeHeader(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header);
        $header = strtolower(trim($header));
        $header = preg_replace('/[^a-z0-9]+/', '_', $header) ?? '';

        return trim($header, '_');
    }

    /**
     * @param  array<int, string>  $headers
     * @return array<string, string> canonical field => original header
     */
    public static function suggest(array $headers): array
    {
        $normalized = [];
        foreach ($headers as $header) {
            $key = self::normalizeHeader((string) $header);
            if ($key !== '' && ! array_key_exists($key, $normalized)) {
                $normalized[$key] = (string) $header;
            }
        }

        $mapping = [];
        foreach (self::ALIASES as $field => $aliases) {
            foreach ($aliases as $alias) {
                if (array_key_exists($alias, $normalized)) {
                    $mapping[$field] = $normalized[$alias];
                    break;
                }
            }
        }

        return $mapping;
    }

    /**
     * Merge an admin-supplied mapping over the auto-detected one, dropping
     * values that do not exist in the file.
     *
     * @param  array<string, string>  $suggested
     * @param  array<string, mixed>  $provided
     * @param  array<int, string>  $headers
     * @return array<string, string>
     */
    public static function merge(array $suggested, array $provided, array $headers): array
    {
        $available = [];
        foreach ($headers as $header) {
            $available[(string) $header] = true;
        }

        $mapping = $suggested;

        foreach (array_keys(self::FIELDS) as $field) {
            $value = $provided[$field] ?? null;
            if (is_string($value) && $value !== '' && isset($available[$value])) {
                $mapping[$field] = $value;
            }
        }

        return $mapping;
    }

    public static function missingRequired(array $mapping): array
    {
        return array_values(array_filter(
            self::REQUIRED,
            fn (string $field) => empty($mapping[$field]),
        ));
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, string>  $mapping
     * @return array<string, string|null>
     */
    public static function apply(array $row, array $mapping): array
    {
        $out = [];

        foreach (array_keys(self::FIELDS) as $field) {
            $header = $mapping[$field] ?? null;
            $out[$field] = $header !== null && array_key_exists($header, $row)
                ? trim((string) $row[$header])
                : null;
        }

        return $out;
    }
}
