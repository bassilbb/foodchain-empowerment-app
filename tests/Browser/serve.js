/**
 * Prepares a throwaway SQLite database and boots the Laravel dev server for
 * the Playwright browser tests. The real application database is never used.
 */
import { spawnSync, spawn } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '..', '..');
const dbPath = path.join(root, 'database', 'playwright.sqlite');

if (fs.existsSync(dbPath)) {
  fs.rmSync(dbPath, { force: true });
}
fs.writeFileSync(dbPath, '');

const env = {
  ...process.env,
  APP_ENV: 'local',
  APP_URL: 'http://127.0.0.1:8123',
  DB_CONNECTION: 'sqlite',
  DB_DATABASE: dbPath,
  DB_FOREIGN_KEYS: 'true',
  SESSION_DRIVER: 'file',
  CACHE_STORE: 'file',
  QUEUE_CONNECTION: 'sync',
};

function run(args) {
  const result = spawnSync('php', args, {
    cwd: root,
    env,
    stdio: 'inherit',
    shell: false,
  });

  if (result.error) {
    console.error(result.error);
    process.exit(1);
  }

  if (result.status !== 0) {
    process.exit(result.status ?? 1);
  }
}

run(['artisan', 'migrate:fresh', '--force']);
run(['artisan', 'db:seed', '--class=BrowserTestSeeder', '--force']);

const server = spawn(
  'php',
  ['artisan', 'serve', '--host=127.0.0.1', '--port=8123'],
  { cwd: root, env, stdio: 'inherit' },
);

const shutdown = () => {
  try {
    server.kill();
  } catch (error) {
    // The process may already have exited.
  }
  process.exit(0);
};

process.on('SIGTERM', shutdown);
process.on('SIGINT', shutdown);
process.on('exit', () => {
  try {
    server.kill();
  } catch (error) {
    // Ignore.
  }
});
