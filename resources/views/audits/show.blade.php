<x-layouts.cmms :title="$audit->code" :headerTitle="$audit->code . ' – ' . $audit->title">

    @php
        $statusMeta = [
            'planned'     => ['label' => 'Planificada',  'color' => 'bg-yellow-100 text-yellow-700',  'dot' => 'bg-yellow-500'],
            'in_progress' => ['label' => 'En Progreso',  'color' => 'bg-blue-100 text-blue-700',      'dot' => 'bg-blue-500'],
            'completed'   => ['label' => 'Completada',   'color' => 'bg-green-100 text-green-700',    'dot' => 'bg-green-500'],
            'cancelled'   => ['label' => 'Cancelada',    'color' => 'bg-red-100 text-red-600',        'dot' => 'bg-red-500'],
        ];
        $typeLabels = ['internal' => 'Interna', 'external' => 'Externa', 'regulatory' => 'Regulatoria', 'supplier' => 'Proveedores'];
        $severityMeta = [
            'minor'       => ['label' => 'Menor',        'color' => 'bg-yellow-50 text-yellow-700 border-yellow-200'],
            'major'       => ['label' => 'Mayor',        'color' => 'bg-orange-50 text-orange-700 border-orange-200'],
            'critical'    => ['label' => 'Crítico',      'color' => 'bg-red-50 text-red-700 border-red-200'],
            'observation' => ['label' => 'Observación',  'color' => 'bg-blue-50 text-blue-700 border-blue-200'],
        ];
        $findingStatusLabels = ['open' => 'Abierto', 'in_progress' => 'En Progreso', 'closed' => 'Cerrado', 'accepted_risk' => 'Riesgo Aceptado'];
        $findingStatusColors = [
            'open'          => 'bg-yellow-50 text-yellow-700',
            'in_progress'   => 'bg-blue-50 text-blue-700',
            'closed'        => 'bg-green-50 text-green-700',
            'accepted_risk' => 'bg-gray-100 text-gray-600',
        ];
        $sm = $statusMeta[$audit->status->value] ?? $statusMeta['planned'];
    @endphp

    <div class="p-6 space-y-5">

        {{-- ── Top bar ──────────────────────────────────── --}}
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('audits.index') }}"
                   class="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-extrabold text-[#002046] font-headline">{{ $audit->title }}</h2>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $sm['color'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $sm['dot'] }}"></span>
                            {{ $sm['label'] }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-400 font-mono mt-0.5">{{ $audit->code }}</p>
                </div>
            </div>
            <a href="{{ route('audits.edit', $audit) }}"
               class="flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:border-[#002046]/40 transition-colors shadow-sm">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                Editar
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ── Audit details ──────────────────────────── --}}
            <div class="lg:col-span-2 space-y-5">

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Detalles</h3>
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div>
                            <dt class="text-gray-400 font-medium">Tipo</dt>
                            <dd class="font-semibold text-gray-800 mt-0.5">{{ $typeLabels[$audit->type->value] ?? $audit->type->value }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 font-medium">Fecha Programada</dt>
                            <dd class="font-semibold text-gray-800 mt-0.5">{{ $audit->scheduled_date->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 font-medium">Auditor</dt>
                            <dd class="font-semibold text-gray-800 mt-0.5">{{ optional($audit->auditor)->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 font-medium">Fecha Completada</dt>
                            <dd class="font-semibold text-gray-800 mt-0.5">{{ $audit->completed_date ? $audit->completed_date->format('d/m/Y') : '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 font-medium">Ubicación</dt>
                            <dd class="font-semibold text-gray-800 mt-0.5">{{ $audit->location ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 font-medium">Hallazgos</dt>
                            <dd class="font-semibold text-gray-800 mt-0.5">{{ $audit->findings_count }}</dd>
                        </div>
                    </dl>

                    @if ($audit->scope)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Alcance</p>
                            <p class="text-sm text-gray-600">{{ $audit->scope }}</p>
                        </div>
                    @endif

                    @if ($audit->description)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Descripción</p>
                            <p class="text-sm text-gray-600">{{ $audit->description }}</p>
                        </div>
                    @endif
                </div>

                {{-- ── Findings ─────────────────────────── --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-[#002046]">Hallazgos <span class="ml-1.5 text-gray-400 font-normal">({{ $audit->findings->count() }})</span></h3>
                    </div>

                    @if ($audit->findings->isEmpty())
                        <div class="flex flex-col items-center py-10 text-center">
                            <i data-lucide="search-x" class="w-10 h-10 text-gray-200 mb-2"></i>
                            <p class="text-gray-400 text-sm">No hay hallazgos registrados aún</p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-50">
                            @foreach ($audit->findings as $finding)
                                @php
                                    $sev = $severityMeta[$finding->severity->value] ?? $severityMeta['minor'];
                                    $fsc = $findingStatusColors[$finding->status] ?? 'bg-gray-50 text-gray-600';
                                    $fsl = $findingStatusLabels[$finding->status] ?? $finding->status;
                                @endphp
                                <div class="px-6 py-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-mono text-xs text-gray-400">{{ $finding->code }}</span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border {{ $sev['color'] }}">{{ $sev['label'] }}</span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $fsc }}">{{ $fsl }}</span>
                                            </div>
                                            <p class="text-sm text-gray-800 font-medium mt-1">{{ $finding->description }}</p>
                                            @if ($finding->assignedTo)
                                                <p class="text-xs text-gray-400 mt-1">Asignado a: {{ $finding->assignedTo->name }}</p>
                                            @endif
                                            @if ($finding->due_date)
                                                <p class="text-xs text-gray-400 mt-0.5">Vence: {{ $finding->due_date->format('d/m/Y') }}</p>
                                            @endif
                                        </div>
                                        <div class="shrink-0">
                                            @if ($finding->correctiveActions->count() > 0)
                                                <span class="text-xs text-teal-600 font-semibold bg-teal-50 px-2 py-0.5 rounded">
                                                    {{ $finding->correctiveActions->count() }} CAPA
                                                </span>
                                            @else
                                                <a href="{{ route('corrective-actions.create', ['finding_id' => $finding->id]) }}"
                                                   class="text-xs text-[#002046] font-semibold hover:underline">
                                                    + CAPA
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($finding->notes)
                                        <p class="text-xs text-gray-500 mt-2 italic">{{ $finding->notes }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Add finding form --}}
                    <div class="border-t border-gray-100 px-6 py-4"
                         x-data="{ open: false }">
                        <button type="button" @click="open = !open"
                                class="flex items-center gap-2 text-sm font-semibold text-[#002046] hover:text-[#1b365d]">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            Agregar Hallazgo
                        </button>

                        <div x-show="open" x-cloak class="mt-4">
                            <form method="POST" action="{{ route('audits.findings.store', $audit) }}" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Descripción <span class="text-red-500">*</span></label>
                                    <input type="text" name="description"
                                           placeholder="Describe el hallazgo..."
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
                                    @error('description')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Severidad <span class="text-red-500">*</span></label>
                                        <select name="severity"
                                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
                                            <option value="observation">Observación</option>
                                            <option value="minor">Menor</option>
                                            <option value="major">Mayor</option>
                                            <option value="critical">Crítico</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Asignar a</label>
                                        <select name="assigned_to"
                                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
                                            <option value="">Sin asignar</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha Límite</label>
                                        <input type="date" name="due_date"
                                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Notas</label>
                                    <textarea name="notes" rows="2"
                                              class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none"></textarea>
                                </div>

                                <div class="flex items-center gap-3">
                                    <button type="submit"
                                            class="px-5 py-2 text-sm font-bold bg-[#002046] text-white rounded-lg hover:bg-[#1b365d] transition-colors">
                                        Guardar Hallazgo
                                    </button>
                                    <button type="button" @click="open = false"
                                            class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Sidebar info ───────────────────────────── --}}
            <div class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Resumen</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Total hallazgos</span>
                            <span class="font-bold text-[#002046]">{{ $audit->findings->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Abiertos</span>
                            <span class="font-bold text-yellow-600">{{ $audit->findings->where('status', 'open')->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Cerrados</span>
                            <span class="font-bold text-green-600">{{ $audit->findings->where('status', 'closed')->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Críticos</span>
                            <span class="font-bold text-red-600">{{ $audit->findings->filter(fn($f) => $f->severity->value === 'critical')->count() }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Creado por</h3>
                    <p class="text-sm font-semibold text-gray-700">{{ optional($audit->createdBy)->name ?? '—' }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $audit->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

        </div>

    </div>

</x-layouts.cmms>
