<x-layouts.cmms :title="$workOrder->code" :headerTitle="$workOrder->code . ' – ' . $workOrder->title">

    @php
        $statusMeta = [
            'draft'       => ['label' => 'Borrador',   'color' => 'bg-gray-100 text-gray-600',    'dot' => 'bg-gray-400'],
            'pending'     => ['label' => 'Pendiente',  'color' => 'bg-yellow-100 text-yellow-700', 'dot' => 'bg-yellow-500'],
            'in_progress' => ['label' => 'En Progreso','color' => 'bg-blue-100 text-blue-700',     'dot' => 'bg-blue-500'],
            'on_hold'     => ['label' => 'En Pausa',   'color' => 'bg-orange-100 text-orange-700', 'dot' => 'bg-orange-500'],
            'completed'   => ['label' => 'Completada', 'color' => 'bg-green-100 text-green-700',   'dot' => 'bg-green-500'],
            'cancelled'   => ['label' => 'Cancelada',  'color' => 'bg-red-100 text-red-600',       'dot' => 'bg-red-500'],
        ];
        $typeLabels = ['corrective' => 'CORRECTIVO', 'preventive' => 'PREVENTIVO', 'predictive' => 'PREDICTIVO'];
        $typeColors = ['corrective' => 'bg-red-100 text-red-700', 'preventive' => 'bg-blue-100 text-blue-700', 'predictive' => 'bg-purple-100 text-purple-700'];
        $priorityMeta = [
            'low'      => ['label' => 'BAJA',           'color' => 'text-gray-500'],
            'medium'   => ['label' => 'MEDIA',          'color' => 'text-blue-600'],
            'high'     => ['label' => 'ALTA',           'color' => 'text-orange-600'],
            'critical' => ['label' => 'CRÍTICA',        'color' => 'text-red-600'],
        ];
        $transitions = [
            'draft'       => [['status' => 'pending',     'label' => 'Enviar para aprobación'], ['status' => 'cancelled', 'label' => 'Cancelar']],
            'pending'     => [['status' => 'in_progress', 'label' => 'Iniciar trabajo'], ['status' => 'on_hold', 'label' => 'Pausar'], ['status' => 'cancelled', 'label' => 'Cancelar']],
            'in_progress' => [['status' => 'on_hold',     'label' => 'Pausar'], ['status' => 'completed', 'label' => 'Finalizar y cerrar OT'], ['status' => 'cancelled', 'label' => 'Cancelar']],
            'on_hold'     => [['status' => 'in_progress', 'label' => 'Reanudar'], ['status' => 'cancelled', 'label' => 'Cancelar']],
        ];
        $statusVal   = $workOrder->status->value;
        $typeVal     = $workOrder->type->value;
        $priorityVal = $workOrder->priority->value;
        $sm          = $statusMeta[$statusVal] ?? $statusMeta['draft'];
        $pm          = $priorityMeta[$priorityVal] ?? $priorityMeta['medium'];
        $nextSteps   = $transitions[$statusVal] ?? [];

        $totalItems     = $workOrder->checklists->sum(fn ($cl) => $cl->items->count());
        $completedItems = $workOrder->checklists->sum(fn ($cl) => $cl->items->where('is_completed', true)->count());
        $progress       = $totalItems > 0 ? round($completedItems / $totalItems * 100) : 0;

        $activityIcons = [
            'created'                  => 'plus-circle',
            'status_changed'           => 'refresh-cw',
            'assigned'                 => 'user-plus',
            'note_added'               => 'message-circle',
            'checklist_item_completed' => 'check-circle-2',
            'part_added'               => 'package',
        ];
        $activityLabels = [
            'created'                  => 'Orden creada',
            'status_changed'           => 'Estado cambiado',
            'assigned'                 => 'Técnico asignado',
            'note_added'               => 'Nota agregada',
            'checklist_item_completed' => 'Ítem completado',
            'part_added'               => 'Repuesto agregado',
        ];

        function fmtMinutes(int $mins): string {
            $h = intdiv($mins, 60); $m = $mins % 60;
            if ($h === 0) return "{$m}min";
            return $m === 0 ? "{$h}h" : "{$h}h {$m}min";
        }
    @endphp

    <div class="p-6 space-y-5">

        {{-- ── Breadcrumb + actions ────────────────────── --}}
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('work-orders.index') }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                    Órdenes de Trabajo
                </a>
                <span class="text-gray-300">/</span>
                <span class="font-semibold text-[#002046]">{{ $workOrder->code }}</span>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                {{-- Edit button --}}
                @if (!in_array($statusVal, ['completed', 'cancelled']))
                    <a href="{{ route('work-orders.edit', $workOrder) }}"
                       class="flex items-center gap-2 px-4 py-2 text-sm font-semibold border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-gray-600">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                        Editar
                    </a>
                @endif

                {{-- Status transitions --}}
                @foreach ($nextSteps as $step)
                    <form method="POST" action="{{ route('work-orders.status.update', $workOrder) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $step['status'] }}">
                        <button type="submit"
                                class="{{ $step['status'] === 'cancelled' ? 'px-4 py-2 text-sm font-semibold border border-gray-200 text-gray-500 hover:bg-gray-50 rounded-lg transition-colors' : 'px-4 py-2 text-sm font-semibold bg-[#002046] text-white rounded-lg hover:bg-[#1b365d] transition-colors shadow-sm' }}">
                            {{ $step['label'] }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>

        {{-- ── Header card ─────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-[#002046]/5 flex items-center justify-center shrink-0">
                        <i data-lucide="clipboard-list" class="w-6 h-6 text-[#002046]"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold px-1.5 py-0.5 rounded {{ $typeColors[$typeVal] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $typeLabels[$typeVal] ?? $typeVal }}
                            </span>
                            <span class="text-xs font-mono text-gray-400">{{ $workOrder->code }}</span>
                        </div>
                        <h2 class="text-xl font-extrabold text-[#002046] font-headline">{{ $workOrder->title }}</h2>
                        @if ($workOrder->description)
                            <p class="text-sm text-gray-500 mt-1">{{ $workOrder->description }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ $sm['dot'] }}"></span>
                    <span class="text-sm font-bold {{ $sm['color'] }} px-3 py-1.5 rounded-lg">{{ $sm['label'] }}</span>
                </div>
            </div>

            {{-- Meta grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-5 border-t border-gray-100">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Activo</p>
                    @if ($workOrder->asset)
                        <a href="{{ route('assets.show', $workOrder->asset) }}" class="text-sm font-semibold text-[#002046] hover:underline">
                            {{ $workOrder->asset->name }}
                        </a>
                        <p class="text-xs text-gray-400">{{ optional($workOrder->asset->location)->name }}</p>
                    @else
                        <p class="text-sm text-gray-400">—</p>
                    @endif
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Asignado a</p>
                    <p class="text-sm font-semibold text-gray-700">{{ optional($workOrder->assignedTo)->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Prioridad</p>
                    <p class="text-sm font-bold {{ $pm['color'] }}">{{ $pm['label'] }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Fecha Límite</p>
                    @if ($workOrder->due_date)
                        <p class="text-sm font-semibold {{ $workOrder->due_date->isPast() && !in_array($statusVal, ['completed', 'cancelled']) ? 'text-red-600' : 'text-gray-700' }}">
                            {{ $workOrder->due_date->format('d/m/Y') }}
                        </p>
                    @else
                        <p class="text-sm text-gray-400">—</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ── Left: Checklist + Parts ─────────────── --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Checklist --}}
                @if ($workOrder->checklists->isNotEmpty())
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Checklist</h3>
                            <span class="text-xs font-bold text-gray-500">{{ $completedItems }}/{{ $totalItems }}</span>
                        </div>
                        @if ($totalItems > 0)
                            <div class="h-1.5 bg-gray-100 rounded-full mb-4 overflow-hidden">
                                <div class="h-full bg-green-500 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                            </div>
                        @endif

                        @foreach ($workOrder->checklists as $checklist)
                            @if ($workOrder->checklists->count() > 1)
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mt-4 mb-2">{{ $checklist->title }}</p>
                            @endif
                            <div class="space-y-2">
                                @foreach ($checklist->items as $item)
                                    <div class="flex items-center gap-3 p-2.5 rounded-lg {{ $item->is_completed ? 'bg-green-50' : 'bg-gray-50' }}">
                                        @if (!$item->is_completed && $statusVal === 'in_progress')
                                            <form method="POST" action="{{ route('work-orders.complete-item', $workOrder) }}">
                                                @csrf
                                                <input type="hidden" name="checklist_item_id" value="{{ $item->id }}">
                                                <button type="submit"
                                                        class="w-5 h-5 rounded border-2 border-gray-300 hover:border-green-500 transition-colors flex-shrink-0 bg-white">
                                                </button>
                                            </form>
                                        @else
                                            <div class="w-5 h-5 rounded bg-green-500 flex items-center justify-center flex-shrink-0">
                                                <i data-lucide="check" class="w-3.5 h-3.5 text-white"></i>
                                            </div>
                                        @endif
                                        <span class="text-sm {{ $item->is_completed ? 'text-gray-400 line-through' : 'text-gray-700' }}">
                                            {{ $item->description }}
                                        </span>
                                        @if ($item->is_completed && $item->completedBy)
                                            <span class="text-[10px] text-gray-400 ml-auto">{{ $item->completedBy->name }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Failure cause --}}
                @if ($workOrder->failure_cause)
                    <div class="bg-red-50 border border-red-100 rounded-xl p-5">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-red-400 mb-2 flex items-center gap-1.5">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            Causa de Falla
                        </h3>
                        <p class="text-sm text-gray-700">{{ $workOrder->failure_cause }}</p>
                    </div>
                @endif

                {{-- Add note --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Agregar Nota</h3>
                    <form method="POST" action="{{ route('work-orders.notes.store', $workOrder) }}">
                        @csrf
                        <textarea name="notes" rows="3"
                                  placeholder="Escribe una nota o comentario..."
                                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none mb-3"></textarea>
                        @error('notes') <p class="text-xs text-red-500 mb-2">{{ $message }}</p> @enderror
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold bg-[#002046] text-white rounded-lg hover:bg-[#1b365d] transition-colors">
                            Guardar nota
                        </button>
                    </form>
                </div>

            </div>

            {{-- ── Right: Activity + Info ───────────────── --}}
            <div class="space-y-5">

                {{-- Durations --}}
                @if ($workOrder->estimated_duration || $workOrder->actual_duration || $workOrder->started_at)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Tiempos</h3>
                        <div class="space-y-2 text-sm">
                            @if ($workOrder->estimated_duration)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Estimado</span>
                                    <span class="font-semibold">{{ fmtMinutes($workOrder->estimated_duration) }}</span>
                                </div>
                            @endif
                            @if ($workOrder->actual_duration)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Real</span>
                                    <span class="font-semibold">{{ fmtMinutes($workOrder->actual_duration) }}</span>
                                </div>
                            @endif
                            @if ($workOrder->started_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Inicio</span>
                                    <span class="font-semibold">{{ $workOrder->started_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                            @if ($workOrder->completed_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Cierre</span>
                                    <span class="font-semibold">{{ $workOrder->completed_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Activity feed --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Actividad</h3>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse ($workOrder->activities->sortByDesc('created_at') as $activity)
                            @php $actionVal = $activity->action->value; @endphp
                            <div class="flex gap-3">
                                <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center shrink-0 mt-0.5">
                                    <i data-lucide="{{ $activityIcons[$actionVal] ?? 'circle' }}" class="w-3.5 h-3.5 text-gray-500"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-gray-700">{{ $activityLabels[$actionVal] ?? $actionVal }}</p>
                                    @if ($activity->notes)
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $activity->notes }}</p>
                                    @endif
                                    <p class="text-[10px] text-gray-400 mt-0.5">
                                        {{ optional($activity->user)->name }} · {{ $activity->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-2">Sin actividad registrada</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </div>

</x-layouts.cmms>
