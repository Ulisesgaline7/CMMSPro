<x-layouts.cmms title="Planes de Mantenimiento" headerTitle="Planes de Mantenimiento">

    <div class="p-6 space-y-5">

        {{-- ── Header ───────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Planes de Mantenimiento</h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    {{ $plans->total() }} {{ $plans->total() === 1 ? 'plan registrado' : 'planes registrados' }}
                </p>
            </div>
            <a href="{{ route('maintenance-plans.create') }}"
               class="flex items-center gap-2 bg-[#002046] text-white px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Nuevo Plan
            </a>
        </div>

        {{-- ── Filters ──────────────────────────────────── --}}
        <form method="GET" action="{{ route('maintenance-plans.index') }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">

            <div class="relative flex-1 min-w-48">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Buscar por nombre..."
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            </div>

            <select name="frequency" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Toda frecuencia</option>
                @foreach ($frequencies as $freq)
                    <option value="{{ $freq->value }}" {{ ($filters['frequency'] ?? '') === $freq->value ? 'selected' : '' }}>
                        {{ $freq->label() }}
                    </option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los estados</option>
                <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Activo</option>
                <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
            </select>

            <button type="submit"
                    class="px-4 py-2 text-sm font-semibold bg-[#002046] text-white rounded-lg hover:bg-[#1b365d] transition-colors">
                Buscar
            </button>

            @if (array_filter($filters))
                <a href="{{ route('maintenance-plans.index') }}"
                   class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Limpiar
                </a>
            @endif
        </form>

        {{-- ── Table ────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($plans->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <i data-lucide="calendar-clock" class="w-12 h-12 text-gray-200 mb-3"></i>
                    <p class="text-gray-500 font-medium">No se encontraron planes de mantenimiento</p>
                    <p class="text-gray-400 text-sm mt-1">
                        {{ array_filter($filters) ? 'Intenta con otros filtros' : 'Crea el primer plan' }}
                    </p>
                    @if (!array_filter($filters))
                        <a href="{{ route('maintenance-plans.create') }}"
                           class="mt-4 flex items-center gap-1.5 text-sm font-semibold text-[#002046] hover:underline">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            Nuevo Plan
                        </a>
                    @endif
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Plan</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">Activo</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Frecuencia</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Próxima Ejecución</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($plans as $plan)
                            @php
                                $isOverdue = $plan->is_active
                                    && $plan->next_execution_date
                                    && $plan->next_execution_date->isPast();
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-[#002046]">{{ $plan->name }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $plan->type->label() }}</div>
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell text-gray-600">
                                    @if ($plan->asset)
                                        <span class="text-sm font-medium text-gray-700">{{ $plan->asset->name }}</span>
                                        <div class="text-xs text-gray-400 font-mono">{{ $plan->asset->code }}</div>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell text-gray-600">
                                    {{ $plan->frequency->label() }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                        {{ $plan->next_execution_date?->format('d/m/Y') ?? '—' }}
                                    </span>
                                    @if ($isOverdue)
                                        <div class="text-xs text-red-500 font-medium">Vencido</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    @if ($plan->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border bg-green-50 text-green-700 border-green-200">
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border bg-gray-100 text-gray-600 border-gray-200">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('maintenance-plans.show', $plan) }}"
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
        @if ($plans->hasPages())
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $plans->firstItem() }}–{{ $plans->lastItem() }} de {{ $plans->total() }}</span>
                {{ $plans->withQueryString()->links('pagination::simple-tailwind') }}
            </div>
        @endif

    </div>

</x-layouts.cmms>
