<x-layouts.cmms :title="$purchaseOrder->code" :headerTitle="$purchaseOrder->code . ' – ' . $purchaseOrder->supplier_name">

    @php
        $statusMeta = [
            'draft'            => ['label' => 'Borrador',          'color' => 'bg-gray-100 text-gray-600',       'dot' => 'bg-gray-400'],
            'pending_approval' => ['label' => 'Pend. Aprobación',  'color' => 'bg-yellow-100 text-yellow-700',   'dot' => 'bg-yellow-500'],
            'approved'         => ['label' => 'Aprobada',          'color' => 'bg-blue-100 text-blue-700',       'dot' => 'bg-blue-500'],
            'ordered'          => ['label' => 'Pedida',            'color' => 'bg-purple-100 text-purple-700',   'dot' => 'bg-purple-500'],
            'received'         => ['label' => 'Recibida',          'color' => 'bg-green-100 text-green-700',     'dot' => 'bg-green-500'],
            'cancelled'        => ['label' => 'Cancelada',         'color' => 'bg-red-100 text-red-600',         'dot' => 'bg-red-500'],
        ];
        $priorityMeta = [
            'low'    => ['label' => 'BAJA',    'color' => 'text-gray-500'],
            'medium' => ['label' => 'MEDIA',   'color' => 'text-blue-600'],
            'high'   => ['label' => 'ALTA',    'color' => 'text-orange-600'],
            'urgent' => ['label' => 'URGENTE', 'color' => 'text-red-600'],
        ];
        $statusVal   = $purchaseOrder->status->value;
        $priorityVal = $purchaseOrder->priority->value;
        $sm          = $statusMeta[$statusVal] ?? $statusMeta['draft'];
        $pm          = $priorityMeta[$priorityVal] ?? $priorityMeta['medium'];
        $isClosed    = in_array($statusVal, ['received', 'cancelled']);
    @endphp

    <div class="p-6 space-y-5">

        {{-- ── Breadcrumb + actions ────────────────────── --}}
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('purchase-orders.index') }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                    Órdenes de Compra
                </a>
                <span class="text-gray-300">/</span>
                <span class="font-semibold text-[#002046]">{{ $purchaseOrder->code }}</span>
            </div>

            <div class="flex items-center gap-2">
                @if (!$isClosed)
                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}"
                       class="flex items-center gap-2 px-4 py-2 text-sm font-semibold border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-gray-600">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                        Editar
                    </a>
                @endif
            </div>
        </div>

        {{-- ── Header card ──────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xl font-extrabold text-[#002046]">{{ $purchaseOrder->code }}</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $sm['color'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $sm['dot'] }}"></span>
                            {{ $sm['label'] }}
                        </span>
                        <span class="text-xs font-bold uppercase {{ $pm['color'] }}">
                            <i data-lucide="flag" class="w-3.5 h-3.5 inline -mt-0.5"></i>
                            {{ $pm['label'] }}
                        </span>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-800">{{ $purchaseOrder->supplier_name }}</p>
                        @if ($purchaseOrder->supplier_contact)
                            <p class="text-sm text-gray-400 mt-0.5">{{ $purchaseOrder->supplier_contact }}</p>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-extrabold text-[#002046]">${{ number_format($purchaseOrder->total_amount, 2) }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $purchaseOrder->currency }}</p>
                </div>
            </div>

            {{-- Meta info --}}
            <div class="mt-6 pt-5 border-t border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Solicitado por</p>
                    <p class="font-medium text-gray-700">{{ optional($purchaseOrder->requestedBy)->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">OT Vinculada</p>
                    @if ($purchaseOrder->workOrder)
                        <a href="{{ route('work-orders.show', $purchaseOrder->workOrder) }}"
                           class="font-medium text-[#002046] hover:underline font-mono text-xs">
                            {{ $purchaseOrder->workOrder->code }}
                        </a>
                    @else
                        <p class="text-gray-300">—</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Entrega Estimada</p>
                    <p class="font-medium text-gray-700">
                        {{ $purchaseOrder->expected_delivery ? $purchaseOrder->expected_delivery->format('d/m/Y') : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Fecha Recepción</p>
                    <p class="font-medium text-gray-700">
                        {{ $purchaseOrder->received_at ? $purchaseOrder->received_at->format('d/m/Y') : '—' }}
                    </p>
                </div>
            </div>

            @if ($purchaseOrder->notes)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Notas</p>
                    <p class="text-sm text-gray-600">{{ $purchaseOrder->notes }}</p>
                </div>
            @endif
        </div>

        {{-- ── Items table ──────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500">
                    Artículos ({{ $purchaseOrder->items->count() }})
                </h3>
            </div>
            @if ($purchaseOrder->items->isEmpty())
                <div class="py-10 text-center text-gray-400 text-sm">Sin artículos registrados.</div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/60 border-b border-gray-100">
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Descripción</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400 hidden md:table-cell">No. Parte</th>
                            <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Cant.</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Unidad</th>
                            <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">P. Unitario</th>
                            <th class="text-right px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($purchaseOrder->items as $item)
                            <tr class="hover:bg-gray-50/40">
                                <td class="px-5 py-3.5">
                                    <div class="font-medium text-gray-800">{{ $item->description }}</div>
                                    @if ($item->part && $item->part->name !== $item->description)
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $item->part->name }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell text-gray-500 font-mono text-xs">
                                    {{ $item->part_number ?? '—' }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-medium text-gray-700">
                                    {{ number_format($item->quantity, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-gray-500 text-xs">{{ $item->unit }}</td>
                                <td class="px-4 py-3.5 text-right text-gray-700">
                                    ${{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-semibold text-gray-800">
                                    ${{ number_format($item->total_price, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-200 bg-gray-50/60">
                            <td colspan="5" class="px-5 py-3 text-right text-sm font-bold text-gray-600">Total {{ $purchaseOrder->currency }}:</td>
                            <td class="px-5 py-3 text-right text-base font-extrabold text-[#002046]">
                                ${{ number_format($purchaseOrder->total_amount, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>

        {{-- ── Timestamps ───────────────────────────────── --}}
        <div class="flex items-center gap-6 text-xs text-gray-400 px-1">
            <span>Creada: {{ $purchaseOrder->created_at->format('d/m/Y H:i') }}</span>
            @if ($purchaseOrder->updated_at->ne($purchaseOrder->created_at))
                <span>Actualizada: {{ $purchaseOrder->updated_at->format('d/m/Y H:i') }}</span>
            @endif
        </div>

    </div>

</x-layouts.cmms>
