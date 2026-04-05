<x-layouts.cmms title="Inventario" headerTitle="Inventario">

    <div class="p-6 space-y-5">

        {{-- ── Header ───────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Inventario</h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    {{ $parts->total() }} {{ $parts->total() === 1 ? 'repuesto registrado' : 'repuestos registrados' }}
                </p>
            </div>
            <a href="{{ route('inventory.create') }}"
               class="flex items-center gap-2 bg-[#002046] text-white px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Nuevo Repuesto
            </a>
        </div>

        {{-- ── Filters ──────────────────────────────────── --}}
        <form method="GET" action="{{ route('inventory.index') }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">

            <div class="relative flex-1 min-w-48">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Buscar por nombre, número de parte, marca..."
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            </div>

            <select name="stock" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todo el stock</option>
                <option value="low" {{ ($filters['stock'] ?? '') === 'low' ? 'selected' : '' }}>Stock bajo</option>
                <option value="ok"  {{ ($filters['stock'] ?? '') === 'ok'  ? 'selected' : '' }}>Stock OK</option>
            </select>

            <button type="submit"
                    class="px-4 py-2 text-sm font-semibold bg-[#002046] text-white rounded-lg hover:bg-[#1b365d] transition-colors">
                Buscar
            </button>

            @if (array_filter($filters))
                <a href="{{ route('inventory.index') }}"
                   class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Limpiar
                </a>
            @endif
        </form>

        {{-- ── Table ────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($parts->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <i data-lucide="package" class="w-12 h-12 text-gray-200 mb-3"></i>
                    <p class="text-gray-500 font-medium">No se encontraron repuestos</p>
                    <p class="text-gray-400 text-sm mt-1">
                        {{ array_filter($filters) ? 'Intenta con otros filtros' : 'Crea el primer repuesto' }}
                    </p>
                    @if (!array_filter($filters))
                        <a href="{{ route('inventory.create') }}"
                           class="mt-4 flex items-center gap-1.5 text-sm font-semibold text-[#002046] hover:underline">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            Nuevo Repuesto
                        </a>
                    @endif
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Repuesto</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">Marca</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Ubicación</th>
                            <th class="text-center px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Stock</th>
                            <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">Costo Unit.</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($parts as $part)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-[#002046]">{{ $part->name }}</div>
                                    @if ($part->part_number)
                                        <div class="text-xs text-gray-400 mt-0.5 font-mono">{{ $part->part_number }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell text-gray-600">
                                    {{ $part->brand ?? '—' }}
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell text-gray-600">
                                    {{ $part->storage_location ?? '—' }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @php $belowMin = $part->isBelowMinStock(); @endphp
                                    <div class="inline-flex flex-col items-center">
                                        <span class="text-base font-extrabold {{ $belowMin ? 'text-red-600' : 'text-gray-800' }}">
                                            {{ $part->stock_quantity }}
                                        </span>
                                        <span class="text-[10px] text-gray-400">/ mín {{ $part->min_stock }} {{ $part->unit }}</span>
                                    </div>
                                    @if ($belowMin)
                                        <div class="mt-0.5">
                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-red-600 bg-red-50 border border-red-200 px-1.5 py-0.5 rounded">
                                                <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                                BAJO
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell text-right text-gray-600">
                                    {{ $part->unit_cost ? '$' . number_format($part->unit_cost, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('inventory.show', $part) }}"
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
        @if ($parts->hasPages())
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $parts->firstItem() }}–{{ $parts->lastItem() }} de {{ $parts->total() }}</span>
                {{ $parts->withQueryString()->links('pagination::simple-tailwind') }}
            </div>
        @endif

    </div>

</x-layouts.cmms>
