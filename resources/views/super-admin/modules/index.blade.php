<x-layouts.super-admin title="Módulos — {{ $tenant->name }}" headerTitle="Super Admin — Módulos">

    <div class="p-6 space-y-5">

        <div class="flex items-center gap-3">
            <a href="{{ route('super-admin.tenants.show', $tenant->id) }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h2 class="text-2xl font-extrabold text-[#002046] font-headline">Módulos: {{ $tenant->name }}</h2>
                <p class="text-sm text-gray-400 mt-0.5">Activa o desactiva módulos para este tenant</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($modules as $module)
                @php $isActive = in_array($module->value, $activeModuleKeys); @endphp
                <div class="bg-white rounded-xl border {{ $isActive ? 'border-blue-200' : 'border-gray-100' }} shadow-sm p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-[#002046]">{{ $module->label() }}</span>
                                @if ($isActive)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-green-100 text-green-700">Activo</span>
                                @endif
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 leading-relaxed">{{ $module->description() }}</p>
                            <p class="text-xs font-semibold text-gray-500 mt-1">
                                {{ $module->price() > 0 ? '$' . $module->price() . '/mes' : 'Incluido' }}
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('super-admin.tenant-modules.toggle', $tenant->id) }}" class="mt-3">
                        @csrf
                        <input type="hidden" name="module_key" value="{{ $module->value }}">
                        <button type="submit"
                                class="w-full py-1.5 rounded-lg text-xs font-bold transition-colors
                                    {{ $isActive ? 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100' : 'bg-[#002046] text-white hover:bg-[#1b365d]' }}">
                            {{ $isActive ? 'Desactivar' : 'Activar' }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

    </div>

</x-layouts.super-admin>
