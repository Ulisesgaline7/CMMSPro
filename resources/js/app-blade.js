import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});

// ── Laravel Echo (Reverb) ─────────────────────────────────────────────────
if (
    import.meta.env.VITE_REVERB_APP_KEY &&
    document.querySelector('meta[name="user-id"]')
) {
    window.Pusher = Pusher;

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST ?? 'localhost',
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
    });

    const userId = document.querySelector('meta[name="user-id"]').content;

    window.Echo.private(`App.Models.User.${userId}`).listen(
        '.notification.created',
        (data) => {
            showPushToast(data);
            updateBellBadge();
        },
    );
}

// ── Push Toast ────────────────────────────────────────────────────────────
function showPushToast(data) {
    // 1. Browser native notification (if granted)
    if (Notification.permission === 'granted') {
        new Notification(data.title, {
            body: data.message,
            icon: '/favicon.ico',
        });
    }

    // 2. In-page toast overlay
    const container = getOrCreateToastContainer();
    const toast = document.createElement('div');

    const color  = data.color  ?? '#6366f1';
    const icon   = data.icon   ?? 'bell';
    const title  = data.title  ?? 'Notificación';
    const msg    = data.message ?? '';
    const url    = data.url    ?? '/super-admin/notifications';

    toast.style.cssText = `
        display:flex; align-items:flex-start; gap:12px;
        background:#fff; border:1px solid #e2e8f0;
        border-left:4px solid ${color};
        border-radius:12px; padding:14px 16px;
        box-shadow:0 8px 32px rgba(15,23,42,0.12);
        max-width:360px; width:100%;
        animation:slideInRight 0.3s ease;
        cursor:pointer;
        position:relative;
    `;

    toast.innerHTML = `
        <div style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:${color}18;shrink:0;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 data-lucide="${icon}"></svg>
        </div>
        <div style="flex:1;min-width:0;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 2px;">${title}</p>
            <p style="font-size:11px;color:#64748b;margin:0;line-height:1.4;">${msg}</p>
        </div>
        <button onclick="this.closest('[data-toast]').remove()"
                style="position:absolute;top:8px;right:8px;background:none;border:none;cursor:pointer;padding:2px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                 fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    `;

    toast.dataset.toast = '1';
    toast.addEventListener('click', (e) => {
        if (!e.target.closest('button')) {
            window.location.href = url;
        }
    });

    container.appendChild(toast);

    // Re-render lucide icons inside toast
    if (window.lucide) {
        window.lucide.createIcons({ icons: window.lucide.icons });
    }

    // Auto-remove after 6s
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }, 6000);
}

function getOrCreateToastContainer() {
    let container = document.getElementById('push-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'push-toast-container';
        container.style.cssText = `
            position:fixed; bottom:24px; right:24px;
            z-index:9999; display:flex; flex-direction:column; gap:10px;
            pointer-events:none;
        `;
        // Make toasts clickable
        container.addEventListener('click', (e) => {
            e.stopPropagation();
        });
        container.style.pointerEvents = 'auto';
        document.body.appendChild(container);

        // Inject animation keyframes once
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from { opacity:0; transform:translateX(40px); }
                to   { opacity:1; transform:translateX(0); }
            }
            @keyframes slideOutRight {
                from { opacity:1; transform:translateX(0); }
                to   { opacity:0; transform:translateX(40px); }
            }
        `;
        document.head.appendChild(style);
    }
    return container;
}

// ── Update bell badge without page reload ─────────────────────────────────
function updateBellBadge() {
    fetch('/super-admin/notifications/unread-count', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
        .then((r) => r.json())
        .then(({ count }) => {
            const badge = document.getElementById('bell-badge');
            if (!badge) { return; }
            if (count > 0) {
                badge.textContent = count > 9 ? '9+' : count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(() => {});
}

// ── Request browser notification permission on load ───────────────────────
if ('Notification' in window && Notification.permission === 'default') {
    // Ask after a short delay so it doesn't block the UI immediately
    setTimeout(() => Notification.requestPermission(), 3000);
}
