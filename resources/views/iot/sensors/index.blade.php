<x-layouts.cmms title="Sensores IoT" headerTitle="Sensores IoT">

    <div class="p-6 space-y-5">

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Sensores</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $sensors->total() }} sensores registrados</p>
            </div>
            <a href="{{ route('iot.sensors.create') }}"
               class="flex items-center gap-2 bg-[#002046] text-white px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Nuevo Sensor
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('iot.sensors.index') }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">
            <div class="relative flex-1 min-w-48">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Buscar por nombre o código..."
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
            </div>
            <select name="status" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none">
                <option value="">Todos los estados</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" {{ ($filters['status'] ?? '') === $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-[#002046] text-white rounded-lg hover:bg-[#1b365d] transition-colors">
                Buscar
            </button>
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($sensors->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <i data-lucide="radio-tower" class="w-12 h-12 text-gray-200 mb-3"></i>
                    <p class="text-gray-500 font-medium">No hay sensores configurados</p>
                    <a href="{{ route('iot.sensors.create') }}" class="mt-3 text-sm text-blue-600 hover:underline font-semibold">Agregar primer sensor</a>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Sensor</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">Activo</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Última Lectura</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($sensors as $sensor)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-[#002046]">{{ $sensor->name }}</div>
                                    <div class="text-xs text-gray-400 font-mono">{{ $sensor->code }}</div>
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell text-gray-600">{{ $sensor->asset?->name ?? '—' }}</td>
                                <td class="px-4 py-3.5 hidden lg:table-cell">
                                    @if ($sensor->last_reading_value !== null)
                                        <span class="font-semibold text-gray-700">{{ number_format((float) $sensor->last_reading_value, 2) }} {{ $sensor->unit }}</span>
                                        <span class="block text-[10px] text-gray-400">{{ $sensor->last_reading_at?->diffForHumans() }}</span>
                                    @else
                                        <span class="text-gray-300">Sin lectura</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border {{ $sensor->status->color() }}">
                                        {{ $sensor->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('iot.sensors.show', $sensor) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-[#002046] hover:underline">
                                        Ver <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if ($sensors->hasPages())
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $sensors->firstItem() }}–{{ $sensors->lastItem() }} de {{ $sensors->total() }}</span>
                {{ $sensors->withQueryString()->links('pagination::simple-tailwind') }}
            </div>
        @endif

    </div>

</x-layouts.cmms>
