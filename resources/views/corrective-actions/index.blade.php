<x-layouts.cmms title="CAPA" headerTitle="Acciones Correctivas y Preventivas (CAPA)">

    @php
        $statusLabels = ['open' => 'Abierta', 'in_progress' => 'En Progreso', 'completed' => 'Completada', 'verified' => 'Verificada', 'cancelled' => 'Cancelada'];
        $statusColors = [
            'open'        => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            'in_progress' => 'bg-blue-50 text-blue-700 border-blue-200',
            'completed'   => 'bg-teal-50 text-teal-700 border-teal-200',
            'verified'    => 'bg-green-50 text-green-700 border-green-200',
            'cancelled'   => 'bg-red-50 text-red-600 border-red-200',
        ];
        $typeLabels = ['corrective' => 'Correctiva', 'preventive' => 'Preventiva'];
        $typeColors = ['corrective' => 'bg-red-100 text-red-700', 'preventive' => 'bg-blue-100 text-blue-700'];
        $priorityColors = [
            'low'      => 'text-gray-400',
            'medium'   => 'text-blue-500',
            'high'     => 'text-orange-500',
            'critical' => 'text-red-600',
        ];
        $priorityLabels = ['low' => 'Baja', 'medium' => 'Media', 'high' => 'Alta', 'critical' => 'Crítica'];
    @endphp

    <div class="p-6 space-y-5">

        {{-- ── Stats row ────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @php
                $statCards = [
                    ['label' => 'Total',         'value' => $stats['total'],       'color' => 'text-[#002046]'],
                    ['label' => 'Abiertas',      'value' => $stats['open'],        'color' => 'text-yellow-600'],
                    ['label' => 'En Progreso',   'value' => $stats['in_progress'], 'color' => 'text-blue-600'],
                    ['label' => 'Completadas',   'value' => $stats['completed'],   'color' => 'text-green-600'],
                ];
            @endphp
            @foreach ($statCards as $card)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 text-center">
                    <p class="text-2xl font-extrabold {{ $card['color'] }} font-headline">{{ $card['value'] }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mt-0.5">{{ $card['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- ── Header + create ─────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Acciones CAPA</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $correctiveActions->total() }} {{ $correctiveActions->total() === 1 ? 'acción registrada' : 'acciones registradas' }}</p>
            </div>
            <a href="{{ route('corrective-actions.create') }}"
               class="flex items-center gap-2 bg-[#002046] text-white px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Nueva CAPA
            </a>
        </div>

        {{-- ── Filters ──────────────────────────────────── --}}
        <form method="GET" action="{{ route('corrective-actions.index') }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">

            <div class="relative flex-1 min-w-48">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Buscar por código, título..."
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            </div>

            <select name="status" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los estados</option>
                @foreach ($statusLabels as $val => $lbl)
                    <option value="{{ $val }}" {{ ($filters['status'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>

            <select name="type" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los tipos</option>
                <option value="corrective" {{ ($filters['type'] ?? '') === 'corrective' ? 'selected' : '' }}>Correctiva</option>
                <option value="preventive" {{ ($filters['type'] ?? '') === 'preventive' ? 'selected' : '' }}>Preventiva</option>
            </select>

            <button type="submit"
                    class="px-4 py-2 text-sm font-semibold bg-[#002046] text-white rounded-lg hover:bg-[#1b365d] transition-colors">
                Buscar
            </button>

            @if (array_filter($filters))
                <a href="{{ route('corrective-actions.index') }}"
                   class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Limpiar
                </a>
            @endif
        </form>

        {{-- ── Table ────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($correctiveActions->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <i data-lucide="shield-check" class="w-12 h-12 text-gray-200 mb-3"></i>
                    <p class="text-gray-500 font-medium">No se encontraron acciones CAPA</p>
                    <p class="text-gray-400 text-sm mt-1">
                        {{ array_filter($filters) ? 'Intenta con otros filtros' : 'Crea la primera acción correctiva o preventiva' }}
                    </p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Acción</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">Tipo</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Prioridad</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Asignado</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Vencimiento</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($correctiveActions as $ca)
                            @php
                                $statusVal   = $ca->status->value;
                                $typeVal     = $ca->type;
                                $priorityVal = $ca->priority;
                                $isOverdue   = in_array($statusVal, ['open', 'in_progress']) && $ca->due_date && $ca->due_date->isPast();
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-[#002046]">{{ $ca->title }}</div>
                                    <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $ca->code }}</div>
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $typeColors[$typeVal] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $typeLabels[$typeVal] ?? $typeVal }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell">
                                    <span class="font-semibold text-xs {{ $priorityColors[$priorityVal] ?? 'text-gray-400' }}">
                                        {{ $priorityLabels[$priorityVal] ?? $priorityVal }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell text-gray-600">
                                    {{ optional($ca->assignedTo)->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell">
                                    @if ($ca->due_date)
                                        <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                            {{ $ca->due_date->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border {{ $statusColors[$statusVal] ?? '' }}">
                                        {{ $statusLabels[$statusVal] ?? $statusVal }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('corrective-actions.show', $ca) }}"
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
        @if ($correctiveActions->hasPages())
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $correctiveActions->firstItem() }}–{{ $correctiveActions->lastItem() }} de {{ $correctiveActions->total() }}</span>
                {{ $correctiveActions->withQueryString()->links('pagination::simple-tailwind') }}
            </div>
        @endif

    </div>

</x-layouts.cmms>
