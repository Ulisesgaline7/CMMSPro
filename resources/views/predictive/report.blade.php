<x-layouts.cmms title="Reporte de Confiabilidad" headerTitle="IA Predictiva — Reporte">

    <div class="p-6 space-y-5">

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Reporte de Flota</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $metrics->total() }} activos con análisis</p>
            </div>
        </div>

        {{-- Sort --}}
        <div class="flex gap-2">
            @foreach (['risk' => 'Riesgo', 'availability' => 'Disponibilidad', 'mtbf' => 'MTBF'] as $key => $label)
                <a href="{{ route('predictive.report', ['sort' => $key]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors
                       {{ $sort === $key ? 'bg-[#002046] text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-[#002046]' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($metrics->isEmpty())
                <div class="py-16 text-center">
                    <i data-lucide="bar-chart-2" class="w-12 h-12 mx-auto text-gray-200 mb-3"></i>
                    <p class="text-gray-500">No hay métricas de confiabilidad calculadas aún.</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Activo</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">MTBF</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden md:table-cell">MTTR</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Disponibilidad</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">P(Falla 30d)</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 hidden lg:table-cell">PM Recomendado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($metrics as $metric)
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
                                <td class="px-4 py-3.5">
                                    @if ($metric->availability_percent !== null)
                                        @php $avail = (float) $metric->availability_percent; @endphp
                                        <span class="font-semibold {{ $avail < 90 ? 'text-red-600' : ($avail < 95 ? 'text-orange-600' : 'text-green-600') }}">
                                            {{ number_format($avail, 1) }}%
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell">
                                    @if ($metric->failure_probability_30d !== null)
                                        @php $prob = (float) $metric->failure_probability_30d; @endphp
                                        <span class="font-bold text-xs {{ $prob > 60 ? 'text-red-600' : ($prob > 30 ? 'text-orange-600' : 'text-green-600') }}">
                                            {{ number_format($prob, 1) }}%
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 hidden lg:table-cell text-gray-600">
                                    {{ $metric->recommended_pm_interval_days !== null ? $metric->recommended_pm_interval_days . ' días' : '—' }}
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    @if ($metric->asset)
                                        <a href="{{ route('predictive.assets.show', $metric->asset) }}"
                                           class="text-xs font-semibold text-[#002046] hover:underline">Ver</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if ($metrics->hasPages())
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $metrics->firstItem() }}–{{ $metrics->lastItem() }} de {{ $metrics->total() }}</span>
                {{ $metrics->withQueryString()->links('pagination::simple-tailwind') }}
            </div>
        @endif

    </div>

</x-layouts.cmms>
