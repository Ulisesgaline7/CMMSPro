import { defineConfig, devices } from '@playwright/test';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const authDir = path.join(__dirname, 'tests/playwright/.auth');

export default defineConfig({
    testDir: './tests/playwright',
    fullyParallel: false,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 2 : 0,
    workers: 1,
    reporter: [
        ['html', { outputFolder: 'tests/playwright/reports', open: 'never' }],
        ['list'],
    ],

    use: {
        baseURL: process.env.APP_URL ?? 'http://localhost:8000',
        trace: 'on-first-retry',
        screenshot: 'only-on-failure',
        video: 'off',
        ignoreHTTPSErrors: true,
    },

    projects: [
        // ── 1. Auth setup — runs first, saves session state ──────────────────
        {
            name: 'setup',
            testMatch: /auth\.setup\.ts/,
        },

        // ── 2. Tests that reuse saved auth state ─────────────────────────────
        {
            name: 'chromium',
            use: {
                ...devices['Desktop Chrome'],
                // Default to tenant admin auth; individual tests can override
                storageState: path.join(authDir, 'tenant-admin.json'),
            },
            dependencies: ['setup'],
            testIgnore: /auth\.setup\.ts/,
        },
    ],
});
