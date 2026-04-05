<div x-data="{
    frequency: '{{ old('frequency', $plan?->frequency?->value ?? 'monthly') }}',
    type: '{{ old('type', $plan?->type?->value ?? 'preventive') }}',
    priority: '{{ old('priority', $plan?->priority?->value ?? 'medium') }}',
    isMetricBased() {
        return ['by_hours', 'by_kilometers', 'by_cycles'].includes(this.frequency);
    }
}" class="space-y-6">

    {{-- ── Sección 1: Información General ─────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Información General</h3>

        {{-- Nombre --}}
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                Nombre <span class="text-red-400">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name', $plan?->name) }}"
                   placeholder="Ej: Mantenimiento preventivo compresor L3"
                   class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] {{ $errors->has('name') ? 'border-red-300' : 'border-gray-200' }}">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Activo --}}
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                Activo <span class="text-red-400">*</span>
            </label>
            <select name="asset_id"
                    class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white {{ $errors->has('asset_id') ? 'border-red-300' : 'border-gray-200' }}">
                <option value="">Seleccionar activo...</option>
                @foreach ($assets as $asset)
                    <option value="{{ $asset->id }}" {{ old('asset_id', $plan?->asset_id) == $asset->id ? 'selected' : '' }}>
                        [{{ $asset->code }}] {{ $asset->name }}
                    </option>
                @endforeach
            </select>
            @error('asset_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Tipo --}}
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                Tipo de Mantenimiento <span class="text-red-400">*</span>
            </label>
            <div class="grid grid-cols-3 gap-3">
                @php
                    $types = [
                        ['value' => 'preventive', 'label' => 'Preventivo (PM)',  'icon' => 'calendar-check', 'color' => 'border-blue-200 bg-blue-50 text-blue-700'],
                        ['value' => 'corrective', 'label' => 'Correctivo (CM)',  'icon' => 'wrench',         'color' => 'border-red-200 bg-red-50 text-red-700'],
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

        {{-- Descripción --}}
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Descripción</label>
            <textarea name="description" rows="3"
                      placeholder="Describe el trabajo de mantenimiento..."
                      class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none">{{ old('description', $plan?->description) }}</textarea>
            @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ── Sección 2: Programación ─────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Programación</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Frecuencia --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                    Frecuencia <span class="text-red-400">*</span>
                </label>
                <select name="frequency"
                        x-model="frequency"
                        @change="frequency = $event.target.value"
                        class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white {{ $errors->has('frequency') ? 'border-red-300' : 'border-gray-200' }}">
                    @foreach ($frequencies as $freq)
                        <option value="{{ $freq->value }}" {{ old('frequency', $plan?->frequency?->value) === $freq->value ? 'selected' : '' }}>
                            {{ $freq->label() }}
                        </option>
                    @endforeach
                </select>
                @error('frequency') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Valor de frecuencia (métrico) --}}
            <div x-show="isMetricBased()" x-cloak>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                    Valor de Frecuencia <span class="text-red-400">*</span>
                </label>
                <input type="number" name="frequency_value" value="{{ old('frequency_value', $plan?->frequency_value) }}"
                       placeholder="Ej: 500" min="1"
                       class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
                <p class="text-xs text-gray-400 mt-1">Número de horas, km o ciclos entre ejecuciones</p>
                @error('frequency_value') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            {{-- Fecha de inicio --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                    Fecha de Inicio <span class="text-red-400">*</span>
                </label>
                <input type="date" name="start_date" value="{{ old('start_date', $plan?->start_date?->format('Y-m-d')) }}"
                       class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] {{ $errors->has('start_date') ? 'border-red-300' : 'border-gray-200' }}">
                @error('start_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Próxima ejecución --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Próxima Ejecución</label>
                <input type="date" name="next_execution_date" value="{{ old('next_execution_date', $plan?->next_execution_date?->format('Y-m-d')) }}"
                       class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
                @error('next_execution_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Fecha de fin --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Fecha de Fin</label>
                <input type="date" name="end_date" value="{{ old('end_date', $plan?->end_date?->format('Y-m-d')) }}"
                       class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
                @error('end_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Duración estimada --}}
        <div class="md:w-1/3">
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Duración Estimada (min)</label>
            <input type="number" name="estimated_duration" value="{{ old('estimated_duration', $plan?->estimated_duration) }}"
                   placeholder="Ej: 120" min="1"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            @error('estimated_duration') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ── Sección 3: Asignación ────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Asignación</h3>

        {{-- Técnico asignado --}}
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Técnico Asignado</label>
            <select name="assigned_to"
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white">
                <option value="">Sin asignar</option>
                @foreach ($technicians as $tech)
                    <option value="{{ $tech->id }}" {{ old('assigned_to', $plan?->assigned_to) == $tech->id ? 'selected' : '' }}>
                        {{ $tech->name }}{{ $tech->employee_code ? ' (' . $tech->employee_code . ')' : '' }}
                    </option>
                @endforeach
            </select>
            @error('assigned_to') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Prioridad --}}
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                Prioridad <span class="text-red-400">*</span>
            </label>
            <div class="flex gap-2">
                @php
                    $priorityOpts = [
                        ['value' => 'low',      'label' => 'Baja',    'dot' => 'bg-gray-300'],
                        ['value' => 'medium',   'label' => 'Media',   'dot' => 'bg-blue-400'],
                        ['value' => 'high',     'label' => 'Alta',    'dot' => 'bg-orange-400'],
                        ['value' => 'critical', 'label' => 'Crítica', 'dot' => 'bg-red-500'],
                    ];
                @endphp
                @foreach ($priorityOpts as $opt)
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

        {{-- Activo checkbox --}}
        <div class="flex items-center gap-3">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" id="is_active" name="is_active" value="1"
                   {{ old('is_active', $plan?->is_active ?? true) ? 'checked' : '' }}
                   class="w-4 h-4 rounded border-gray-300 text-[#002046] focus:ring-[#002046]/20">
            <label for="is_active" class="text-sm font-semibold text-gray-700">Plan activo</label>
        </div>
    </div>

</div>
