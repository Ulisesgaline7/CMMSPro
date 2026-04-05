{{-- Title --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400">Información General</h3>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Título <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', optional($audit)->title) }}"
               placeholder="Ej: Auditoría interna Q1 2026"
               class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('title') border-red-400 @enderror">
        @error('title')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Type button selector --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipo <span class="text-red-500">*</span></label>
        <div class="flex flex-wrap gap-2">
            @foreach (['internal' => 'Interna', 'external' => 'Externa', 'regulatory' => 'Regulatoria', 'supplier' => 'Proveedores'] as $val => $lbl)
                <button type="button" @click="type = '{{ $val }}'"
                        :class="type === '{{ $val }}' ? 'bg-[#002046] text-white border-[#002046]' : 'bg-white text-gray-600 border-gray-200 hover:border-[#002046]/40'"
                        class="px-4 py-2 text-sm font-semibold rounded-lg border transition-colors">
                    {{ $lbl }}
                </button>
            @endforeach
        </div>
        <input type="hidden" name="type" :value="type">
        @error('type')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    @if ($audit)
        {{-- Status (edit only) --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Estado</label>
            <select name="status"
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
                @foreach (['planned' => 'Planificada', 'in_progress' => 'En Progreso', 'completed' => 'Completada', 'cancelled' => 'Cancelada'] as $val => $lbl)
                    <option value="{{ $val }}" {{ old('status', $audit->status->value) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Fecha Programada <span class="text-red-500">*</span></label>
            <input type="date" name="scheduled_date"
                   value="{{ old('scheduled_date', optional($audit)->scheduled_date?->format('Y-m-d')) }}"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('scheduled_date') border-red-400 @enderror">
            @error('scheduled_date')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        @if ($audit)
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Fecha Completada</label>
                <input type="date" name="completed_date"
                       value="{{ old('completed_date', optional($audit)->completed_date?->format('Y-m-d')) }}"
                       class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Auditor</label>
            <select name="auditor_id"
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
                <option value="">Sin asignar</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}"
                            {{ old('auditor_id', optional($audit)->auditor_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ubicación / Sede</label>
            <input type="text" name="location" value="{{ old('location', optional($audit)->location) }}"
                   placeholder="Ej: Planta Norte, Piso 2"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alcance</label>
        <textarea name="scope" rows="3"
                  placeholder="Describe qué áreas o procesos se auditarán..."
                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none">{{ old('scope', optional($audit)->scope) }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Descripción</label>
        <textarea name="description" rows="4"
                  placeholder="Información adicional sobre la auditoría..."
                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none">{{ old('description', optional($audit)->description) }}</textarea>
    </div>
</div>
