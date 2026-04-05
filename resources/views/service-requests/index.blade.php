<x-layouts.cmms title="Facilities" headerTitle="Facilities / Solicitudes de Servicio">

    <div class="p-6 space-y-5">

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @php
                $statCards = [
                    ['label' => 'Total',         'value' => $stats['total'],       'color' => 'text-[#002046]'],
                    ['label' => 'Abiertas',       'value' => $stats['open'],        'color' => 'text-blue-600'],
                    ['label' => 'En Progreso',   'value' => $stats['in_progress'], 'color' => 'text-amber-600'],
                    ['label' => 'Riesgo SLA',    'value' => $stats['sla_at_risk'], 'color' => 'text-red-600'],
                ];
            @endphp
            @foreach ($statCards as $card)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 text-center">
                    <p class="text-2xl font-extrabold {{ $card['color'] }} font-headline">{{ $card['value'] }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mt-0.5">{{ $card['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Header + create --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Solicitudes de Servicio</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $serviceRequests->total() }} {{ $serviceRequests->total() === 1 ? 'solicitud registrada' : 'solicitudes registradas' }}</p>
            </div>
            <a href="{{ route('service-requests.create') }}"
               class="flex items-center gap-2 bg-[#002046] text-white px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Nueva Solicitud
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('service-requests.index') }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">

            <div class="relative flex-1 min-w-48">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Buscar por código, título..."
                       class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
            </div>

            <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los estados</option>
                @foreach (\App\Enums\ServiceRequestStatus::cases() as $s)
                    <option value="{{ $s->value }}" @selected(($filters['status'] ?? '') === $s->value)>{{ $s->label() }}</option>
                @endforeach
            </select>

            <select name="category" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todas las categorías</option>
                @foreach (\App\Enums\ServiceRequestCategory::cases() as $c)
                    <option value="{{ $c->value }}" @selected(($filters['category'] ?? '') === $c->value)>{{ $c->label() }}</option>
                @endforeach
            </select>

            <select name="priority" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todas las prioridades</option>
                @foreach (\App\Enums\ServiceRequestPriority::cases() as $p)
                    <option value="{{ $p->value }}" @selected(($filters['priority'] ?? '') === $p->value)>{{ $p->label() }}</option>
                @endforeach
            </select>

            <button type="submit" class="flex items-center gap-1.5 bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-700 transition-colors">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filtrar
            </button>
            @if (array_filter($filters))
                <a href="{{ route('service-requests.index') }}" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">Limpiar</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#f9f9fd] border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Código</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Título</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Categoría</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Prioridad</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Estado</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">SLA</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Asignado</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($serviceRequests as $sr)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-xs text-gray-500">{{ $sr->code }}</td>
                            <td class="px-5 py-3.5 font-semibold text-[#002046] max-w-xs truncate">{{ $sr->title }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $sr->category->color() }}">
                                    <i data-lucide="{{ $sr->category->icon() }}" class="w-3 h-3"></i>
                                    {{ $sr->category->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $sr->priority->color() }}">
                                    {{ $sr->priority->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $sr->status->color() }}">
                                    {{ $sr->status->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($sr->sla_deadline)
                                    @if ($sr->isSlaBreached())
                                        <span class="inline-flex items-center gap-1 text-[11px] text-red-600 font-semibold">
                                            <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                            Vencido
                                        </span>
                                    @else
                                        <span class="text-[11px] text-gray-500">{{ $sr->sla_deadline->format('d/m H:i') }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-600">{{ $sr->assignedTo?->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center gap-2 justify-end">
                                    <a href="{{ route('service-requests.show', $sr) }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('service-requests.edit', $sr) }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-gray-400 text-sm">
                                <i data-lucide="building-2" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                                <p class="font-medium">No hay solicitudes de servicio</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($serviceRequests->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $serviceRequests->links() }}</div>
            @endif
        </div>

    </div>

</x-layouts.cmms>
