<x-layouts.super-admin title="MRR & Analytics" breadcrumb="MRR & Analytics">

    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="color:#0f172a; font-family:'Manrope',sans-serif;">MRR & Analytics</h1>
                <p class="text-sm mt-1" style="color:#64748b;">Métricas de ingresos recurrentes y crecimiento</p>
            </div>
            <a href="{{ route('super-admin.revenue.report') }}"
               class="text-sm font-bold px-4 py-2 rounded-lg transition-colors"
               style="background:#6366f1; color:#fff;">
                Reporte Completo →
            </a>
        </div>

        {{-- KPIs principales --}}
        <div class="grid grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">MRR</p>
                <p class="text-3xl font-black" style="color:#16a34a; font-variant-numeric:tabular-nums;">
                    ${{ number_format($mrr, 0) }}
                </p>
                <p class="text-xs mt-1" style="color:#94a3b8;">Ingreso mensual recurrente</p>
            </div>
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">ARR</p>
                <p class="text-3xl font-black" style="color:#6366f1; font-variant-numeric:tabular-nums;">
                    ${{ number_format($arr, 0) }}
                </p>
                <p class="text-xs mt-1" style="color:#94a3b8;">Ingreso anual recurrente</p>
            </div>
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Suscripciones Activas</p>
                <p class="text-3xl font-black" style="color:#0f172a; font-variant-numeric:tabular-nums;">{{ $activeSubscriptions }}</p>
                <p class="text-xs mt-1" style="color:#94a3b8;">Clientes con plan activo</p>
            </div>
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">ARPU</p>
                <p class="text-3xl font-black" style="color:#f59e0b; font-variant-numeric:tabular-nums;">
                    ${{ number_format($avgRevenue, 0) }}
                </p>
                <p class="text-xs mt-1" style="color:#94a3b8;">Ingreso promedio por usuario</p>
            </div>
        </div>

        {{-- Conversiones --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-sm font-bold mb-4" style="color:#0f172a;">Conversión Trial → Activo</p>
                <div class="flex items-end gap-6">
                    <div>
                        <p class="text-3xl font-black" style="color:#22c55e;">{{ $trialConversions }}</p>
                        <p class="text-xs mt-1" style="color:#94a3b8;">Clientes activos</p>
                    </div>
                    <div>
                        <p class="text-3xl font-black" style="color:#f59e0b;">{{ $trialActive }}</p>
                        <p class="text-xs mt-1" style="color:#94a3b8;">En trial</p>
                    </div>
                    @if($trialConversions + $trialActive > 0)
                        <div>
                            <p class="text-3xl font-black" style="color:#6366f1;">
                                {{ round($trialConversions / ($trialConversions + $trialActive) * 100) }}%
                            </p>
                            <p class="text-xs mt-1" style="color:#94a3b8;">Tasa conversión</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-sm font-bold mb-4" style="color:#0f172a;">Ingresos Últimos 12 Meses</p>
                @if($mrrByMonth->count() > 0)
                    <div class="space-y-2">
                        @foreach($mrrByMonth->takeLast(6) as $month)
                            @php $max = $mrrByMonth->max('total'); @endphp
                            <div class="flex items-center gap-3">
                                <span class="text-xs w-16 shrink-0" style="color:#64748b;">{{ $month->month }}</span>
                                <div class="flex-1 h-2 rounded-full" style="background:#f1f5f9;">
                                    <div class="h-2 rounded-full" style="width:{{ $max > 0 ? round($month->total / $max * 100) : 0 }}%; background:#6366f1;"></div>
                                </div>
                                <span class="text-xs font-bold w-20 text-right" style="color:#0f172a;">
                                    ${{ number_format($month->total / 100, 0) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm" style="color:#94a3b8;">Sin datos de facturación aún</p>
                @endif
            </div>
        </div>

        {{-- Top tenants por MRR --}}
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#e2e8f0;">
            <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:#f1f5f9;">
                <p class="text-sm font-bold" style="color:#0f172a;">Top Clientes por MRR</p>
                <span class="text-xs px-2 py-0.5 rounded-full font-semibold" style="background:#dcfce7; color:#166534;">
                    Top 10
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom:1px solid #f1f5f9; background:#f8fafc;">
                            @foreach(['#', 'Cliente', 'Plan', 'Estado', 'MRR Mensual', 'Periodo'] as $h)
                                <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest" style="color:#94a3b8;">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topTenants as $i => $sub)
                            <tr class="border-b transition-colors hover:bg-slate-50" style="border-color:#f1f5f9;">
                                <td class="px-5 py-3.5">
                                    <span class="text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center"
                                          style="background:{{ $i === 0 ? '#fef9c3' : ($i === 1 ? '#f1f5f9' : '#fff7ed') }}; color:{{ $i === 0 ? '#854d0e' : '#475569' }};">
                                        {{ $i + 1 }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold" style="color:#0f172a;">{{ $sub->tenant?->name ?? '—' }}</p>
                                    <p class="text-xs" style="color:#94a3b8;">{{ $sub->tenant?->slug ?? '' }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-xs font-semibold capitalize" style="color:#475569;">
                                    {{ $sub->tenant?->plan?->label() ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $sub->status->color() }}">
                                        {{ $sub->status->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="font-black text-base" style="color:#16a34a;">
                                        ${{ number_format($sub->total_monthly, 0) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs" style="color:#64748b;">
                                    {{ $sub->current_period_start?->format('d/m/Y') ?? '—' }} –
                                    {{ $sub->current_period_end?->format('d/m/Y') ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-sm" style="color:#94a3b8;">
                                    Sin suscripciones activas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</x-layouts.super-admin>
