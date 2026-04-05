<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' – ' . config('app.name') : config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app-blade.js'])

    <style>
        .nav-section-label {
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(148,163,184,0.55);
            padding: 14px 20px 4px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 20px;
            font-size: 12px;
            font-weight: 500;
            color: rgba(148,163,184,0.8);
            border-left: 3px solid transparent;
            transition: all 0.15s;
            text-decoration: none;
        }
        .nav-item:hover {
            background: rgba(255,255,255,0.05);
            color: #e2e8f0;
        }
        .nav-item.active {
            background: rgba(255,255,255,0.09);
            color: #ffffff;
            border-left-color: #e07b30;
            padding-left: 17px;
        }
        .nav-item .nav-icon {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }
        .sidebar-scrollbar::-webkit-scrollbar { width: 3px; }
        .sidebar-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
    </style>
</head>
<body class="antialiased" style="background:#f4f5f9; font-family:'Inter',sans-serif;">

@php
    $user = auth()->user();
    $tenant = $user->tenant;
    $path = request()->path();
    $role = $user->role;

    // Role checks
    $isAdmin = $role === \App\Enums\UserRole::Admin;
    $isSupervisorOrAbove = in_array($role, [\App\Enums\UserRole::Admin, \App\Enums\UserRole::Supervisor]);
    $isTechnicianOrAbove = in_array($role, [\App\Enums\UserRole::Admin, \App\Enums\UserRole::Supervisor, \App\Enums\UserRole::Technician]);
    $isReader = $role === \App\Enums\UserRole::Reader;
    $isRequester = $role === \App\Enums\UserRole::Requester;

    // Module checks
    $hasIot = $user->isSuperAdmin() || $tenant?->hasModule('iot');
    $hasPredictive = $user->isSuperAdmin() || $tenant?->hasModule('ai_predictive');

    // Active path checks
    $isActive = fn(string $prefix) => $path === $prefix || str_starts_with($path, $prefix . '/');

    // Initials
    $nameParts = explode(' ', $user->name);
    $initials = strtoupper(substr($nameParts[0] ?? '', 0, 1)) . strtoupper(substr($nameParts[1] ?? '', 0, 1));

    // Avatar color based on role
    $avatarColors = [
        'admin'      => '#1e40af',
        'supervisor' => '#065f46',
        'technician' => '#92400e',
        'reader'     => '#3730a3',
        'requester'  => '#6b21a8',
    ];
    $avatarColor = $avatarColors[$role?->value ?? 'technician'] ?? '#1e293b';

    // Role label
    $roleLabels = [
        'admin'      => 'Administrador',
        'supervisor' => 'Supervisor',
        'technician' => 'Técnico',
        'reader'     => 'Auditor',
        'requester'  => 'Solicitante',
    ];
    $roleLabel = $roleLabels[$role?->value ?? ''] ?? 'Usuario';
@endphp

<div class="flex min-h-screen">

    {{-- ══ SIDEBAR ══════════════════════════════════════════════════════════ --}}
    <aside class="fixed left-0 top-0 h-screen flex flex-col z-50"
           style="width:248px; background:#002046; box-shadow:4px 0 20px rgba(0,0,0,0.25);">

        {{-- ── Brand & Tenant ──────────────────────────────────────── --}}
        <div class="px-5 pt-5 pb-4 border-b shrink-0" style="border-color:rgba(255,255,255,0.08);">
            @if($tenant?->logo_path)
                <img src="{{ Storage::url($tenant->logo_path) }}" alt="{{ $tenant->name }}"
                     class="h-7 object-contain mb-2">
            @else
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-7 h-7 rounded-md flex items-center justify-center shrink-0"
                         style="background:linear-gradient(135deg,#e07b30,#c45c1a);">
                        <i data-lucide="wrench" class="w-3.5 h-3.5 text-white"></i>
                    </div>
                    <h1 class="text-white text-sm font-extrabold tracking-wide leading-none font-headline">
                        {{ $tenant?->brand_name ?? config('app.name') }}
                    </h1>
                </div>
            @endif
            @if($tenant)
                <p class="text-[10px] font-medium truncate" style="color:rgba(148,163,184,0.6);">
                    {{ $tenant->name }}
                </p>
            @endif
        </div>

        {{-- ── Scrollable Nav ───────────────────────────────────────── --}}
        <nav class="flex-1 overflow-y-auto py-2 sidebar-scrollbar">

            {{-- PRINCIPAL --}}
            <a href="{{ url('dashboard') }}"
               class="nav-item {{ $isActive('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="nav-icon"></i>
                <span>Dashboard</span>
            </a>

            {{-- ── ACTIVOS & MANTENIMIENTO ──────────────────────────── --}}
            @if(!$isRequester)
                <p class="nav-section-label">Activos & Mantenimiento</p>

                @if($isSupervisorOrAbove || $isTechnicianOrAbove)
                    <a href="{{ url('assets') }}"
                       class="nav-item {{ $isActive('assets') || $isActive('asset-categories') ? 'active' : '' }}">
                        <i data-lucide="factory" class="nav-icon"></i>
                        <span>Activos / EAM</span>
                    </a>
                @endif

                <a href="{{ url('work-orders') }}"
                   class="nav-item {{ $isActive('work-orders') ? 'active' : '' }}">
                    <i data-lucide="wrench" class="nav-icon"></i>
                    <span>Órdenes de Trabajo</span>
                </a>

                @if($isSupervisorOrAbove)
                    <a href="{{ url('maintenance-plans') }}"
                       class="nav-item {{ $isActive('maintenance-plans') ? 'active' : '' }}">
                        <i data-lucide="calendar-clock" class="nav-icon"></i>
                        <span>Planes de Mantenimiento</span>
                    </a>
                @endif
            @endif

            {{-- ── SOLICITUDES / FACILITIES ────────────────────────── --}}
            @if($isRequester || $isAdmin || $isSupervisorOrAbove)
                @if(!$isRequester)
                    <p class="nav-section-label">Facilities</p>
                @else
                    <p class="nav-section-label">Mis Solicitudes</p>
                @endif
                <a href="{{ url('service-requests') }}"
                   class="nav-item {{ $isActive('service-requests') ? 'active' : '' }}">
                    <i data-lucide="building-2" class="nav-icon"></i>
                    <span>Solicitudes de Servicio</span>
                </a>
            @endif

            {{-- ── INVENTARIO & COMPRAS ─────────────────────────────── --}}
            @if(!$isRequester && !$isReader)
                <p class="nav-section-label">Inventario & Compras</p>
                <a href="{{ url('inventory') }}"
                   class="nav-item {{ $isActive('inventory') ? 'active' : '' }}">
                    <i data-lucide="package" class="nav-icon"></i>
                    <span>Inventario</span>
                </a>
                @if($isSupervisorOrAbove)
                    <a href="{{ url('purchase-orders') }}"
                       class="nav-item {{ $isActive('purchase-orders') ? 'active' : '' }}">
                        <i data-lucide="shopping-cart" class="nav-icon"></i>
                        <span>Órdenes de Compra</span>
                    </a>
                @endif
            @endif

            {{-- ── CUMPLIMIENTO ─────────────────────────────────────── --}}
            @if(!$isRequester)
                <p class="nav-section-label">Cumplimiento & Calidad</p>
                <a href="{{ url('audits') }}"
                   class="nav-item {{ $isActive('audits') ? 'active' : '' }}">
                    <i data-lucide="clipboard-check" class="nav-icon"></i>
                    <span>Auditorías</span>
                </a>
                @if(!$isReader)
                    <a href="{{ url('corrective-actions') }}"
                       class="nav-item {{ $isActive('corrective-actions') ? 'active' : '' }}">
                        <i data-lucide="shield-check" class="nav-icon"></i>
                        <span>CAPA</span>
                    </a>
                @endif
                <a href="{{ url('certifications') }}"
                   class="nav-item {{ $isActive('certifications') ? 'active' : '' }}">
                    <i data-lucide="award" class="nav-icon"></i>
                    <span>Certificaciones</span>
                </a>
                <a href="{{ url('documents') }}"
                   class="nav-item {{ $isActive('documents') ? 'active' : '' }}">
                    <i data-lucide="file-text" class="nav-icon"></i>
                    <span>Documentos</span>
                </a>
                @if($isTechnicianOrAbove)
                    <a href="{{ url('permits') }}"
                       class="nav-item {{ $isActive('permits') ? 'active' : '' }}">
                        <i data-lucide="lock" class="nav-icon"></i>
                        <span>LOTO / Permisos</span>
                    </a>
                @endif
            @endif

            {{-- ── OPERACIONES ──────────────────────────────────────── --}}
            @if($isSupervisorOrAbove)
                <p class="nav-section-label">Operaciones</p>
                <a href="{{ url('shifts') }}"
                   class="nav-item {{ $isActive('shifts') ? 'active' : '' }}">
                    <i data-lucide="clock" class="nav-icon"></i>
                    <span>Turnos</span>
                </a>
            @endif

            {{-- ── MÓDULOS AVANZADOS ────────────────────────────────── --}}
            @if($hasIot || $hasPredictive)
                <p class="nav-section-label">Módulos Avanzados</p>
                @if($hasIot)
                    <a href="{{ url('iot') }}"
                       class="nav-item {{ $isActive('iot') ? 'active' : '' }}">
                        <i data-lucide="radio-tower" class="nav-icon"></i>
                        <span>IoT Sensores</span>
                        <span class="ml-auto text-[9px] font-bold px-1.5 py-0.5 rounded-full"
                              style="background:rgba(34,197,94,0.15); color:#4ade80;">LIVE</span>
                    </a>
                @endif
                @if($hasPredictive)
                    <a href="{{ url('predictive') }}"
                       class="nav-item {{ $isActive('predictive') ? 'active' : '' }}">
                        <i data-lucide="brain-circuit" class="nav-icon"></i>
                        <span>IA Predictiva</span>
                        <span class="ml-auto text-[9px] font-bold px-1.5 py-0.5 rounded-full"
                              style="background:rgba(139,92,246,0.2); color:#c4b5fd;">AI</span>
                    </a>
                @endif
            @endif

            {{-- ── ADMINISTRACIÓN ───────────────────────────────────── --}}
            @if($isAdmin)
                <p class="nav-section-label">Administración</p>
                <a href="{{ url('billing/checkout') }}"
                   class="nav-item {{ $isActive('billing') ? 'active' : '' }}">
                    <i data-lucide="credit-card" class="nav-icon"></i>
                    <span>Facturación</span>
                </a>
                <a href="{{ url('settings/branding') }}"
                   class="nav-item {{ $isActive('settings/branding') ? 'active' : '' }}">
                    <i data-lucide="palette" class="nav-icon"></i>
                    <span>Marca & Apariencia</span>
                </a>
            @endif

            {{-- Super Admin shortcut --}}
            @if($user->isSuperAdmin())
                <div class="mx-3 mt-3 mb-1 rounded-lg px-3 py-2.5"
                     style="background:rgba(99,102,241,0.12); border:1px solid rgba(99,102,241,0.2);">
                    <p class="text-[9px] font-bold uppercase tracking-widest mb-1.5"
                       style="color:rgba(165,180,252,0.7);">Acceso Super Admin</p>
                    <a href="{{ url('super-admin') }}"
                       class="flex items-center gap-2 text-xs font-semibold transition-colors"
                       style="color:#a5b4fc;">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                        Panel Super Admin
                    </a>
                </div>
            @endif

            {{-- Impersonation banner --}}
            @if(session('impersonating_as'))
                <div class="mx-3 my-2 rounded-lg px-3 py-2.5"
                     style="background:rgba(249,115,22,0.15); border:1px solid rgba(249,115,22,0.25);">
                    <p class="text-[9px] font-bold uppercase tracking-widest mb-1.5"
                       style="color:rgba(253,186,116,0.8);">Impersonando usuario</p>
                    <form method="POST" action="{{ route('super-admin.users.stop-impersonating') }}">
                        @csrf
                        <button type="submit"
                                class="text-[11px] font-bold underline"
                                style="color:#fdba74;">← Volver a Super Admin</button>
                    </form>
                </div>
            @endif

            <div class="h-4"></div>
        </nav>

        {{-- ── Settings & Profile (bottom) ──────────────────────────── --}}
        <div class="shrink-0 border-t" style="border-color:rgba(255,255,255,0.08);">
            <a href="{{ url('settings/profile') }}"
               class="nav-item {{ $isActive('settings') ? 'active' : '' }}" style="padding:10px 20px;">
                <i data-lucide="settings" class="nav-icon"></i>
                <span>Configuración</span>
            </a>
        </div>

    </aside>

    {{-- ══ MAIN AREA ════════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col min-h-screen" style="margin-left:248px;">

        {{-- ── Top Header ─────────────────────────────────────────────── --}}
        <header class="sticky top-0 z-40 flex items-center justify-between px-6 h-14 bg-white border-b shrink-0"
                style="border-color:#e5e7eb;">

            {{-- Left: Title + Status --}}
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold tracking-wide font-headline" style="color:#002046;">
                    {{ $headerTitle ?? config('app.name') }}
                </span>
                <div class="h-4 w-px bg-gray-200 hidden sm:block"></div>
                <div class="hidden sm:flex items-center gap-1.5 px-2 py-0.5 rounded-full"
                     style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <i data-lucide="wifi" class="w-3 h-3" style="color:#16a34a;"></i>
                    <span class="text-[9px] font-bold uppercase tracking-widest" style="color:#16a34a;">En línea</span>
                </div>
            </div>

            {{-- Right: Notifications + Profile Dropdown --}}
            <div class="flex items-center gap-2">

                {{-- Notifications --}}
                @php $unreadCount = $user->unreadNotifications()->count(); @endphp
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                            class="relative p-2 rounded-lg transition-colors hover:bg-gray-100">
                        <i data-lucide="bell" class="w-4 h-4" style="color:#6b7280;"></i>
                        @if($unreadCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 rounded-full text-white flex items-center justify-center text-[8px] font-bold"
                                  style="background:#ef4444;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border overflow-hidden z-50"
                         style="border-color:#e5e7eb;">

                        <div class="flex items-center justify-between px-4 py-3 border-b" style="border-color:#f3f4f6;">
                            <span class="text-sm font-bold" style="color:#002046;">Notificaciones</span>
                            @if($unreadCount > 0)
                                <form action="{{ route('notifications.read-all') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-[10px] font-semibold" style="color:#3b82f6;">
                                        Marcar todas leídas
                                    </button>
                                </form>
                            @endif
                        </div>

                        @php $recentNotifications = $user->notifications()->latest()->limit(6)->get(); @endphp

                        @forelse($recentNotifications as $notif)
                            <div class="flex items-start gap-3 px-4 py-3 border-b transition-colors hover:bg-gray-50 {{ is_null($notif->read_at) ? '' : 'opacity-60' }}"
                                 style="border-color:#f9fafb;">
                                <div class="w-2 h-2 mt-1.5 rounded-full shrink-0"
                                     style="background:{{ is_null($notif->read_at) ? '#3b82f6' : '#d1d5db' }};"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold truncate" style="color:#111827;">
                                        {{ $notif->data['title'] ?? 'Notificación' }}
                                    </p>
                                    <p class="text-[11px] mt-0.5 line-clamp-2" style="color:#6b7280;">
                                        {{ $notif->data['message'] ?? '' }}
                                    </p>
                                    <p class="text-[10px] mt-1" style="color:#9ca3af;">
                                        {{ $notif->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center">
                                <i data-lucide="bell-off" class="w-6 h-6 mx-auto mb-2" style="color:#d1d5db;"></i>
                                <p class="text-xs" style="color:#9ca3af;">Sin notificaciones</p>
                            </div>
                        @endforelse

                        <a href="{{ route('notifications.index') }}"
                           class="block px-4 py-3 text-center text-xs font-semibold transition-colors hover:bg-gray-50 border-t"
                           style="color:#3b82f6; border-color:#f3f4f6;">
                            Ver todas las notificaciones
                        </a>
                    </div>
                </div>

                {{-- Profile Dropdown --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                            class="flex items-center gap-2 pl-2 pr-1.5 py-1.5 rounded-xl transition-colors hover:bg-gray-100">
                        {{-- Avatar --}}
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-[11px] font-bold shrink-0"
                             style="background:{{ $avatarColor }};">
                            {{ $initials }}
                        </div>
                        <div class="text-left hidden md:block">
                            <p class="text-xs font-semibold leading-none" style="color:#111827;">
                                {{ $user->name }}
                            </p>
                            <p class="text-[10px] leading-none mt-0.5" style="color:#9ca3af;">
                                {{ $roleLabel }}
                            </p>
                        </div>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 ml-0.5" style="color:#9ca3af;"></i>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-60 bg-white rounded-xl shadow-xl border overflow-hidden z-50"
                         style="border-color:#e5e7eb;">

                        {{-- Header --}}
                        <div class="px-4 py-3 border-b" style="border-color:#f3f4f6; background:#f9fafb;">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm font-bold shrink-0"
                                     style="background:{{ $avatarColor }};">
                                    {{ $initials }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold truncate" style="color:#111827;">
                                        {{ $user->name }}
                                    </p>
                                    <p class="text-[10px] truncate" style="color:#6b7280;">
                                        {{ $user->email }}
                                    </p>
                                    <span class="inline-block text-[9px] font-bold px-1.5 py-0.5 rounded-full mt-0.5 uppercase tracking-wide"
                                          style="background:rgba(59,130,246,0.1); color:#2563eb;">
                                        {{ $roleLabel }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Menu Items --}}
                        <div class="py-1">
                            <a href="{{ url('settings/profile') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-medium transition-colors hover:bg-gray-50"
                               style="color:#374151;">
                                <i data-lucide="user-circle" class="w-3.5 h-3.5" style="color:#6b7280;"></i>
                                Mi Perfil
                            </a>
                            <a href="{{ url('settings/security') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-medium transition-colors hover:bg-gray-50"
                               style="color:#374151;">
                                <i data-lucide="shield" class="w-3.5 h-3.5" style="color:#6b7280;"></i>
                                Seguridad & Contraseña
                            </a>
                            @if($isAdmin)
                                <a href="{{ url('settings/branding') }}"
                                   class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-medium transition-colors hover:bg-gray-50"
                                   style="color:#374151;">
                                    <i data-lucide="palette" class="w-3.5 h-3.5" style="color:#6b7280;"></i>
                                    Marca & Apariencia
                                </a>
                                <a href="{{ url('billing/checkout') }}"
                                   class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-medium transition-colors hover:bg-gray-50"
                                   style="color:#374151;">
                                    <i data-lucide="credit-card" class="w-3.5 h-3.5" style="color:#6b7280;"></i>
                                    Facturación & Plan
                                </a>
                            @endif
                            @if($user->isSuperAdmin())
                                <a href="{{ url('super-admin') }}"
                                   class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-medium transition-colors hover:bg-indigo-50"
                                   style="color:#6366f1;">
                                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                    Panel Super Admin
                                </a>
                            @endif
                        </div>

                        <div class="border-t py-1" style="border-color:#f3f4f6;">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-medium transition-colors hover:bg-red-50"
                                        style="color:#dc2626;">
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

        {{-- Page content --}}
        <div class="flex-1 pb-10">
            {{ $slot }}
        </div>

        {{-- Status bar --}}
        <footer class="h-7 px-6 flex items-center justify-between border-t shrink-0"
                style="background:#f0f1f3; border-color:rgba(0,0,0,0.06);">
            <div class="flex items-center gap-4 text-[9px] font-bold uppercase tracking-widest" style="color:#9ca3af;">
                <span class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background:#22c55e;"></span>
                    Sistema Sincronizado
                </span>
                <span style="color:#d1d5db;">|</span>
                <span>v1.0.0-BETA</span>
            </div>
            @if($tenant)
                <span class="text-[9px] font-semibold uppercase tracking-wider" style="color:#d1d5db;">
                    {{ $tenant->name }}
                </span>
            @endif
        </footer>

    </div>

</div>

</body>
</html>
