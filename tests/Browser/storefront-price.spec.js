import { test, expect } from '@playwright/test';

/**
 * Proves that a bulk supplier price update is what the shopper actually pays:
 * storefront listing -> product page -> cart, and that a rollback reverses it.
 *
 * Two isolated browser contexts are used: one admin (applies the price list)
 * and one customer (browses and buys).
 */
const BASE_URL = 'http://127.0.0.1:8123';

const ADMIN_EMAIL = 'playwright-admin@foodchain.test';
const ADMIN_PASSWORD = 'password';

const CUSTOMER_EMAIL = 'playwright-customer@foodchain.test';
const CUSTOMER_PASSWORD = 'password';

// Headline price on the storefront product page (a bare `.text-emerald-700`
// also matches decorative separator spans).
const STORE_PRICE = 'p.text-3xl.text-emerald-700';

const CSV = [
  'sku,supplier_product_code,barcode,product_name,category,unit,price',
  'PW-0001,,,Browser Rice,Groceries,5kg,150',
].join('\n');

async function loginAdmin(page) {
  await page.goto('/admin/login');
  await page.fill('#email', ADMIN_EMAIL);
  await page.fill('#password', ADMIN_PASSWORD);
  await Promise.all([
    page.waitForURL('**/admin/dashboard'),
    page.getByRole('button', { name: 'Sign In' }).click(),
  ]);
}

async function loginCustomer(page) {
  await page.goto('/login');
  await page.fill('#email', CUSTOMER_EMAIL);
  await page.fill('#password', CUSTOMER_PASSWORD);
  await page.getByRole('button', { name: 'Sign In' }).click();
  await page.waitForURL((url) => !url.pathname.startsWith('/login'));
}

test('a bulk price update reaches the storefront listing, product page and cart', async ({ browser }) => {
  const adminContext = await browser.newContext({ baseURL: BASE_URL });
  const customerContext = await browser.newContext({ baseURL: BASE_URL });
  const admin = await adminContext.newPage();
  const customer = await customerContext.newPage();

  admin.on('dialog', (dialog) => dialog.accept());

  // ---- Baseline: shopper sees and buys at the old price -------------------
  await loginCustomer(customer);

  await customer.goto('/shop/all');
  const listingCard = customer.locator('div.group:has(a[aria-label="Browser Rice"])').first();
  await expect(listingCard).toContainText('Browser Rice');
  await expect(listingCard).toContainText('100');

  await customer.goto('/shop/product/browser-rice');
  await expect(customer.locator(STORE_PRICE)).toContainText('100.00');

  await customer.locator('#add-cart-form').getByRole('button', { name: 'Add to Cart' }).click();
  await customer.goto('/marketplace/cart');
  await expect(customer.getByText('100.00').first()).toBeVisible();

  // ---- Admin uploads and applies a new supplier price list ----------------
  await loginAdmin(admin);
  await admin.goto('/admin/marketplace/price-imports');
  await admin.selectOption('#vendor_id', { index: 1 });
  await admin.setInputFiles('#file', {
    name: 'supplier-prices.csv',
    mimeType: 'text/csv',
    buffer: Buffer.from(CSV),
  });
  await admin.getByRole('button', { name: 'Upload & Preview' }).click();
  await admin.waitForURL(/\/admin\/marketplace\/price-imports\/\d+$/);
  const batchUrl = admin.url();

  await admin.check('input[name="confirm_review"]');
  await admin.getByRole('button', { name: 'Apply Price Updates' }).click();
  await expect(admin.getByRole('heading', { name: 'Applied' })).toBeVisible();

  // ---- Shopper now sees and buys at the new price -------------------------
  await customer.goto('/shop/all');
  const updatedCard = customer.locator('div.group:has(a[aria-label="Browser Rice"])').first();
  await expect(updatedCard).toContainText('150');

  await customer.goto('/shop/product/browser-rice');
  await expect(customer.locator(STORE_PRICE)).toContainText('150.00');

  await customer.goto('/marketplace/cart');
  await expect(customer.getByText('150.00').first()).toBeVisible();

  // ---- Rollback restores the shopper's price ------------------------------
  await admin.goto(batchUrl);
  await admin.fill('#reason', 'Playwright storefront verification');
  await admin.check('input[name="confirm_rollback"]');
  await admin.getByRole('button', { name: 'Rollback Prices' }).click();
  await expect(admin.getByText(/restored/i).first()).toBeVisible();

  await customer.goto('/shop/product/browser-rice');
  await expect(customer.locator(STORE_PRICE)).toContainText('100.00');

  await customer.goto('/marketplace/cart');
  await expect(customer.getByText('100.00').first()).toBeVisible();

  await adminContext.close();
  await customerContext.close();
});
