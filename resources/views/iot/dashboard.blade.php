<x-layouts.cmms title="IoT Dashboard" headerTitle="IoT & Sensores">

    <div class="p-6 space-y-6">

        {{-- KPIs --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                <p class="text-3xl font-extrabold text-green-600 font-headline">{{ $activeSensors }}</p>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-1">Sensores Activos</p>
            </div>
            <div class="bg-white rounded-xl border border-red-100 shadow-sm px-5 py-4">
                <p class="text-3xl font-extrabold text-red-600 font-headline">{{ $alertingSensors }}</p>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-1">Con Alertas</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                <p class="text-3xl font-extrabold text-orange-600 font-headline">{{ $disconnectedSensors }}</p>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-1">Desconectados</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                <p class="text-3xl font-extrabold text-red-700 font-headline">{{ $faultSensors }}</p>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mt-1">En Falla</p>
            </div>
        </div>

        {{-- Alerts + Sensors grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Active Alerts --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-[#002046]">Alertas Activas</h3>
                    <a href="{{ route('iot.alerts.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Ver todas</a>
                </div>
                @forelse ($recentAlerts as $alert)
                    <div class="flex items-start gap-3 px-5 py-3 border-b border-gray-50 hover:bg-gray-50/50">
                        <div class="w-2 h-2 mt-1.5 rounded-full shrink-0 {{ $alert->severity === \App\Enums\AlertSeverity::Critical ? 'bg-red-500' : 'bg-orange-400' }}"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-800">{{ $alert->sensor?->name ?? 'Sensor' }}</p>
                            <p class="text-[11px] text-gray-500 truncate">{{ $alert->message }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $alert->triggered_at->diffForHumans() }}</p>
                        </div>
                        <span class="shrink-0 px-1.5 py-0.5 rounded text-[9px] font-bold {{ $alert->severity->color() }}">
                            {{ $alert->severity->label() }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center">
                        <i data-lucide="check-circle" class="w-8 h-8 mx-auto text-green-300 mb-2"></i>
                        <p class="text-sm text-gray-400">Sin alertas activas</p>
                    </div>
                @endforelse
            </div>

            {{-- Sensor Status Grid --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-[#002046]">Estado de Sensores</h3>
                    <a href="{{ route('iot.sensors.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Ver todos</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($sensors as $sensor)
                        <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50/50">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-[#002046] truncate">{{ $sensor->name }}</p>
                                <p class="text-[10px] text-gray-400">{{ $sensor->asset?->name ?? '—' }}</p>
                            </div>
                            <div class="text-right ml-3">
                                @if ($sensor->last_reading_value !== null)
                                    <p class="text-sm font-bold text-gray-700">{{ number_format((float) $sensor->last_reading_value, 2) }} {{ $sensor->unit }}</p>
                                @else
                                    <p class="text-xs text-gray-300">Sin lectura</p>
                                @endif
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold border {{ $sensor->status->color() }}">
                                    {{ $sensor->status->label() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center">
                            <p class="text-sm text-gray-400">No hay sensores configurados.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</x-layouts.cmms>
