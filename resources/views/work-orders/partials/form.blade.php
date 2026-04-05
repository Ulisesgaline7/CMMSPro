{{-- ── Tipo de orden ─────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Tipo de Orden</h3>
    <div class="grid grid-cols-3 gap-3">
        @php
            $types = [
                ['value' => 'corrective', 'label' => 'Correctivo (CM)',  'icon' => 'wrench',         'color' => 'border-red-200 bg-red-50 text-red-700'],
                ['value' => 'preventive', 'label' => 'Preventivo (PM)',  'icon' => 'calendar-check', 'color' => 'border-blue-200 bg-blue-50 text-blue-700'],
                ['value' => 'predictive', 'label' => 'Predictivo (PdM)', 'icon' => 'bar-chart-2',    'color' => 'border-purple-200 bg-purple-50 text-purple-700'],
            ];
        @endphp
        @foreach ($types as $opt)
            <button type="button"
                    @click="type = '{{ $opt['value'] }}'"
                    :class="type === '{{ $opt['value'] }}'
                        ? '{{ $opt['color'] }} border-current flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all'
                        : 'border-gray-100 text-gray-400 hover:border-gray-200 flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all'">
                <i data-lucide="{{ $opt['icon'] }}" class="w-6 h-6"></i>
                <span class="text-xs font-bold">{{ $opt['label'] }}</span>
            </button>
        @endforeach
    </div>
    <input type="hidden" name="type" :value="type">
    @error('type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

{{-- ── Información general ───────────────────── --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Información General</h3>

    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
            Título <span class="text-red-400">*</span>
        </label>
        <input type="text" name="title" value="{{ old('title', $workOrder?->title) }}"
               placeholder="Ej: Cambio de aceite compresor línea 3"
               class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] {{ $errors->has('title') ? 'border-red-300' : 'border-gray-200' }}">
        @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Descripción</label>
        <textarea name="description" rows="3"
                  placeholder="Describe el trabajo a realizar..."
                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none">{{ old('description', $workOrder?->description) }}</textarea>
        @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div x-show="type === 'corrective'">
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Causa de Falla</label>
        <textarea name="failure_cause" rows="2"
                  placeholder="Describe la causa o síntoma de la falla..."
                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none">{{ old('failure_cause', $workOrder?->failure_cause) }}</textarea>
        @error('failure_cause') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- ── Activo y asignación ───────────────────── --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Activo y Asignación</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Activo</label>
            <select name="asset_id"
                    class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white {{ $errors->has('asset_id') ? 'border-red-300' : 'border-gray-200' }}">
                <option value="">Seleccionar activo...</option>
                @foreach ($assets as $asset)
                    <option value="{{ $asset->id }}" {{ old('asset_id', $workOrder?->asset_id) == $asset->id ? 'selected' : '' }}>
                        [{{ $asset->code }}] {{ $asset->name }}{{ $asset->location ? ' — ' . $asset->location->name : '' }}
                    </option>
                @endforeach
            </select>
            @error('asset_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Asignar a</label>
            <select name="assigned_to"
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white">
                <option value="">Sin asignar</option>
                @foreach ($technicians as $tech)
                    <option value="{{ $tech->id }}" {{ old('assigned_to', $workOrder?->assigned_to) == $tech->id ? 'selected' : '' }}>
                        {{ $tech->name }}{{ $tech->employee_code ? ' (' . $tech->employee_code . ')' : '' }}
                    </option>
                @endforeach
            </select>
            @error('assigned_to') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- ── Prioridad y planificación ─────────────── --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Prioridad y Planificación</h3>

    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
            Prioridad <span class="text-red-400">*</span>
        </label>
        <div class="flex gap-2">
            @php
                $priorities = [
                    ['value' => 'low',      'label' => 'Baja',    'dot' => 'bg-gray-300'],
                    ['value' => 'medium',   'label' => 'Media',   'dot' => 'bg-blue-400'],
                    ['value' => 'high',     'label' => 'Alta',    'dot' => 'bg-orange-400'],
                    ['value' => 'critical', 'label' => 'Crítica', 'dot' => 'bg-red-500'],
                ];
            @endphp
            @foreach ($priorities as $opt)
                <button type="button"
                        @click="priority = '{{ $opt['value'] }}'"
                        :class="priority === '{{ $opt['value'] }}'
                            ? 'flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg border-2 text-xs font-bold transition-all border-[#002046] bg-[#002046] text-white'
                            : 'flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg border-2 text-xs font-bold transition-all border-gray-100 text-gray-500 hover:border-gray-200'">
                    <span class="w-2 h-2 rounded-full {{ $opt['dot'] }}"></span>
                    {{ $opt['label'] }}
                </button>
            @endforeach
        </div>
        <input type="hidden" name="priority" :value="priority">
        @error('priority') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Fecha Límite</label>
            <input type="date" name="due_date" value="{{ old('due_date', $workOrder?->due_date?->format('Y-m-d')) }}"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            @error('due_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Duración Estimada (minutos)</label>
            <input type="number" name="estimated_duration" value="{{ old('estimated_duration', $workOrder?->estimated_duration) }}"
                   placeholder="Ej: 120" min="1"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            @error('estimated_duration') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
