<x-layouts.super-admin title="Dashboard" headerTitle="Panel de Negocio">

    <div class="p-6 space-y-6">

        {{-- ── Past-due alert ─────────────────────────────────────────────── --}}
        @if ($pastDueCount > 0)
            <div class="flex items-center gap-4 px-5 py-4 rounded-xl border"
                 style="background:#fffbeb; border-color:#fde68a;">
                <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0" style="color:#d97706;"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold" style="color:#92400e;">
                        {{ $pastDueCount }} cliente{{ $pastDueCount > 1 ? 's' : '' }} con pago vencido
                    </p>
                    <p class="text-xs mt-0.5" style="color:#b45309;">
                        ${{ number_format($pastDueRevenue, 0) }} USD en riesgo de churn. Gestionar antes de la suspensión automática (10 días).
                    </p>
                </div>
                <a href="{{ route('super-admin.tenants.index') }}?billing=past_due"
                   class="shrink-0 px-4 py-2 rounded-lg text-xs font-bold text-white transition-opacity hover:opacity-90"
                   style="background:#d97706;">
                    Gestionar cobros
                </a>
            </div>
        @endif

        {{-- ── KPI row 1: MRR + Tenants + Users ──────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- MRR --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-5 relative overflow-hidden">
                <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full opacity-10"
                     style="background:radial-gradient(circle,#22c55e,transparent);"></div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">MRR</p>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                         style="background:#f0fdf4; border:1px solid #bbf7d0;">
                        <i data-lucide="banknote" class="w-4 h-4" style="color:#16a34a;"></i>
                    </div>
                </div>
                <p class="text-3xl font-black leading-none" style="color:#16a34a; font-variant-numeric:tabular-nums;">
                    ${{ number_format($mrr, 0) }}
                </p>
                <p class="text-[11px] text-gray-400 mt-1">Ingreso mensual recurrente</p>
                @if ($mrrGrowth !== 0)
                    <div class="flex items-center gap-1 mt-2">
                        <i data-lucide="{{ $mrrGrowth >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3.5 h-3.5"
                           style="color:{{ $mrrGrowth >= 0 ? '#22c55e' : '#ef4444' }};"></i>
                        <span class="text-[10px] font-bold" style="color:{{ $mrrGrowth >= 0 ? '#22c55e' : '#ef4444' }};">
                            {{ $mrrGrowth >= 0 ? '+' : '' }}{{ $mrrGrowth }}%
                        </span>
                        <span class="text-[10px] text-gray-400">vs mes anterior</span>
                    </div>
                @endif
            </div>

            {{-- Clientes activos --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-5 relative overflow-hidden">
                <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full opacity-10"
                     style="background:radial-gradient(circle,#6366f1,transparent);"></div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Clientes Activos</p>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                         style="background:#ede9fe; border:1px solid #ddd6fe;">
                        <i data-lucide="building-2" class="w-4 h-4" style="color:#6d28d9;"></i>
                    </div>
                </div>
                <p class="text-3xl font-black leading-none" style="color:#6d28d9; font-variant-numeric:tabular-nums;">
                    {{ $activeTenants }}
                </p>
                <p class="text-[11px] text-gray-400 mt-1">
                    {{ $totalTenants }} total · {{ $trialTenants }} en trial
                </p>
            </div>

            {{-- Usuarios --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-5 relative overflow-hidden">
                <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full opacity-10"
                     style="background:radial-gradient(circle,#0ea5e9,transparent);"></div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Usuarios</p>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                         style="background:#f0f9ff; border:1px solid #bae6fd;">
                        <i data-lucide="users" class="w-4 h-4" style="color:#0284c7;"></i>
                    </div>
                </div>
                <p class="text-3xl font-black leading-none" style="color:#0284c7; font-variant-numeric:tabular-nums;">
                    {{ $totalUsers }}
                </p>
                <p class="text-[11px] text-gray-400 mt-1">En toda la plataforma</p>
            </div>

            {{-- Pagos vencidos --}}
            <div class="bg-white rounded-xl border shadow-sm px-5 py-5 relative overflow-hidden
                        {{ $pastDueCount > 0 ? 'border-amber-200' : 'border-gray-100' }}">
                <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full opacity-10"
                     style="background:radial-gradient(circle,{{ $pastDueCount > 0 ? '#f59e0b' : '#22c55e' }},transparent);"></div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Pagos Vencidos</p>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                         style="background:{{ $pastDueCount > 0 ? '#fffbeb' : '#f0fdf4' }}; border:1px solid {{ $pastDueCount > 0 ? '#fde68a' : '#bbf7d0' }};">
                        <i data-lucide="{{ $pastDueCount > 0 ? 'alert-circle' : 'check-circle-2' }}" class="w-4 h-4"
                           style="color:{{ $pastDueCount > 0 ? '#d97706' : '#16a34a' }};"></i>
                    </div>
                </div>
                <p class="text-3xl font-black leading-none" style="color:{{ $pastDueCount > 0 ? '#d97706' : '#16a34a' }}; font-variant-numeric:tabular-nums;">
                    {{ $pastDueCount }}
                </p>
                <p class="text-[11px] text-gray-400 mt-1">
                    {{ $pastDueCount > 0 ? '$' . number_format($pastDueRevenue, 0) . ' USD en riesgo' : 'Todo al corriente' }}
                </p>
            </div>

        </div>

        {{-- ── KPI row 2: secondary metrics ──────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $secondaryKpis = [
                    ['label' => 'Nuevos este mes',       'value' => $newTenantsThisMonth, 'icon' => 'user-plus',    'color' => '#22c55e'],
                    ['label' => 'Churn este mes',        'value' => $churnCount,          'icon' => 'user-minus',   'color' => $churnCount > 0 ? '#ef4444' : '#22c55e'],
                    ['label' => 'OTs en plataforma',     'value' => number_format($totalWorkOrders), 'icon' => 'wrench',    'color' => '#0ea5e9'],
                    ['label' => 'Activos registrados',   'value' => number_format($totalAssets),      'icon' => 'factory',   'color' => '#8b5cf6'],
                ];
            @endphp
            @foreach ($secondaryKpis as $kpi)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                         style="background:{{ $kpi['color'] }}18; border:1px solid {{ $kpi['color'] }}30;">
                        <i data-lucide="{{ $kpi['icon'] }}" class="w-4.5 h-4.5"
                           style="color:{{ $kpi['color'] }};"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black leading-none" style="color:{{ $kpi['color'] }}; font-variant-numeric:tabular-nums;">
                            {{ $kpi['value'] }}
                        </p>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mt-0.5">{{ $kpi['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Plan distribution + Adoption alerts + Status ──────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Plan distribution --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Distribución de Planes</p>
                </div>
                <div class="p-5 space-y-4">
                    @php
                        $planTotal = max(array_sum(array_values($planDistribution)), 1);
                        $planMeta  = [
                            'starter'      => ['label' => 'Starter',       'color' => '#64748b'],
                            'professional' => ['label' => 'Professional',   'color' => '#6366f1'],
                            'enterprise'   => ['label' => 'Enterprise',     'color' => '#f59e0b'],
                        ];
                    @endphp
                    @forelse ($planDistribution as $plan => $count)
                        @php
                            $meta = $planMeta[$plan] ?? ['label' => ucfirst($plan), 'color' => '#94a3b8'];
                            $pct  = round($count / $planTotal * 100);
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-semibold text-gray-700">{{ $meta['label'] }}</span>
                                <span class="text-xs font-black" style="color:{{ $meta['color'] }};">{{ $count }} ({{ $pct }}%)</span>
                            </div>
                            <div class="h-2 rounded-full overflow-hidden bg-gray-100">
                                <div class="h-full rounded-full" style="width:{{ $pct }}%; background:{{ $meta['color'] }};"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Sin datos</p>
                    @endforelse
                </div>
            </div>

            {{-- Adoption alerts --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Alertas de Adopción</p>
                    <a href="{{ route('super-admin.tenants.index') }}"
                       class="text-[10px] font-bold uppercase tracking-wider hover:underline" style="color:#6366f1;">
                        Ver todos →
                    </a>
                </div>
                @if ($adoptionAlerts->isEmpty())
                    <div class="py-10 text-center">
                        <i data-lucide="check-circle-2" class="w-8 h-8 mx-auto mb-2" style="color:#22c55e;"></i>
                        <p class="text-sm font-semibold text-green-600">Todos los clientes activos</p>
                        <p class="text-xs text-gray-400 mt-1">Ninguno sin actividad</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach ($adoptionAlerts as $alert)
                            <a href="{{ route('super-admin.tenants.show', $alert->id) }}"
                               class="flex items-center gap-3 px-5 py-3.5 hover:bg-amber-50/50 transition-colors group">
                                <div class="w-1.5 h-1.5 rounded-full shrink-0 bg-amber-400"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-800 truncate">{{ $alert->name }}</p>
                                    <p class="text-[10px] text-gray-400">
                                        {{ $alert->assets_count }} activos · {{ $alert->work_orders_count }} OTs
                                    </p>
                                </div>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full shrink-0"
                                      style="background:#fffbeb; color:#d97706; border:1px solid #fde68a;">
                                    Sin uso
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Estado de clientes --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Estado de Clientes</p>
                </div>
                <div class="p-5 space-y-4">
                    @php
                        $statusRows = [
                            ['label' => 'Activos',      'value' => $activeTenants,       'icon' => 'check-circle-2', 'color' => '#22c55e'],
                            ['label' => 'Trial',        'value' => $trialTenants,        'icon' => 'clock',          'color' => '#0ea5e9'],
                            ['label' => 'Suspendidos',  'value' => $suspendedTenants,    'icon' => 'ban',            'color' => '#ef4444'],
                            ['label' => 'Nuevos / mes', 'value' => $newTenantsThisMonth, 'icon' => 'user-plus',      'color' => '#8b5cf6'],
                            ['label' => 'Churn / mes',  'value' => $churnCount,          'icon' => 'log-out',        'color' => $churnCount > 0 ? '#f87171' : '#22c55e'],
                        ];
                    @endphp
                    @foreach ($statusRows as $row)
                        <div class="flex items-center gap-3">
                            <i data-lucide="{{ $row['icon'] }}" class="w-4 h-4 shrink-0" style="color:{{ $row['color'] }};"></i>
                            <span class="text-sm flex-1 text-gray-600">{{ $row['label'] }}</span>
                            <span class="text-lg font-black" style="color:{{ $row['color'] }}; font-variant-numeric:tabular-nums;">{{ $row['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ── Tenants table ───────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Todos los Clientes</p>
                <a href="{{ route('super-admin.tenants.index') }}"
                   class="text-[10px] font-bold uppercase tracking-wider hover:underline" style="color:#6366f1;">
                    Gestionar →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-50 bg-gray-50/60">
                            @foreach (['Cliente', 'Plan', 'Estado', 'Usuarios', 'Activos', 'OTs', 'MRR', 'Suscripción', ''] as $h)
                                <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($recentTenants as $tenant)
                            @php
                                $statusColors = [
                                    'active'    => 'bg-green-50 text-green-700 border-green-200',
                                    'trial'     => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'inactive'  => 'bg-gray-100 text-gray-600 border-gray-200',
                                    'suspended' => 'bg-red-50 text-red-700 border-red-200',
                                ];
                                $planColors = [
                                    'starter'      => 'bg-slate-100 text-slate-600 border-slate-200',
                                    'professional' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'enterprise'   => 'bg-amber-50 text-amber-700 border-amber-200',
                                ];
                                $subStatusColors = [
                                    'active'    => 'text-green-600',
                                    'trialing'  => 'text-blue-600',
                                    'past_due'  => 'text-orange-500',
                                    'canceled'  => 'text-red-500',
                                    'suspended' => 'text-red-500',
                                    'incomplete'=> 'text-gray-400',
                                ];
                                $statusVal = $tenant->status->value;
                                $planVal   = $tenant->plan->value;
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="text-sm font-bold text-[#0f172a]">{{ $tenant->name }}</p>
                                    <p class="text-[10px] text-gray-400 font-mono">{{ $tenant->slug }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border uppercase tracking-wide {{ $planColors[$planVal] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                        {{ $tenant->plan->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $statusColors[$statusVal] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                        {{ $tenant->status->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    @php $maxUsers = $tenant->max_users ?? 0; @endphp
                                    @if ($maxUsers > 0)
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden max-w-16">
                                                <div class="h-full rounded-full {{ ($tenant->users_count / $maxUsers) >= 0.8 ? 'bg-amber-400' : 'bg-purple-400' }}"
                                                     style="width:{{ min(round($tenant->users_count / $maxUsers * 100), 100) }}%"></div>
                                            </div>
                                            <span class="text-[10px] font-bold text-gray-500">{{ $tenant->users_count }}/{{ $maxUsers }}</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-gray-400">{{ $tenant->users_count }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @php $maxAssets = $tenant->max_assets ?? 0; @endphp
                                    @if ($maxAssets > 0)
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden max-w-16">
                                                <div class="h-full rounded-full {{ ($tenant->assets_count / $maxAssets) >= 0.8 ? 'bg-amber-400' : 'bg-sky-400' }}"
                                                     style="width:{{ min(round($tenant->assets_count / $maxAssets * 100), 100) }}%"></div>
                                            </div>
                                            <span class="text-[10px] font-bold text-gray-500">{{ $tenant->assets_count }}/{{ $maxAssets }}</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-gray-400">{{ $tenant->assets_count }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-sm font-bold text-gray-700">{{ $tenant->work_orders_count }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($tenant->subscription)
                                        <span class="text-sm font-bold text-green-600">${{ number_format($tenant->subscription->total_monthly, 0) }}</span>
                                    @else
                                        <span class="text-sm text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($tenant->subscription)
                                        <span class="text-[11px] font-bold {{ $subStatusColors[$tenant->subscription->status->value] ?? 'text-gray-400' }}">
                                            {{ $tenant->subscription->status->label() }}
                                        </span>
                                    @else
                                        <span class="text-[11px] text-gray-300">Sin plan</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('super-admin.tenants.show', $tenant->id) }}"
                                           class="text-[10px] font-bold px-2 py-1 rounded-lg transition-colors hover:opacity-80"
                                           style="background:#ede9fe; color:#6d28d9; border:1px solid #ddd6fe;">
                                            Ver
                                        </a>
                                        @if ($statusVal === 'active')
                                            <a href="{{ route('super-admin.tenants.edit', $tenant->id) }}"
                                               class="text-[10px] font-bold px-2 py-1 rounded-lg transition-colors hover:opacity-80"
                                               style="background:#f8fafc; color:#64748b; border:1px solid #e2e8f0;">
                                                Editar
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-12 text-sm text-gray-400">Sin clientes registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</x-layouts.super-admin>
