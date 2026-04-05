import { Page, expect } from '@playwright/test';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

/** Credentials loaded from env so they never live in test files. */
export const CREDS = {
    superAdmin: {
        email:    process.env.SA_EMAIL    ?? 'superadmin@cmms.app',
        password: process.env.SA_PASSWORD ?? 'superadmin123',
    },
    tenantAdmin: {
        email:    process.env.TENANT_EMAIL    ?? 'admin@metalurgica.com',
        password: process.env.TENANT_PASSWORD ?? 'password',
    },
    // A second tenant's admin – used for cross-tenant isolation tests.
    tenantAdmin2: {
        email:    process.env.TENANT2_EMAIL    ?? 'admin@tenant2.com',
        password: process.env.TENANT2_PASSWORD ?? 'password',
    },
};

/** Paths to saved auth states (created by auth.setup.ts). */
export const AUTH_FILE = {
    tenantAdmin: path.join(__dirname, '.auth/tenant-admin.json'),
    superAdmin:  path.join(__dirname, '.auth/super-admin.json'),
};

/**
 * Navigate to the dashboard using the saved tenant admin session.
 * Uses storageState so we don't hit the login form (avoids rate-limiting).
 */
export async function goToDashboard(page: Page) {
    await page.goto('/dashboard');
    await page.waitForLoadState('networkidle');
}

/**
 * Navigate to the SA panel using the saved super admin session.
 */
export async function goToSuperAdmin(page: Page) {
    await page.goto('/super-admin');
    await page.waitForLoadState('networkidle');
}

/**
 * Full login via the Fortify form — use ONLY when testing the login form itself.
 * For all other tests, use storageState from playwright.config.ts or AUTH_FILE.
 */
export async function loginAs(page: Page, email: string, password: string) {
    await page.context().clearCookies();
    await page.goto('/login');
    await page.fill('input[name="email"]',    email);
    await page.fill('input[name="password"]', password);
    await page.click('button[type="submit"]');
    await page.waitForURL(/\/(dashboard|super-admin)/, { timeout: 15_000 });
}

/** Full SA login via the custom SA login form. */
export async function loginAsSuperAdmin(page: Page) {
    await page.context().clearCookies();
    await page.goto('/super-admin/login');
    await page.fill('input[name="email"]',    CREDS.superAdmin.email);
    await page.fill('input[name="password"]', CREDS.superAdmin.password);
    await page.click('button[type="submit"]');
    await page.waitForURL('/super-admin', { timeout: 15_000 });
}

/** Assert the page redirects to login for an unauthenticated visitor. */
export async function expectAuthRedirect(page: Page, url: string) {
    await page.context().clearCookies(); // ensure unauthenticated
    const response = await page.goto(url);
    const finalUrl = page.url();
    const blocked  = finalUrl.includes('/login') || finalUrl.includes('/super-admin/login');
    const status   = response?.status() ?? 200;
    expect(
        blocked || status === 401 || status === 403,
        `Expected auth protection at ${url} but got ${status} → ${finalUrl}`,
    ).toBeTruthy();
}
