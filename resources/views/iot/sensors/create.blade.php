<x-layouts.cmms title="Nuevo Sensor" headerTitle="IoT — Nuevo Sensor">

    <div class="p-6 space-y-5">

        <div class="flex items-center gap-3">
            <a href="{{ route('iot.sensors.index') }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <h2 class="text-2xl font-extrabold text-[#002046] font-headline">Nuevo Sensor</h2>
        </div>

        <form method="POST" action="{{ route('iot.sensors.store') }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5 max-w-2xl">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Activo <span class="text-red-500">*</span></label>
                    <select name="asset_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                        <option value="">Seleccionar activo</option>
                        @foreach ($assets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                {{ $asset->name }} ({{ $asset->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('asset_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Código <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="SEN-001"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                    @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Tipo <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>
                                {{ $type->label() }} ({{ $type->unit() }})
                            </option>
                        @endforeach
                    </select>
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Unidad <span class="text-red-500">*</span></label>
                    <input type="text" name="unit" value="{{ old('unit') }}" required placeholder="°C, bar, mm/s..."
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Umbral Mínimo (Crítico)</label>
                    <input type="number" name="min_threshold" value="{{ old('min_threshold') }}" step="0.0001"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Umbral Máximo (Crítico)</label>
                    <input type="number" name="max_threshold" value="{{ old('max_threshold') }}" step="0.0001"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Advertencia Bajo</label>
                    <input type="number" name="warning_threshold_low" value="{{ old('warning_threshold_low') }}" step="0.0001"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Advertencia Alto</label>
                    <input type="number" name="warning_threshold_high" value="{{ old('warning_threshold_high') }}" step="0.0001"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Intervalo de Muestreo (seg)</label>
                    <input type="number" name="sampling_interval_seconds" value="{{ old('sampling_interval_seconds', 60) }}" min="1"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Notas</label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">{{ old('notes') }}</textarea>
                </div>

            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-[#002046] text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-[#1b365d] transition-colors">
                    Crear Sensor
                </button>
                <a href="{{ route('iot.sensors.index') }}" class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-200 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>

    </div>

</x-layouts.cmms>
