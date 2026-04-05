<x-layouts.cmms title="Nueva Ubicación" headerTitle="Nueva Ubicación">

<div class="p-8 max-w-4xl mx-auto">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('locations.index') }}"
           class="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Nueva Ubicación</h2>
            <p class="text-sm text-gray-400 mt-0.5">Completa los campos para registrar la ubicación</p>
        </div>
    </div>

    <form method="POST" action="{{ route('locations.store') }}" class="space-y-6">
        @csrf

        @include('locations.partials.form', ['location' => null, 'parents' => $parents])

        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('locations.index') }}"
               class="px-5 py-2.5 text-sm font-semibold text-gray-500 hover:text-gray-700 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="flex items-center gap-2 bg-[#002046] text-white px-8 py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Crear Ubicación
            </button>
        </div>
    </form>

</div>

</x-layouts.cmms>
