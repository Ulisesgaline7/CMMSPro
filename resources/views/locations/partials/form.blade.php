{{--
    Shared locations form partial.
    Variables: $location (for edit, null for create), $parents (Collection)
--}}

{{-- ── Identificación ─────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Identificación</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                Nombre <span class="text-red-400">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name', optional($location)->name) }}"
                   placeholder="Ej: Planta Principal"
                   class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] {{ $errors->has('name') ? 'border-red-300' : 'border-gray-200' }}">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                Tipo <span class="text-red-400">*</span>
            </label>
            @php $selectedType = old('type', optional($location)->type?->value) @endphp
            <select name="type"
                    class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white {{ $errors->has('type') ? 'border-red-300' : 'border-gray-200' }}">
                <option value="">Seleccionar tipo...</option>
                @foreach (\App\Enums\LocationType::cases() as $type)
                    <option value="{{ $type->value }}" {{ $selectedType === $type->value ? 'selected' : '' }}>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </select>
            @error('type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Código</label>
            <input type="text" name="code" value="{{ old('code', optional($location)->code) }}"
                   placeholder="Ej: LOC-001"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] font-mono {{ $errors->has('code') ? 'border-red-300' : 'border-gray-200' }}">
            @error('code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Ubicación Padre</label>
            @php $selectedParent = old('parent_id', optional($location)->parent_id) @endphp
            <select name="parent_id"
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white {{ $errors->has('parent_id') ? 'border-red-300' : 'border-gray-200' }}">
                <option value="">Sin ubicación padre</option>
                @foreach ($parents as $parent)
                    <option value="{{ $parent->id }}" {{ (string) $selectedParent === (string) $parent->id ? 'selected' : '' }}>
                        {{ $parent->name }}{{ $parent->code ? ' (' . $parent->code . ')' : '' }}
                    </option>
                @endforeach
            </select>
            @error('parent_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- ── Detalles ─────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Detalles</h3>

    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Dirección</label>
        <input type="text" name="address" value="{{ old('address', optional($location)->address) }}"
               placeholder="Ej: Av. Industrial 123, Zona Norte"
               class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] {{ $errors->has('address') ? 'border-red-300' : 'border-gray-200' }}">
        @error('address') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Descripción</label>
        <textarea name="description" rows="3"
                  placeholder="Descripción de la ubicación..."
                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none {{ $errors->has('description') ? 'border-red-300' : 'border-gray-200' }}">{{ old('description', optional($location)->description) }}</textarea>
        @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>
