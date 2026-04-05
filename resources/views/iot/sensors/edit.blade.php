<x-layouts.cmms title="Editar Sensor" headerTitle="IoT — Editar Sensor">

    <div class="p-6 space-y-5">

        <div class="flex items-center gap-3">
            <a href="{{ route('iot.sensors.show', $sensor) }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <h2 class="text-2xl font-extrabold text-[#002046] font-headline">Editar: {{ $sensor->name }}</h2>
        </div>

        <form method="POST" action="{{ route('iot.sensors.update', $sensor) }}"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5 max-w-2xl">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Activo</label>
                    <select name="asset_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                        @foreach ($assets as $asset)
                            <option value="{{ $asset->id }}" {{ $sensor->asset_id == $asset->id ? 'selected' : '' }}>
                                {{ $asset->name }} ({{ $asset->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Estado</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" {{ $sensor->status->value === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Nombre</label>
                    <input type="text" name="name" value="{{ old('name', $sensor->name) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Tipo</label>
                    <select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" {{ $sensor->type->value === $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Unidad</label>
                    <input type="text" name="unit" value="{{ old('unit', $sensor->unit) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Umbral Mínimo (Crítico)</label>
                    <input type="number" name="min_threshold" value="{{ old('min_threshold', $sensor->min_threshold) }}" step="0.0001"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Umbral Máximo (Crítico)</label>
                    <input type="number" name="max_threshold" value="{{ old('max_threshold', $sensor->max_threshold) }}" step="0.0001"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Advertencia Bajo</label>
                    <input type="number" name="warning_threshold_low" value="{{ old('warning_threshold_low', $sensor->warning_threshold_low) }}" step="0.0001"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Advertencia Alto</label>
                    <input type="number" name="warning_threshold_high" value="{{ old('warning_threshold_high', $sensor->warning_threshold_high) }}" step="0.0001"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Intervalo de Muestreo (seg)</label>
                    <input type="number" name="sampling_interval_seconds" value="{{ old('sampling_interval_seconds', $sensor->sampling_interval_seconds) }}" min="1"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Notas</label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">{{ old('notes', $sensor->notes) }}</textarea>
                </div>

            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-[#002046] text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-[#1b365d] transition-colors">
                    Guardar Cambios
                </button>
                <a href="{{ route('iot.sensors.show', $sensor) }}" class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-200 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>

    </div>

</x-layouts.cmms>
