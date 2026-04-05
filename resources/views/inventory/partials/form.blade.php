{{--
    Shared inventory form partial.
    Variables: $part (for edit, null for create)
--}}

{{-- ── Identificación ─────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Identificación</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                Nombre <span class="text-red-400">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name', optional($part)->name) }}"
                   placeholder="Ej: Rodamiento SKF 6205"
                   class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] {{ $errors->has('name') ? 'border-red-300' : 'border-gray-200' }}">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Número de Parte</label>
            <input type="text" name="part_number" value="{{ old('part_number', optional($part)->part_number) }}"
                   placeholder="Ej: PN-12345"
                   class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] font-mono {{ $errors->has('part_number') ? 'border-red-300' : 'border-gray-200' }}">
            @error('part_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Marca</label>
            <input type="text" name="brand" value="{{ old('brand', optional($part)->brand) }}"
                   placeholder="Ej: SKF, Parker, Bosch"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            @error('brand') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Ubicación en Almacén</label>
            <input type="text" name="storage_location" value="{{ old('storage_location', optional($part)->storage_location) }}"
                   placeholder="Ej: Estante A3-02"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            @error('storage_location') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Descripción</label>
        <textarea name="description" rows="2"
                  placeholder="Descripción del repuesto, especificaciones técnicas..."
                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none">{{ old('description', optional($part)->description) }}</textarea>
        @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- ── Stock y Costos ──────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Stock y Costos</h3>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                Stock Actual <span class="text-red-400">*</span>
            </label>
            <input type="number" name="stock_quantity"
                   value="{{ old('stock_quantity', optional($part)->stock_quantity ?? 0) }}"
                   min="0"
                   class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] {{ $errors->has('stock_quantity') ? 'border-red-300' : 'border-gray-200' }}">
            @error('stock_quantity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                Stock Mínimo <span class="text-red-400">*</span>
            </label>
            <input type="number" name="min_stock"
                   value="{{ old('min_stock', optional($part)->min_stock ?? 0) }}"
                   min="0"
                   class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] {{ $errors->has('min_stock') ? 'border-red-300' : 'border-gray-200' }}">
            @error('min_stock') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                Unidad <span class="text-red-400">*</span>
            </label>
            <select name="unit"
                    class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white">
                @php
                    $units = ['pieza', 'litro', 'kg', 'metro', 'juego', 'caja', 'par', 'rollo', 'galón'];
                    $selectedUnit = old('unit', optional($part)->unit ?? 'pieza');
                @endphp
                @foreach ($units as $u)
                    <option value="{{ $u }}" {{ $selectedUnit === $u ? 'selected' : '' }}>{{ ucfirst($u) }}</option>
                @endforeach
            </select>
            @error('unit') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Costo Unitario</label>
            <input type="number" name="unit_cost"
                   value="{{ old('unit_cost', optional($part)->unit_cost) }}"
                   placeholder="0.00" min="0" step="0.01"
                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
            @error('unit_cost') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
