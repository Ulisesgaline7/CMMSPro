<x-layouts.cmms title="Editar Solicitud" headerTitle="Facilities / Solicitudes de Servicio">

    <div class="p-6 max-w-2xl mx-auto space-y-5">

        <div class="flex items-center gap-3">
            <a href="{{ route('service-requests.show', $sr) }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Editar Solicitud</h2>
                <p class="text-xs text-gray-400 font-mono">{{ $sr->code }}</p>
            </div>
        </div>

        <form action="{{ route('service-requests.update', $sr) }}" method="POST"
              x-data="{ priority: '{{ old('priority', $sr->priority->value) }}' }"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
            @csrf
            @method('PATCH')

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            {{-- Título --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Título *</label>
                <input type="text" name="title" value="{{ old('title', $sr->title) }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
            </div>

            {{-- Estado --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Estado *</label>
                <select name="status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                    @foreach (\App\Enums\ServiceRequestStatus::cases() as $s)
                        <option value="{{ $s->value }}" @selected(old('status', $sr->status->value) === $s->value)>{{ $s->label() }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Categoría --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Categoría *</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach (\App\Enums\ServiceRequestCategory::cases() as $cat)
                        <label class="flex items-center gap-2 px-3 py-2.5 border border-gray-200 rounded-lg cursor-pointer hover:border-gray-300 transition-colors has-[:checked]:{{ $cat->color() }} has-[:checked]:border-2">
                            <input type="radio" name="category" value="{{ $cat->value }}" class="sr-only"
                                   @checked(old('category', $sr->category->value) === $cat->value)>
                            <i data-lucide="{{ $cat->icon() }}" class="w-4 h-4 shrink-0"></i>
                            <span class="text-xs font-semibold">{{ $cat->label() }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Prioridad --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Prioridad *</label>
                <div class="grid grid-cols-4 gap-2">
                    @foreach ([
                        ['low',      'Baja',     'bg-gray-50 text-gray-700 border-gray-300'],
                        ['medium',   'Media',    'bg-blue-50 text-blue-700 border-blue-300'],
                        ['high',     'Alta',     'bg-orange-50 text-orange-700 border-orange-300'],
                        ['critical', 'Crítica',  'bg-red-50 text-red-700 border-red-300'],
                    ] as [$val, $lbl, $activeClass])
                        <button type="button" @click="priority = '{{ $val }}'"
                                :class="priority === '{{ $val }}' ? '{{ $activeClass }} border-2 shadow-sm' : 'border border-gray-200 text-gray-400'"
                                class="py-2.5 rounded-lg text-xs font-semibold transition-all">{{ $lbl }}</button>
                    @endforeach
                </div>
                <input type="hidden" name="priority" :value="priority">
            </div>

            {{-- Descripción --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Descripción</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20 resize-none">{{ old('description', $sr->description) }}</textarea>
            </div>

            {{-- Ubicación --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Ubicación</label>
                <input type="text" name="location_description" value="{{ old('location_description', $sr->location_description) }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Activo</label>
                    <select name="asset_id"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                        <option value="">Sin activo</option>
                        @foreach ($assets as $asset)
                            <option value="{{ $asset->id }}" @selected(old('asset_id', $sr->asset_id) == $asset->id)>{{ $asset->name }} ({{ $asset->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Asignado a</label>
                    <select name="assigned_to"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                        <option value="">Sin asignar</option>
                        @foreach ($technicians as $tech)
                            <option value="{{ $tech->id }}" @selected(old('assigned_to', $sr->assigned_to) == $tech->id)>{{ $tech->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Notas de resolución --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Notas de resolución</label>
                <textarea name="resolution_notes" rows="2"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20 resize-none">{{ old('resolution_notes', $sr->resolution_notes) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-[#002046] text-white py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors">
                    Guardar Cambios
                </button>
                <a href="{{ route('service-requests.show', $sr) }}"
                   class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

</x-layouts.cmms>
