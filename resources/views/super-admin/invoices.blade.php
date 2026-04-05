<x-layouts.super-admin title="Facturas & Pagos" breadcrumb="Facturas & Pagos">

    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold" style="color:#0f172a; font-family:'Manrope',sans-serif;">Facturas & Pagos</h1>
            <p class="text-sm mt-1" style="color:#64748b;">Historial completo de facturación de todos los clientes</p>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Ingresos Totales</p>
                <p class="text-3xl font-black" style="color:#16a34a; font-variant-numeric:tabular-nums;">
                    ${{ number_format($totalRevenue / 100, 0) }}
                </p>
                <p class="text-xs mt-1" style="color:#94a3b8;">Suma de facturas pagadas</p>
            </div>
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Por Cobrar</p>
                <p class="text-3xl font-black" style="color:#f59e0b; font-variant-numeric:tabular-nums;">
                    ${{ number_format($pendingAmount / 100, 0) }}
                </p>
                <p class="text-xs mt-1" style="color:#94a3b8;">Facturas abiertas</p>
            </div>
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Vencidas</p>
                <p class="text-3xl font-black" style="color:{{ $overdueCount > 0 ? '#ef4444' : '#22c55e' }}; font-variant-numeric:tabular-nums;">
                    {{ $overdueCount }}
                </p>
                <p class="text-xs mt-1" style="color:#94a3b8;">{{ $overdueCount > 0 ? 'Requieren atención' : 'Todo al corriente' }}</p>
            </div>
            <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Total Facturas</p>
                <p class="text-3xl font-black" style="color:#6366f1; font-variant-numeric:tabular-nums;">{{ $totalCount }}</p>
                <p class="text-xs mt-1" style="color:#94a3b8;">Todos los tiempos</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#e2e8f0;">
            <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:#f1f5f9;">
                <p class="text-sm font-bold" style="color:#0f172a;">Todas las Facturas</p>
                <span class="text-xs px-2 py-0.5 rounded-full font-semibold" style="background:#ede9fe; color:#6d28d9;">
                    {{ $invoices->total() }} registros
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom:1px solid #f1f5f9; background:#f8fafc;">
                            @foreach(['Cliente', 'Stripe ID', 'Monto Due', 'Monto Pagado', 'Estado', 'Fecha Pago', 'Vencimiento', 'PDF'] as $h)
                                <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest" style="color:#94a3b8;">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $inv)
                            <tr class="border-b transition-colors hover:bg-slate-50" style="border-color:#f1f5f9;">
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold" style="color:#0f172a;">{{ $inv->tenant?->name ?? '—' }}</p>
                                    <p class="text-xs" style="color:#94a3b8;">{{ $inv->tenant?->slug ?? '' }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-xs font-mono" style="color:#64748b;">
                                    {{ $inv->stripe_invoice_id ? substr($inv->stripe_invoice_id, 0, 18) . '…' : '—' }}
                                </td>
                                <td class="px-5 py-3.5 font-semibold" style="color:#0f172a;">
                                    ${{ number_format($inv->amount_due / 100, 2) }}
                                    <span class="text-xs uppercase" style="color:#94a3b8;">{{ $inv->currency }}</span>
                                </td>
                                <td class="px-5 py-3.5 font-bold" style="color:#16a34a;">
                                    ${{ number_format($inv->amount_paid / 100, 2) }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @php
                                        $statusMap = [
                                            'paid'   => ['bg:#dcfce7; color:#166534;', 'Pagada'],
                                            'open'   => ['bg:#fef9c3; color:#854d0e;', 'Abierta'],
                                            'draft'  => ['bg:#f1f5f9; color:#475569;', 'Borrador'],
                                            'void'   => ['bg:#f1f5f9; color:#94a3b8;', 'Anulada'],
                                            'uncollectible' => ['bg:#fee2e2; color:#991b1b;', 'Incobrable'],
                                        ];
                                        [$style, $label] = $statusMap[$inv->status] ?? ['bg:#f1f5f9; color:#64748b;', $inv->status];
                                    @endphp
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="{{ $style }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs" style="color:#64748b;">
                                    {{ $inv->paid_at?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-xs" style="color:{{ $inv->due_date && $inv->due_date->isPast() && $inv->status !== 'paid' ? '#ef4444' : '#64748b' }};">
                                    {{ $inv->due_date?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($inv->invoice_pdf_url)
                                        <a href="{{ $inv->invoice_pdf_url }}" target="_blank"
                                           class="text-xs font-bold px-3 py-1 rounded-lg transition-colors"
                                           style="background:#ede9fe; color:#6d28d9;">
                                            Ver PDF
                                        </a>
                                    @else
                                        <span class="text-xs" style="color:#cbd5e1;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12 text-sm" style="color:#94a3b8;">
                                    Sin facturas registradas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($invoices->hasPages())
                <div class="px-5 py-4 border-t" style="border-color:#f1f5f9;">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>

    </div>

</x-layouts.super-admin>
