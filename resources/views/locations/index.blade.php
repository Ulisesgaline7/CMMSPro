<x-layouts.cmms title="Ubicaciones" headerTitle="Ubicaciones">

    <div class="p-6 space-y-5">

        {{-- ── Header ───────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Ubicaciones</h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    {{ $locations->total() }} {{ $locations->total() === 1 ? 'ubicación registrada' : 'ubicaciones registradas' }}
                </p>
            </div>
            <a href="{{ route('locations.create') }}"
               class="flex items-center gap-2 bg-[#002046] text-white px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Nueva Ubicación
            </a>
        </div>

        {{-- ── Filters ──────────────────────────────────── --}}
        <form method="GET" action="{{ route('locations.index') }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">

            <div class="relative flex-1 min-w-48">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Buscar por nombre o código..."
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            </div>

            <select name="type" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los tipos</option>
                @foreach ($types as $typeOption)
                    <option value="{{ $typeOption->value }}"
                            {{ ($filters['type'] ?? '') === $typeOption->value ? 'selected' : '' }}>
                        {{ $typeOption->label() }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                    class="px-4 py-2 text-sm font-semibold bg-[#002046] text-white rounded-lg hover:bg-[#1b365d] transition-colors">
                Buscar
            </button>

            @if (array_filter($filters))
                <a href="{{ route('locations.index') }}"
                   class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Limpiar
                </a>
            @endif
        </form>

        {{-- ── Table ────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($locations->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <i data-lucide="map-pin" class="w-12 h-12 text-gray-200 mb-3"></i>
                    <p class="text-gray-500 font-medium">No se encontraron ubicaciones</p>
                    <p class="text-gray-400 text-sm mt-1">
                        {{ array_filter($filters) ? 'Intenta con otros filtros' : 'Crea la primera ubicación' }}
                    </p>
                    @if (!array_filter($filters))
                        <a href="{{ route('locations.create') }}"
                           class="mt-4 flex items-center gap-1.5 text-sm font-semibold text-[#002046] hover:underline">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            Nueva Ubicación
                        </a>
                    @endif
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Tipo</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Nombre / Código</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">Ubicación Padre</th>
                            <th class="text-center px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Activos</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($locations as $location)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <i data-lucide="{{ $location->type->icon() }}" class="w-4 h-4"></i>
                                        <span class="text-xs font-semibold">{{ $location->type->label() }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-[#002046]">{{ $location->name }}</div>
                                    @if ($location->code)
                                        <div class="text-xs text-gray-400 mt-0.5 font-mono">{{ $location->code }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell text-gray-600">
                                    {{ $location->parent?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell text-center">
                                    <span class="text-gray-700 font-semibold">{{ $location->assets_count }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('locations.edit', $location) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-[#002046] hover:underline">
                                        Editar
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- ── Pagination ───────────────────────────────── --}}
        @if ($locations->hasPages())
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $locations->firstItem() }}–{{ $locations->lastItem() }} de {{ $locations->total() }}</span>
                {{ $locations->withQueryString()->links('pagination::simple-tailwind') }}
            </div>
        @endif

    </div>

</x-layouts.cmms>
