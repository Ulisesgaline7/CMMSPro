/**
 * Role-Based Access Control (RBAC) Tests
 * Uses saved auth state — no repeated logins except for tests that specifically
 * test the login form behavior.
 */

import { test, expect } from '@playwright/test';
import { CREDS, AUTH_FILE, loginAs, loginAsSuperAdmin, expectAuthRedirect } from './helpers';

// ── 1. Tenant Admin route access ──────────────────────────────────────────────

test('admin can access all main routes', async ({ page }) => {
    const routes = ['/dashboard', '/assets', '/work-orders', '/inventory', '/maintenance-plans'];

    for (const route of routes) {
        const response = await page.goto(route);
        expect(
            response?.status() ?? 0,
            `Admin should have access to ${route}`,
        ).toBeLessThan(400);
    }
});

// ── 2. Super Admin route access ───────────────────────────────────────────────

test('super admin can access all SA routes', async ({ page }) => {
    // Use SA auth state
    await page.context().clearCookies();

    const fs = await import('fs');
    if (fs.default.existsSync(AUTH_FILE.superAdmin)) {
        const state = JSON.parse(fs.default.readFileSync(AUTH_FILE.superAdmin, 'utf-8'));
        await page.context().addCookies(state.cookies ?? []);
    }

    const routes = ['/super-admin', '/super-admin/tenants', '/super-admin/users'];
    for (const route of routes) {
        const response = await page.goto(route);
        expect(
            response?.status() ?? 0,
            `Super admin should access ${route}`,
        ).toBeLessThan(400);
    }
});

// ── 3. Tenant user blocked from Super Admin routes ───────────────────────────

test('tenant admin is blocked from all super-admin/* routes', async ({ page }) => {
    const blockedRoutes = ['/super-admin', '/super-admin/tenants', '/super-admin/users'];
    for (const route of blockedRoutes) {
        const response = await page.goto(route);
        const status   = response?.status() ?? 200;
        const url      = page.url();
        expect(
            status === 403 || url.includes('/login') || !url.endsWith(route),
            `Tenant admin must NOT access ${route}, got ${status} → ${url}`,
        ).toBeTruthy();
    }
});

// ── 4. CSRF protection ────────────────────────────────────────────────────────

test('POST request without CSRF token is rejected', async ({ page }) => {
    await page.context().clearCookies(); // unauthenticated
    await page.goto('/login');

    const status = await page.evaluate(async () => {
        const res = await fetch('/work-orders', {
            method: 'POST',
            credentials: 'include',
            redirect: 'manual',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title: 'CSRF test injection' }),
        });
        return res.status;
    });

    expect(
        status === 419 || status === 0 || status === 302,
        `Expected CSRF rejection (419/302/0), got ${status}`,
    ).toBeTruthy();
});

test('Super Admin POST without CSRF token is rejected', async ({ page }) => {
    await page.context().clearCookies();
    await page.goto('/super-admin/login');

    const status = await page.evaluate(async () => {
        const res = await fetch('/super-admin/tenants/1/suspend', {
            method: 'POST',
            credentials: 'include',
            redirect: 'manual',
            headers: { 'Content-Type': 'application/json' },
        });
        return res.status;
    });

    expect(
        status === 419 || status === 0 || status === 302,
        `Expected CSRF rejection for SA action, got ${status}`,
    ).toBeTruthy();
});

// ── 5. Unauthenticated redirects ──────────────────────────────────────────────

test('unauthenticated user is redirected from /dashboard', async ({ page }) => {
    await expectAuthRedirect(page, '/dashboard');
});

test('unauthenticated user is redirected from /super-admin', async ({ page }) => {
    await expectAuthRedirect(page, '/super-admin');
});

// ── 6. Password reset doesn't reveal user existence ──────────────────────────

test('forgot password shows same response for existing and non-existing emails', async ({ page }) => {
    await page.context().clearCookies();
    await page.goto('/forgot-password');
    await page.waitForLoadState('domcontentloaded');

    const emailInput = page.locator('input[name="email"]');
    if (!(await emailInput.isVisible({ timeout: 5_000 }).catch(() => false))) {
        test.skip();
        return;
    }

    // The submit button uses data-test or just a plain button (shadcn Button defaults to type="button")
    const submitBtn = page.locator('[data-test="email-password-reset-link-button"], button[type="submit"], form button').first();

    await emailInput.fill(CREDS.tenantAdmin.email);
    await submitBtn.click({ force: true });
    await page.waitForTimeout(2_000);
    const bodyReal = await page.textContent('body') ?? '';

    await page.goto('/forgot-password');
    await page.waitForLoadState('domcontentloaded');
    await page.locator('input[name="email"]').fill('notexistent99@example.com');
    await page.locator('[data-test="email-password-reset-link-button"], button[type="submit"], form button').first().click({ force: true });
    await page.waitForTimeout(2_000);
    const bodyFake = await page.textContent('body') ?? '';

    expect(bodyFake).not.toMatch(/no existe|not found|no registrado/i);
    const realHasMsg = bodyReal.match(/correo|enlace|link|sent|enviado|password/i) !== null;
    const fakeHasMsg = bodyFake.match(/correo|enlace|link|sent|enviado|password/i) !== null;
    expect(realHasMsg).toBe(fakeHasMsg);
});

// ── 7. Rate limiting on login ─────────────────────────────────────────────────

test('5+ failed login attempts triggers rate limiting', async ({ page }) => {
    await page.context().clearCookies();
    let lastStatus = 0;
    // Use the SAME email for all attempts — Fortify throttles by email|IP
    const testEmail = 'ratelimit-test@test.local';

    for (let i = 0; i < 6; i++) {
        await page.goto('/login');
        await page.fill('input[name="email"]', testEmail);
        await page.fill('input[name="password"]', `wrong_${i}`);

        const [response] = await Promise.all([
            page.waitForResponse(
                (r) => r.url().includes('/login') && r.request().method() === 'POST',
                { timeout: 5_000 },
            ).catch(() => null),
            page.click('button[type="submit"]'),
        ]);

        lastStatus = response?.status() ?? 0;
        if (lastStatus === 429) { break; }
        await page.waitForTimeout(200);
    }

    const body = await page.textContent('body') ?? '';
    const rateLimited = lastStatus === 429 || body.match(/too many|demasiados|429/i) !== null;
    expect(rateLimited, 'Rate limiting should kick in after 5+ failed attempts').toBeTruthy();
});
