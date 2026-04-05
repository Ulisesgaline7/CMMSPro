{{--
    Shared asset form partial.
    Variables available: $asset (for edit, null for create), $locations, $categories, $parents
    x-data state: status, criticality  (must be initialized by parent)
--}}

{{-- ── Identificación ─────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Identificación</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                Nombre <span class="text-red-400">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name', optional($asset)->name) }}"
                   placeholder="Ej: Compresor de Aire Línea 3"
                   class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] {{ $errors->has('name') ? 'border-red-300' : 'border-gray-200' }}">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                Código <span class="text-red-400">*</span>
            </label>
            <input type="text" name="code" value="{{ old('code', optional($asset)->code) }}"
                   placeholder="Ej: COMP-001"
                   class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] font-mono {{ $errors->has('code') ? 'border-red-300' : 'border-gray-200' }}">
            @error('code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Número de Serie</label>
            <input type="text" name="serial_number" value="{{ old('serial_number', optional($asset)->serial_number) }}"
                   placeholder="Ej: SN-12345AB"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            @error('serial_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Categoría</label>
            <select name="asset_category_id"
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white">
                <option value="">Sin categoría</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('asset_category_id', optional($asset)->asset_category_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            @error('asset_category_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- ── Fabricante ───────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Fabricante</h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Marca</label>
            <input type="text" name="brand" value="{{ old('brand', optional($asset)->brand) }}"
                   placeholder="Ej: Siemens"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            @error('brand') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Modelo</label>
            <input type="text" name="model" value="{{ old('model', optional($asset)->model) }}"
                   placeholder="Ej: 1LE1503"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            @error('model') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Año de Fabricación</label>
            <input type="number" name="manufacture_year" value="{{ old('manufacture_year', optional($asset)->manufacture_year) }}"
                   placeholder="{{ date('Y') }}" min="1900" max="{{ date('Y') + 1 }}"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            @error('manufacture_year') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- ── Ubicación y Jerarquía ────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Ubicación y Jerarquía</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Ubicación</label>
            <select name="location_id"
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white">
                <option value="">Sin ubicación</option>
                @foreach ($locations as $loc)
                    <option value="{{ $loc->id }}" {{ old('location_id', optional($asset)->location_id) == $loc->id ? 'selected' : '' }}>
                        {{ $loc->name }}
                    </option>
                @endforeach
            </select>
            @error('location_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Activo Padre</label>
            <select name="parent_id"
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white">
                <option value="">Sin activo padre</option>
                @foreach ($parents as $parent)
                    @if (!isset($asset) || $parent->id !== $asset->id)
                        <option value="{{ $parent->id }}" {{ old('parent_id', optional($asset)->parent_id) == $parent->id ? 'selected' : '' }}>
                            [{{ $parent->code }}] {{ $parent->name }}
                        </option>
                    @endif
                @endforeach
            </select>
            @error('parent_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- ── Estado y Criticidad ──────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Estado y Criticidad</h3>

    {{-- Estado --}}
    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
            Estado <span class="text-red-400">*</span>
        </label>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            @php
                $statusOpts = [
                    ['value' => 'active',            'label' => 'Activo',           'color' => 'border-green-200 bg-green-50 text-green-700'],
                    ['value' => 'inactive',          'label' => 'Inactivo',         'color' => 'border-gray-200 bg-gray-50 text-gray-600'],
                    ['value' => 'under_maintenance', 'label' => 'En Mantenimiento', 'color' => 'border-yellow-200 bg-yellow-50 text-yellow-700'],
                    ['value' => 'retired',           'label' => 'Dado de Baja',     'color' => 'border-red-200 bg-red-50 text-red-700'],
                ];
            @endphp
            @foreach ($statusOpts as $opt)
                <button type="button"
                        @click="status = '{{ $opt['value'] }}'"
                        :class="status === '{{ $opt['value'] }}'
                            ? '{{ $opt['color'] }} border-current py-2.5 px-3 rounded-lg border-2 text-xs font-bold transition-all'
                            : 'border-gray-100 text-gray-400 hover:border-gray-200 py-2.5 px-3 rounded-lg border-2 text-xs font-bold transition-all'">
                    {{ $opt['label'] }}
                </button>
            @endforeach
        </div>
        <input type="hidden" name="status" :value="status">
        @error('status') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Criticidad --}}
    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
            Criticidad <span class="text-red-400">*</span>
        </label>
        <div class="flex gap-2">
            @php
                $critOpts = [
                    ['value' => 'low',      'label' => 'Baja',    'dot' => 'bg-gray-300'],
                    ['value' => 'medium',   'label' => 'Media',   'dot' => 'bg-blue-400'],
                    ['value' => 'high',     'label' => 'Alta',    'dot' => 'bg-orange-400'],
                    ['value' => 'critical', 'label' => 'Crítica', 'dot' => 'bg-red-500'],
                ];
            @endphp
            @foreach ($critOpts as $opt)
                <button type="button"
                        @click="criticality = '{{ $opt['value'] }}'"
                        :class="criticality === '{{ $opt['value'] }}'
                            ? 'flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg border-2 text-xs font-bold transition-all border-[#002046] bg-[#002046] text-white'
                            : 'flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg border-2 text-xs font-bold transition-all border-gray-100 text-gray-500 hover:border-gray-200'">
                    <span class="w-2 h-2 rounded-full {{ $opt['dot'] }}"></span>
                    {{ $opt['label'] }}
                </button>
            @endforeach
        </div>
        <input type="hidden" name="criticality" :value="criticality">
        @error('criticality') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- ── Fechas y Costo ───────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Fechas y Costo</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Fecha de Compra</label>
            <input type="date" name="purchase_date"
                   value="{{ old('purchase_date', optional($asset)->purchase_date?->format('Y-m-d')) }}"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            @error('purchase_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Fecha de Instalación</label>
            <input type="date" name="installation_date"
                   value="{{ old('installation_date', optional($asset)->installation_date?->format('Y-m-d')) }}"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            @error('installation_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Garantía hasta</label>
            <input type="date" name="warranty_expires_at"
                   value="{{ old('warranty_expires_at', optional($asset)->warranty_expires_at?->format('Y-m-d')) }}"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            @error('warranty_expires_at') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Costo de Compra</label>
            <input type="number" name="purchase_cost"
                   value="{{ old('purchase_cost', optional($asset)->purchase_cost) }}"
                   placeholder="Ej: 25000.00" min="0" step="0.01"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            @error('purchase_cost') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- ── Notas ─────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Notas</h3>
    <textarea name="notes" rows="3"
              placeholder="Observaciones adicionales sobre el activo..."
              class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none">{{ old('notes', optional($asset)->notes) }}</textarea>
    @error('notes') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>
