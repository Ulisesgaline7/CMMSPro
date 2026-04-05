<x-layouts.super-admin title="Suscripciones" breadcrumb="Suscripciones">

    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold" style="color:#0f172a; font-family:'Manrope',sans-serif;">Suscripciones</h1>
            <p class="text-sm mt-1" style="color:#64748b;">Todas las suscripciones activas en la plataforma</p>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">MRR Total</p>
                <p class="text-3xl font-black" style="color:#16a34a; font-variant-numeric:tabular-nums;">
                    ${{ number_format($mrr, 0) }}
                </p>
                <p class="text-xs mt-1" style="color:#94a3b8;">Ingreso mensual recurrente</p>
            </div>
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Suscripciones Activas</p>
                <p class="text-3xl font-black" style="color:#6366f1; font-variant-numeric:tabular-nums;">{{ $active }}</p>
                <p class="text-xs mt-1" style="color:#94a3b8;">Clientes con plan activo</p>
            </div>
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Pagos Vencidos</p>
                <p class="text-3xl font-black" style="color:{{ $pastDue > 0 ? '#ef4444' : '#22c55e' }}; font-variant-numeric:tabular-nums;">
                    {{ $pastDue }}
                </p>
                <p class="text-xs mt-1" style="color:#94a3b8;">{{ $pastDue > 0 ? 'Requieren atención' : 'Todo al corriente' }}</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#e2e8f0;">
            <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:#f1f5f9;">
                <p class="text-sm font-bold" style="color:#0f172a;">Todas las Suscripciones</p>
                <span class="text-xs px-2 py-0.5 rounded-full font-semibold" style="background:#ede9fe; color:#6d28d9;">
                    {{ $subscriptions->total() }} registros
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom:1px solid #f1f5f9; background:#f8fafc;">
                            @foreach(['Cliente', 'Plan', 'Estado', 'MRR', 'Inicio del Ciclo', 'Fin del Ciclo', 'Acciones'] as $h)
                                <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest" style="color:#94a3b8;">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $sub)
                            <tr class="border-b transition-colors hover:bg-slate-50" style="border-color:#f1f5f9;">
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold" style="color:#0f172a;">{{ $sub->tenant?->name ?? '—' }}</p>
                                    <p class="text-xs" style="color:#94a3b8;">{{ $sub->tenant?->slug ?? '' }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-xs font-semibold capitalize" style="color:#475569;">
                                        {{ $sub->tenant?->plan?->label() ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $sub->status->color() }}">
                                        {{ $sub->status->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="font-bold" style="color:#16a34a;">
                                        ${{ number_format($sub->total_monthly, 0) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs" style="color:#64748b;">
                                    {{ $sub->current_period_start?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-xs" style="color:#64748b;">
                                    {{ $sub->current_period_end?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($sub->tenant)
                                        <a href="{{ route('super-admin.tenants.show', $sub->tenant_id) }}"
                                           class="text-xs font-bold px-3 py-1 rounded-lg transition-colors"
                                           style="background:#ede9fe; color:#6d28d9;">
                                            Ver cliente
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-sm" style="color:#94a3b8;">
                                    Sin suscripciones registradas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($subscriptions->hasPages())
                <div class="px-5 py-4 border-t" style="border-color:#f1f5f9;">
                    {{ $subscriptions->links() }}
                </div>
            @endif
        </div>

    </div>

</x-layouts.super-admin>
