{{-- Shared form partial for create/edit document --}}

@php
    $types = [
        ['value' => 'procedure',   'label' => 'Procedimiento', 'icon' => 'list-checks'],
        ['value' => 'manual',      'label' => 'Manual',        'icon' => 'book-open'],
        ['value' => 'certificate', 'label' => 'Certificado',   'icon' => 'award'],
        ['value' => 'regulation',  'label' => 'Reglamento',    'icon' => 'scale'],
        ['value' => 'form',        'label' => 'Formato',       'icon' => 'clipboard'],
        ['value' => 'report',      'label' => 'Reporte',       'icon' => 'bar-chart-2'],
        ['value' => 'other',       'label' => 'Otro',          'icon' => 'file'],
    ];
@endphp

{{-- Title --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400">Información General</h3>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
            Título <span class="text-red-500">*</span>
        </label>
        <input type="text" name="title" value="{{ old('title', $document?->title) }}"
               placeholder="Ej: Procedimiento de Bloqueo y Etiquetado (LOTO)"
               class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('title') border-red-400 @enderror">
        @error('title')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                Versión Actual
            </label>
            <input type="text" name="current_version" value="{{ old('current_version', $document?->current_version ?? '1.0') }}"
                   placeholder="1.0"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('current_version') border-red-400 @enderror">
            @error('current_version')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                Fecha de Revisión
            </label>
            <input type="date" name="review_date"
                   value="{{ old('review_date', $document?->review_date?->format('Y-m-d')) }}"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('review_date') border-red-400 @enderror">
            @error('review_date')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Descripción</label>
        <textarea name="description" rows="3"
                  placeholder="Describe el propósito y alcance del documento..."
                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none @error('description') border-red-400 @enderror">{{ old('description', $document?->description) }}</textarea>
        @error('description')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- Type selector --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400">Tipo de Documento <span class="text-red-500">*</span></h3>

    <input type="hidden" name="type" :value="type">

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        @foreach ($types as $t)
            <button type="button"
                    @click="type = '{{ $t['value'] }}'"
                    :class="type === '{{ $t['value'] }}' ? 'border-[#002046] bg-[#002046]/5 text-[#002046]' : 'border-gray-200 text-gray-500 hover:border-gray-300'"
                    class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-lg border-2 text-xs font-semibold transition-all">
                <i data-lucide="{{ $t['icon'] }}" class="w-5 h-5"></i>
                {{ $t['label'] }}
            </button>
        @endforeach
    </div>

    @error('type')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Category & Asset --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400">Clasificación</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Categoría</label>
            <select name="category"
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('category') border-red-400 @enderror">
                <option value="">Sin categoría</option>
                <option value="safety" {{ old('category', $document?->category) === 'safety' ? 'selected' : '' }}>Seguridad</option>
                <option value="quality" {{ old('category', $document?->category) === 'quality' ? 'selected' : '' }}>Calidad</option>
                <option value="maintenance" {{ old('category', $document?->category) === 'maintenance' ? 'selected' : '' }}>Mantenimiento</option>
                <option value="regulatory" {{ old('category', $document?->category) === 'regulatory' ? 'selected' : '' }}>Regulatorio</option>
            </select>
            @error('category')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Activo Relacionado</label>
            <select name="asset_id"
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('asset_id') border-red-400 @enderror">
                <option value="">Sin activo asociado</option>
                @foreach ($assets as $asset)
                    <option value="{{ $asset->id }}"
                            {{ old('asset_id', $document?->asset_id) == $asset->id ? 'selected' : '' }}>
                        {{ $asset->name }} ({{ $asset->code }})
                    </option>
                @endforeach
            </select>
            @error('asset_id')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
