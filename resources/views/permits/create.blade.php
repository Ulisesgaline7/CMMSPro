<x-layouts.cmms title="Nuevo Permiso de Trabajo" headerTitle="Nuevo Permiso de Trabajo">

    <div class="p-6 max-w-3xl mx-auto space-y-5">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('permits.index') }}" class="text-gray-400 hover:text-[#002046] transition-colors">Permisos</a>
            <span class="text-gray-300">/</span>
            <span class="font-semibold text-[#002046]">Nuevo</span>
        </div>

        <form method="POST" action="{{ route('permits.store') }}" class="space-y-5">
            @csrf

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Información General</h3>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Título del Permiso <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20 @error('title') border-red-300 @enderror"
                           placeholder="Ej: Bloqueo compresor L1 para cambio de rodamiento">
                    @error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo de Permiso <span class="text-red-500">*</span></label>
                        <select name="type" required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                            <option value="">Seleccionar tipo...</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" @selected(old('type') === $type->value)>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nivel de Riesgo <span class="text-red-500">*</span></label>
                        <select name="risk_level" required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                            <option value="">Seleccionar nivel...</option>
                            @foreach ($riskLevels as $level)
                                <option value="{{ $level->value }}" @selected(old('risk_level') === $level->value)>
                                    {{ $level->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('risk_level')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Orden de Trabajo Vinculada</label>
                        <select name="work_order_id"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                            <option value="">Sin OT vinculada</option>
                            @foreach ($workOrders as $wo)
                                <option value="{{ $wo->id }}" @selected(old('work_order_id', $selectedWorkOrder?->id) == $wo->id)>
                                    {{ $wo->code }} — {{ Str::limit($wo->title, 40) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha/Hora de Vencimiento</label>
                        <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                        @error('expires_at')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción del Trabajo</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20"
                              placeholder="Describir el trabajo a realizar...">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Seguridad LOTO</h3>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Puntos de Bloqueo / Lock-out Points</label>
                    <textarea name="lockout_points" rows="3"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20"
                              placeholder="Listar cada punto de energía a bloquear: disyuntores, válvulas, etc.">{{ old('lockout_points') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">EPP Requerido</label>
                    <textarea name="required_ppe" rows="2"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20"
                              placeholder="Ej: Casco, guantes dieléctricos, gafas, arnés...">{{ old('required_ppe') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Precauciones Adicionales</label>
                    <textarea name="precautions" rows="2"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20"
                              placeholder="Precauciones específicas del trabajo...">{{ old('precautions') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('permits.index') }}"
                   class="px-5 py-2.5 border border-gray-200 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="bg-[#002046] text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-[#1b365d] transition-colors shadow-sm">
                    Crear Permiso
                </button>
            </div>
        </form>

    </div>

</x-layouts.cmms>
