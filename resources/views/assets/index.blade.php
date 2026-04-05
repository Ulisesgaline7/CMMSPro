<x-layouts.cmms title="Activos" headerTitle="Activos">

    @php
        $statusLabels = ['active' => 'Activo', 'inactive' => 'Inactivo', 'under_maintenance' => 'En Mantenimiento', 'retired' => 'Dado de Baja'];
        $statusColors = ['active' => 'bg-green-50 text-green-700 border-green-200', 'inactive' => 'bg-gray-100 text-gray-600 border-gray-200', 'under_maintenance' => 'bg-yellow-50 text-yellow-700 border-yellow-200', 'retired' => 'bg-red-50 text-red-600 border-red-200'];
        $criticalityLabels = ['low' => 'Baja', 'medium' => 'Media', 'high' => 'Alta', 'critical' => 'Crítica'];
        $criticalityColors = ['low' => 'text-gray-400', 'medium' => 'text-blue-500', 'high' => 'text-orange-500', 'critical' => 'text-red-600'];
    @endphp

    <div class="p-6 space-y-5">

        {{-- ── Header ───────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Activos</h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    {{ $assets->total() }} {{ $assets->total() === 1 ? 'activo registrado' : 'activos registrados' }}
                </p>
            </div>
            <a href="{{ route('assets.create') }}"
               class="flex items-center gap-2 bg-[#002046] text-white px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Nuevo Activo
            </a>
        </div>

        {{-- ── Filters ──────────────────────────────────── --}}
        <form method="GET" action="{{ route('assets.index') }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">

            <div class="relative flex-1 min-w-48">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Buscar por código, nombre, serie..."
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            </div>

            <select name="status" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los estados</option>
                @foreach ($statusLabels as $val => $lbl)
                    <option value="{{ $val }}" {{ ($filters['status'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>

            <select name="criticality" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Toda criticidad</option>
                @foreach ($criticalityLabels as $val => $lbl)
                    <option value="{{ $val }}" {{ ($filters['criticality'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>

            @if ($categories->isNotEmpty())
                <select name="category" onchange="this.form.submit()"
                        class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                    <option value="">Todas las categorías</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ ($filters['category'] ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            @endif

            <button type="submit"
                    class="px-4 py-2 text-sm font-semibold bg-[#002046] text-white rounded-lg hover:bg-[#1b365d] transition-colors">
                Buscar
            </button>

            @if (array_filter($filters))
                <a href="{{ route('assets.index') }}"
                   class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Limpiar
                </a>
            @endif
        </form>

        {{-- ── Table ────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($assets->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <i data-lucide="cpu" class="w-12 h-12 text-gray-200 mb-3"></i>
                    <p class="text-gray-500 font-medium">No se encontraron activos</p>
                    <p class="text-gray-400 text-sm mt-1">
                        {{ array_filter($filters) ? 'Intenta con otros filtros' : 'Crea el primer activo' }}
                    </p>
                    @if (!array_filter($filters))
                        <a href="{{ route('assets.create') }}"
                           class="mt-4 flex items-center gap-1.5 text-sm font-semibold text-[#002046] hover:underline">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            Nuevo Activo
                        </a>
                    @endif
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Activo</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">Marca / Modelo</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Ubicación</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Criticidad</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($assets as $asset)
                            @php
                                $sv = $asset->status->value;
                                $cv = $asset->criticality->value;
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-[#002046]">{{ $asset->name }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5 font-mono">{{ $asset->code }}</div>
                                    @if ($asset->category)
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $asset->category->name }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell">
                                    @if ($asset->brand || $asset->model)
                                        <span class="text-gray-700">{{ $asset->brand }}</span>
                                        @if ($asset->brand && $asset->model) <span class="text-gray-300 mx-1">·</span> @endif
                                        <span class="text-gray-500">{{ $asset->model }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                    @if ($asset->serial_number)
                                        <div class="text-xs text-gray-400 mt-0.5 font-mono">{{ $asset->serial_number }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell text-gray-600">
                                    {{ optional($asset->location)->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $criticalityColors[$cv] ?? '' }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                        {{ $criticalityLabels[$cv] ?? $cv }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border {{ $statusColors[$sv] ?? '' }}">
                                        {{ $statusLabels[$sv] ?? $sv }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('assets.show', $asset) }}"
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
        @if ($assets->hasPages())
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $assets->firstItem() }}–{{ $assets->lastItem() }} de {{ $assets->total() }}</span>
                {{ $assets->withQueryString()->links('pagination::simple-tailwind') }}
            </div>
        @endif

    </div>

</x-layouts.cmms>
