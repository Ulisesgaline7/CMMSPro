/**
 * Global auth setup — runs ONCE before the test suite.
 * Logs in as each role and saves the browser storage state (cookies + localStorage).
 * Tests then reuse this state instead of logging in on every test.
 */

import { test as setup, expect } from '@playwright/test';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

export const AUTH_STATE = {
    tenantAdmin: path.join(__dirname, '.auth/tenant-admin.json'),
    superAdmin:  path.join(__dirname, '.auth/super-admin.json'),
};

setup('authenticate as tenant admin', async ({ page }) => {
    await page.context().clearCookies();
    await page.goto('/login');
    await page.fill('input[name="email"]',    process.env.TENANT_EMAIL    ?? 'admin@metalurgica.com');
    await page.fill('input[name="password"]', process.env.TENANT_PASSWORD ?? 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL(/\/dashboard/, { timeout: 15_000 });
    await expect(page).toHaveURL(/\/dashboard/);
    await page.context().storageState({ path: AUTH_STATE.tenantAdmin });
});

setup('authenticate as super admin', async ({ page }) => {
    await page.context().clearCookies();
    await page.goto('/super-admin/login');
    await page.fill('input[name="email"]',    process.env.SA_EMAIL    ?? 'superadmin@cmms.app');
    await page.fill('input[name="password"]', process.env.SA_PASSWORD ?? 'superadmin123');
    await page.click('button[type="submit"]');
    await page.waitForURL('/super-admin', { timeout: 15_000 });
    await page.context().storageState({ path: AUTH_STATE.superAdmin });
});
