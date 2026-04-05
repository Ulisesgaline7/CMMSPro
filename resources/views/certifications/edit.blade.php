<x-layouts.cmms title="Editar Certificación" headerTitle="Certificaciones">

    <div class="p-6 max-w-3xl">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
            <a href="{{ route('certifications.index') }}" class="hover:text-[#002046] transition-colors">Certificaciones</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <a href="{{ route('certifications.show', $certification) }}" class="hover:text-[#002046] transition-colors">{{ $certification->name }}</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-[#002046] font-medium">Editar</span>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-lg font-extrabold text-[#002046] font-headline mb-6">Editar Certificación</h2>

            <form method="POST" action="{{ route('certifications.update', $certification) }}" class="space-y-5">
                @csrf
                @method('PATCH')

                {{-- Técnico --}}
                <div>
                    <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Técnico <span class="text-red-500">*</span>
                    </label>
                    <select id="user_id" name="user_id"
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('user_id') border-red-400 @enderror">
                        <option value="">Seleccionar técnico...</option>
                        @foreach ($technicians as $technician)
                            <option value="{{ $technician->id }}"
                                {{ old('user_id', $certification->user_id) == $technician->id ? 'selected' : '' }}>
                                {{ $technician->name }}{{ $technician->employee_code ? ' ('.$technician->employee_code.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Certificate Name --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Nombre de la Certificación <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $certification->name) }}"
                           placeholder="Ej. OSHA 30-Hour General Industry"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Issuing Body --}}
                <div>
                    <label for="issuing_body" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Entidad Emisora <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="issuing_body" name="issuing_body" value="{{ old('issuing_body', $certification->issuing_body) }}"
                           placeholder="Ej. OSHA, ISO, NFPA"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('issuing_body') border-red-400 @enderror">
                    @error('issuing_body')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Certificate Number --}}
                <div>
                    <label for="certificate_number" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Número de Certificado
                    </label>
                    <input type="text" id="certificate_number" name="certificate_number"
                           value="{{ old('certificate_number', $certification->certificate_number) }}"
                           placeholder="Ej. CERT-2024-0123"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('certificate_number') border-red-400 @enderror">
                    @error('certificate_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="issued_at" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Fecha de Emisión <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="issued_at" name="issued_at"
                               value="{{ old('issued_at', $certification->issued_at->format('Y-m-d')) }}"
                               class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('issued_at') border-red-400 @enderror">
                        @error('issued_at')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="expires_at" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Fecha de Vencimiento
                        </label>
                        <input type="date" id="expires_at" name="expires_at"
                               value="{{ old('expires_at', optional($certification->expires_at)?->format('Y-m-d')) }}"
                               class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('expires_at') border-red-400 @enderror">
                        @error('expires_at')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status"
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('status') border-red-400 @enderror">
                        <option value="">Seleccionar estado...</option>
                        @foreach (\App\Enums\CertificationStatus::cases() as $status)
                            <option value="{{ $status->value }}"
                                {{ old('status', $certification->status->value) === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-1.5">Notas</label>
                    <textarea id="notes" name="notes" rows="3"
                              placeholder="Observaciones adicionales..."
                              class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('notes') border-red-400 @enderror">{{ old('notes', $certification->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 bg-[#002046] text-white text-sm font-bold rounded-lg hover:bg-[#1b365d] transition-colors shadow-sm">
                        Guardar Cambios
                    </button>
                    <a href="{{ route('certifications.show', $certification) }}"
                       class="px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-800 transition-colors">
                        Cancelar
                    </a>
                </div>

            </form>
        </div>

    </div>

</x-layouts.cmms>
