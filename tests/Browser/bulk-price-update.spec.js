import { test, expect } from '@playwright/test';

const ADMIN_EMAIL = 'playwright-admin@foodchain.test';
const ADMIN_PASSWORD = 'password';

// The big headline price on the storefront product page. A bare
// `.text-emerald-700` also matches decorative separator spans.
const STORE_PRICE = 'p.text-3xl.text-emerald-700';

const CSV = [
  'sku,supplier_product_code,barcode,product_name,category,unit,price',
  'PW-0001,,,Browser Rice,Groceries,5kg,150',
  'PW-0002,,,Browser Beans,Groceries,5kg,150',
  'PW-0003,,,Browser Garri,Groceries,5kg,300',
  'OTHER-0001,,,Foreign Item,Groceries,1kg,1',
].join('\n');

async function login(page) {
  await page.goto('/admin/login');
  await page.fill('#email', ADMIN_EMAIL);
  await page.fill('#password', ADMIN_PASSWORD);
  await Promise.all([
    page.waitForURL('**/admin/dashboard'),
    page.getByRole('button', { name: 'Sign In' }).click(),
  ]);
}

async function uploadFile(page, { name, mimeType, body }) {
  await page.goto('/admin/marketplace/price-imports');
  await page.selectOption('#vendor_id', { index: 1 });
  await page.setInputFiles('#file', { name, mimeType, buffer: Buffer.from(body) });
  await page.getByRole('button', { name: 'Upload & Preview' }).click();
}

test.beforeEach(async ({ page }) => {
  page.on('dialog', (dialog) => dialog.accept());
  await login(page);
});

test('admin can preview, apply, verify the storefront price and roll back a supplier upload', async ({ page }) => {
  await uploadFile(page, { name: 'supplier-prices.csv', mimeType: 'text/csv', body: CSV });

  await page.waitForURL(/\/admin\/marketplace\/price-imports\/\d+$/);
  const batchUrl = page.url();

  // Preview: nothing applied yet.
  await expect(page.getByRole('heading', { name: 'Confirm and apply' })).toBeVisible();
  await expect(page.getByText('Price Increased').first()).toBeVisible();
  await expect(page.getByText('Price Reduced').first()).toBeVisible();
  await expect(page.getByText('Unchanged').first()).toBeVisible();
  await expect(page.getByText(/another supplier/i).first()).toBeVisible();

  await page.check('input[name="confirm_review"]');
  await page.getByRole('button', { name: 'Apply Price Updates' }).click();

  await expect(page.getByRole('heading', { name: 'Applied' })).toBeVisible();
  await expect(page.getByText('Bulk upload').first()).toBeVisible();

  // Admin product page records the change. Scope the "View" link to the table
  // so it cannot match sidebar links such as "Marketplace Overview".
  await page.goto('/admin/marketplace/products?search=PW-0001');
  await page.locator('table').getByRole('link', { name: 'View', exact: true }).first().click();
  await expect(page).toHaveURL(/\/admin\/marketplace\/products\/\d+$/);
  await expect(page.getByText('Price Change History')).toBeVisible();
  await expect(page.getByText('Bulk upload').first()).toBeVisible();
  await expect(page.getByText('150.00').first()).toBeVisible();

  // Storefront reflects the new base price.
  await page.goto('/shop/product/browser-rice');
  await expect(page.locator(STORE_PRICE)).toContainText('150.00');

  // Roll the batch back and confirm the storefront returns to the old price.
  await page.goto(batchUrl);
  await page.fill('#reason', 'Playwright verification rollback');
  await page.check('input[name="confirm_rollback"]');
  await page.getByRole('button', { name: 'Rollback Prices' }).click();

  await expect(page.getByText(/restored/i).first()).toBeVisible();
  await expect(page.getByText('Rolled back').first()).toBeVisible();

  await page.goto('/shop/product/browser-rice');
  await expect(page.locator(STORE_PRICE)).toContainText('100.00');
});

test('a file for one supplier cannot touch another supplier product', async ({ page }) => {
  await uploadFile(page, {
    name: 'foreign.csv',
    mimeType: 'text/csv',
    body: 'sku,product_name,category,price\nOTHER-0001,Foreign Item,Groceries,1',
  });

  await page.waitForURL(/\/admin\/marketplace\/price-imports\/\d+$/);

  await expect(page.getByText(/another supplier/i).first()).toBeVisible();
  await expect(page.getByText('Invalid Record').first()).toBeVisible();

  // The foreign product keeps its price.
  await page.goto('/shop/product/foreign-item');
  await expect(page.locator(STORE_PRICE)).toContainText('500.00');
});

test('a malformed file is rejected with a readable error', async ({ page }) => {
  await uploadFile(page, {
    name: 'broken.csv',
    mimeType: 'text/csv',
    body: 'this is not a price list\nand neither is this',
  });

  await expect(page.locator('.alert-error')).toContainText(/could not be read/i);
});

test('sample CSV and XML templates download', async ({ page }) => {
  await page.goto('/admin/marketplace/price-imports');

  const csvDownload = page.waitForEvent('download');
  await page.getByRole('link', { name: 'Sample CSV' }).click();
  expect((await csvDownload).suggestedFilename()).toBe('foodchain-price-list-template.csv');

  const xmlDownload = page.waitForEvent('download');
  await page.getByRole('link', { name: 'Sample XML' }).click();
  expect((await xmlDownload).suggestedFilename()).toBe('foodchain-price-list-template.xml');
});
