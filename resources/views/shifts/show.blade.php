<x-layouts.cmms title="Turno #{{ $shift->id }}" headerTitle="Turnos de Técnicos">

    <div class="p-6 max-w-2xl mx-auto space-y-5">

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('shifts.index') }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">{{ $shift->name }}</h2>
                    <p class="text-sm text-gray-400">{{ $shift->date->format('d/m/Y') }}</p>
                </div>
            </div>
            <a href="{{ route('shifts.edit', $shift) }}"
               class="flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-colors">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                Editar
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Técnico</p>
                    <p class="font-semibold text-[#002046]">{{ $shift->technician?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Estado</p>
                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold border {{ $shift->status->color() }}">
                        {{ $shift->status->label() }}
                    </span>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tipo</p>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $shift->type->color() }}">
                        <i data-lucide="{{ $shift->type->icon() }}" class="w-3.5 h-3.5"></i>
                        {{ $shift->type->label() }}
                    </span>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Horario</p>
                    <p class="font-mono font-semibold text-gray-700">{{ $shift->start_time }} – {{ $shift->end_time }}</p>
                </div>
            </div>

            @if ($shift->notes)
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Notas</p>
                    <p class="text-sm text-gray-600">{{ $shift->notes }}</p>
                </div>
            @endif
        </div>

    </div>

</x-layouts.cmms>
