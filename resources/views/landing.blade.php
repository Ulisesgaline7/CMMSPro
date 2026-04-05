<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $settings['site_tagline'] ?? 'Software CMMS para equipos de mantenimiento industrial en LATAM.' }}">
    <title>{{ $settings['site_name'] ?? 'CMMS Pro' }} — {{ $settings['site_tagline'] ?? 'Mantenimiento Industrial Inteligente' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app-blade.js'])

    <style>
        :root {
            --primary: {{ $settings['primary_color'] ?? '#0f172a' }};
            --accent:  {{ $settings['accent_color']  ?? '#6366f1' }};
            --accent-dark: color-mix(in srgb, {{ $settings['accent_color'] ?? '#6366f1' }} 80%, #000);
        }

        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; color: #0f172a; background: #fff; margin: 0; }
        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* ── Navbar ───────────────────────────────────────────── */
        #navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 50;
            transition: background 0.3s, box-shadow 0.3s, backdrop-filter 0.3s;
            background: transparent;
        }
        #navbar.scrolled {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(12px);
            box-shadow: 0 1px 0 rgba(0,0,0,0.08);
        }
        #navbar.scrolled .nav-link { color: #0f172a !important; }
        #navbar.scrolled .nav-logo { color: #0f172a !important; }
        #navbar.scrolled .nav-cta-ghost {
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
        }

        /* ── Hero ─────────────────────────────────────────────── */
        .hero {
            background: #0f172a;
            min-height: 100vh;
            display: flex; flex-direction: column; justify-content: center;
            position: relative; overflow: hidden;
        }
        .hero-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(99,102,241,0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,102,241,0.08) 1px, transparent 1px);
            background-size: 64px 64px;
        }
        .hero-glow {
            position: absolute; top: -20%; left: 50%; transform: translateX(-50%);
            width: 900px; height: 600px; border-radius: 50%;
            background: radial-gradient(ellipse, rgba(99,102,241,0.25) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ── Badge pill ───────────────────────────────────────── */
        .badge-pill {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 99px; font-size: 12px; font-weight: 600;
            border: 1px solid rgba(99,102,241,0.3);
            background: rgba(99,102,241,0.1);
            color: #a5b4fc;
            letter-spacing: 0.02em;
        }

        /* ── Buttons ──────────────────────────────────────────── */
        .btn-primary {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 13px 28px; border-radius: 10px;
            background: var(--accent); color: #fff;
            font-weight: 700; font-size: 15px; text-decoration: none;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 20px rgba(99,102,241,0.4);
        }
        .btn-primary:hover {
            background: var(--accent-dark);
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(99,102,241,0.5);
        }
        .btn-ghost-light {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 13px 28px; border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.2); color: #fff;
            font-weight: 600; font-size: 15px; text-decoration: none;
            transition: border-color 0.2s, background 0.2s;
            background: rgba(255,255,255,0.05);
        }
        .btn-ghost-light:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.35);
        }
        .btn-ghost-dark {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 13px 28px; border-radius: 10px;
            border: 1px solid #e2e8f0; color: #0f172a;
            font-weight: 600; font-size: 15px; text-decoration: none;
            transition: border-color 0.2s, background 0.2s;
        }
        .btn-ghost-dark:hover { background: #f8fafc; border-color: #cbd5e1; }

        /* ── Dashboard mockup ─────────────────────────────────── */
        .mockup-wrapper {
            position: relative;
            border-radius: 16px;
            background: #1e293b;
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 32px 80px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.04);
            overflow: hidden;
        }
        .mockup-bar {
            display: flex; align-items: center; gap: 6px;
            padding: 10px 14px;
            background: #0f172a;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .mockup-dot { width: 10px; height: 10px; border-radius: 50%; }

        /* ── Logos strip ──────────────────────────────────────── */
        .logos-strip {
            display: flex; align-items: center; gap: 48px;
            overflow: hidden; flex-wrap: nowrap;
        }

        /* ── Section label ────────────────────────────────────── */
        .section-label {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12px; font-weight: 700; letter-spacing: 0.1em;
            text-transform: uppercase; color: var(--accent);
            margin-bottom: 12px;
        }

        /* ── Feature card ─────────────────────────────────────── */
        .feat-card {
            background: #f8fafc; border-radius: 16px; padding: 32px;
            border: 1px solid #f1f5f9;
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        }
        .feat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 48px rgba(0,0,0,0.08);
            border-color: #e2e8f0;
        }
        .feat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
        }

        /* ── Module pill ──────────────────────────────────────── */
        .module-pill {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 16px; border-radius: 99px;
            font-size: 13px; font-weight: 600;
            border: 1px solid #e2e8f0;
            color: #475569;
            background: #fff;
            transition: border-color 0.2s, color 0.2s;
        }
        .module-pill:hover { border-color: var(--accent); color: var(--accent); }

        /* ── How it works steps ───────────────────────────────── */
        .step-number {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 18px;
            background: var(--accent); color: #fff;
            flex-shrink: 0;
        }

        /* ── Testimonial card ─────────────────────────────────── */
        .testi-card {
            background: #fff; border-radius: 16px; padding: 32px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        /* ── Pricing card ─────────────────────────────────────── */
        .pricing-card {
            background: #fff; border-radius: 20px; padding: 40px 32px;
            border: 2px solid #f1f5f9;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .pricing-card:hover { transform: translateY(-4px); box-shadow: 0 24px 60px rgba(0,0,0,0.08); }
        .pricing-card.featured {
            background: #0f172a;
            border-color: var(--accent);
            box-shadow: 0 24px 60px rgba(99,102,241,0.25);
        }

        /* ── CTA section ──────────────────────────────────────── */
        .cta-section {
            background: #0f172a; position: relative; overflow: hidden;
        }
        .cta-glow {
            position: absolute; bottom: -50%; left: 50%; transform: translateX(-50%);
            width: 800px; height: 400px; border-radius: 50%;
            background: radial-gradient(ellipse, rgba(99,102,241,0.2) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ── Scroll reveal ────────────────────────────────────── */
        .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.6s ease, transform 0.6s ease; }
        .reveal.visible { opacity: 1; transform: none; }

        /* ── Mobile menu ──────────────────────────────────────── */
        #mobile-menu { display: none; }
        #mobile-menu.open { display: block; }

        /* ── Divider ──────────────────────────────────────────── */
        .section-divider { height: 1px; background: #f1f5f9; margin: 0; }

        @media (max-width: 768px) {
            .hero-grid { background-size: 40px 40px; }

            /* Collapse 2-col grids on mobile */
            .two-col-grid { grid-template-columns: 1fr !important; gap: 40px !important; }

            /* Footer grid */
            .footer-grid { grid-template-columns: 1fr 1fr !important; gap: 32px !important; }
        }
        @media (max-width: 480px) {
            .footer-grid { grid-template-columns: 1fr !important; }
        }
    </style>
</head>
<body class="antialiased">

{{-- ══════════════════════════════════════════════════════════════════
     NAVBAR
═══════════════════════════════════════════════════════════════════════ --}}
<nav id="navbar" class="font-display">
    <div style="max-width:1200px; margin:0 auto; padding:0 24px;">
        <div style="display:flex; align-items:center; justify-content:space-between; height:64px;">

            {{-- Logo --}}
            <a href="/" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
                <div style="width:34px; height:34px; border-radius:9px; background:var(--accent); display:flex; align-items:center; justify-content:center;">
                    <i data-lucide="wrench" style="width:17px; height:17px; color:#fff;"></i>
                </div>
                <span class="nav-logo font-display" style="font-weight:800; font-size:18px; color:#fff; letter-spacing:-0.02em;">
                    {{ $settings['site_name'] ?? 'CMMS Pro' }}
                </span>
            </a>

            {{-- Nav links (desktop) --}}
            <div style="display:flex; align-items:center; gap:4px;" class="hidden lg:flex">
                @foreach([
                    ['label' => 'Producto',      'href' => '#features'],
                    ['label' => 'Módulos',        'href' => '#modules'],
                    ['label' => 'Cómo funciona',  'href' => '#how-it-works'],
                    ['label' => 'Precios',         'href' => '#pricing'],
                    ['label' => 'Clientes',        'href' => '#testimonials'],
                ] as $item)
                    <a href="{{ $item['href'] }}" class="nav-link"
                       style="padding:8px 14px; border-radius:8px; font-size:14px; font-weight:500; color:rgba(255,255,255,0.8); text-decoration:none; transition:color 0.2s, background 0.2s;"
                       onmouseover="this.style.color='#fff'; this.style.background='rgba(255,255,255,0.06)'"
                       onmouseout="this.style.color='rgba(255,255,255,0.8)'; this.style.background='transparent'">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>

            {{-- CTAs --}}
            <div style="display:flex; align-items:center; gap:10px;" class="hidden lg:flex">
                <a href="{{ route('login') }}"
                   class="nav-cta-ghost"
                   style="padding:8px 18px; border-radius:9px; font-size:14px; font-weight:600; color:#fff; text-decoration:none; border:1px solid rgba(255,255,255,0.18); transition:background 0.2s;"
                   onmouseover="this.style.background='rgba(255,255,255,0.08)'"
                   onmouseout="this.style.background='transparent'">
                    Iniciar sesión
                </a>
                <a href="{{ route('register') }}"
                   style="padding:8px 18px; border-radius:9px; font-size:14px; font-weight:700; background:var(--accent); color:#fff; text-decoration:none; transition:background 0.2s; box-shadow:0 2px 12px rgba(99,102,241,0.4);">
                    Prueba gratis
                </a>
            </div>

            {{-- Mobile hamburger --}}
            <button id="mobile-toggle" class="lg:hidden"
                    style="background:none; border:none; cursor:pointer; padding:8px; color:#fff;"
                    onclick="document.getElementById('mobile-menu').classList.toggle('open')">
                <i data-lucide="menu" style="width:22px; height:22px;"></i>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div id="mobile-menu" style="background:#0f172a; border-top:1px solid rgba(255,255,255,0.08); padding:16px 24px 24px;">
        @foreach([
            ['label' => 'Producto',     'href' => '#features'],
            ['label' => 'Módulos',       'href' => '#modules'],
            ['label' => 'Cómo funciona', 'href' => '#how-it-works'],
            ['label' => 'Precios',        'href' => '#pricing'],
            ['label' => 'Clientes',       'href' => '#testimonials'],
        ] as $item)
            <a href="{{ $item['href'] }}"
               style="display:block; padding:12px 0; font-size:15px; font-weight:600; color:rgba(255,255,255,0.85); text-decoration:none; border-bottom:1px solid rgba(255,255,255,0.06);">
                {{ $item['label'] }}
            </a>
        @endforeach
        <div style="margin-top:20px; display:flex; flex-direction:column; gap:10px;">
            <a href="{{ route('login') }}" style="text-align:center; padding:12px; border-radius:10px; border:1px solid rgba(255,255,255,0.18); color:#fff; font-weight:600; font-size:14px; text-decoration:none;">
                Iniciar sesión
            </a>
            <a href="{{ route('register') }}" style="text-align:center; padding:12px; border-radius:10px; background:var(--accent); color:#fff; font-weight:700; font-size:14px; text-decoration:none;">
                Prueba gratis 14 días
            </a>
        </div>
    </div>
</nav>

{{-- ══════════════════════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════════════════════════ --}}
<section class="hero">
    <div class="hero-grid"></div>
    <div class="hero-glow"></div>

    <div style="max-width:1200px; margin:0 auto; padding:120px 24px 80px; position:relative; z-index:1;">
        <div style="max-width:780px; margin:0 auto; text-align:center;">

            {{-- Badge --}}
            <div class="badge-pill" style="margin-bottom:28px;">
                <i data-lucide="zap" style="width:12px; height:12px;"></i>
                Plataforma CMMS para equipos LATAM
            </div>

            {{-- Headline --}}
            <h1 class="font-display" style="font-size:clamp(2.4rem,5vw,4rem); font-weight:800; line-height:1.1; letter-spacing:-0.03em; color:#fff; margin:0 0 24px;">
                {!! nl2br(e($settings['hero_title'] ?? 'El CMMS que entiende\ncómo trabaja tu equipo')) !!}
            </h1>

            {{-- Subtitle --}}
            <p style="font-size:1.125rem; color:rgba(255,255,255,0.6); line-height:1.7; margin:0 0 40px; max-width:580px; margin-left:auto; margin-right:auto;">
                {{ $settings['hero_subtitle'] ?? 'Reduce costos, aumenta disponibilidad y toma decisiones basadas en datos con la plataforma de mantenimiento más completa del mercado.' }}
            </p>

            {{-- CTA buttons --}}
            <div style="display:flex; align-items:center; justify-content:center; gap:14px; flex-wrap:wrap; margin-bottom:56px;">
                <a href="{{ route('register') }}" class="btn-primary">
                    {{ $settings['hero_cta_primary'] ?? 'Prueba gratis 14 días' }}
                    <i data-lucide="arrow-right" style="width:16px; height:16px;"></i>
                </a>
                <a href="#how-it-works" class="btn-ghost-light">
                    <i data-lucide="play-circle" style="width:16px; height:16px;"></i>
                    {{ $settings['hero_cta_secondary'] ?? 'Ver demostración' }}
                </a>
            </div>

            {{-- Trust note --}}
            <p style="font-size:12px; color:rgba(255,255,255,0.35); margin-bottom:64px;">
                Sin tarjeta de crédito · Configuración en 5 minutos · Cancela cuando quieras
            </p>

            {{-- Dashboard Mockup --}}
            <div class="mockup-wrapper reveal" style="text-align:left;">
                <div class="mockup-bar">
                    <div class="mockup-dot" style="background:#ff5f57;"></div>
                    <div class="mockup-dot" style="background:#febc2e;"></div>
                    <div class="mockup-dot" style="background:#28c840;"></div>
                    <div style="flex:1; margin-left:12px; background:rgba(255,255,255,0.06); border-radius:4px; height:22px; display:flex; align-items:center; padding:0 10px;">
                        <span style="font-size:11px; color:rgba(255,255,255,0.3);">app.cmmspro.com/dashboard</span>
                    </div>
                </div>
                {{-- Simulated dashboard UI --}}
                <div style="padding:24px; background:#1e293b;">
                    {{-- Top KPI row --}}
                    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px;">
                        @foreach([
                            ['label'=>'Órdenes activas','value'=>'24','color'=>'#6366f1','icon'=>'clipboard-list'],
                            ['label'=>'Activos operativos','value'=>'142','color'=>'#22c55e','icon'=>'cpu'],
                            ['label'=>'Mantenimientos hoy','value'=>'7','color'=>'#f59e0b','icon'=>'calendar'],
                            ['label'=>'Alertas críticas','value'=>'3','color'=>'#ef4444','icon'=>'alert-triangle'],
                        ] as $kpi)
                        <div style="background:#0f172a; border-radius:10px; padding:16px; border:1px solid rgba(255,255,255,0.06);">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                                <span style="font-size:11px; color:rgba(255,255,255,0.4);">{{ $kpi['label'] }}</span>
                                <div style="width:28px; height:28px; border-radius:7px; background:{{ $kpi['color'] }}18; display:flex; align-items:center; justify-content:center;">
                                    <i data-lucide="{{ $kpi['icon'] }}" style="width:13px; height:13px; color:{{ $kpi['color'] }};"></i>
                                </div>
                            </div>
                            <span style="font-size:22px; font-weight:800; color:#fff; font-family:'Plus Jakarta Sans',sans-serif;">{{ $kpi['value'] }}</span>
                        </div>
                        @endforeach
                    </div>
                    {{-- Charts row --}}
                    <div style="display:grid; grid-template-columns:2fr 1fr; gap:12px;">
                        <div style="background:#0f172a; border-radius:10px; padding:20px; border:1px solid rgba(255,255,255,0.06);">
                            <p style="font-size:12px; font-weight:600; color:rgba(255,255,255,0.5); margin:0 0 16px;">Órdenes de trabajo — últimos 7 días</p>
                            <div style="display:flex; align-items:flex-end; gap:6px; height:72px;">
                                @foreach([40,65,45,80,55,90,70] as $h)
                                    <div style="flex:1; border-radius:4px; background:linear-gradient(to top, #6366f1, #818cf8); height:{{ $h }}%; opacity:0.85;"></div>
                                @endforeach
                            </div>
                        </div>
                        <div style="background:#0f172a; border-radius:10px; padding:20px; border:1px solid rgba(255,255,255,0.06);">
                            <p style="font-size:12px; font-weight:600; color:rgba(255,255,255,0.5); margin:0 0 12px;">Por estado</p>
                            @foreach([['Completadas','#22c55e',60],['En progreso','#6366f1',25],['Pendientes','#f59e0b',15]] as [$label,$color,$pct])
                            <div style="margin-bottom:10px;">
                                <div style="display:flex; justify-content:space-between; font-size:11px; color:rgba(255,255,255,0.5); margin-bottom:4px;">
                                    <span>{{ $label }}</span><span>{{ $pct }}%</span>
                                </div>
                                <div style="height:4px; border-radius:2px; background:rgba(255,255,255,0.06);">
                                    <div style="height:100%; border-radius:2px; background:{{ $color }}; width:{{ $pct }}%;"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     SOCIAL PROOF — Logos
═══════════════════════════════════════════════════════════════════════ --}}
<section style="padding:48px 24px; background:#fff; border-bottom:1px solid #f1f5f9;">
    <div style="max-width:1200px; margin:0 auto;">
        <p style="text-align:center; font-size:13px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:32px;">
            Usado por equipos de mantenimiento en toda LATAM
        </p>
        <div style="display:flex; align-items:center; justify-content:center; gap:48px; flex-wrap:wrap;">
            @foreach(['Pemex','GRUMA','Vitro','CEMEX','Bimbo','Grupo Herdez'] as $company)
            <div style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:800; font-size:18px; color:#cbd5e1; letter-spacing:-0.02em;">
                {{ $company }}
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     STATS
═══════════════════════════════════════════════════════════════════════ --}}
<section style="padding:80px 24px; background:#f8fafc;">
    <div style="max-width:1200px; margin:0 auto;">
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:2px; background:#e2e8f0; border-radius:20px; overflow:hidden;">
            @foreach([
                ['number' => $settings['stat1_number'] ?? '98%',  'label' => $settings['stat1_label'] ?? 'Disponibilidad de activos',         'icon' => 'trending-up',  'color' => '#6366f1'],
                ['number' => $settings['stat2_number'] ?? '35%',  'label' => $settings['stat2_label'] ?? 'Reducción en costos de mantenimiento','icon' => 'piggy-bank',   'color' => '#22c55e'],
                ['number' => $settings['stat3_number'] ?? '2x',   'label' => $settings['stat3_label'] ?? 'Vida útil de equipos',               'icon' => 'clock',        'color' => '#f59e0b'],
                ['number' => $settings['stat4_number'] ?? '500+', 'label' => $settings['stat4_label'] ?? 'Empresas confían en nosotros',       'icon' => 'building-2',   'color' => '#3b82f6'],
            ] as $stat)
            <div class="reveal" style="background:#fff; padding:40px 32px; text-align:center;">
                <div style="width:48px; height:48px; border-radius:12px; background:{{ $stat['color'] }}12; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                    <i data-lucide="{{ $stat['icon'] }}" style="width:22px; height:22px; color:{{ $stat['color'] }};"></i>
                </div>
                <div class="font-display" style="font-size:2.5rem; font-weight:800; color:#0f172a; letter-spacing:-0.03em; line-height:1;">
                    {{ $stat['number'] }}
                </div>
                <p style="font-size:14px; color:#64748b; margin:8px 0 0; font-weight:500;">{{ $stat['label'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     FEATURES — Capabilities
═══════════════════════════════════════════════════════════════════════ --}}
<section id="features" style="padding:100px 24px; background:#fff;">
    <div style="max-width:1200px; margin:0 auto;">

        <div class="reveal" style="text-align:center; max-width:620px; margin:0 auto 64px;">
            <div class="section-label">
                <i data-lucide="sparkles" style="width:12px; height:12px;"></i>
                Capacidades
            </div>
            <h2 class="font-display" style="font-size:clamp(1.8rem,3.5vw,2.75rem); font-weight:800; letter-spacing:-0.03em; color:#0f172a; margin:0 0 16px; line-height:1.15;">
                {{ $settings['features_title'] ?? 'Todo lo que necesitas para gestionar el mantenimiento' }}
            </h2>
            <p style="font-size:1rem; color:#64748b; line-height:1.7; margin:0;">
                {{ $settings['features_subtitle'] ?? 'Una plataforma unificada para equipos de mantenimiento de cualquier tamaño.' }}
            </p>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px,1fr)); gap:20px;">
            @foreach([
                ['icon'=>'clipboard-list','color'=>'#6366f1','bg'=>'#eef2ff','title'=>'Órdenes de Trabajo','desc'=>'Crea, asigna y da seguimiento a correctivos y preventivos. Flujos de aprobación, checklist y evidencia fotográfica en campo.'],
                ['icon'=>'calendar-check','color'=>'#22c55e','bg'=>'#f0fdf4','title'=>'Mantenimiento Preventivo','desc'=>'Programas PM por tiempo, uso o condición. Genera OTs automáticamente y envía recordatorios antes del vencimiento.'],
                ['icon'=>'cpu','color'=>'#f59e0b','bg'=>'#fffbeb','title'=>'Gestión de Activos','desc'=>'Árbol de activos jerárquico, historial completo, documentos técnicos, QR y código de barras para trazabilidad total.'],
                ['icon'=>'package','color'=>'#3b82f6','bg'=>'#eff6ff','title'=>'Inventario y Refacciones','desc'=>'Control de stock, alertas de nivel mínimo, órdenes de compra y trazabilidad de consumo por activo u orden de trabajo.'],
                ['icon'=>'activity','color'=>'#8b5cf6','bg'=>'#f5f3ff','title'=>'IoT y Sensores','desc'=>'Conecta sensores industriales, monitorea variables en tiempo real y dispara alertas o OTs automáticas por umbral.'],
                ['icon'=>'brain','color'=>'#ec4899','bg'=>'#fdf2f8','title'=>'IA Predictiva','desc'=>'Detección de anomalías, cálculo de RUL y recomendaciones de mantenimiento basadas en datos históricos y modelos ML.'],
            ] as $f)
            <div class="feat-card reveal">
                <div class="feat-icon" style="background:{{ $f['bg'] }};">
                    <i data-lucide="{{ $f['icon'] }}" style="width:22px; height:22px; color:{{ $f['color'] }};"></i>
                </div>
                <h3 class="font-display" style="font-size:1.05rem; font-weight:700; color:#0f172a; margin:0 0 10px;">{{ $f['title'] }}</h3>
                <p style="font-size:14px; color:#64748b; line-height:1.65; margin:0;">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     SPLIT FEATURE — Mantenimiento Predictivo Highlight
═══════════════════════════════════════════════════════════════════════ --}}
<section style="padding:100px 24px; background:#f8fafc;">
    <div style="max-width:1200px; margin:0 auto;">
        <div class="two-col-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:80px; align-items:center;">

            {{-- Text --}}
            <div class="reveal">
                <div class="section-label">
                    <i data-lucide="brain" style="width:12px; height:12px;"></i>
                    IA Predictiva
                </div>
                <h2 class="font-display" style="font-size:clamp(1.8rem,3vw,2.5rem); font-weight:800; letter-spacing:-0.03em; color:#0f172a; margin:0 0 20px; line-height:1.15;">
                    Predice fallos antes de que ocurran
                </h2>
                <p style="font-size:1rem; color:#64748b; line-height:1.7; margin:0 0 32px;">
                    Nuestro motor de IA analiza vibraciones, temperatura y consumo para detectar patrones de deterioro temprano. Recibe alertas antes de que el equipo falle y planifica intervenciones con tiempo.
                </p>
                <div style="display:flex; flex-direction:column; gap:14px; margin-bottom:36px;">
                    @foreach([
                        'Detección de anomalías en tiempo real',
                        'Cálculo de vida útil restante (RUL)',
                        'Recomendaciones automáticas de PM',
                        'Integración nativa con sensores OPC-UA / MQTT',
                    ] as $point)
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:20px; height:20px; border-radius:6px; background:#6366f1; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i data-lucide="check" style="width:11px; height:11px; color:#fff;"></i>
                        </div>
                        <span style="font-size:14px; color:#374151; font-weight:500;">{{ $point }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('register') }}" class="btn-primary" style="display:inline-flex;">
                    Explorar módulo predictivo
                    <i data-lucide="arrow-right" style="width:16px; height:16px;"></i>
                </a>
            </div>

            {{-- Visual --}}
            <div class="reveal">
                <div class="mockup-wrapper">
                    <div class="mockup-bar">
                        <div class="mockup-dot" style="background:#ff5f57;"></div>
                        <div class="mockup-dot" style="background:#febc2e;"></div>
                        <div class="mockup-dot" style="background:#28c840;"></div>
                        <span style="margin-left:10px; font-size:11px; color:rgba(255,255,255,0.3);">Análisis predictivo</span>
                    </div>
                    <div style="padding:24px; background:#0f172a;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
                            <div>
                                <p style="font-size:11px; color:rgba(255,255,255,0.4); margin:0 0 4px;">Motor A3 — Compresor principal</p>
                                <p class="font-display" style="font-size:22px; font-weight:800; color:#fff; margin:0;">RUL: <span style="color:#f59e0b;">34 días</span></p>
                            </div>
                            <div style="padding:6px 12px; border-radius:99px; background:#f59e0b18; border:1px solid #f59e0b40;">
                                <span style="font-size:12px; font-weight:700; color:#f59e0b;">⚠ Atención</span>
                            </div>
                        </div>
                        {{-- Simulated sparkline --}}
                        <div style="margin-bottom:20px;">
                            <p style="font-size:11px; color:rgba(255,255,255,0.35); margin:0 0 8px;">Vibración (mm/s) — últimas 48h</p>
                            <svg viewBox="0 0 300 60" style="width:100%; height:60px;">
                                <defs>
                                    <linearGradient id="sparkGrad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#6366f1" stop-opacity="0.3"/>
                                        <stop offset="100%" stop-color="#6366f1" stop-opacity="0"/>
                                    </linearGradient>
                                </defs>
                                <path d="M0,50 C20,48 30,40 50,38 S80,30 100,28 S130,25 150,22 S180,18 200,16 S230,10 250,8 S280,4 300,2"
                                      fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round"/>
                                <path d="M0,50 C20,48 30,40 50,38 S80,30 100,28 S130,25 150,22 S180,18 200,16 S230,10 250,8 S280,4 300,2 L300,60 L0,60 Z"
                                      fill="url(#sparkGrad)"/>
                                {{-- Anomaly marker --}}
                                <circle cx="250" cy="8" r="4" fill="#f59e0b"/>
                                <line x1="250" y1="8" x2="250" y2="60" stroke="#f59e0b" stroke-width="1" stroke-dasharray="3,3" opacity="0.4"/>
                            </svg>
                        </div>
                        @foreach([
                            ['label'=>'Temperatura','val'=>'72°C','pct'=>72,'color'=>'#f59e0b'],
                            ['label'=>'Vibración','val'=>'4.2 mm/s','pct'=>84,'color'=>'#ef4444'],
                            ['label'=>'RPM','val'=>'2,980','pct'=>55,'color'=>'#22c55e'],
                        ] as $sensor)
                        <div style="margin-bottom:10px;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                <span style="font-size:11px; color:rgba(255,255,255,0.45);">{{ $sensor['label'] }}</span>
                                <span style="font-size:11px; font-weight:700; color:{{ $sensor['color'] }};">{{ $sensor['val'] }}</span>
                            </div>
                            <div style="height:4px; border-radius:2px; background:rgba(255,255,255,0.07);">
                                <div style="height:100%; border-radius:2px; background:{{ $sensor['color'] }}; width:{{ $sensor['pct'] }}%; opacity:0.85;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     MODULES
═══════════════════════════════════════════════════════════════════════ --}}
<section id="modules" style="padding:100px 24px; background:#fff;">
    <div style="max-width:1200px; margin:0 auto;">

        <div class="reveal" class="two-col-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:80px; align-items:start;">
            <div>
                <div class="section-label">
                    <i data-lucide="layers" style="width:12px; height:12px;"></i>
                    Módulos
                </div>
                <h2 class="font-display" style="font-size:clamp(1.8rem,3vw,2.5rem); font-weight:800; letter-spacing:-0.03em; color:#0f172a; margin:0 0 16px; line-height:1.15;">
                    Activa solo lo que necesitas
                </h2>
                <p style="font-size:1rem; color:#64748b; line-height:1.7; margin:0 0 32px;">
                    Modular y escalable. Comienza con lo básico y agrega capacidades conforme crece tu operación. Sin compromisos de largo plazo.
                </p>
                <a href="{{ route('register') }}" class="btn-ghost-dark">
                    Ver todos los módulos
                    <i data-lucide="arrow-right" style="width:15px; height:15px;"></i>
                </a>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:10px; padding-top:8px;">
                @foreach([
                    ['icon'=>'clipboard-list','label'=>'Órdenes de Trabajo'],
                    ['icon'=>'calendar-check','label'=>'Mantenimiento PM'],
                    ['icon'=>'cpu','label'=>'Gestión de Activos'],
                    ['icon'=>'package','label'=>'Inventario'],
                    ['icon'=>'activity','label'=>'IoT / Sensores'],
                    ['icon'=>'brain','label'=>'IA Predictiva'],
                    ['icon'=>'file-text','label'=>'Permisos de Trabajo'],
                    ['icon'=>'bar-chart-2','label'=>'Reportes y KPIs'],
                    ['icon'=>'users','label'=>'Multi-usuario'],
                    ['icon'=>'map-pin','label'=>'Ubicaciones'],
                    ['icon'=>'shopping-cart','label'=>'Compras'],
                    ['icon'=>'shield-check','label'=>'Auditorías'],
                ] as $mod)
                <div class="module-pill">
                    <i data-lucide="{{ $mod['icon'] }}" style="width:13px; height:13px; color:var(--accent);"></i>
                    {{ $mod['label'] }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     HOW IT WORKS
═══════════════════════════════════════════════════════════════════════ --}}
<section id="how-it-works" style="padding:100px 24px; background:#f8fafc;">
    <div style="max-width:1200px; margin:0 auto;">

        <div class="reveal" style="text-align:center; max-width:560px; margin:0 auto 64px;">
            <div class="section-label">
                <i data-lucide="map" style="width:12px; height:12px;"></i>
                Cómo funciona
            </div>
            <h2 class="font-display" style="font-size:clamp(1.8rem,3.5vw,2.75rem); font-weight:800; letter-spacing:-0.03em; color:#0f172a; margin:0 0 16px; line-height:1.15;">
                En funcionamiento en menos de un día
            </h2>
            <p style="font-size:1rem; color:#64748b; line-height:1.7; margin:0;">
                Sin consultores ni meses de implementación. Tu equipo puede estar operando el primer día.
            </p>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:28px;">
            @foreach([
                ['step'=>'01','title'=>'Regístrate gratis','desc'=>'Crea tu cuenta en 2 minutos. Sin datos de pago, sin compromisos.','icon'=>'user-plus'],
                ['step'=>'02','title'=>'Importa tus activos','desc'=>'Sube tu listado de equipos en Excel o conéctalos manualmente con código QR.','icon'=>'upload'],
                ['step'=>'03','title'=>'Configura PM y OT','desc'=>'Define planes de mantenimiento preventivo y crea tus primeras órdenes de trabajo.','icon'=>'settings-2'],
                ['step'=>'04','title'=>'Opera desde el campo','desc'=>'Tu equipo trabaja desde móvil. Evidencias, firmas y actualizaciones en tiempo real.','icon'=>'smartphone'],
            ] as $idx => $step)
            <div class="reveal" style="background:#fff; border-radius:16px; padding:32px; border:1px solid #f1f5f9; position:relative;">
                @if($idx < 3)
                <div style="position:absolute; top:38px; right:-14px; width:28px; height:2px; background:linear-gradient(90deg,#e2e8f0,transparent); display:none;" class="lg:block"></div>
                @endif
                <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px;">
                    <div class="step-number">{{ $step['step'] }}</div>
                    <div style="width:40px; height:40px; border-radius:10px; background:#f8fafc; display:flex; align-items:center; justify-content:center; border:1px solid #e2e8f0;">
                        <i data-lucide="{{ $step['icon'] }}" style="width:18px; height:18px; color:#6366f1;"></i>
                    </div>
                </div>
                <h3 class="font-display" style="font-size:1rem; font-weight:700; color:#0f172a; margin:0 0 8px;">{{ $step['title'] }}</h3>
                <p style="font-size:14px; color:#64748b; line-height:1.6; margin:0;">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     TESTIMONIALS
═══════════════════════════════════════════════════════════════════════ --}}
<section id="testimonials" style="padding:100px 24px; background:#fff;">
    <div style="max-width:1200px; margin:0 auto;">

        <div class="reveal" style="text-align:center; max-width:520px; margin:0 auto 56px;">
            <div class="section-label">
                <i data-lucide="star" style="width:12px; height:12px;"></i>
                Testimonios
            </div>
            <h2 class="font-display" style="font-size:clamp(1.8rem,3.5vw,2.75rem); font-weight:800; letter-spacing:-0.03em; color:#0f172a; margin:0; line-height:1.15;">
                Equipos que ya transformaron su mantenimiento
            </h2>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:20px;">
            @foreach([
                [
                    'quote' => 'Redujimos el tiempo de respuesta a fallas en un 60%. Antes tardábamos días en asignar y cerrar una OT, ahora es cuestión de horas.',
                    'name'  => 'Carlos Mendoza',
                    'role'  => 'Jefe de Mantenimiento · Planta automotriz, Monterrey',
                    'initials' => 'CM', 'color' => '#6366f1',
                    'stars' => 5,
                ],
                [
                    'quote' => 'El módulo de IoT nos permite ver en tiempo real el estado de nuestros compresores. Tuvimos ROI en menos de 3 meses.',
                    'name'  => 'Ana Ruiz',
                    'role'  => 'Directora de Operaciones · Industria alimentaria, CDMX',
                    'initials' => 'AR', 'color' => '#22c55e',
                    'stars' => 5,
                ],
                [
                    'quote' => 'Implementamos en dos días. El soporte es excelente y la app móvil es muy fácil para nuestros técnicos en campo.',
                    'name'  => 'Roberto Vega',
                    'role'  => 'Gerente de Facility · Corporativo, Guadalajara',
                    'initials' => 'RV', 'color' => '#f59e0b',
                    'stars' => 5,
                ],
            ] as $t)
            <div class="testi-card reveal">
                {{-- Stars --}}
                <div style="display:flex; gap:3px; margin-bottom:16px;">
                    @for($i = 0; $i < $t['stars']; $i++)
                    <i data-lucide="star" style="width:14px; height:14px; color:#f59e0b; fill:#f59e0b;"></i>
                    @endfor
                </div>
                <p style="font-size:15px; color:#374151; line-height:1.7; margin:0 0 24px; font-style:italic;">
                    "{{ $t['quote'] }}"
                </p>
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:40px; height:40px; border-radius:50%; background:{{ $t['color'] }}; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:14px; flex-shrink:0;">
                        {{ $t['initials'] }}
                    </div>
                    <div>
                        <p style="font-size:14px; font-weight:700; color:#0f172a; margin:0;">{{ $t['name'] }}</p>
                        <p style="font-size:12px; color:#94a3b8; margin:2px 0 0;">{{ $t['role'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     PRICING
═══════════════════════════════════════════════════════════════════════ --}}
<section id="pricing" style="padding:100px 24px; background:#f8fafc;">
    <div style="max-width:1200px; margin:0 auto;">

        <div class="reveal" style="text-align:center; max-width:560px; margin:0 auto 64px;">
            <div class="section-label">
                <i data-lucide="tag" style="width:12px; height:12px;"></i>
                Precios
            </div>
            <h2 class="font-display" style="font-size:clamp(1.8rem,3.5vw,2.75rem); font-weight:800; letter-spacing:-0.03em; color:#0f172a; margin:0 0 16px; line-height:1.15;">
                Planes simples y transparentes
            </h2>
            <p style="font-size:1rem; color:#64748b; line-height:1.7; margin:0;">
                Sin cargos ocultos. Cambia de plan cuando quieras.
            </p>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:20px; max-width:960px; margin:0 auto;">

            {{-- Starter --}}
            <div class="pricing-card reveal">
                <p class="font-display" style="font-size:13px; font-weight:700; color:#6366f1; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 8px;">Starter</p>
                <div style="display:flex; align-items:baseline; gap:4px; margin-bottom:6px;">
                    <span class="font-display" style="font-size:2.5rem; font-weight:800; color:#0f172a; letter-spacing:-0.03em;">$49</span>
                    <span style="font-size:14px; color:#94a3b8;">/mes</span>
                </div>
                <p style="font-size:13px; color:#94a3b8; margin:0 0 28px;">Hasta 5 usuarios · 100 activos</p>
                <div style="height:1px; background:#f1f5f9; margin-bottom:28px;"></div>
                <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:32px;">
                    @foreach(['OTs ilimitadas','Mantenimiento PM básico','App móvil','Soporte por email'] as $feat)
                    <div style="display:flex; align-items:center; gap:10px;">
                        <i data-lucide="check" style="width:16px; height:16px; color:#22c55e; flex-shrink:0;"></i>
                        <span style="font-size:14px; color:#374151;">{{ $feat }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('register') }}" class="btn-ghost-dark" style="width:100%; justify-content:center; text-align:center;">
                    Comenzar gratis
                </a>
            </div>

            {{-- Professional (featured) --}}
            <div class="pricing-card featured reveal" style="position:relative;">
                <div style="position:absolute; top:-12px; left:50%; transform:translateX(-50%); padding:4px 14px; border-radius:99px; background:var(--accent); font-size:11px; font-weight:800; color:#fff; white-space:nowrap; text-transform:uppercase; letter-spacing:0.06em;">
                    Más popular
                </div>
                <p class="font-display" style="font-size:13px; font-weight:700; color:#818cf8; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 8px;">Professional</p>
                <div style="display:flex; align-items:baseline; gap:4px; margin-bottom:6px;">
                    <span class="font-display" style="font-size:2.5rem; font-weight:800; color:#fff; letter-spacing:-0.03em;">$149</span>
                    <span style="font-size:14px; color:rgba(255,255,255,0.4);">/mes</span>
                </div>
                <p style="font-size:13px; color:rgba(255,255,255,0.4); margin:0 0 28px;">Hasta 20 usuarios · 500 activos</p>
                <div style="height:1px; background:rgba(255,255,255,0.1); margin-bottom:28px;"></div>
                <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:32px;">
                    @foreach(['Todo lo de Starter','IoT y sensores','IA Predictiva básica','Inventario avanzado','Reportes y KPIs','Soporte prioritario'] as $feat)
                    <div style="display:flex; align-items:center; gap:10px;">
                        <i data-lucide="check" style="width:16px; height:16px; color:#818cf8; flex-shrink:0;"></i>
                        <span style="font-size:14px; color:rgba(255,255,255,0.8);">{{ $feat }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('register') }}" class="btn-primary" style="width:100%; justify-content:center; text-align:center;">
                    Comenzar gratis
                </a>
            </div>

            {{-- Enterprise --}}
            <div class="pricing-card reveal">
                <p class="font-display" style="font-size:13px; font-weight:700; color:#6366f1; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 8px;">Enterprise</p>
                <div style="display:flex; align-items:baseline; gap:4px; margin-bottom:6px;">
                    <span class="font-display" style="font-size:2.5rem; font-weight:800; color:#0f172a; letter-spacing:-0.03em;">Custom</span>
                </div>
                <p style="font-size:13px; color:#94a3b8; margin:0 0 28px;">Usuarios y activos ilimitados</p>
                <div style="height:1px; background:#f1f5f9; margin-bottom:28px;"></div>
                <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:32px;">
                    @foreach(['Todo lo de Professional','IA Predictiva completa','White label disponible','SLA garantizado','Onboarding dedicado','Integración ERP/SAP'] as $feat)
                    <div style="display:flex; align-items:center; gap:10px;">
                        <i data-lucide="check" style="width:16px; height:16px; color:#22c55e; flex-shrink:0;"></i>
                        <span style="font-size:14px; color:#374151;">{{ $feat }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="mailto:{{ $settings['contact_email'] ?? 'hola@cmmspro.com' }}" class="btn-ghost-dark" style="width:100%; justify-content:center; text-align:center;">
                    Hablar con ventas
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     CTA FINAL
═══════════════════════════════════════════════════════════════════════ --}}
<section class="cta-section">
    <div class="cta-glow"></div>
    <div style="max-width:1200px; margin:0 auto; padding:100px 24px; text-align:center; position:relative; z-index:1;">
        <div class="reveal">
            <div class="badge-pill" style="margin:0 auto 28px;">
                <i data-lucide="rocket" style="width:12px; height:12px;"></i>
                Empieza hoy sin costo
            </div>
            <h2 class="font-display" style="font-size:clamp(2rem,4vw,3.5rem); font-weight:800; letter-spacing:-0.03em; color:#fff; margin:0 0 20px; line-height:1.1;">
                Tu equipo merece mejores herramientas
            </h2>
            <p style="font-size:1.1rem; color:rgba(255,255,255,0.55); line-height:1.7; max-width:520px; margin:0 auto 40px;">
                Únete a cientos de empresas que ya optimizaron su mantenimiento con CMMS Pro. Sin consultores, sin costos de arranque.
            </p>
            <div style="display:flex; align-items:center; justify-content:center; gap:14px; flex-wrap:wrap;">
                <a href="{{ route('register') }}" class="btn-primary" style="font-size:16px; padding:15px 32px;">
                    Prueba gratis 14 días
                    <i data-lucide="arrow-right" style="width:18px; height:18px;"></i>
                </a>
                <a href="mailto:{{ $settings['contact_email'] ?? 'hola@cmmspro.com' }}" class="btn-ghost-light" style="font-size:16px; padding:15px 32px;">
                    Hablar con un experto
                </a>
            </div>
            <p style="margin-top:20px; font-size:13px; color:rgba(255,255,255,0.3);">
                Sin tarjeta de crédito · Cancela cuando quieras
            </p>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════════════════════════════ --}}
<footer style="background:#020617; padding:64px 24px 32px;">
    <div style="max-width:1200px; margin:0 auto;">

        {{-- Top grid --}}
        <div class="footer-grid" style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:48px; margin-bottom:56px;">

            {{-- Brand --}}
            <div>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
                    <div style="width:34px; height:34px; border-radius:9px; background:var(--accent); display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="wrench" style="width:17px; height:17px; color:#fff;"></i>
                    </div>
                    <span class="font-display" style="font-weight:800; font-size:18px; color:#fff; letter-spacing:-0.02em;">
                        {{ $settings['site_name'] ?? 'CMMS Pro' }}
                    </span>
                </div>
                <p style="font-size:14px; color:rgba(255,255,255,0.4); line-height:1.7; max-width:280px; margin:0 0 24px;">
                    Software de Gestión de Mantenimiento Industrial para equipos LATAM. PM, CM, PdM, IoT e IA Predictiva.
                </p>
                {{-- Social --}}
                <div style="display:flex; gap:10px;">
                    @foreach([
                        ['icon' => 'x',       'label' => 'X'],
                        ['icon' => 'linkedin', 'label' => 'LinkedIn'],
                        ['icon' => 'play',     'label' => 'YouTube'],
                    ] as $social)
                    <a href="#" aria-label="{{ $social['label'] }}"
                       style="width:36px; height:36px; border-radius:9px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); transition:background 0.2s; text-decoration:none;"
                       onmouseover="this.style.background='rgba(255,255,255,0.12)'"
                       onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                        <i data-lucide="{{ $social['icon'] }}" style="width:15px; height:15px; color:rgba(255,255,255,0.5);"></i>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Producto --}}
            <div>
                <p style="font-size:12px; font-weight:700; color:rgba(255,255,255,0.3); text-transform:uppercase; letter-spacing:0.1em; margin:0 0 16px;">Producto</p>
                @foreach(['Funciones','Módulos','Integraciones','Roadmap','Actualizaciones'] as $link)
                <a href="#" style="display:block; font-size:14px; color:rgba(255,255,255,0.5); text-decoration:none; margin-bottom:10px; transition:color 0.2s;"
                   onmouseover="this.style.color='rgba(255,255,255,0.85)'"
                   onmouseout="this.style.color='rgba(255,255,255,0.5)'">{{ $link }}</a>
                @endforeach
            </div>

            {{-- Empresa --}}
            <div>
                <p style="font-size:12px; font-weight:700; color:rgba(255,255,255,0.3); text-transform:uppercase; letter-spacing:0.1em; margin:0 0 16px;">Empresa</p>
                @foreach(['Acerca de','Clientes','Blog','Prensa','Empleo'] as $link)
                <a href="#" style="display:block; font-size:14px; color:rgba(255,255,255,0.5); text-decoration:none; margin-bottom:10px; transition:color 0.2s;"
                   onmouseover="this.style.color='rgba(255,255,255,0.85)'"
                   onmouseout="this.style.color='rgba(255,255,255,0.5)'">{{ $link }}</a>
                @endforeach
            </div>

            {{-- Soporte --}}
            <div>
                <p style="font-size:12px; font-weight:700; color:rgba(255,255,255,0.3); text-transform:uppercase; letter-spacing:0.1em; margin:0 0 16px;">Soporte</p>
                @foreach(['Documentación','Centro de ayuda','Estado del servicio','Contacto','Seguridad'] as $link)
                <a href="#" style="display:block; font-size:14px; color:rgba(255,255,255,0.5); text-decoration:none; margin-bottom:10px; transition:color 0.2s;"
                   onmouseover="this.style.color='rgba(255,255,255,0.85)'"
                   onmouseout="this.style.color='rgba(255,255,255,0.5)'">{{ $link }}</a>
                @endforeach
            </div>
        </div>

        {{-- Divider --}}
        <div style="height:1px; background:rgba(255,255,255,0.06); margin-bottom:28px;"></div>

        {{-- Bottom --}}
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
            <p style="font-size:13px; color:rgba(255,255,255,0.3); margin:0;">
                {{ $settings['footer_text'] ?? '© 2026 CMMS Pro. Todos los derechos reservados.' }}
            </p>
            <div style="display:flex; gap:20px;">
                @foreach(['Privacidad','Términos','Cookies'] as $link)
                <a href="#" style="font-size:13px; color:rgba(255,255,255,0.3); text-decoration:none; transition:color 0.2s;"
                   onmouseover="this.style.color='rgba(255,255,255,0.65)'"
                   onmouseout="this.style.color='rgba(255,255,255,0.3)'">{{ $link }}</a>
                @endforeach
            </div>
        </div>
    </div>
</footer>

{{-- ══════════════════════════════════════════════════════════════════
     SCRIPTS
═══════════════════════════════════════════════════════════════════════ --}}
<script>
    // Navbar scroll state
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 20);
    }, { passive: true });

    // Scroll reveal
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); } });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    // Smooth close mobile menu on link click
    document.querySelectorAll('#mobile-menu a').forEach(a => {
        a.addEventListener('click', () => document.getElementById('mobile-menu').classList.remove('open'));
    });
</script>
</body>
</html>
