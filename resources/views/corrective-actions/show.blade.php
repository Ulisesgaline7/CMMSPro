<x-layouts.cmms :title="$correctiveAction->code" :headerTitle="$correctiveAction->code . ' – ' . $correctiveAction->title">

    @php
        $statusMeta = [
            'open'        => ['label' => 'Abierta',      'color' => 'bg-yellow-100 text-yellow-700',  'dot' => 'bg-yellow-500'],
            'in_progress' => ['label' => 'En Progreso',  'color' => 'bg-blue-100 text-blue-700',      'dot' => 'bg-blue-500'],
            'completed'   => ['label' => 'Completada',   'color' => 'bg-teal-100 text-teal-700',      'dot' => 'bg-teal-500'],
            'verified'    => ['label' => 'Verificada',   'color' => 'bg-green-100 text-green-700',    'dot' => 'bg-green-500'],
            'cancelled'   => ['label' => 'Cancelada',    'color' => 'bg-red-100 text-red-600',        'dot' => 'bg-red-500'],
        ];
        $typeLabels = ['corrective' => 'Correctiva (CA)', 'preventive' => 'Preventiva (PA)'];
        $typeColors = ['corrective' => 'bg-red-100 text-red-700', 'preventive' => 'bg-blue-100 text-blue-700'];
        $priorityMeta = [
            'low'      => ['label' => 'Baja',     'color' => 'text-gray-500'],
            'medium'   => ['label' => 'Media',    'color' => 'text-blue-600'],
            'high'     => ['label' => 'Alta',     'color' => 'text-orange-600'],
            'critical' => ['label' => 'Crítica',  'color' => 'text-red-600'],
        ];
        $sm = $statusMeta[$correctiveAction->status->value] ?? $statusMeta['open'];
        $pm = $priorityMeta[$correctiveAction->priority] ?? $priorityMeta['medium'];
    @endphp

    <div class="p-6 space-y-5">

        {{-- ── Top bar ──────────────────────────────────── --}}
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('corrective-actions.index') }}"
                   class="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="text-2xl font-extrabold text-[#002046] font-headline">{{ $correctiveAction->title }}</h2>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $sm['color'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $sm['dot'] }}"></span>
                            {{ $sm['label'] }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-400 font-mono mt-0.5">{{ $correctiveAction->code }}</p>
                </div>
            </div>
            <a href="{{ route('corrective-actions.edit', $correctiveAction) }}"
               class="flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:border-[#002046]/40 transition-colors shadow-sm shrink-0">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                Editar
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ── Details ────────────────────────────────── --}}
            <div class="lg:col-span-2 space-y-5">

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Detalles</h3>
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div>
                            <dt class="text-gray-400 font-medium">Tipo</dt>
                            <dd class="mt-0.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $typeColors[$correctiveAction->type] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $typeLabels[$correctiveAction->type] ?? $correctiveAction->type }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 font-medium">Prioridad</dt>
                            <dd class="font-semibold {{ $pm['color'] }} mt-0.5">{{ $pm['label'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 font-medium">Asignado a</dt>
                            <dd class="font-semibold text-gray-800 mt-0.5">{{ optional($correctiveAction->assignedTo)->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 font-medium">Fecha Límite</dt>
                            <dd class="font-semibold text-gray-800 mt-0.5">
                                @if ($correctiveAction->due_date)
                                    <span class="{{ $correctiveAction->due_date->isPast() && !in_array($correctiveAction->status->value, ['completed', 'verified', 'cancelled']) ? 'text-red-600' : '' }}">
                                        {{ $correctiveAction->due_date->format('d/m/Y') }}
                                    </span>
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                        @if ($correctiveAction->completed_at)
                            <div>
                                <dt class="text-gray-400 font-medium">Completada</dt>
                                <dd class="font-semibold text-gray-800 mt-0.5">{{ $correctiveAction->completed_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        @endif
                        @if ($correctiveAction->verified_at)
                            <div>
                                <dt class="text-gray-400 font-medium">Verificada</dt>
                                <dd class="font-semibold text-gray-800 mt-0.5">{{ $correctiveAction->verified_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        @endif
                    </dl>

                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Descripción</p>
                        <p class="text-sm text-gray-700">{{ $correctiveAction->description }}</p>
                    </div>

                    @if ($correctiveAction->root_cause)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Causa Raíz</p>
                            <p class="text-sm text-gray-700">{{ $correctiveAction->root_cause }}</p>
                        </div>
                    @endif

                    @if ($correctiveAction->action_taken)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Acción Tomada</p>
                            <p class="text-sm text-gray-700">{{ $correctiveAction->action_taken }}</p>
                        </div>
                    @endif
                </div>

                {{-- Linked finding --}}
                @if ($correctiveAction->finding)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Hallazgo Vinculado</h3>
                        <div class="text-sm">
                            <p class="font-semibold text-[#002046]">{{ $correctiveAction->finding->description }}</p>
                            <p class="text-gray-400 font-mono text-xs mt-1">{{ $correctiveAction->finding->code }}</p>
                            @if ($correctiveAction->finding->audit)
                                <a href="{{ route('audits.show', $correctiveAction->finding->audit_id) }}"
                                   class="inline-flex items-center gap-1 text-xs text-[#002046] font-semibold mt-2 hover:underline">
                                    <i data-lucide="clipboard-check" class="w-3.5 h-3.5"></i>
                                    Ver auditoría: {{ $correctiveAction->finding->audit->code }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Linked work order --}}
                @if ($correctiveAction->workOrder)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Orden de Trabajo Vinculada</h3>
                        <div class="text-sm">
                            <p class="font-semibold text-[#002046]">{{ $correctiveAction->workOrder->title }}</p>
                            <a href="{{ route('work-orders.show', $correctiveAction->workOrder) }}"
                               class="inline-flex items-center gap-1 text-xs text-[#002046] font-semibold mt-1 hover:underline">
                                <i data-lucide="wrench" class="w-3.5 h-3.5"></i>
                                {{ $correctiveAction->workOrder->code }}
                            </a>
                        </div>
                    </div>
                @endif

            </div>

            {{-- ── Sidebar ─────────────────────────────────── --}}
            <div class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Información</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-400 font-medium text-xs">Creado por</p>
                            <p class="font-semibold text-gray-700 mt-0.5">{{ optional($correctiveAction->createdBy)->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 font-medium text-xs">Fecha de creación</p>
                            <p class="font-semibold text-gray-700 mt-0.5">{{ $correctiveAction->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</x-layouts.cmms>
