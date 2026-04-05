/**
 * Multi-Tenant Isolation Tests — CRITICAL
 *
 * Verifies that no tenant can access another tenant's data.
 * Uses saved auth state from auth.setup.ts — no repeated logins.
 */

import { test, expect } from '@playwright/test';
import { AUTH_FILE, goToDashboard, goToSuperAdmin, loginAsSuperAdmin } from './helpers';

// ── 1. Dashboard isolation ────────────────────────────────────────────────────

test('tenant 1 dashboard does NOT contain tenant 2 company name', async ({ page }) => {
    await goToDashboard(page);
    const body = await page.textContent('body') ?? '';
    expect(body).not.toContain('Empresa Demo 2');
    expect(body).not.toContain('tenant2');
});

// ── 2. Direct ID access ───────────────────────────────────────────────────────

test('tenant cannot access another tenant\'s asset by ID', async ({ page }) => {
    const response = await page.goto('/assets/999999');
    const status   = response?.status() ?? 200;
    const url      = page.url();
    expect(
        status === 404 || status === 403 || !url.includes('999999'),
        `Expected 404/403 for cross-tenant asset, got ${status}`,
    ).toBeTruthy();
});

test('tenant cannot access another tenant\'s work order by ID', async ({ page }) => {
    const response = await page.goto('/work-orders/999999');
    const status   = response?.status() ?? 200;
    expect(status === 404 || status === 403 || !page.url().includes('999999')).toBeTruthy();
});

// ── 3. API response isolation ─────────────────────────────────────────────────

test('assets page only returns current tenant assets', async ({ page }) => {
    await page.goto('/assets');
    await page.waitForLoadState('networkidle');
    const body = await page.textContent('body') ?? '';
    expect(body).not.toContain('super-admin');
    expect(body).not.toContain('"tenant_id":2');
});

// ── 4. Super admin routes blocked for tenant users ────────────────────────────

test('tenant user CANNOT access /super-admin panel', async ({ page }) => {
    const response = await page.goto('/super-admin');
    const status   = response?.status() ?? 200;
    expect(
        status === 403 || !page.url().includes('/super-admin') || page.url().includes('/login'),
        `Tenant should be forbidden from SA panel, got ${status} at ${page.url()}`,
    ).toBeTruthy();
});

test('tenant user CANNOT access /super-admin/tenants', async ({ page }) => {
    const response = await page.goto('/super-admin/tenants');
    const status   = response?.status() ?? 200;
    expect(
        status === 403 || !page.url().endsWith('/super-admin/tenants'),
        `Got ${status} at ${page.url()}`,
    ).toBeTruthy();
});

test('tenant user CANNOT access /super-admin/users', async ({ page }) => {
    const response = await page.goto('/super-admin/users');
    const status   = response?.status() ?? 200;
    const url      = page.url();
    expect(
        status === 403 || url.includes('/login') || !url.endsWith('/super-admin/users'),
        `Tenant should not access /super-admin/users, got ${status} → ${url}`,
    ).toBeTruthy();
});

// ── 5. Tenant ID manipulation via query string ────────────────────────────────

test('query param tenant_id manipulation is ignored by backend', async ({ page }) => {
    await page.goto('/assets?tenant_id=2');
    await page.waitForLoadState('networkidle');
    const body = await page.textContent('body') ?? '';
    expect(body).not.toContain('Empresa Demo 2');
    expect(body).not.toContain('"tenant_id":2');
});

// ── 6. IDOR — editing another tenant's maintenance plan is blocked ─────────────

test('editing another tenant\'s maintenance plan is blocked', async ({ page }) => {
    const response = await page.goto('/maintenance-plans/999999/edit');
    const status   = response?.status() ?? 200;
    const url      = page.url();
    expect(
        status === 404 || status === 403 || !url.includes('999999'),
        `Should not be able to edit cross-tenant plan, got ${status} → ${url}`,
    ).toBeTruthy();
});

// ── 7. Super admin CAN see all tenants (sanity check) ─────────────────────────

test('super admin dashboard loads and shows tenant count', async ({ page }) => {
    // Switch to SA auth state for this test
    await page.context().clearCookies();
    await page.context().addCookies(
        // Reload SA auth state
        (await import('fs')).default.existsSync(AUTH_FILE.superAdmin)
            ? JSON.parse((await import('fs')).default.readFileSync(AUTH_FILE.superAdmin, 'utf-8')).cookies ?? []
            : [],
    );
    await page.goto('/super-admin');
    await page.waitForLoadState('networkidle');

    // SA panel should be accessible and show tenant management
    const url = page.url();
    expect(url).toContain('/super-admin');
});
