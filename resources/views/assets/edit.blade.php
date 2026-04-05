<x-layouts.cmms :title="'Editar – ' . $asset->name" headerTitle="Editar Activo">

<div class="p-8 max-w-4xl mx-auto">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('assets.show', $asset) }}"
           class="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">
                Editar: {{ $asset->name }}
            </h2>
            <p class="text-sm text-gray-400 mt-0.5 font-mono">{{ $asset->code }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('assets.update', $asset) }}" class="space-y-6"
          x-data="{ status: '{{ old('status', $asset->status->value) }}', criticality: '{{ old('criticality', $asset->criticality->value) }}' }">
        @csrf
        @method('PATCH')

        @include('assets.partials.form', ['asset' => $asset])

        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('assets.show', $asset) }}"
               class="px-5 py-2.5 text-sm font-semibold text-gray-500 hover:text-gray-700 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="flex items-center gap-2 bg-[#002046] text-white px-8 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="save" class="w-4 h-4"></i>
                Guardar Cambios
            </button>
        </div>
    </form>

</div>

</x-layouts.cmms>
