<x-layouts.cmms title="{{ $sensor->name }}" headerTitle="IoT — Sensor">

    <div class="p-6 space-y-5">

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('iot.sensors.index') }}" class="text-gray-400 hover:text-[#002046]">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-extrabold text-[#002046] font-headline">{{ $sensor->name }}</h2>
                    <p class="text-sm text-gray-400 font-mono">{{ $sensor->code }} • {{ $sensor->asset?->name ?? '—' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $sensor->status->color() }}">
                    {{ $sensor->status->label() }}
                </span>
                <a href="{{ route('iot.sensors.edit', $sensor) }}"
                   class="flex items-center gap-2 bg-[#002046] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#1b365d] transition-colors">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                    Editar
                </a>
            </div>
        </div>

        {{-- Active Alerts --}}
        @if ($activeAlerts->isNotEmpty())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-3">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600"></i>
                    <span class="text-sm font-bold text-red-700">{{ $activeAlerts->count() }} alerta(s) activa(s)</span>
                </div>
                @foreach ($activeAlerts as $alert)
                    <div class="flex items-center justify-between py-2 border-b border-red-100 last:border-0">
                        <div>
                            <p class="text-xs font-semibold text-red-800">{{ $alert->message }}</p>
                            <p class="text-[10px] text-red-500">{{ $alert->triggered_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex gap-2">
                            @if (! $alert->acknowledged_at)
                                <form method="POST" action="{{ route('iot.alerts.acknowledge', $alert) }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold text-orange-600 border border-orange-300 px-2 py-1 rounded hover:bg-orange-50">
                                        Reconocer
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('iot.alerts.resolve', $alert) }}">
                                @csrf
                                <button type="submit" class="text-xs font-semibold text-green-600 border border-green-300 px-2 py-1 rounded hover:bg-green-50">
                                    Resolver
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Current reading + manual input --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Última Lectura</h3>
                @if ($sensor->last_reading_value !== null)
                    <p class="text-4xl font-extrabold text-[#002046] font-headline">
                        {{ number_format((float) $sensor->last_reading_value, 2) }}
                    </p>
                    <p class="text-lg text-gray-500 font-medium">{{ $sensor->unit }}</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $sensor->last_reading_at?->diffForHumans() }}</p>
                @else
                    <p class="text-gray-400 text-sm">Sin lecturas</p>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Umbrales</h3>
                <div class="space-y-1 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Máx. crítico:</span>
                        <span class="font-semibold">{{ $sensor->max_threshold !== null ? number_format((float) $sensor->max_threshold, 2) . ' ' . $sensor->unit : '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Mín. crítico:</span>
                        <span class="font-semibold">{{ $sensor->min_threshold !== null ? number_format((float) $sensor->min_threshold, 2) . ' ' . $sensor->unit : '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Adv. alto:</span>
                        <span class="font-semibold">{{ $sensor->warning_threshold_high !== null ? number_format((float) $sensor->warning_threshold_high, 2) . ' ' . $sensor->unit : '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Adv. bajo:</span>
                        <span class="font-semibold">{{ $sensor->warning_threshold_low !== null ? number_format((float) $sensor->warning_threshold_low, 2) . ' ' . $sensor->unit : '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Ingresar Lectura Manual</h3>
                <form method="POST" action="{{ route('iot.sensors.readings.store', $sensor) }}" class="space-y-3">
                    @csrf
                    <div class="flex gap-2">
                        <input type="number" name="value" step="0.0001" required placeholder="Valor..."
                               class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                        <span class="flex items-center text-xs font-semibold text-gray-500 px-2">{{ $sensor->unit }}</span>
                    </div>
                    <button type="submit" class="w-full bg-[#002046] text-white py-2 rounded-lg text-xs font-bold hover:bg-[#1b365d] transition-colors">
                        Registrar Lectura
                    </button>
                </form>
            </div>

        </div>

        {{-- Readings Chart --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5"
             x-data="{
                readings: @js($recentReadings->map(fn($r) => ['value' => (float) $r->value, 'time' => $r->read_at->format('H:i d/m')])->reverse()->values()),
                get chartPoints() {
                    if (this.readings.length === 0) return '';
                    const vals = this.readings.map(r => r.value);
                    const min = Math.min(...vals) * 0.95;
                    const max = Math.max(...vals) * 1.05 || 1;
                    const range = max - min || 1;
                    const w = 800, h = 120;
                    return this.readings.map((r, i) => {
                        const x = (i / (this.readings.length - 1 || 1)) * w;
                        const y = h - ((r.value - min) / range) * h;
                        return x + ',' + y;
                    }).join(' ');
                }
             }">
            <h3 class="font-bold text-[#002046] mb-4">Histórico de Lecturas (últimas 50)</h3>
            <template x-if="readings.length > 1">
                <div class="overflow-x-auto">
                    <svg viewBox="0 0 800 120" class="w-full h-24 text-blue-500" preserveAspectRatio="none">
                        <polyline :points="chartPoints" fill="none" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
            </template>
            <template x-if="readings.length <= 1">
                <p class="text-sm text-gray-400 text-center py-4">Se necesitan al menos 2 lecturas para mostrar la gráfica.</p>
            </template>
        </div>

    </div>

</x-layouts.cmms>
