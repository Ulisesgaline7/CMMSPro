<x-layouts.super-admin title="Reporte de Ingresos" breadcrumb="Reporte de Ingresos">

    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="color:#0f172a; font-family:'Manrope',sans-serif;">Reporte de Ingresos</h1>
                <p class="text-sm mt-1" style="color:#64748b;">Análisis detallado de ingresos, planes y riesgo de churn</p>
            </div>
            <a href="{{ route('super-admin.revenue.index') }}"
               class="text-xs font-bold px-3 py-1.5 rounded-lg border transition-colors"
               style="border-color:#e2e8f0; color:#475569;">
                ← MRR Dashboard
            </a>
        </div>

        {{-- Resumen de cobranza --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Total Facturado</p>
                <p class="text-3xl font-black" style="color:#0f172a; font-variant-numeric:tabular-nums;">
                    ${{ number_format($totalInvoiced, 0) }}
                </p>
            </div>
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Total Cobrado</p>
                <p class="text-3xl font-black" style="color:#16a34a; font-variant-numeric:tabular-nums;">
                    ${{ number_format($totalCollected, 0) }}
                </p>
            </div>
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Tasa de Cobranza</p>
                <p class="text-3xl font-black" style="color:{{ $collectionRate >= 90 ? '#16a34a' : ($collectionRate >= 70 ? '#f59e0b' : '#ef4444') }}; font-variant-numeric:tabular-nums;">
                    {{ $collectionRate }}%
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">

            {{-- Ingresos por plan --}}
            <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#e2e8f0;">
                <div class="px-5 py-4 border-b" style="border-color:#f1f5f9;">
                    <p class="text-sm font-bold" style="color:#0f172a;">MRR por Plan</p>
                </div>
                <div class="p-5 space-y-4">
                    @php $totalMrr = $revenueByPlan->sum('mrr'); @endphp
                    @forelse($revenueByPlan as $row)
                        @php
                            $planColors = ['starter' => '#6366f1', 'professional' => '#0ea5e9', 'enterprise' => '#f59e0b'];
                            $color = $planColors[$row->plan] ?? '#94a3b8';
                            $pct = $totalMrr > 0 ? round($row->mrr / $totalMrr * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-semibold capitalize" style="color:#0f172a;">{{ $row->plan }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs" style="color:#94a3b8;">{{ $row->count }} clientes</span>
                                    <span class="text-xs font-bold" style="color:#0f172a;">${{ number_format($row->mrr, 0) }}/mes</span>
                                    <span class="text-xs font-bold" style="color:{{ $color }};">{{ $pct }}%</span>
                                </div>
                            </div>
                            <div class="h-2 rounded-full" style="background:#f1f5f9;">
                                <div class="h-2 rounded-full transition-all" style="width:{{ $pct }}%; background:{{ $color }};"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm" style="color:#94a3b8;">Sin datos de suscripciones activas</p>
                    @endforelse
                </div>
            </div>

            {{-- Historial mensual --}}
            <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#e2e8f0;">
                <div class="px-5 py-4 border-b" style="border-color:#f1f5f9;">
                    <p class="text-sm font-bold" style="color:#0f172a;">Ingresos por Mes (últimos 12)</p>
                </div>
                <div class="p-5 space-y-2">
                    @forelse($monthlyData as $row)
                        @php $maxRev = $monthlyData->max('revenue'); @endphp
                        <div class="flex items-center gap-3">
                            <span class="text-xs w-16 shrink-0" style="color:#64748b;">{{ $row->month }}</span>
                            <div class="flex-1 h-2 rounded-full" style="background:#f1f5f9;">
                                <div class="h-2 rounded-full" style="width:{{ $maxRev > 0 ? round($row->revenue / $maxRev * 100) : 0 }}%; background:#22c55e;"></div>
                            </div>
                            <span class="text-xs font-bold w-20 text-right" style="color:#0f172a;">${{ number_format($row->revenue / 100, 0) }}</span>
                            <span class="text-xs w-10 text-right" style="color:#94a3b8;">{{ $row->invoices }} fact.</span>
                        </div>
                    @empty
                        <p class="text-sm" style="color:#94a3b8;">Sin datos de facturación</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Riesgo de churn --}}
        @if($churnRisk->count() > 0)
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#fee2e2; box-shadow:0 0 0 1px #fee2e2;">
            <div class="px-5 py-4 border-b flex items-center gap-3" style="border-color:#fecaca; background:#fef2f2;">
                <i data-lucide="alert-triangle" class="w-4 h-4" style="color:#ef4444;"></i>
                <p class="text-sm font-bold" style="color:#991b1b;">Riesgo de Churn ({{ $churnRisk->count() }} clientes)</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom:1px solid #fee2e2; background:#fff5f5;">
                            @foreach(['Cliente', 'Estado', 'MRR en Riesgo', 'Fin del Período', 'Cancela al Vencer'] as $h)
                                <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest" style="color:#ef4444;">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($churnRisk as $sub)
                            <tr class="border-b transition-colors hover:bg-red-50" style="border-color:#fee2e2;">
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold" style="color:#0f172a;">{{ $sub->tenant?->name ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $sub->status->color() }}">
                                        {{ $sub->status->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-bold" style="color:#ef4444;">
                                    ${{ number_format($sub->total_monthly, 0) }}/mes
                                </td>
                                <td class="px-5 py-3.5 text-xs" style="color:#64748b;">
                                    {{ $sub->current_period_end?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($sub->cancel_at_period_end)
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:#fee2e2; color:#991b1b;">Sí</span>
                                    @else
                                        <span class="text-xs" style="color:#94a3b8;">No</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

</x-layouts.super-admin>
