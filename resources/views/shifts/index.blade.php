<x-layouts.cmms title="Turnos" headerTitle="Turnos de Técnicos">

    <div class="p-6 space-y-5">

        {{-- ── Stats row ────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
            @php
                $statCards = [
                    ['label' => 'Total',       'value' => $stats['total'],     'color' => 'text-[#002046]'],
                    ['label' => 'Programados', 'value' => $stats['scheduled'], 'color' => 'text-yellow-600'],
                    ['label' => 'Activos',     'value' => $stats['active'],    'color' => 'text-green-600'],
                    ['label' => 'Hoy',         'value' => $stats['today'],     'color' => 'text-blue-600'],
                    ['label' => 'Ausentes',    'value' => $stats['absent'],    'color' => 'text-red-600'],
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
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Turnos</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $shifts->total() }} {{ $shifts->total() === 1 ? 'turno registrado' : 'turnos registrados' }}</p>
            </div>
            <a href="{{ route('shifts.create') }}"
               class="flex items-center gap-2 bg-[#002046] text-white px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Programar Turno
            </a>
        </div>

        {{-- ── Filters ──────────────────────────────────── --}}
        <form method="GET" action="{{ route('shifts.index') }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">

            <input type="date" name="date" value="{{ $filters['date'] ?? '' }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">

            <select name="type" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los tipos</option>
                <option value="morning" @selected(($filters['type'] ?? '') === 'morning')>Mañana</option>
                <option value="afternoon" @selected(($filters['type'] ?? '') === 'afternoon')>Tarde</option>
                <option value="night" @selected(($filters['type'] ?? '') === 'night')>Noche</option>
                <option value="custom" @selected(($filters['type'] ?? '') === 'custom')>Personalizado</option>
            </select>

            <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los estados</option>
                <option value="scheduled" @selected(($filters['status'] ?? '') === 'scheduled')>Programado</option>
                <option value="active" @selected(($filters['status'] ?? '') === 'active')>Activo</option>
                <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Completado</option>
                <option value="absent" @selected(($filters['status'] ?? '') === 'absent')>Ausente</option>
            </select>

            <select name="user_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                <option value="">Todos los técnicos</option>
                @foreach ($technicians as $tech)
                    <option value="{{ $tech->id }}" @selected(($filters['user_id'] ?? '') == $tech->id)>{{ $tech->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="flex items-center gap-1.5 bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-700 transition-colors">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filtrar
            </button>

            @if (array_filter($filters))
                <a href="{{ route('shifts.index') }}" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">Limpiar</a>
            @endif
        </form>

        {{-- ── Table ─────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#f9f9fd] border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Técnico</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Turno</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Fecha</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Horario</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Tipo</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Estado</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($shifts as $shift)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3.5 font-semibold text-[#002046]">{{ $shift->technician?->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-gray-700">{{ $shift->name }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $shift->date->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-gray-600 font-mono text-xs">{{ $shift->start_time }} – {{ $shift->end_time }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $shift->type->color() }}">
                                    <i data-lucide="{{ $shift->type->icon() }}" class="w-3 h-3"></i>
                                    {{ $shift->type->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $shift->status->color() }}">
                                    {{ $shift->status->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center gap-2 justify-end">
                                    <a href="{{ route('shifts.show', $shift) }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('shifts.edit', $shift) }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-gray-400 text-sm">
                                <i data-lucide="clock" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                                <p class="font-medium">No hay turnos registrados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($shifts->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $shifts->links() }}
                </div>
            @endif
        </div>

    </div>

</x-layouts.cmms>
