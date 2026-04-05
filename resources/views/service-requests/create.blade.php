<x-layouts.cmms title="Nueva Solicitud" headerTitle="Facilities / Solicitudes de Servicio">

    <div class="p-6 max-w-2xl mx-auto space-y-5">

        <div class="flex items-center gap-3">
            <a href="{{ route('service-requests.index') }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Nueva Solicitud de Servicio</h2>
        </div>

        <form action="{{ route('service-requests.store') }}" method="POST"
              x-data="{
                  priority: 'medium',
                  slaLabels: { low: 'SLA: 72 horas', medium: 'SLA: 24 horas', high: 'SLA: 8 horas', critical: 'SLA: 2 horas' }
              }"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
            @csrf

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            {{-- Título --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Título *</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
            </div>

            {{-- Categoría --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Categoría *</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach (\App\Enums\ServiceRequestCategory::cases() as $cat)
                        <label class="flex items-center gap-2 px-3 py-2.5 border border-gray-200 rounded-lg cursor-pointer hover:border-gray-300 transition-colors has-[:checked]:{{ $cat->color() }} has-[:checked]:border-2">
                            <input type="radio" name="category" value="{{ $cat->value }}" class="sr-only" @checked(old('category') === $cat->value)>
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
                                class="py-2.5 rounded-lg text-xs font-semibold transition-all">
                            {{ $lbl }}
                        </button>
                    @endforeach
                </div>
                <p class="text-[11px] text-gray-400 mt-1.5" x-text="slaLabels[priority]"></p>
                <input type="hidden" name="priority" :value="priority">
            </div>

            {{-- Descripción --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Descripción</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20 resize-none">{{ old('description') }}</textarea>
            </div>

            {{-- Ubicación --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Ubicación / Descripción del lugar</label>
                <input type="text" name="location_description" value="{{ old('location_description') }}"
                       placeholder="Ej: Planta 2, Sala de servidores"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Activo --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Activo relacionado</label>
                    <select name="asset_id"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                        <option value="">Sin activo</option>
                        @foreach ($assets as $asset)
                            <option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>{{ $asset->name }} ({{ $asset->code }})</option>
                        @endforeach
                    </select>
                </div>
                {{-- Asignado a --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Asignar a</label>
                    <select name="assigned_to"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                        <option value="">Sin asignar</option>
                        @foreach ($technicians as $tech)
                            <option value="{{ $tech->id }}" @selected(old('assigned_to') == $tech->id)>{{ $tech->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-[#002046] text-white py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors">
                    Crear Solicitud
                </button>
                <a href="{{ route('service-requests.index') }}"
                   class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

</x-layouts.cmms>
