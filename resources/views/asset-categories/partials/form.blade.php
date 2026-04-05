{{--
    Shared asset-category form partial.
    Variables: $category (for edit, null for create)
--}}

{{-- ── Información de la Categoría ─────────────────── --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Información de la Categoría</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                Nombre <span class="text-red-400">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name', $category?->name) }}"
                   placeholder="Ej: Bombas y Compresores"
                   class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] {{ $errors->has('name') ? 'border-red-300' : 'border-gray-200' }}">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Código</label>
            <input type="text" name="code" value="{{ old('code', $category?->code) }}"
                   placeholder="CAT-001"
                   class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] font-mono {{ $errors->has('code') ? 'border-red-300' : 'border-gray-200' }}">
            @error('code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Descripción</label>
        <textarea name="description" rows="3"
                  placeholder="Descripción de la categoría..."
                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none {{ $errors->has('description') ? 'border-red-300' : 'border-gray-200' }}">{{ old('description', $category?->description) }}</textarea>
        @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>
