<x-layouts.cmms title="{{ $sr->code }}" headerTitle="Facilities / Solicitudes de Servicio">

    <div class="p-6 max-w-2xl mx-auto space-y-5">

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('service-requests.index') }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono text-gray-400">{{ $sr->code }}</span>
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold border {{ $sr->status->color() }}">{{ $sr->status->label() }}</span>
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold border {{ $sr->priority->color() }}">{{ $sr->priority->label() }}</span>
                    </div>
                    <h2 class="text-xl font-extrabold text-[#002046] font-headline tracking-tight mt-0.5">{{ $sr->title }}</h2>
                </div>
            </div>
            <a href="{{ route('service-requests.edit', $sr) }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-colors">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                Editar
            </a>
        </div>

        {{-- SLA Status --}}
        @if ($sr->sla_deadline)
            @if (in_array($sr->status->value, ['resolved', 'closed']))
                <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600 shrink-0"></i>
                    <span class="text-sm font-semibold text-green-700">
                        @if ($sr->sla_met) SLA cumplido @else SLA incumplido @endif
                        — Resuelto {{ $sr->resolved_at?->diffForHumans() }}
                    </span>
                </div>
            @elseif ($sr->isSlaBreached())
                <div class="flex items-center gap-2 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600 shrink-0"></i>
                    <span class="text-sm font-semibold text-red-700">SLA Vencido — Venció {{ $sr->sla_deadline->diffForHumans() }}</span>
                </div>
            @else
                <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600 shrink-0"></i>
                    <span class="text-sm font-semibold text-amber-700">Vence {{ $sr->sla_deadline->diffForHumans() }} — {{ $sr->sla_deadline->format('d/m/Y H:i') }}</span>
                </div>
            @endif
        @endif

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Categoría</p>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $sr->category->color() }}">
                        <i data-lucide="{{ $sr->category->icon() }}" class="w-3.5 h-3.5"></i>
                        {{ $sr->category->label() }}
                    </span>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Solicitado por</p>
                    <p class="text-sm font-semibold text-gray-700">{{ $sr->requestedBy?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Asignado a</p>
                    <p class="text-sm font-semibold text-gray-700">{{ $sr->assignedTo?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Activo</p>
                    <p class="text-sm text-gray-700">
                        @if ($sr->asset)
                            <a href="{{ route('assets.show', $sr->asset) }}" class="text-[#002046] hover:underline font-semibold">{{ $sr->asset->name }}</a>
                        @else
                            —
                        @endif
                    </p>
                </div>
                @if ($sr->location_description)
                    <div class="col-span-2">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Ubicación</p>
                        <p class="text-sm text-gray-700">{{ $sr->location_description }}</p>
                    </div>
                @endif
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Creado</p>
                    <p class="text-sm text-gray-600">{{ $sr->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @if ($sr->resolution_time)
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tiempo de resolución</p>
                        <p class="text-sm font-semibold text-gray-700">{{ round($sr->resolution_time / 60, 1) }} horas</p>
                    </div>
                @endif
            </div>

            @if ($sr->description)
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Descripción</p>
                    <p class="text-sm text-gray-600">{{ $sr->description }}</p>
                </div>
            @endif

            @if ($sr->resolution_notes)
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Notas de resolución</p>
                    <p class="text-sm text-gray-600">{{ $sr->resolution_notes }}</p>
                </div>
            @endif
        </div>
    </div>

</x-layouts.cmms>
