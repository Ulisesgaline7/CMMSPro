<x-layouts.cmms title="Marca y Personalización" headerTitle="Configuración — Marca">

    <div class="p-6 space-y-5">

        <div>
            <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Marca y Personalización</h2>
            <p class="text-sm text-gray-400 mt-0.5">Personaliza la apariencia de tu CMMS</p>
        </div>

        <form method="POST" action="{{ route('settings.branding.update') }}"
              enctype="multipart/form-data"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6 max-w-2xl">
            @csrf
            @method('PATCH')

            {{-- Logo --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Logo</label>
                @if ($tenant?->logo_path)
                    <div class="mb-3">
                        <img src="{{ Storage::url($tenant->logo_path) }}" alt="Logo" class="h-16 object-contain border border-gray-100 rounded-lg p-2">
                    </div>
                @endif
                <input type="file" name="logo" accept="image/*"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none">
                <p class="text-[10px] text-gray-400 mt-1">Máximo 2MB. Formatos: PNG, JPG, SVG.</p>
                @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Brand name --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Nombre de Marca</label>
                <input type="text" name="brand_name" value="{{ old('brand_name', $tenant?->brand_name) }}"
                       placeholder="CMMS Pro" maxlength="100"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                @error('brand_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Subdomain --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Subdominio</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="subdomain" value="{{ old('subdomain', $tenant?->subdomain) }}"
                           placeholder="miempresa" maxlength="50"
                           class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                    <span class="text-xs text-gray-400 font-medium">.cmms.app</span>
                </div>
                @error('subdomain') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Colors --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Color Primario</label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="primary_color" value="{{ old('primary_color', $tenant?->primary_color ?? '#3B82F6') }}"
                               class="h-9 w-16 border border-gray-200 rounded-lg cursor-pointer">
                        <input type="text" id="primary_color_text" value="{{ old('primary_color', $tenant?->primary_color ?? '#3B82F6') }}"
                               placeholder="#3B82F6" maxlength="7"
                               class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                    </div>
                    @error('primary_color') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Color Secundario</label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="secondary_color" value="{{ old('secondary_color', $tenant?->secondary_color ?? '#1E40AF') }}"
                               class="h-9 w-16 border border-gray-200 rounded-lg cursor-pointer">
                        <input type="text" id="secondary_color_text" value="{{ old('secondary_color', $tenant?->secondary_color ?? '#1E40AF') }}"
                               placeholder="#1E40AF" maxlength="7"
                               class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                    </div>
                    @error('secondary_color') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="bg-[#002046] text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-[#1b365d] transition-colors">
                    Guardar Configuración
                </button>
            </div>
        </form>

    </div>

</x-layouts.cmms>
