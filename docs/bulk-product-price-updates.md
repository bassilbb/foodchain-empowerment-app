# Bulk Product Price Updates

Two-stage supplier price-list import: **upload → preview → confirm → apply → audit → rollback**.
Only the selling price (`marketplace_products.price`) is ever changed.

## Admin instructions

1. Go to **Admin → Marketplace → Bulk Price Update**
   (`/admin/marketplace/price-imports`).
2. Pick the **supplier** the file belongs to. A file can only ever match that
   supplier's products.
3. Choose a **CSV** or **XML** file (max 10 MB). Download **Sample CSV** or
   **Sample XML** for the expected columns.
4. Optional: tick **Create new products** and pick a default category. New
   products without a resolvable category are flagged for manual completion
   instead of being created.
5. Click **Upload & Preview**. Nothing is written to products yet.
6. Review the preview:
   - Price Increased / Price Reduced / Unchanged / New Product / Invalid / Ambiguous
   - **Products Not Found in Latest Upload** (never deleted automatically)
   - per-row errors, match method (SKU → supplier code → barcode → name)
   - warnings for rows that also have discounts or state prices (those are untouched)
7. Tick **I have reviewed these changes**, then **Apply Price Updates**.
   Files over 500 rows are queued when `QUEUE_CONNECTION` is not `sync`.
8. To undo a completed batch, open it and use **Rollback Prices** with a reason.
   Rollback restores the old price only where the current price still equals the
   batch's applied price; newer price changes are skipped. Products created by
   the batch are left in place.

## File format

CSV header (order is auto-detected, extra columns ignored):

```csv
sku,supplier_product_code,barcode,product_name,category,unit,price
OPEY-0001,SUP-1001,6151100012345,Golden Penny Spaghetti,Food Items,500g,12500
```

XML:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<products>
  <product>
    <sku>OPEY-0001</sku>
    <supplier_product_code>SUP-1001</supplier_product_code>
    <barcode>6151100012345</barcode>
    <product_name>Golden Penny Spaghetti</product_name>
    <category>Food Items</category>
    <unit>500g</unit>
    <price>12500</price>
  </product>
</products>
```

`price` must be a positive number. Rows with a missing name, missing price,
zero/negative price, non-numeric price, duplicate SKU/supplier code/barcode, or
an ambiguous name match are reported and never applied.

## Matching rules

Priority per row: **SKU → supplier product code → barcode → name + category**.
Name matching is only accepted when exactly one product of that supplier
matches; otherwise the row is `ambiguous` and skipped.

## Audit trail

Every applied change creates a `marketplace_product_price_histories` row with
product ID/name/SKU, supplier, old price, new price, difference, percentage,
batch ID, filename, admin, timestamp, source and status. The same history is
shown on the admin product page and exported from the batch page
(items / errors / history CSV).

## Code map

| Area | Path |
| --- | --- |
| Controller | `app/Http/Controllers/Admin/ProductPriceImportController.php` |
| Preview/apply | `app/Services/Marketplace/PriceImport/ProductPriceImportService.php` |
| Matching | `app/Services/Marketplace/PriceImport/ProductMatchingService.php` |
| Rollback | `app/Services/Marketplace/PriceImport/PriceImportRollbackService.php` |
| Parsers | `app/Services/Marketplace/PriceImport/{Csv,Xml}ProductPriceParser.php` |
| Queue job | `app/Jobs/ApplyPriceImportBatch.php` |
| Models | `app/Models/Marketplace/{PriceImportBatch,PriceImportItem,PriceImportMissingProduct,ProductPriceHistory}.php` |
| Views | `resources/views/admin/marketplace/price-imports/` |
| Migrations | `database/migrations/2026_11_15_00000{1,2,3}_*.php` |

## Tests

```bash
php artisan test tests/Feature/Admin/BulkProductPriceImportTest.php
npx playwright test
```

Playwright runs against a throwaway SQLite database
(`database/playwright.sqlite`) seeded by `BrowserTestSeeder`, so the real
application database is never touched.
