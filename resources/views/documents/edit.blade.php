<x-layouts.cmms :title="'Editar ' . $document->code" :headerTitle="'Editar ' . $document->code">

<div class="p-8 max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('documents.show', $document) }}"
           class="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Editar Documento</h2>
            <p class="text-sm text-gray-400 mt-0.5">{{ $document->code }} · {{ $document->title }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('documents.update', $document) }}" class="space-y-6"
          x-data="{ type: '{{ old('type', $document->type->value) }}' }">
        @csrf
        @method('PATCH')

        @include('documents.partials.form', ['document' => $document, 'assets' => $assets])

        {{-- Status (only on edit) --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 mb-4">Estado del Documento</h3>
            <select name="status"
                    class="w-full md:w-64 px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
                <option value="draft" {{ old('status', $document->status->value) === 'draft' ? 'selected' : '' }}>Borrador</option>
                <option value="review" {{ old('status', $document->status->value) === 'review' ? 'selected' : '' }}>En Revisión</option>
                <option value="approved" {{ old('status', $document->status->value) === 'approved' ? 'selected' : '' }}>Aprobado</option>
                <option value="obsolete" {{ old('status', $document->status->value) === 'obsolete' ? 'selected' : '' }}>Obsoleto</option>
            </select>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('documents.show', $document) }}"
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
