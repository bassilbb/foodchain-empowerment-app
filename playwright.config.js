import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './tests/Browser',
  timeout: 90_000,
  expect: { timeout: 10_000 },
  fullyParallel: false,
  workers: 1,
  retries: 0,
  reporter: [['list']],
  use: {
    baseURL: 'http://127.0.0.1:8123',
    headless: true,
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
  },
  webServer: {
    command: 'node tests/Browser/serve.js',
    url: 'http://127.0.0.1:8123/admin/login',
    reuseExistingServer: false,
    timeout: 180_000,
  },
});
