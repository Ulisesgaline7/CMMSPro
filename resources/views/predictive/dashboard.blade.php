<x-layouts.cmms title="IA Predictiva" headerTitle="IA Predictiva">

    <div class="p-6 space-y-6">

        {{-- KPIs --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                <p class="text-3xl font-extrabold text-[#002046] font-headline">{{ $assetsWithMetrics }}</p>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-1">Activos Analizados</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                <p class="text-3xl font-extrabold text-blue-600 font-headline">{{ $avgMtbf !== null ? $avgMtbf . 'h' : '—' }}</p>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-1">MTBF Promedio</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                <p class="text-3xl font-extrabold text-red-600 font-headline">{{ $lowAvailability }}</p>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-1">Disponibilidad &lt; 90%</p>
            </div>
        </div>

        {{-- Top Risk Assets --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-[#002046]">Top Activos por Riesgo de Falla (30 días)</h3>
                <a href="{{ route('predictive.report') }}" class="text-xs font-semibold text-blue-600 hover:underline">Reporte completo</a>
            </div>
            @if ($topRiskAssets->isEmpty())
                <div class="py-12 text-center">
                    <i data-lucide="bar-chart-2" class="w-10 h-10 mx-auto text-gray-200 mb-3"></i>
                    <p class="text-sm text-gray-400">No hay métricas calculadas aún.</p>
                    <p class="text-xs text-gray-400 mt-1">Navega a un activo y ejecuta el cálculo de confiabilidad.</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-50 bg-gray-50/60">
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Activo</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">MTBF</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">MTTR</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">Disponibilidad</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">P(Falla 30d)</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($topRiskAssets as $metric)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-[#002046]">{{ $metric->asset?->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-400 font-mono">{{ $metric->asset?->code }}</div>
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell text-gray-600">
                                    {{ $metric->mtbf_hours !== null ? number_format((float) $metric->mtbf_hours, 1) . 'h' : '—' }}
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell text-gray-600">
                                    {{ $metric->mttr_hours !== null ? number_format((float) $metric->mttr_hours, 1) . 'h' : '—' }}
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell">
                                    @if ($metric->availability_percent !== null)
                                        @php $avail = (float) $metric->availability_percent; @endphp
                                        <span class="font-semibold {{ $avail < 90 ? 'text-red-600' : ($avail < 95 ? 'text-orange-600' : 'text-green-600') }}">
                                            {{ number_format($avail, 1) }}%
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    @if ($metric->failure_probability_30d !== null)
                                        @php $prob = (float) $metric->failure_probability_30d; @endphp
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full max-w-16">
                                                <div class="h-1.5 rounded-full {{ $prob > 60 ? 'bg-red-500' : ($prob > 30 ? 'bg-orange-400' : 'bg-green-400') }}"
                                                     style="width: {{ min(100, $prob) }}%"></div>
                                            </div>
                                            <span class="text-xs font-bold {{ $prob > 60 ? 'text-red-600' : ($prob > 30 ? 'text-orange-600' : 'text-green-600') }}">
                                                {{ number_format($prob, 1) }}%
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    @if ($metric->asset)
                                        <a href="{{ route('predictive.assets.show', $metric->asset) }}"
                                           class="text-xs font-semibold text-[#002046] hover:underline">Analizar</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

</x-layouts.cmms>
