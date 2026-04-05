@props(['settings' => [], 'title' => '', 'description' => ''])

@php
    $siteName    = $settings['site_name']    ?? 'CMMS Pro';
    $siteTagline = $settings['site_tagline'] ?? 'Mantenimiento Industrial Inteligente';
    $primary     = $settings['primary_color'] ?? '#0f172a';
    $accent      = $settings['accent_color']  ?? '#6366f1';
    $pageTitle   = $title ? "{$title} — {$siteName}" : "{$siteName} — {$siteTagline}";
    $metaDesc    = $description ?: ($settings['hero_subtitle'] ?? 'Software CMMS para equipos de mantenimiento industrial en LATAM.');

    $navItems = [
        ['label' => 'Producto',      'route' => 'landing.producto',   'name' => 'landing.producto'],
        ['label' => 'Módulos',        'route' => 'landing.modulos',    'name' => 'landing.modulos'],
        ['label' => 'Precios',         'route' => 'landing.precios',    'name' => 'landing.precios'],
        ['label' => 'Clientes',        'route' => 'landing.clientes',   'name' => 'landing.clientes'],
        ['label' => 'Contacto',        'route' => 'landing.contacto',   'name' => 'landing.contacto'],
    ];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $metaDesc }}">
    <title>{{ $pageTitle }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app-blade.js'])

    <style>
        :root {
            --primary: {{ $primary }};
            --accent:  {{ $accent }};
            --accent-dark: color-mix(in srgb, {{ $accent }} 80%, #000);
        }
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; color: #0f172a; background: #fff; margin: 0; }
        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* ── Navbar ─────────────────────────────────────── */
        #lnav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 50;
            transition: background .3s, box-shadow .3s;
        }
        #lnav.on-dark      { background: transparent; }
        #lnav.on-dark.scrolled,
        #lnav.on-light     {
            background: rgba(255,255,255,0.96);
            backdrop-filter: blur(14px);
            box-shadow: 0 1px 0 rgba(0,0,0,0.07);
        }
        #lnav.on-dark      .lnav-link  { color: rgba(255,255,255,.75); }
        #lnav.on-dark      .lnav-logo  { color: #fff; }
        #lnav.on-dark      .lnav-login { color: #fff; border-color: rgba(255,255,255,.2); }
        #lnav.on-light     .lnav-link  { color: #475569; }
        #lnav.on-light     .lnav-logo  { color: #0f172a; }
        #lnav.on-light     .lnav-login { color: #475569; border-color: #e2e8f0; }
        #lnav.on-dark.scrolled .lnav-link  { color: #475569; }
        #lnav.on-dark.scrolled .lnav-logo  { color: #0f172a; }
        #lnav.on-dark.scrolled .lnav-login { color: #475569; border-color: #e2e8f0; }

        .lnav-link {
            padding: 7px 13px; border-radius: 8px; font-size: 14px; font-weight: 500;
            text-decoration: none; transition: color .2s, background .2s;
        }
        .lnav-link:hover, .lnav-link.active { color: var(--accent) !important; }
        .lnav-link.active { font-weight: 600; }

        /* ── Buttons ────────────────────────────────────── */
        .btn-accent {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 24px; border-radius: 10px;
            background: var(--accent); color: #fff;
            font-weight: 700; font-size: 14px; text-decoration: none;
            transition: background .2s, transform .15s, box-shadow .2s;
            box-shadow: 0 3px 16px color-mix(in srgb, var(--accent) 50%, transparent);
        }
        .btn-accent:hover { background: var(--accent-dark); transform: translateY(-1px); }
        .btn-outline {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 24px; border-radius: 10px;
            border: 1.5px solid #e2e8f0; color: #374151;
            font-weight: 600; font-size: 14px; text-decoration: none;
            transition: border-color .2s, background .2s;
        }
        .btn-outline:hover { background: #f8fafc; border-color: #cbd5e1; }
        .btn-ghost-white {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 24px; border-radius: 10px;
            border: 1.5px solid rgba(255,255,255,.2); color: #fff;
            font-weight: 600; font-size: 14px; text-decoration: none;
            background: rgba(255,255,255,.05);
            transition: background .2s, border-color .2s;
        }
        .btn-ghost-white:hover { background: rgba(255,255,255,.12); border-color: rgba(255,255,255,.35); }

        /* ── Section helpers ────────────────────────────── */
        .section-label {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12px; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: var(--accent); margin-bottom: 12px;
        }
        .badge-pill {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 99px; font-size: 12px; font-weight: 600;
            border: 1px solid rgba(99,102,241,.3); background: rgba(99,102,241,.1);
            color: #a5b4fc; letter-spacing: .02em;
        }
        .mockup-wrap {
            border-radius: 16px; background: #1e293b; overflow: hidden;
            border: 1px solid rgba(255,255,255,.08);
            box-shadow: 0 32px 80px rgba(0,0,0,.45), 0 0 0 1px rgba(255,255,255,.04);
        }
        .mockup-bar {
            display: flex; align-items: center; gap: 6px;
            padding: 10px 14px; background: #0f172a;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }
        .mockup-dot { width: 10px; height: 10px; border-radius: 50%; }

        /* ── Reveal ─────────────────────────────────────── */
        .reveal { opacity: 0; transform: translateY(24px); transition: opacity .6s ease, transform .6s ease; }
        .reveal.visible { opacity: 1; transform: none; }

        /* ── Two-col responsive ─────────────────────────── */
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
        @media (max-width: 900px) {
            .two-col { grid-template-columns: 1fr; gap: 40px; }
            .footer-grid { grid-template-columns: 1fr 1fr !important; }
        }
        @media (max-width: 480px) {
            .footer-grid { grid-template-columns: 1fr !important; }
        }

        /* ── Mobile menu ─────────────────────────────────── */
        #lnav-mobile { display: none; }
        #lnav-mobile.open { display: block; }
    </style>
    {{ $head ?? '' }}
</head>
<body class="antialiased">

{{-- ── NAVBAR ─────────────────────────────────────────────────── --}}
<nav id="lnav" class="{{ $darkNav ?? false ? 'on-dark' : 'on-light' }}">
    <div style="max-width:1200px; margin:0 auto; padding:0 24px;">
        <div style="display:flex; align-items:center; justify-content:space-between; height:64px;">

            {{-- Logo --}}
            <a href="{{ route('home') }}" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
                <div style="width:34px; height:34px; border-radius:9px; background:var(--accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i data-lucide="wrench" style="width:17px; height:17px; color:#fff;"></i>
                </div>
                <span class="lnav-logo font-display" style="font-weight:800; font-size:18px; letter-spacing:-.02em;">
                    {{ $siteName }}
                </span>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden lg:flex" style="align-items:center; gap:2px;">
                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="lnav-link {{ request()->routeIs($item['name']) ? 'active' : '' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>

            {{-- CTAs --}}
            <div class="hidden lg:flex" style="align-items:center; gap:10px;">
                <a href="{{ route('login') }}" class="lnav-login"
                   style="padding:8px 18px; border-radius:9px; font-size:14px; font-weight:600; text-decoration:none; border:1px solid; transition:background .2s;">
                    Iniciar sesión
                </a>
                <a href="{{ route('register') }}" class="btn-accent" style="padding:8px 18px; font-size:14px;">
                    Prueba gratis
                </a>
            </div>

            {{-- Mobile toggle --}}
            <button onclick="document.getElementById('lnav-mobile').classList.toggle('open')"
                    class="lg:hidden" style="background:none; border:none; cursor:pointer; padding:8px; color:inherit;">
                <i data-lucide="menu" style="width:22px; height:22px;"></i>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div id="lnav-mobile" style="background:#0f172a; border-top:1px solid rgba(255,255,255,.08); padding:16px 24px 24px;">
        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               style="display:block; padding:12px 0; font-size:15px; font-weight:600; color:rgba(255,255,255,.8); text-decoration:none; border-bottom:1px solid rgba(255,255,255,.05);">
                {{ $item['label'] }}
            </a>
        @endforeach
        <div style="margin-top:20px; display:flex; flex-direction:column; gap:10px;">
            <a href="{{ route('login') }}" style="text-align:center; padding:12px; border-radius:10px; border:1px solid rgba(255,255,255,.18); color:#fff; font-weight:600; font-size:14px; text-decoration:none;">Iniciar sesión</a>
            <a href="{{ route('register') }}" style="text-align:center; padding:12px; border-radius:10px; background:var(--accent); color:#fff; font-weight:700; font-size:14px; text-decoration:none;">Prueba gratis 14 días</a>
        </div>
    </div>
</nav>

{{-- ── PAGE CONTENT ─────────────────────────────────────────────── --}}
{{ $slot }}

{{-- ── FOOTER ──────────────────────────────────────────────────── --}}
<footer style="background:#020617; padding:64px 24px 32px;">
    <div style="max-width:1200px; margin:0 auto;">
        <div class="footer-grid" style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:48px; margin-bottom:56px;">

            <div>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
                    <div style="width:34px; height:34px; border-radius:9px; background:var(--accent); display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="wrench" style="width:17px; height:17px; color:#fff;"></i>
                    </div>
                    <span class="font-display" style="font-weight:800; font-size:18px; color:#fff; letter-spacing:-.02em;">{{ $siteName }}</span>
                </div>
                <p style="font-size:14px; color:rgba(255,255,255,.4); line-height:1.7; max-width:280px; margin:0 0 24px;">
                    Software de Gestión de Mantenimiento Industrial para equipos LATAM. PM, CM, PdM, IoT e IA Predictiva.
                </p>
                <div style="display:flex; gap:10px;">
                    @foreach([['x','X'],['linkedin','LinkedIn'],['play','YouTube']] as [$icon,$label])
                    <a href="#" aria-label="{{ $label }}"
                       style="width:36px; height:36px; border-radius:9px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.08); text-decoration:none; transition:background .2s;"
                       onmouseover="this.style.background='rgba(255,255,255,.12)'" onmouseout="this.style.background='rgba(255,255,255,.05)'">
                        <i data-lucide="{{ $icon }}" style="width:15px; height:15px; color:rgba(255,255,255,.5);"></i>
                    </a>
                    @endforeach
                </div>
            </div>

            @foreach([
                ['Producto',  ['Funciones','Módulos','Integraciones','Roadmap','Actualizaciones']],
                ['Empresa',   ['Acerca de','Clientes','Blog','Prensa','Empleo']],
                ['Soporte',   ['Documentación','Centro de ayuda','Estado','Contacto','Seguridad']],
            ] as [$col, $links])
            <div>
                <p style="font-size:12px; font-weight:700; color:rgba(255,255,255,.3); text-transform:uppercase; letter-spacing:.1em; margin:0 0 16px;">{{ $col }}</p>
                @foreach($links as $link)
                <a href="#" style="display:block; font-size:14px; color:rgba(255,255,255,.45); text-decoration:none; margin-bottom:10px; transition:color .2s;"
                   onmouseover="this.style.color='rgba(255,255,255,.85)'" onmouseout="this.style.color='rgba(255,255,255,.45)'">{{ $link }}</a>
                @endforeach
            </div>
            @endforeach
        </div>

        <div style="height:1px; background:rgba(255,255,255,.06); margin-bottom:28px;"></div>
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
            <p style="font-size:13px; color:rgba(255,255,255,.3); margin:0;">{{ $settings['footer_text'] ?? '© 2026 CMMS Pro. Todos los derechos reservados.' }}</p>
            <div style="display:flex; gap:20px;">
                @foreach(['Privacidad','Términos','Cookies'] as $link)
                <a href="#" style="font-size:13px; color:rgba(255,255,255,.3); text-decoration:none; transition:color .2s;"
                   onmouseover="this.style.color='rgba(255,255,255,.65)'" onmouseout="this.style.color='rgba(255,255,255,.3)'">{{ $link }}</a>
                @endforeach
            </div>
        </div>
    </div>
</footer>

<script>
    // Navbar scroll behavior (only for dark nav pages)
    const lnav = document.getElementById('lnav');
    if (lnav.classList.contains('on-dark')) {
        window.addEventListener('scroll', () => lnav.classList.toggle('scrolled', scrollY > 20), { passive: true });
    }
    // Reveal
    new IntersectionObserver((es) => es.forEach(e => e.isIntersecting && e.target.classList.add('visible')), { threshold: .1 })
        .observe.bind(null); // init below after DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        const ro = new IntersectionObserver((es) => es.forEach(e => e.isIntersecting && e.target.classList.add('visible')), { threshold: .1 });
        document.querySelectorAll('.reveal').forEach(el => ro.observe(el));
        document.querySelectorAll('#lnav-mobile a').forEach(a => a.addEventListener('click', () => document.getElementById('lnav-mobile').classList.remove('open')));
    });
</script>
{{ $scripts ?? '' }}
</body>
</html>
