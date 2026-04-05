<x-layouts.cmms :title="$plan->name" :headerTitle="$plan->name">

    @php
        $typeColors = [
            'preventive' => 'bg-blue-50 text-blue-700 border-blue-200',
            'corrective'  => 'bg-red-50 text-red-700 border-red-200',
            'predictive'  => 'bg-purple-50 text-purple-700 border-purple-200',
        ];
        $priorityColors = [
            'low'      => 'text-gray-500',
            'medium'   => 'text-blue-500',
            'high'     => 'text-orange-500',
            'critical' => 'text-red-600',
        ];
        $isOverdue = $plan->is_active
            && $plan->next_execution_date
            && $plan->next_execution_date->isPast();
    @endphp

    <div class="p-6 space-y-5">

        {{-- ── Breadcrumb + actions ────────────────────── --}}
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('maintenance-plans.index') }}" class="text-gray-400 hover:text-[#002046] transition-colors">Planes PM</a>
                <span class="text-gray-300">/</span>
                <span class="font-semibold text-[#002046]">{{ $plan->name }}</span>
            </div>
            <a href="{{ route('maintenance-plans.edit', $plan) }}"
               class="flex items-center gap-2 bg-[#002046] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                Editar
            </a>
        </div>

        {{-- ── Header card ──────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-[#002046]/5 flex items-center justify-center shrink-0">
                        <i data-lucide="calendar-clock" class="w-6 h-6 text-[#002046]"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold text-[#002046] font-headline">{{ $plan->name }}</h2>
                        @if ($plan->asset)
                            <p class="text-sm text-gray-500 mt-0.5">
                                <span class="font-mono text-xs text-gray-400">{{ $plan->asset->code }}</span>
                                — {{ $plan->asset->name }}
                            </p>
                        @endif
                        @if ($plan->description)
                            <p class="text-sm text-gray-400 mt-1">{{ $plan->description }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col items-end gap-2">
                    @if ($plan->is_active)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold border bg-green-50 text-green-700 border-green-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5 animate-pulse"></span>
                            Activo
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold border bg-gray-100 text-gray-600 border-gray-200">
                            Inactivo
                        </span>
                    @endif
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold border {{ $typeColors[$plan->type->value] ?? '' }}">
                        {{ $plan->type->label() }}
                    </span>
                </div>
            </div>

            {{-- Detail grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-5 border-t border-gray-100">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Frecuencia</p>
                    <p class="text-sm font-semibold text-gray-700">{{ $plan->frequency->label() }}</p>
                    @if ($plan->frequency_value)
                        <p class="text-xs text-gray-400">Cada {{ $plan->frequency_value }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Prioridad</p>
                    <p class="text-sm font-semibold {{ $priorityColors[$plan->priority->value] ?? 'text-gray-700' }}">
                        {{ $plan->priority->label() }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Duración Estimada</p>
                    <p class="text-sm font-semibold text-gray-700">
                        {{ $plan->estimated_duration ? $plan->estimated_duration . ' min' : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Técnico Asignado</p>
                    <p class="text-sm font-semibold text-gray-700">
                        {{ $plan->assignedTo?->name ?? '—' }}
                    </p>
                    @if ($plan->assignedTo?->employee_code)
                        <p class="text-xs text-gray-400 font-mono">{{ $plan->assignedTo->employee_code }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Dates card ────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Fechas</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Fecha Inicio</p>
                    <p class="text-sm font-semibold text-gray-700">
                        {{ $plan->start_date?->format('d/m/Y') ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Próxima Ejecución</p>
                    <p class="text-sm font-semibold {{ $isOverdue ? 'text-red-600' : 'text-gray-700' }}">
                        {{ $plan->next_execution_date?->format('d/m/Y') ?? '—' }}
                    </p>
                    @if ($isOverdue)
                        <p class="text-xs text-red-500 font-medium">Vencido</p>
                    @endif
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Última Ejecución</p>
                    <p class="text-sm font-semibold text-gray-700">
                        {{ $plan->last_execution_date?->format('d/m/Y') ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Fecha Fin</p>
                    <p class="text-sm font-semibold text-gray-700">
                        {{ $plan->end_date?->format('d/m/Y') ?? '—' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Asset location card (if available) ────────── --}}
        @if ($plan->asset?->location)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Ubicación del Activo</h3>
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <i data-lucide="map-pin" class="w-4 h-4 text-gray-400"></i>
                    {{ $plan->asset->location->name }}
                </div>
            </div>
        @endif

    </div>

</x-layouts.cmms>
