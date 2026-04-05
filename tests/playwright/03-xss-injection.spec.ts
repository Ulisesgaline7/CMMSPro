/**
 * XSS & Injection Tests
 * Uses saved auth state — no repeated logins.
 */

import { test, expect } from '@playwright/test';

const XSS_PAYLOADS = [
    '<script>window.__xss_fired = true;</script>',
    '<img src=x onerror="window.__xss_fired=true">',
    '"><script>window.__xss_fired=true</script>',
    '<svg onload="window.__xss_fired=true">',
];

async function checkXssFired(page: any): Promise<boolean> {
    return page.evaluate(() => !!(window as any).__xss_fired);
}

// ── 1. Work Order title ───────────────────────────────────────────────────────

test('XSS in work order title is not executed', async ({ page }) => {
    for (const payload of XSS_PAYLOADS) {
        await page.goto('/work-orders/create');
        await page.waitForLoadState('networkidle');

        const titleInput = page.locator('input[name="title"]');
        if (!(await titleInput.isVisible({ timeout: 3_000 }).catch(() => false))) { continue; }

        await titleInput.fill(payload);

        const submitBtn = page.locator('button[type="submit"]').first();
        if (await submitBtn.isVisible({ timeout: 2_000 }).catch(() => false)) {
            await submitBtn.click({ force: true });
            await page.waitForTimeout(800);
        }

        expect(await checkXssFired(page), `XSS fired for: ${payload}`).toBe(false);
    }
});

// ── 2. Work Order notes ───────────────────────────────────────────────────────

test('XSS in work order notes is not executed', async ({ page }) => {
    await page.goto('/work-orders');
    await page.waitForLoadState('networkidle');

    const firstLink = page.locator('a[href*="/work-orders/"]').first();
    if (!(await firstLink.isVisible({ timeout: 3_000 }).catch(() => false))) { return; }
    await firstLink.click();
    await page.waitForLoadState('networkidle');

    const noteInput = page.locator('textarea').first();
    if (!(await noteInput.isVisible({ timeout: 3_000 }).catch(() => false))) { return; }

    await noteInput.fill('<script>window.__xss_fired=true</script>');
    const btn = page.locator('button[type="submit"]').first();
    if (await btn.isVisible({ timeout: 1_000 }).catch(() => false)) {
        await btn.click({ force: true });
        await page.waitForTimeout(800);
    }

    expect(await checkXssFired(page), 'XSS in notes should NOT fire').toBe(false);
});

// ── 3. Asset name ─────────────────────────────────────────────────────────────

test('XSS in asset name field is not executed', async ({ page }) => {
    await page.goto('/assets/create');
    await page.waitForLoadState('networkidle');

    const nameInput = page.locator('input[name="name"]');
    if (!(await nameInput.isVisible({ timeout: 3_000 }).catch(() => false))) { return; }

    await nameInput.fill('<script>window.__xss_fired=true</script>');

    const codeInput = page.locator('input[name="code"]');
    if (await codeInput.isVisible({ timeout: 1_000 }).catch(() => false)) {
        await codeInput.fill('XSS-TEST-001');
    }

    // Try clicking the submit button if visible and enabled
    const submitBtn = page.locator('button[type="submit"]:not([disabled])').first();
    const isSubmitVisible = await submitBtn.isVisible({ timeout: 2_000 }).catch(() => false);
    if (isSubmitVisible) {
        await submitBtn.click({ force: true });
        await page.waitForTimeout(800);
    }

    expect(await checkXssFired(page), 'XSS in asset name should NOT fire').toBe(false);
});

// ── 4. Reflected XSS via URL ──────────────────────────────────────────────────

test('reflected XSS via URL query parameter is sanitized', async ({ page }) => {
    await page.goto('/work-orders?search=<script>window.__xss_fired=true</script>');
    await page.waitForLoadState('networkidle');

    expect(await checkXssFired(page), 'Reflected XSS via URL should NOT fire').toBe(false);

    const html = await page.content();
    expect(html).not.toContain('<script>window.__xss_fired');
});

// ── 5. SQL injection in search ────────────────────────────────────────────────

test('SQL injection in work order search does not break the app', async ({ page }) => {
    const payloads = [
        "' OR 1=1 --",
        "1; DROP TABLE work_orders; --",
        "' UNION SELECT username, password FROM users --",
    ];

    for (const payload of payloads) {
        await page.goto(`/work-orders?search=${encodeURIComponent(payload)}`);
        await page.waitForLoadState('networkidle');

        const body = await page.textContent('body') ?? '';
        expect(body).not.toMatch(/SQL syntax|mysql_fetch|PDOException|SQLSTATE/i);
        expect(body).not.toContain('Whoops, looks like something went wrong');
    }
});

test('SQL injection in asset search does not leak data', async ({ page }) => {
    await page.goto("/assets?search=' OR 1=1 --");
    await page.waitForLoadState('networkidle');
    const body = await page.textContent('body') ?? '';
    expect(body).not.toMatch(/SQL syntax|SQLSTATE|PDOException/i);
});

// ── 6. Security headers ───────────────────────────────────────────────────────

test('login page response includes OWASP security headers', async ({ page }) => {
    // Use a fresh unauthenticated context for this test
    await page.context().clearCookies();
    const response = await page.goto('/login');
    const headers  = response?.headers() ?? {};

    expect(headers['x-content-type-options'], 'Missing X-Content-Type-Options').toBe('nosniff');
    expect(headers['x-frame-options'], 'Missing X-Frame-Options').toBe('SAMEORIGIN');
    expect(headers['referrer-policy'], 'Missing Referrer-Policy').toBeTruthy();

    const hasFrameProtection =
        headers['x-frame-options'] != null ||
        (headers['content-security-policy'] ?? '').includes('frame-ancestors');
    expect(hasFrameProtection, 'Missing clickjacking protection').toBeTruthy();
});
