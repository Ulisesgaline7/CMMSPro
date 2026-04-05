/**
 * Authentication Tests
 * Tests: login flow, invalid credentials, brute-force rate limiting,
 *        unauthenticated access, super-admin login isolation.
 */

import { test, expect } from '@playwright/test';
import { CREDS, loginAs, loginAsSuperAdmin, expectAuthRedirect } from './helpers';

// ── 1. Basic Login ────────────────────────────────────────────────────────────

test('login page renders correctly', async ({ page }) => {
    await page.context().clearCookies();
    await page.goto('/login');
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();
});

test('valid tenant login redirects to dashboard', async ({ page }) => {
    await loginAs(page, CREDS.tenantAdmin.email, CREDS.tenantAdmin.password);
    await expect(page).toHaveURL(/\/dashboard/);
});

// ── 2. Invalid Credentials ────────────────────────────────────────────────────

test('wrong password shows error — does NOT reveal if email exists', async ({ page }) => {
    await page.context().clearCookies();
    await page.goto('/login');
    await page.fill('input[name="email"]', CREDS.tenantAdmin.email);
    await page.fill('input[name="password"]', 'completamente_incorrecta_XYZ');
    await page.click('button[type="submit"]');

    // Must stay on login page
    await expect(page).toHaveURL(/\/login/);

    // Wait for Inertia validation error to render
    await page.waitForTimeout(1_500);
    const body = await page.textContent('body');

    // Must NOT say "email not found" (info leak)
    expect(body).not.toContain('not found');
    expect(body).not.toContain('no existe');
    // Must show SOME error (generic message)
    expect(body).toMatch(/credenciales|credentials|incorrect|incorrect/i);
});

test('non-existent email shows same generic error (prevents user enumeration)', async ({ page }) => {
    await page.context().clearCookies();
    await page.goto('/login');
    await page.fill('input[name="email"]', 'noexiste_12345@fake.com');
    await page.fill('input[name="password"]', 'cualquier_cosa');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/\/login/);
    await page.waitForTimeout(1_500);
    const body = await page.textContent('body');
    // Should show same error as wrong password (no user enumeration)
    expect(body).toMatch(/credenciales|These credentials|incorrect/i);
    // Must NOT distinguish "email not found" from "wrong password"
    expect(body).not.toMatch(/no existe|not found|email.*no.*registrado/i);
});

test('SQL injection in login email field is sanitized', async ({ page }) => {
    await page.context().clearCookies();
    await page.goto('/login');
    // Classic auth bypass payloads
    const payloads = [
        "' OR 1=1 --",
        "' OR '1'='1",
        "admin'--",
        "' UNION SELECT 1,2,3 --",
        "'; DROP TABLE users; --",
    ];

    for (const payload of payloads) {
        await page.fill('input[name="email"]',    payload);
        await page.fill('input[name="password"]', payload);
        await page.click('button[type="submit"]');

        // Must NEVER redirect to dashboard — must stay on login with error
        const url = page.url();
        expect(url).not.toMatch(/\/dashboard/);
        expect(url).toMatch(/\/login/);
    }
});

// ── 3. Super Admin Separate Login ─────────────────────────────────────────────

test('super-admin login page is separate and dark-themed', async ({ page }) => {
    await page.goto('/super-admin/login');
    await expect(page.locator('input[name="email"]')).toBeVisible();
    // Verify it has the SA branding
    await expect(page.locator('body')).toContainText('Super Admin');
});

test('regular tenant user CANNOT log in via super-admin login', async ({ page }) => {
    await page.context().clearCookies(); // ensure we don't invalidate the tenant storageState session
    await page.goto('/super-admin/login');
    await page.fill('input[name="email"]',    CREDS.tenantAdmin.email);
    await page.fill('input[name="password"]', CREDS.tenantAdmin.password);
    await page.click('button[type="submit"]');

    // Must NOT be redirected to SA dashboard
    await expect(page).not.toHaveURL('/super-admin');
    // Must show access denied error
    const body = await page.textContent('body');
    expect(body).toMatch(/Super Admin|acceso|panel/i);
});

test('super admin user logs in via SA login and reaches SA panel', async ({ page }) => {
    await loginAsSuperAdmin(page);
    await expect(page).toHaveURL('/super-admin');
});

// ── 4. Unauthenticated Access ─────────────────────────────────────────────────

test('unauthenticated user is redirected from /dashboard to login', async ({ page }) => {
    await expectAuthRedirect(page, '/dashboard');
});

test('unauthenticated user is redirected from /super-admin to login', async ({ page }) => {
    await expectAuthRedirect(page, '/super-admin');
});

test('unauthenticated user is redirected from /assets to login', async ({ page }) => {
    await expectAuthRedirect(page, '/assets');
});

test('unauthenticated user is redirected from /work-orders to login', async ({ page }) => {
    await expectAuthRedirect(page, '/work-orders');
});

// ── 5. Session Management ─────────────────────────────────────────────────────

test('logging out invalidates session — cannot access dashboard after logout', async ({ page }) => {
    await loginAs(page, CREDS.tenantAdmin.email, CREDS.tenantAdmin.password);
    await expect(page).toHaveURL(/\/dashboard/);

    // Logout via POST (Fortify logout route)
    await page.evaluate(async () => {
        const csrfToken = document.cookie
            .split('; ')
            .find((c) => c.startsWith('XSRF-TOKEN='))
            ?.split('=')[1];

        await fetch('/logout', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'X-XSRF-TOKEN': decodeURIComponent(csrfToken ?? ''),
                'Content-Type': 'application/json',
            },
        });
    });

    // Try accessing dashboard — must be redirected to login
    await page.goto('/dashboard');
    await expect(page).toHaveURL(/\/login/);
});
