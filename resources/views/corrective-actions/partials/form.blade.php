{{-- Main form fields --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400">Información General</h3>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Título <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', optional($correctiveAction)->title) }}"
               placeholder="Describe la acción a tomar..."
               class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('title') border-red-400 @enderror">
        @error('title')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Type button selector --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipo <span class="text-red-500">*</span></label>
        <div class="flex gap-3">
            <button type="button" @click="type = 'corrective'"
                    :class="type === 'corrective' ? 'bg-[#002046] text-white border-[#002046]' : 'bg-white text-gray-600 border-gray-200 hover:border-[#002046]/40'"
                    class="px-5 py-2 text-sm font-semibold rounded-lg border transition-colors">
                Correctiva (CA)
            </button>
            <button type="button" @click="type = 'preventive'"
                    :class="type === 'preventive' ? 'bg-[#002046] text-white border-[#002046]' : 'bg-white text-gray-600 border-gray-200 hover:border-[#002046]/40'"
                    class="px-5 py-2 text-sm font-semibold rounded-lg border transition-colors">
                Preventiva (PA)
            </button>
        </div>
        <input type="hidden" name="type" :value="type">
        @error('type')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Priority button selector --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Prioridad <span class="text-red-500">*</span></label>
        <div class="flex flex-wrap gap-2">
            @foreach (['low' => 'Baja', 'medium' => 'Media', 'high' => 'Alta', 'critical' => 'Crítica'] as $val => $lbl)
                @php
                    $activeClass = match($val) {
                        'low'      => 'bg-gray-600 text-white border-gray-600',
                        'medium'   => 'bg-blue-600 text-white border-blue-600',
                        'high'     => 'bg-orange-500 text-white border-orange-500',
                        'critical' => 'bg-red-600 text-white border-red-600',
                    };
                @endphp
                <button type="button" @click="priority = '{{ $val }}'"
                        :class="priority === '{{ $val }}' ? '{{ $activeClass }}' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400'"
                        class="px-4 py-2 text-sm font-semibold rounded-lg border transition-colors">
                    {{ $lbl }}
                </button>
            @endforeach
        </div>
        <input type="hidden" name="priority" :value="priority">
        @error('priority')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    @if ($correctiveAction)
        {{-- Status (edit only) --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Estado</label>
            <select name="status"
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
                @foreach (['open' => 'Abierta', 'in_progress' => 'En Progreso', 'completed' => 'Completada', 'verified' => 'Verificada', 'cancelled' => 'Cancelada'] as $val => $lbl)
                    <option value="{{ $val }}" {{ old('status', $correctiveAction->status->value) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Asignar a</label>
            <select name="assigned_to"
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
                <option value="">Sin asignar</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}"
                            {{ old('assigned_to', optional($correctiveAction)->assigned_to) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Fecha Límite</label>
            <input type="date" name="due_date"
                   value="{{ old('due_date', optional($correctiveAction)->due_date?->format('Y-m-d')) }}"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Descripción <span class="text-red-500">*</span></label>
        <textarea name="description" rows="4"
                  placeholder="Describe detalladamente la acción a implementar..."
                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none @error('description') border-red-400 @enderror">{{ old('description', optional($correctiveAction)->description) }}</textarea>
        @error('description')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- Root cause & action taken --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400">Análisis</h3>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Causa Raíz</label>
        <textarea name="root_cause" rows="3"
                  placeholder="Describe la causa raíz identificada..."
                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none">{{ old('root_cause', optional($correctiveAction)->root_cause) }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Acción Tomada</label>
        <textarea name="action_taken" rows="3"
                  placeholder="Documenta la acción que se tomó o se planea tomar..."
                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none">{{ old('action_taken', optional($correctiveAction)->action_taken) }}</textarea>
    </div>
</div>
