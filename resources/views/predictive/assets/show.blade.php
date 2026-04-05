<x-layouts.cmms title="Análisis: {{ $asset->name }}" headerTitle="IA Predictiva">

    <div class="p-6 space-y-5">

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('predictive.dashboard') }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-extrabold text-[#002046] font-headline">{{ $asset->name }}</h2>
                    <p class="text-sm text-gray-400 font-mono">{{ $asset->code }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('predictive.assets.recalculate', $asset) }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 bg-[#002046] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#1b365d] transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    Recalcular
                </button>
            </form>
        </div>

        @if ($metric)
            {{-- KPIs --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                    <p class="text-2xl font-extrabold text-blue-600 font-headline">
                        {{ $metric->mtbf_hours !== null ? number_format((float) $metric->mtbf_hours, 1) . 'h' : '—' }}
                    </p>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-1">MTBF</p>
                    <p class="text-[10px] text-gray-400">Tiempo medio entre fallas</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                    <p class="text-2xl font-extrabold text-orange-600 font-headline">
                        {{ $metric->mttr_hours !== null ? number_format((float) $metric->mttr_hours, 1) . 'h' : '—' }}
                    </p>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-1">MTTR</p>
                    <p class="text-[10px] text-gray-400">Tiempo medio de reparación</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                    @php $avail = $metric->availability_percent !== null ? (float) $metric->availability_percent : null; @endphp
                    <p class="text-2xl font-extrabold {{ $avail !== null && $avail < 90 ? 'text-red-600' : 'text-green-600' }} font-headline">
                        {{ $avail !== null ? number_format($avail, 1) . '%' : '—' }}
                    </p>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-1">Disponibilidad</p>
                </div>
                <div class="bg-white rounded-xl border {{ $metric->failure_probability_30d !== null && (float) $metric->failure_probability_30d > 60 ? 'border-red-200' : 'border-gray-100' }} shadow-sm px-5 py-4">
                    @php $prob = $metric->failure_probability_30d !== null ? (float) $metric->failure_probability_30d : null; @endphp
                    <p class="text-2xl font-extrabold {{ $prob !== null && $prob > 60 ? 'text-red-600' : ($prob !== null && $prob > 30 ? 'text-orange-600' : 'text-green-600') }} font-headline">
                        {{ $prob !== null ? number_format($prob, 1) . '%' : '—' }}
                    </p>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-1">P(Falla 30d)</p>
                </div>
            </div>

            {{-- Details --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-[#002046] mb-4">Métricas del Período</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Período analizado</dt>
                            <dd class="font-medium text-xs">{{ $metric->period_start->format('d/m/Y') }} – {{ $metric->period_end->format('d/m/Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Total OTs</dt>
                            <dd class="font-semibold">{{ $metric->total_work_orders }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">OTs Correctivos</dt>
                            <dd class="font-semibold text-red-600">{{ $metric->corrective_count }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Tiempo inactivo total</dt>
                            <dd class="font-semibold">{{ number_format($metric->total_downtime_minutes / 60, 1) }}h</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Intervalo PM recomendado</dt>
                            <dd class="font-semibold text-blue-600">{{ $metric->recommended_pm_interval_days ?? '—' }} días</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Última actualización</dt>
                            <dd class="text-xs text-gray-400">{{ $metric->calculated_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-[#002046] mb-4">OTs Correctivos Recientes</h3>
                    @forelse ($recentCorrectiveWOs as $wo)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div>
                                <p class="text-xs font-semibold text-[#002046]">{{ $wo->title }}</p>
                                <p class="text-[10px] text-gray-400 font-mono">{{ $wo->code }}</p>
                            </div>
                            <span class="text-[10px] text-gray-400">{{ $wo->completed_at?->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">Sin OTs correctivos completados.</p>
                    @endforelse
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm py-16 text-center">
                <i data-lucide="bar-chart-2" class="w-12 h-12 mx-auto text-gray-200 mb-3"></i>
                <p class="text-gray-500 font-medium">No hay métricas calculadas para este activo</p>
                <p class="text-sm text-gray-400 mt-1">Haz clic en "Recalcular" para generar el análisis.</p>
            </div>
        @endif

        {{-- WO by month chart --}}
        @if ($woByMonth->isNotEmpty())
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5"
                 x-data="{
                     months: @js($woByMonth->pluck('month')->toArray()),
                     counts: @js($woByMonth->pluck('count')->map(fn($c) => (int) $c)->toArray()),
                 }">
                <h3 class="font-bold text-[#002046] mb-4">Órdenes de Trabajo por Mes (últimos 12 meses)</h3>
                <div class="flex items-end gap-1 h-24">
                    <template x-for="(count, i) in counts" :key="i">
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-full bg-blue-500 rounded-t" :style="'height: ' + (count / Math.max(...counts) * 80) + 'px; min-height: 2px'"></div>
                            <span class="text-[9px] text-gray-400 font-mono rotate-45 origin-bottom-left" x-text="months[i]?.slice(5)"></span>
                        </div>
                    </template>
                </div>
            </div>
        @endif

    </div>

</x-layouts.cmms>
