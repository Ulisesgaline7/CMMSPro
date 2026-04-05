<x-layouts.cmms title="Órdenes de Compra" headerTitle="Órdenes de Compra">

    @php
        $statusLabels = [
            'draft'            => 'Borrador',
            'pending_approval' => 'Pend. Aprobación',
            'approved'         => 'Aprobada',
            'ordered'          => 'Pedida',
            'received'         => 'Recibida',
            'cancelled'        => 'Cancelada',
        ];
        $statusColors = [
            'draft'            => 'bg-gray-100 text-gray-600 border-gray-200',
            'pending_approval' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            'approved'         => 'bg-blue-50 text-blue-700 border-blue-200',
            'ordered'          => 'bg-purple-50 text-purple-700 border-purple-200',
            'received'         => 'bg-green-50 text-green-700 border-green-200',
            'cancelled'        => 'bg-red-50 text-red-600 border-red-200',
        ];
        $priorityColors = [
            'low'    => 'text-gray-400',
            'medium' => 'text-blue-500',
            'high'   => 'text-orange-500',
            'urgent' => 'text-red-600',
        ];
        $priorityLabels = [
            'low'    => 'Baja',
            'medium' => 'Media',
            'high'   => 'Alta',
            'urgent' => 'Urgente',
        ];
    @endphp

    <div class="p-6 space-y-5">

        {{-- ── Stats row ────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
            @php
                $statCards = [
                    ['label' => 'Total',         'value' => $stats['total'],            'color' => 'text-[#002046]'],
                    ['label' => 'Borradores',    'value' => $stats['draft'],            'color' => 'text-gray-500'],
                    ['label' => 'Pend. Aprobac.','value' => $stats['pending_approval'], 'color' => 'text-yellow-600'],
                    ['label' => 'Pedidas',       'value' => $stats['ordered'],          'color' => 'text-purple-600'],
                    ['label' => 'Recibidas',     'value' => $stats['received'],         'color' => 'text-green-600'],
                ];
            @endphp
            @foreach ($statCards as $card)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 text-center">
                    <p class="text-2xl font-extrabold {{ $card['color'] }} font-headline">{{ $card['value'] }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mt-0.5">{{ $card['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- ── Header + search ─────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Órdenes de Compra</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $purchaseOrders->total() }} {{ $purchaseOrders->total() === 1 ? 'orden registrada' : 'órdenes registradas' }}</p>
            </div>
            <a href="{{ route('purchase-orders.create') }}"
               class="flex items-center gap-2 bg-[#002046] text-white px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Nueva OC
            </a>
        </div>

        {{-- ── Filters ──────────────────────────────────── --}}
        <form method="GET" action="{{ route('purchase-orders.index') }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">

            <div class="relative flex-1 min-w-48">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Buscar por código, proveedor..."
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            </div>

            <select name="status" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los estados</option>
                @foreach ($statusLabels as $val => $lbl)
                    <option value="{{ $val }}" {{ ($filters['status'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>

            <select name="priority" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Toda prioridad</option>
                @foreach ($priorityLabels as $val => $lbl)
                    <option value="{{ $val }}" {{ ($filters['priority'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>

            <button type="submit"
                    class="px-4 py-2 text-sm font-semibold bg-[#002046] text-white rounded-lg hover:bg-[#1b365d] transition-colors">
                Buscar
            </button>

            @if (array_filter($filters))
                <a href="{{ route('purchase-orders.index') }}"
                   class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Limpiar
                </a>
            @endif
        </form>

        {{-- ── Table ────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($purchaseOrders->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <i data-lucide="shopping-cart" class="w-12 h-12 text-gray-200 mb-3"></i>
                    <p class="text-gray-500 font-medium">No se encontraron órdenes de compra</p>
                    <p class="text-gray-400 text-sm mt-1">
                        {{ array_filter($filters) ? 'Intenta con otros filtros' : 'Crea la primera orden de compra' }}
                    </p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Código</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Proveedor</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">OT Vinculada</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Prioridad</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Estado</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Total</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Entrega Est.</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($purchaseOrders as $po)
                            @php
                                $statusVal   = $po->status->value;
                                $priorityVal = $po->priority->value;
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span class="font-mono font-semibold text-[#002046] text-xs">{{ $po->code }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-medium text-gray-800">{{ $po->supplier_name }}</div>
                                    @if ($po->supplier_contact)
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $po->supplier_contact }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell text-gray-500 text-xs font-mono">
                                    {{ optional($po->workOrder)->code ?? '—' }}
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell">
                                    <span class="text-xs font-semibold {{ $priorityColors[$priorityVal] ?? 'text-gray-400' }}">
                                        {{ $priorityLabels[$priorityVal] ?? $priorityVal }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border {{ $statusColors[$statusVal] ?? '' }}">
                                        {{ $statusLabels[$statusVal] ?? $statusVal }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell text-gray-700 font-medium">
                                    ${{ number_format($po->total_amount, 2) }} {{ $po->currency }}
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell">
                                    @if ($po->expected_delivery)
                                        <span class="text-gray-600 text-xs">{{ $po->expected_delivery->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('purchase-orders.show', $po) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-[#002046] hover:underline">
                                        Ver
                                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- ── Pagination ───────────────────────────────── --}}
        @if ($purchaseOrders->hasPages())
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $purchaseOrders->firstItem() }}–{{ $purchaseOrders->lastItem() }} de {{ $purchaseOrders->total() }}</span>
                {{ $purchaseOrders->withQueryString()->links('pagination::simple-tailwind') }}
            </div>
        @endif

    </div>

</x-layouts.cmms>
