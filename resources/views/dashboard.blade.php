<x-layouts.cmms title="Dashboard" headerTitle="Dashboard">

<div class="p-6 space-y-6">

    {{-- ── Hero Banner ───────────────────────────────────────────────────── --}}
    @php
        $roleLabels = [
            'admin'      => 'Administrador',
            'supervisor' => 'Supervisor',
            'technician' => 'Técnico',
            'reader'     => 'Auditor',
            'requester'  => 'Solicitante',
        ];
        $userRoleLabel = $roleLabels[$userRole] ?? 'Usuario';
        $today = now()->locale('es')->translatedFormat('l, j \d\e F');

        $heroBannerKpis = [
            ['label' => 'Total OT',        'value' => $woStats['total'],           'icon' => 'clipboard-list', 'color' => 'text-white'],
            ['label' => 'Pendientes',       'value' => $woStats['pending'],         'icon' => 'clock',          'color' => 'text-yellow-300'],
            ['label' => 'En Progreso',      'value' => $woStats['in_progress'],     'icon' => 'hard-hat',       'color' => 'text-blue-300'],
            ['label' => 'Completadas Hoy',  'value' => $woStats['completed_today'], 'icon' => 'check-circle',   'color' => 'text-green-300'],
            ['label' => 'Vencidas',         'value' => $woStats['overdue'],         'icon' => 'alert-triangle', 'color' => 'text-red-300'],
        ];
    @endphp

    <div class="rounded-2xl overflow-hidden shadow-xl relative"
         style="background: linear-gradient(135deg, #001830 0%, #002046 50%, #003070 100%);">
        <div class="absolute inset-0 opacity-[0.07]"
             style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 28px 28px;"></div>
        <div class="absolute bottom-0 right-1/4 w-80 h-80 rounded-full opacity-10 pointer-events-none"
             style="background: radial-gradient(circle, #e07b30, transparent); filter: blur(80px);"></div>
        <div class="absolute top-0 left-1/3 w-80 h-80 rounded-full opacity-10 pointer-events-none"
             style="background: radial-gradient(circle, #3b82f6, transparent); filter: blur(80px);"></div>

        <div class="relative px-10 py-8">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest mb-1" style="color: rgba(96,165,250,0.6);">
                        CMMS Pro · {{ $userRoleLabel }}
                    </p>
                    <h2 class="text-white font-extrabold text-2xl leading-tight">
                        Bienvenido, <span style="color:#e07b30;">{{ auth()->user()->name }}</span>
                    </h2>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border"
                     style="background:rgba(34,197,94,0.1); border-color:rgba(34,197,94,0.25); color:#4ade80;">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-widest">EN LÍNEA · {{ strtoupper($today) }}</span>
                </div>
            </div>

            @if(in_array($userRole, ['admin', 'supervisor', 'reader', 'technician']))
            <div class="grid grid-cols-5 gap-3 mb-4">
                @foreach($heroBannerKpis as $kpi)
                <div class="rounded-xl px-4 py-3" style="border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.05);">
                    <i data-lucide="{{ $kpi['icon'] }}" class="w-5 h-5 mb-2 {{ $kpi['color'] }}"></i>
                    <p class="text-2xl font-extrabold leading-none {{ $kpi['color'] }} font-headline">{{ $kpi['value'] }}</p>
                    <p class="text-[9px] font-bold uppercase tracking-widest mt-1" style="color:rgba(255,255,255,0.4);">{{ $kpi['label'] }}</p>
                </div>
                @endforeach
            </div>
            @endif

            @if(in_array($userRole, ['admin', 'supervisor', 'reader']))
            <div class="grid grid-cols-3 gap-3">
                @foreach([
                    ['label' => 'MTBF', 'value' => $reliability['mtbf'], 'unit' => 'h', 'color' => '#4ade80', 'icon' => 'trending-up'],
                    ['label' => 'MTTR', 'value' => $reliability['mttr'], 'unit' => 'h', 'color' => '#fbbf24', 'icon' => 'wrench'],
                    ['label' => 'OEE',  'value' => $reliability['oee'],  'unit' => '%', 'color' => '#60a5fa', 'icon' => 'gauge'],
                ] as $r)
                <div class="rounded-xl px-5 py-3 flex items-center gap-3"
                     style="border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.05);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background:{{ $r['color'] }}20; border:1px solid rgba(255,255,255,0.1);">
                        <i data-lucide="{{ $r['icon'] }}" class="w-4 h-4" style="color:{{ $r['color'] }};"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-widest" style="color:rgba(255,255,255,0.4);">{{ $r['label'] }}</p>
                        <p class="text-xl font-extrabold leading-none font-headline" style="color:{{ $r['color'] }};">
                            {{ $r['value'] }}<span class="text-xs ml-0.5 opacity-60">{{ $r['unit'] }}</span>
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($userRole === 'requester')
            <div class="rounded-xl px-5 py-3 flex items-center gap-3 w-fit"
                 style="border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.05);">
                <i data-lucide="life-buoy" class="w-6 h-6 text-purple-300"></i>
                <div>
                    <p class="text-[9px] font-bold uppercase tracking-widest" style="color:rgba(255,255,255,0.4);">Portal de Solicitudes</p>
                    <p class="text-white font-bold text-sm">Reporta fallos y sigue su estado</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Header row ─────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-[#002046] font-headline">Panel de Control</h2>
            <p class="text-sm text-gray-400 capitalize">{{ $today }}</p>
        </div>
        @if(in_array($userRole, ['admin', 'supervisor']))
        <div class="flex items-center gap-3">
            <a href="{{ route('work-orders.create') }}"
               class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white transition-opacity hover:opacity-90"
               style="background:linear-gradient(135deg,#e07b30,#c45c1a);">
                <i data-lucide="plus" class="w-4 h-4"></i> Nueva OT
            </a>
            <a href="{{ route('work-orders.index') }}"
               class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border border-[#002046]/20 text-[#002046] bg-white">
                <i data-lucide="list" class="w-4 h-4"></i> Ver Órdenes
            </a>
        </div>
        @endif
    </div>

    {{-- ── Reliability KPIs (rings) ────────────────────────────────────────── --}}
    @if(in_array($userRole, ['admin', 'supervisor', 'reader']))
    @php
        $rings = [
            ['label' => 'MTBF', 'value' => $reliability['mtbf'], 'max' => max($reliability['mtbf'] * 1.5, 500), 'unit' => 'horas', 'color' => '#22c55e'],
            ['label' => 'MTTR', 'value' => $reliability['mttr'], 'max' => max($reliability['mttr'] * 2, 10),    'unit' => 'horas', 'color' => '#f59e0b'],
            ['label' => 'OEE',  'value' => $reliability['oee'],  'max' => 100,                                   'unit' => '%',     'color' => '#3b82f6'],
        ];
        $r = 42; $circ = 2 * M_PI * $r;
    @endphp
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-6">Indicadores de Confiabilidad</p>
        <div class="flex items-center justify-around">
            @foreach($rings as $ring)
            @php
                $pct  = min($ring['value'] / max($ring['max'], 1), 1);
                $dash = $pct * $circ;
                $rest = $circ - $dash;
            @endphp
            <div class="flex flex-col items-center gap-2">
                <svg width="100" height="100" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="{{ $r }}" fill="none" stroke="#f1f5f9" stroke-width="10"/>
                    <circle cx="50" cy="50" r="{{ $r }}" fill="none"
                            stroke="{{ $ring['color'] }}" stroke-width="10"
                            stroke-dasharray="{{ round($dash,2) }} {{ round($rest,2) }}"
                            stroke-linecap="round"
                            transform="rotate(-90 50 50)"/>
                    <text x="50" y="46" text-anchor="middle" font-size="14" font-weight="800" fill="#002046">{{ $ring['value'] }}</text>
                    <text x="50" y="60" text-anchor="middle" font-size="9" fill="#94a3b8" font-weight="600">{{ $ring['unit'] }}</text>
                </svg>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ $ring['label'] }}</p>
            </div>
            @if(!$loop->last)
            <div class="h-20 w-px bg-gray-100"></div>
            @endif
            @endforeach

            <div class="h-20 w-px bg-gray-100"></div>
            <div class="flex flex-col items-center gap-1">
                <p class="text-3xl font-extrabold text-green-600 font-headline">{{ $assetStats['active'] }}</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Activos Op.</p>
                <p class="text-xs text-gray-400">de {{ $assetStats['total'] }} total</p>
            </div>

            <div class="h-20 w-px bg-gray-100"></div>
            <div class="flex flex-col items-center gap-1">
                <p class="text-3xl font-extrabold text-red-500 font-headline">{{ $assetStats['critical'] }}</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Críticos</p>
                <p class="text-xs text-gray-400">requieren atención</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Charts row ──────────────────────────────────────────────────────── --}}
    @if(in_array($userRole, ['admin', 'supervisor', 'reader']))
    @php
        $total = max(array_sum(array_values($woByStatus)), 1);
        $donutColors = [
            'pending'     => '#eab308',
            'in_progress' => '#3b82f6',
            'on_hold'     => '#f97316',
            'completed'   => '#22c55e',
            'cancelled'   => '#ef4444',
            'draft'       => '#9ca3af',
        ];
        $donutLabels = [
            'pending'     => 'Pendiente',
            'in_progress' => 'En Progreso',
            'on_hold'     => 'En Pausa',
            'completed'   => 'Completada',
            'cancelled'   => 'Cancelada',
            'draft'       => 'Borrador',
        ];
        // SVG donut math
        $dr = 54; $dcx = 64; $dcy = 64;
        $dcirc = 2 * M_PI * $dr;
        $doffset = 0;
        $slices = [];
        foreach ($woByStatus as $key => $val) {
            $dpct  = $total > 0 ? $val / $total : 0;
            $ddash = $dpct * $dcirc;
            $slices[] = ['key' => $key, 'value' => $val, 'offset' => $doffset, 'dash' => $ddash, 'color' => $donutColors[$key] ?? '#d1d5db'];
            $doffset += $ddash;
        }

        $typeMax   = max(max(array_values($woByType) ?: [0]), 1);
        $typePalette = ['preventive' => '#3b82f6', 'corrective' => '#ef4444', 'predictive' => '#a855f7'];
        $typeAbbrev  = ['preventive' => 'PM', 'corrective' => 'CM', 'predictive' => 'PdM'];

        $priorityOrder  = ['critical', 'high', 'medium', 'low'];
        $priorityLabels = ['critical' => 'Crítica', 'high' => 'Alta', 'medium' => 'Media', 'low' => 'Baja'];
        $priorityColors = ['critical' => '#ef4444', 'high' => '#fb923c', 'medium' => '#60a5fa', 'low' => '#d1d5db'];
        $priorityDots   = ['critical' => 'bg-red-500', 'high' => 'bg-orange-400', 'medium' => 'bg-blue-400', 'low' => 'bg-gray-300'];
        $priorityMax    = max(max(array_values($woByPriority) ?: [0]), 1);
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- Donut — OT por Estado --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-5">OT por Estado</p>
            <div class="flex items-center gap-5">
                <svg width="128" height="128" viewBox="0 0 128 128" class="shrink-0">
                    @if($woStats['total'] === 0)
                    <circle cx="{{ $dcx }}" cy="{{ $dcy }}" r="{{ $dr }}" fill="none" stroke="#e5e7eb" stroke-width="14"/>
                    @else
                    @foreach($slices as $s)
                    <circle cx="{{ $dcx }}" cy="{{ $dcy }}" r="{{ $dr }}" fill="none"
                            stroke="{{ $s['color'] }}" stroke-width="14"
                            stroke-dasharray="{{ round($s['dash'],2) }} {{ round($dcirc - $s['dash'],2) }}"
                            stroke-dashoffset="{{ round(-$s['offset'],2) }}"
                            transform="rotate(-90 {{ $dcx }} {{ $dcy }})"/>
                    @endforeach
                    @endif
                    <text x="{{ $dcx }}" y="{{ $dcy - 6 }}" text-anchor="middle" font-size="20" font-weight="800" fill="#002046">{{ $woStats['total'] }}</text>
                    <text x="{{ $dcx }}" y="{{ $dcy + 12 }}" text-anchor="middle" font-size="9" fill="#9ca3af" font-weight="600">TOTAL</text>
                </svg>
                <div class="flex flex-col gap-1.5">
                    @foreach($slices as $s)
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $s['color'] }};"></span>
                        <span class="text-[11px] text-gray-500">{{ $donutLabels[$s['key']] ?? $s['key'] }}</span>
                        <span class="text-[11px] font-bold text-gray-700 ml-auto pl-3">{{ $s['value'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Bars — OT por Tipo --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-5">OT por Tipo</p>
            <div class="flex flex-col gap-3">
                @foreach($woByType as $type => $count)
                @php $pct = round($count / $typeMax * 100); @endphp
                <div class="flex items-center gap-3">
                    <span class="text-[11px] font-bold text-gray-500 w-8">{{ $typeAbbrev[$type] ?? $type }}</span>
                    <div class="flex-1 h-5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full flex items-center justify-end pr-2 transition-all"
                             style="width:{{ max($pct, $count > 0 ? 8 : 0) }}%; background:{{ $typePalette[$type] ?? '#9ca3af' }};">
                            @if($count > 0)
                            <span class="text-[10px] font-bold text-white">{{ $count }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Bars — OT por Prioridad --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-5">OT por Prioridad</p>
            <div class="flex flex-col gap-3">
                @foreach($priorityOrder as $p)
                @php $count = $woByPriority[$p] ?? 0; $pct = round($count / $priorityMax * 100); @endphp
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full shrink-0 {{ $priorityDots[$p] }}"></span>
                    <span class="text-[11px] text-gray-500 w-12">{{ $priorityLabels[$p] }}</span>
                    <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width:{{ $pct }}%; background:{{ $count > 0 ? $priorityColors[$p] : 'transparent' }};"></div>
                    </div>
                    <span class="text-[11px] font-bold text-gray-600 w-4 text-right">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>
    @endif

    {{-- ── Bottom row ──────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Recent Work Orders (2 cols) --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Últimas Órdenes de Trabajo</p>
                <a href="{{ route('work-orders.index') }}" class="text-xs font-semibold text-[#002046] hover:underline">Ver todas →</a>
            </div>
            @if($recentWorkOrders->isEmpty())
            <div class="py-12 text-center text-gray-400 text-sm">Sin órdenes recientes</div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($recentWorkOrders as $wo)
                @php
                    $statusColors  = ['draft' => 'bg-gray-100 text-gray-600 border-gray-200', 'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200', 'in_progress' => 'bg-blue-50 text-blue-700 border-blue-200', 'on_hold' => 'bg-orange-50 text-orange-700 border-orange-200', 'completed' => 'bg-green-50 text-green-700 border-green-200', 'cancelled' => 'bg-red-50 text-red-600 border-red-200'];
                    $statusLabels  = ['draft' => 'Borrador', 'pending' => 'Pendiente', 'in_progress' => 'En Progreso', 'on_hold' => 'En Pausa', 'completed' => 'Completada', 'cancelled' => 'Cancelada'];
                    $typeColors    = ['preventive' => 'bg-blue-100 text-blue-700', 'corrective' => 'bg-red-100 text-red-700', 'predictive' => 'bg-purple-100 text-purple-700'];
                    $typeAbbrevWo  = ['preventive' => 'PM', 'corrective' => 'CM', 'predictive' => 'PdM'];
                    $priorityDotsWo = ['low' => 'bg-gray-300', 'medium' => 'bg-blue-400', 'high' => 'bg-orange-400', 'critical' => 'bg-red-500'];
                    $statusVal     = $wo->status->value;
                    $typeVal       = $wo->type->value;
                    $priorityVal   = $wo->priority->value;
                @endphp
                <a href="{{ route('work-orders.show', $wo) }}"
                   class="flex items-center gap-4 px-6 py-3.5 hover:bg-gray-50/70 transition-colors group">
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded shrink-0 {{ $typeColors[$typeVal] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $typeAbbrevWo[$typeVal] ?? $typeVal }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-mono font-bold text-[#002046]">{{ $wo->code }}</p>
                        <p class="text-sm text-gray-700 font-medium truncate">{{ $wo->title }}</p>
                    </div>
                    @if($wo->asset)
                    <p class="text-[10px] text-gray-400 truncate max-w-[100px] hidden md:block">{{ $wo->asset->name }}</p>
                    @endif
                    <span class="w-2 h-2 rounded-full shrink-0 {{ $priorityDotsWo[$priorityVal] ?? 'bg-gray-300' }}"></span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border shrink-0 {{ $statusColors[$statusVal] ?? 'bg-gray-100 text-gray-500 border-gray-200' }}">
                        {{ $statusLabels[$statusVal] ?? $statusVal }}
                    </span>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity shrink-0"></i>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Right sidebar --}}
        <div class="space-y-4">

            {{-- Upcoming Maintenance --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-50">
                    <i data-lucide="calendar" class="w-4 h-4 text-[#002046]"></i>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Próximos PM</p>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($upcomingMaintenance as $plan)
                    @php
                        $days    = (int) now()->diffInDays($plan->next_execution_date, false);
                        $urgency = $days <= 2 ? 'text-red-600' : ($days <= 7 ? 'text-orange-500' : 'text-gray-500');
                        $dayStr  = $days === 0 ? 'Hoy' : ($days === 1 ? 'Mañana' : "En {$days}d");
                    @endphp
                    <div class="flex items-center justify-between gap-2 px-5 py-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-gray-700 truncate">{{ $plan->name }}</p>
                            <p class="text-[10px] text-gray-400">{{ optional($plan->asset)->code }}</p>
                        </div>
                        <span class="text-xs font-bold {{ $urgency }} shrink-0">{{ $dayStr }}</span>
                    </div>
                    @empty
                    <p class="text-gray-400 text-xs text-center py-6">Sin mantenimientos próximos</p>
                    @endforelse
                </div>
            </div>

            {{-- Critical Assets --}}
            @if(!empty($criticalAssets) && $criticalAssets->isNotEmpty())
            <div class="bg-red-50 border border-red-100 rounded-xl overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-4 border-b border-red-100">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-red-400"></i>
                    <p class="text-xs font-bold uppercase tracking-widest text-red-400">Activos Críticos</p>
                </div>
                <div class="divide-y divide-red-100">
                    @foreach($criticalAssets as $asset)
                    <a href="{{ route('assets.show', $asset) }}"
                       class="flex items-center justify-between gap-2 px-5 py-3 hover:bg-red-100/40 transition-colors">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-gray-700 truncate">{{ $asset->name }}</p>
                            <p class="text-[10px] text-gray-400">{{ $asset->code }}</p>
                        </div>
                        <i data-lucide="chevron-right" class="w-3 h-3 text-red-300 shrink-0"></i>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Low Stock --}}
            @if(!empty($lowStockParts) && $lowStockParts->isNotEmpty())
            <div class="bg-amber-50 border border-amber-100 rounded-xl overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-4 border-b border-amber-100">
                    <i data-lucide="package-x" class="w-4 h-4 text-amber-500"></i>
                    <p class="text-xs font-bold uppercase tracking-widest text-amber-500">Stock Bajo</p>
                </div>
                <div class="divide-y divide-amber-100">
                    @foreach($lowStockParts as $part)
                    <div class="flex items-center justify-between gap-2 px-5 py-3">
                        <p class="text-xs font-semibold text-gray-700 truncate">{{ $part->name }}</p>
                        <span class="text-xs font-bold text-red-600 shrink-0">{{ $part->stock_quantity }}/{{ $part->min_stock }} {{ $part->unit }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

    </div>

</div>

</x-layouts.cmms>
