<x-layouts.cmms title="Alertas IoT" headerTitle="IoT — Alertas">

    <div class="p-6 space-y-5">

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Alertas Activas</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $alerts->total() }} alertas activas</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($alerts->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <i data-lucide="check-circle" class="w-12 h-12 text-green-200 mb-3"></i>
                    <p class="text-gray-500 font-medium">Sin alertas activas</p>
                    <p class="text-sm text-gray-400 mt-1">Todos los sensores operan dentro de los parámetros normales</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Alerta</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">Sensor / Activo</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Valor</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Severidad</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Disparada</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($alerts as $alert)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-semibold text-gray-800 max-w-xs truncate">{{ $alert->message }}</p>
                                    @if ($alert->acknowledged_at)
                                        <p class="text-[10px] text-orange-500 mt-0.5">Reconocida {{ $alert->acknowledged_at->diffForHumans() }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell">
                                    <p class="text-xs font-semibold text-[#002046]">{{ $alert->sensor?->name ?? '—' }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $alert->sensor?->asset?->name ?? '—' }}</p>
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell">
                                    @if ($alert->value !== null)
                                        <span class="font-semibold text-gray-700">{{ number_format((float) $alert->value, 2) }} {{ $alert->sensor?->unit }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold border {{ $alert->severity->color() }}">
                                        {{ $alert->severity->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell text-gray-500 text-xs">
                                    {{ $alert->triggered_at->diffForHumans() }}
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if (! $alert->acknowledged_at)
                                            <form method="POST" action="{{ route('iot.alerts.acknowledge', $alert) }}">
                                                @csrf
                                                <button type="submit" class="text-xs font-semibold text-orange-600 hover:underline">Reconocer</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('iot.alerts.resolve', $alert) }}">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-green-600 hover:underline">Resolver</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if ($alerts->hasPages())
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $alerts->firstItem() }}–{{ $alerts->lastItem() }} de {{ $alerts->total() }}</span>
                {{ $alerts->links('pagination::simple-tailwind') }}
            </div>
        @endif

    </div>

</x-layouts.cmms>
