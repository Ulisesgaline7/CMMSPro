<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <title>{{ isset($title) ? $title . ' – Super Admin' : 'Super Admin' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app-blade.js'])

    <style>
        .sa-nav-group-label {
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #64748b;
            padding: 16px 20px 6px;
        }
        .sa-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 20px;
            font-size: 12.5px;
            font-weight: 500;
            color: #94a3b8;
            border-radius: 0;
            transition: all 0.15s;
            position: relative;
            text-decoration: none;
        }
        .sa-nav-item:hover {
            background: rgba(99,102,241,0.08);
            color: #e2e8f0;
        }
        .sa-nav-item.active {
            background: rgba(99,102,241,0.15);
            color: #a5b4fc;
            border-left: 3px solid #6366f1;
            padding-left: 17px;
        }
        .sa-nav-item .nav-badge {
            margin-left: auto;
            font-size: 9px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 20px;
            background: rgba(99,102,241,0.25);
            color: #a5b4fc;
        }
        .sa-nav-item .nav-badge.green {
            background: rgba(34,197,94,0.2);
            color: #86efac;
        }
        .sa-nav-item .nav-badge.orange {
            background: rgba(249,115,22,0.2);
            color: #fdba74;
        }
        .sa-nav-item.disabled {
            opacity: 0.4;
            pointer-events: none;
            cursor: default;
        }
        .sa-nav-item .nav-badge.soon {
            background: rgba(100,116,139,0.2);
            color: #94a3b8;
            font-size: 8px;
        }
    </style>
</head>
<body class="antialiased" style="background:#0f172a; font-family: 'Inter', sans-serif;">

@php
    $user = auth()->user();
    $path = request()->path();
    $initials = strtoupper(substr($user->name, 0, 1)) . strtoupper(substr(strstr($user->name, ' ') ?: ' ', 1, 1));

    // Count badges
    $totalTenants = \App\Models\Tenant::withoutGlobalScopes()->count();
    $activeTenants = \App\Models\Tenant::withoutGlobalScopes()->where('status', \App\Enums\TenantStatus::Active)->count();
    $totalUsers = \App\Models\User::withoutGlobalScopes()->count();
@endphp

<div class="flex min-h-screen">

    {{-- ══ SIDEBAR ══════════════════════════════════════════════════════════ --}}
    <aside class="fixed left-0 top-0 h-screen flex flex-col z-50"
           style="width:260px; background:#1e293b; border-right:1px solid rgba(255,255,255,0.06);">

        {{-- Brand --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b" style="border-color:rgba(255,255,255,0.06);">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                 style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                <i data-lucide="shield-check" class="w-4 h-4 text-white"></i>
            </div>
            <div>
                <p class="text-white font-bold text-sm leading-none font-headline">CMMS Pro</p>
                <p class="text-xs mt-0.5" style="color:#6366f1;">Super Admin Panel</p>
            </div>
        </div>

        {{-- Scrollable nav --}}
        <nav class="flex-1 overflow-y-auto py-2" style="scrollbar-width:thin; scrollbar-color:#334155 transparent;">

            {{-- === PANEL === --}}
            <p class="sa-nav-group-label">Panel</p>
            <a href="{{ route('super-admin.dashboard') }}"
               class="sa-nav-item {{ $path === 'super-admin' ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0"></i>
                <span>Dashboard General</span>
            </a>

            {{-- === CLIENTES === --}}
            <p class="sa-nav-group-label">Gestión de Clientes</p>
            <a href="{{ route('super-admin.tenants.index') }}"
               class="sa-nav-item {{ str_starts_with($path, 'super-admin/tenants') ? 'active' : '' }}">
                <i data-lucide="building-2" class="w-4 h-4 shrink-0"></i>
                <span>Todos los Clientes</span>
                <span class="nav-badge">{{ $totalTenants }}</span>
            </a>
            <a href="{{ route('super-admin.tenants.index') }}?status=active"
               class="sa-nav-item">
                <i data-lucide="circle-check" class="w-4 h-4 shrink-0"></i>
                <span>Clientes Activos</span>
                <span class="nav-badge green">{{ $activeTenants }}</span>
            </a>
            <a href="{{ route('super-admin.tenants.index') }}?status=trial"
               class="sa-nav-item">
                <i data-lucide="clock" class="w-4 h-4 shrink-0"></i>
                <span>Cuentas Trial</span>
            </a>
            <a href="{{ route('super-admin.tenants.index') }}?status=suspended"
               class="sa-nav-item">
                <i data-lucide="circle-slash" class="w-4 h-4 shrink-0"></i>
                <span>Suspendidos</span>
            </a>

            {{-- === SUSCRIPCIONES === --}}
            <p class="sa-nav-group-label">Suscripciones & Ingresos</p>
            <a href="{{ route('super-admin.subscriptions.index') }}"
               class="sa-nav-item {{ str_starts_with($path, 'super-admin/subscriptions') ? 'active' : '' }}">
                <i data-lucide="credit-card" class="w-4 h-4 shrink-0"></i>
                <span>Suscripciones</span>
            </a>
            <a href="{{ route('super-admin.invoices.index') }}"
               class="sa-nav-item {{ str_starts_with($path, 'super-admin/invoices') ? 'active' : '' }}">
                <i data-lucide="receipt" class="w-4 h-4 shrink-0"></i>
                <span>Facturas & Pagos</span>
            </a>
            <a href="{{ route('super-admin.revenue.index') }}"
               class="sa-nav-item {{ str_starts_with($path, 'super-admin/revenue') ? 'active' : '' }}">
                <i data-lucide="trending-up" class="w-4 h-4 shrink-0"></i>
                <span>MRR & Analytics</span>
            </a>
            <a href="{{ route('super-admin.revenue.report') }}"
               class="sa-nav-item {{ $path === 'super-admin/revenue/report' ? 'active' : '' }}">
                <i data-lucide="bar-chart-2" class="w-4 h-4 shrink-0"></i>
                <span>Reporte de Ingresos</span>
            </a>

            {{-- === MÓDULOS === --}}
            <p class="sa-nav-group-label">Módulos & Planes</p>
            <a href="{{ route('super-admin.modules.index') }}"
               class="sa-nav-item {{ $path === 'super-admin/modules' ? 'active' : '' }}">
                <i data-lucide="puzzle" class="w-4 h-4 shrink-0"></i>
                <span>Catálogo de Módulos</span>
            </a>
            <a href="{{ route('super-admin.modules.assignment') }}"
               class="sa-nav-item {{ $path === 'super-admin/modules/assignment' ? 'active' : '' }}">
                <i data-lucide="toggle-left" class="w-4 h-4 shrink-0"></i>
                <span>Asignación por Cliente</span>
            </a>
            <a href="{{ route('super-admin.pricing.index') }}"
               class="sa-nav-item {{ str_starts_with($path, 'super-admin/pricing') ? 'active' : '' }}">
                <i data-lucide="package" class="w-4 h-4 shrink-0"></i>
                <span>Planes de Precios</span>
            </a>

            {{-- === USUARIOS === --}}
            <p class="sa-nav-group-label">Usuarios del Sistema</p>
            <a href="{{ route('super-admin.users.index') }}"
               class="sa-nav-item {{ str_starts_with($path, 'super-admin/users') ? 'active' : '' }}">
                <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
                <span>Todos los Usuarios</span>
                <span class="nav-badge">{{ $totalUsers }}</span>
            </a>
            <a href="{{ route('super-admin.users.index') }}"
               class="sa-nav-item">
                <i data-lucide="user-check" class="w-4 h-4 shrink-0"></i>
                <span>Admins de Tenant</span>
            </a>
            <a href="{{ route('super-admin.users.index') }}"
               class="sa-nav-item">
                <i data-lucide="user-cog" class="w-4 h-4 shrink-0"></i>
                <span>Impersonar Usuario</span>
            </a>

            {{-- === CONFIGURACIÓN === --}}
            <p class="sa-nav-group-label">Configuración del Sitio</p>
            <a href="#" class="sa-nav-item disabled">
                <i data-lucide="settings-2" class="w-4 h-4 shrink-0"></i>
                <span>Ajustes Generales</span>
                <span class="nav-badge soon">Pronto</span>
            </a>
            <a href="{{ route('super-admin.access.index') }}"
               class="sa-nav-item {{ str_starts_with($path, 'super-admin/access') ? 'active' : '' }}">
                <i data-lucide="globe" class="w-4 h-4 shrink-0"></i>
                <span>Dominios & Acceso</span>
            </a>
            <a href="#" class="sa-nav-item disabled">
                <i data-lucide="mail" class="w-4 h-4 shrink-0"></i>
                <span>Email & Notificaciones</span>
                <span class="nav-badge soon">Pronto</span>
            </a>
            <a href="{{ route('super-admin.site-settings.index') }}"
               class="sa-nav-item {{ str_starts_with($path, 'super-admin/site-settings') ? 'active' : '' }}">
                <i data-lucide="palette" class="w-4 h-4 shrink-0"></i>
                <span>Apariencia Global</span>
            </a>

            {{-- === CREDENCIALES === --}}
            <p class="sa-nav-group-label">Credenciales & API</p>
            <a href="#" class="sa-nav-item disabled">
                <i data-lucide="key" class="w-4 h-4 shrink-0"></i>
                <span>Claves API</span>
                <span class="nav-badge soon">Pronto</span>
            </a>
            <a href="#" class="sa-nav-item disabled">
                <i data-lucide="webhook" class="w-4 h-4 shrink-0"></i>
                <span>Webhooks</span>
                <span class="nav-badge soon">Pronto</span>
            </a>
            <a href="#" class="sa-nav-item disabled">
                <i data-lucide="zap" class="w-4 h-4 shrink-0"></i>
                <span>Integración Stripe</span>
                <span class="nav-badge soon">Pronto</span>
            </a>
            <a href="#" class="sa-nav-item disabled">
                <i data-lucide="plug" class="w-4 h-4 shrink-0"></i>
                <span>Integraciones</span>
                <span class="nav-badge soon">Pronto</span>
            </a>

            {{-- === CONTENIDO === --}}
            <p class="sa-nav-group-label">Contenido & Marketing</p>
            <a href="#" class="sa-nav-item disabled">
                <i data-lucide="file-text" class="w-4 h-4 shrink-0"></i>
                <span>Blog & Noticias</span>
                <span class="nav-badge soon">Pronto</span>
            </a>
            <a href="{{ route('super-admin.site-settings.index') }}"
               class="sa-nav-item {{ str_starts_with($path, 'super-admin/site-settings') ? 'active' : '' }}">
                <i data-lucide="layout" class="w-4 h-4 shrink-0"></i>
                <span>Landing Page</span>
            </a>
            <a href="#" class="sa-nav-item disabled">
                <i data-lucide="book-open" class="w-4 h-4 shrink-0"></i>
                <span>Documentación</span>
                <span class="nav-badge soon">Pronto</span>
            </a>

            {{-- === SISTEMA === --}}
            <p class="sa-nav-group-label">Sistema</p>
            <a href="#" class="sa-nav-item disabled">
                <i data-lucide="scroll-text" class="w-4 h-4 shrink-0"></i>
                <span>Logs del Sistema</span>
                <span class="nav-badge soon">Pronto</span>
            </a>
            <a href="#" class="sa-nav-item disabled">
                <i data-lucide="activity" class="w-4 h-4 shrink-0"></i>
                <span>Actividad Reciente</span>
                <span class="nav-badge soon">Pronto</span>
            </a>
            <a href="#" class="sa-nav-item disabled">
                <i data-lucide="server" class="w-4 h-4 shrink-0"></i>
                <span>Estado del Servidor</span>
                <span class="nav-badge soon">Pronto</span>
            </a>
            <a href="#" class="sa-nav-item disabled">
                <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
                <span>Modo Mantenimiento</span>
                <span class="nav-badge orange">OFF</span>
            </a>

            <div class="h-6"></div>

        </nav>

        {{-- Back to app --}}
        <div class="px-4 py-3 border-t" style="border-color:rgba(255,255,255,0.06);">
            <a href="{{ url('dashboard') }}"
               class="flex items-center gap-2 text-xs font-medium px-3 py-2 rounded-lg transition-colors"
               style="color:#64748b;" onmouseover="this.style.color='#e2e8f0'" onmouseout="this.style.color='#64748b'">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Volver al Panel Tenant</span>
            </a>
        </div>

    </aside>

    {{-- ══ MAIN AREA ════════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col min-h-screen" style="margin-left:260px; background:#f1f5f9;">

        {{-- ── Top Header ─────────────────────────────────────────────── --}}
        <header class="sticky top-0 z-40 flex items-center justify-between px-6 h-14 border-b bg-white"
                style="border-color:#e2e8f0;">

            <div class="flex items-center gap-3">
                {{-- Breadcrumb --}}
                <div class="flex items-center gap-1.5 text-xs">
                    <span class="font-semibold" style="color:#6366f1;">Super Admin</span>
                    @isset($breadcrumb)
                        <i data-lucide="chevron-right" class="w-3 h-3" style="color:#94a3b8;"></i>
                        <span style="color:#475569;">{{ $breadcrumb }}</span>
                    @endisset
                </div>

                {{-- Badge --}}
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider"
                      style="background:#ede9fe; color:#6d28d9;">
                    <i data-lucide="shield-check" class="w-2.5 h-2.5"></i>
                    Super Admin
                </span>
            </div>

            <div class="flex items-center gap-4">

                {{-- Sistema status --}}
                <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-full"
                     style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background:#22c55e;"></span>
                    <span class="text-[10px] font-bold uppercase tracking-wider" style="color:#16a34a;">Sistema OK</span>
                </div>

                {{-- Notifications --}}
                @php $unreadCount = $user->unreadNotifications()->count(); @endphp
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                            class="relative p-1.5 rounded-lg transition-colors hover:bg-slate-100">
                        <i data-lucide="bell" class="w-4.5 h-4.5" style="color:#64748b;"></i>
                        <span id="bell-badge"
                              class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 rounded-full text-white flex items-center justify-center text-[8px] font-bold"
                              style="background:#ef4444; {{ $unreadCount === 0 ? 'display:none;' : '' }}">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl border overflow-hidden z-50"
                         style="border-color:#e2e8f0;">
                        <div class="px-4 py-3 border-b flex items-center justify-between" style="border-color:#f1f5f9;">
                            <span class="text-sm font-bold" style="color:#0f172a;">Notificaciones</span>
                            @if($unreadCount > 0)
                                <form method="POST" action="{{ route('super-admin.notifications.mark-all-read') }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-[10px] font-bold transition-colors" style="color:#6366f1;">
                                        Marcar leídas
                                    </button>
                                </form>
                            @endif
                        </div>
                        @php $recentNotifs = $user->notifications()->latest()->limit(5)->get(); @endphp
                        @forelse($recentNotifs as $n)
                            @php
                                $nIcon  = $n->data['icon']  ?? 'bell';
                                $nColor = $n->data['color'] ?? '#6366f1';
                            @endphp
                            <div class="px-4 py-3 border-b flex gap-3 items-start {{ is_null($n->read_at) ? '' : 'opacity-50' }}"
                                 style="border-color:#f8fafc;">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                                     style="background:{{ $nColor }}18;">
                                    <i data-lucide="{{ $nIcon }}" class="w-3.5 h-3.5" style="color:{{ $nColor }};"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold leading-snug" style="color:#1e293b;">
                                        @if(is_null($n->read_at))
                                            <span class="inline-block w-1 h-1 rounded-full mr-1 align-middle" style="background:#6366f1;"></span>
                                        @endif
                                        {{ $n->data['title'] ?? 'Notificación' }}
                                    </p>
                                    <p class="text-[10px] mt-0.5" style="color:#94a3b8;">{{ $n->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center">
                                <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2" style="color:#e2e8f0;"></i>
                                <p class="text-xs" style="color:#94a3b8;">Sin notificaciones</p>
                            </div>
                        @endforelse
                        <div class="px-4 py-2.5 border-t" style="border-color:#f1f5f9;">
                            <a href="{{ route('super-admin.notifications.index') }}"
                               class="flex items-center justify-center gap-1.5 text-[11px] font-bold transition-colors py-1"
                               style="color:#6366f1;">
                                Ver todas las notificaciones
                                <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Profile Dropdown --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                            class="flex items-center gap-2.5 pl-3 pr-2 py-1.5 rounded-xl transition-colors hover:bg-slate-100">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-[11px] font-bold shrink-0"
                             style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                            {{ $initials }}
                        </div>
                        <div class="text-left hidden sm:block">
                            <p class="text-xs font-semibold leading-none" style="color:#1e293b;">{{ $user->name }}</p>
                            <p class="text-[10px] leading-none mt-0.5" style="color:#64748b;">Super Admin</p>
                        </div>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5" style="color:#94a3b8;"></i>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border overflow-hidden z-50"
                         style="border-color:#e2e8f0;">

                        {{-- User info header --}}
                        <div class="px-4 py-3 border-b" style="border-color:#f1f5f9; background:#f8fafc;">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold"
                                     style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold" style="color:#1e293b;">{{ $user->name }}</p>
                                    <p class="text-[10px]" style="color:#64748b;">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('super-admin.profile.edit') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-medium transition-colors hover:bg-slate-50"
                               style="color:#475569;">
                                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                Mi Perfil
                            </a>
                            <a href="{{ route('super-admin.profile.edit') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-medium transition-colors hover:bg-slate-50"
                               style="color:#475569;">
                                <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                                Seguridad & Contraseña
                            </a>
                            <a href="{{ route('super-admin.dashboard') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-medium transition-colors hover:bg-slate-50"
                               style="color:#475569;">
                                <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                                Configuración Sistema
                            </a>
                        </div>

                        <div class="border-t py-1" style="border-color:#f1f5f9;">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-medium transition-colors hover:bg-red-50"
                                        style="color:#ef4444;">
                                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mx-6 mt-4 flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium"
                 style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534;">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-4 flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium"
                 style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b;">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- Page title bar --}}
        @isset($headerTitle)
            <div class="px-6 pt-6 pb-2 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold" style="color:#0f172a; font-family:'Manrope',sans-serif;">{{ $headerTitle }}</h1>
                    @isset($headerSubtitle)
                        <p class="text-sm mt-0.5" style="color:#64748b;">{{ $headerSubtitle }}</p>
                    @endisset
                </div>
                @isset($headerActions)
                    <div class="flex items-center gap-2">
                        {{ $headerActions }}
                    </div>
                @endisset
            </div>
        @endisset

        {{-- Page content --}}
        <div class="flex-1 pb-10">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        <footer class="px-6 py-3 border-t flex items-center justify-between"
                style="border-color:#e2e8f0; background:#f8fafc;">
            <div class="flex items-center gap-3 text-[10px] font-semibold uppercase tracking-wider" style="color:#94a3b8;">
                <span class="flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background:#22c55e;"></span>
                    Todos los sistemas operativos
                </span>
                <span style="color:#cbd5e1;">|</span>
                <span>CMMS Pro v1.0.0-BETA</span>
            </div>
            <span class="text-[10px]" style="color:#cbd5e1;">Panel de Administración</span>
        </footer>

    </div>

</div>

</body>
</html>
