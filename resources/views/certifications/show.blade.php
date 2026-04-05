<x-layouts.cmms title="Certificación" headerTitle="Certificaciones">

    <div class="p-6 max-w-3xl">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
            <a href="{{ route('certifications.index') }}" class="hover:text-[#002046] transition-colors">Certificaciones</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-[#002046] font-medium">{{ $certification->name }}</span>
        </div>

        {{-- Header card --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-4">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-[#002046]/10 flex items-center justify-center shrink-0">
                        <i data-lucide="award" class="w-6 h-6 text-[#002046]"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold text-[#002046] font-headline">{{ $certification->name }}</h1>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $certification->issuing_body }}</p>
                        @if ($certification->certificate_number)
                            <p class="text-xs font-mono text-gray-400 mt-1">{{ $certification->certificate_number }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold border {{ $certification->status->color() }}">
                        {{ $certification->status->label() }}
                    </span>
                    <a href="{{ route('certifications.edit', $certification) }}"
                       class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-[#002046] border border-[#002046]/30 rounded-lg hover:bg-[#002046]/5 transition-colors">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                        Editar
                    </a>
                </div>
            </div>
        </div>

        {{-- Details --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400">Detalles</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Técnico</p>
                    <p class="text-sm font-semibold text-gray-800">{{ optional($certification->user)->name ?? '—' }}</p>
                    @if(optional($certification->user)->employee_code)
                        <p class="text-xs text-gray-400 font-mono">{{ $certification->user->employee_code }}</p>
                    @endif
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Entidad Emisora</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $certification->issuing_body }}</p>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Fecha de Emisión</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $certification->issued_at->format('d/m/Y') }}</p>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Fecha de Vencimiento</p>
                    @if ($certification->expires_at)
                        @php
                            $isExpiringSoon = $certification->status === \App\Enums\CertificationStatus::Active
                                && $certification->expires_at->lte(now()->addDays(30));
                        @endphp
                        <p class="text-sm font-semibold {{ $isExpiringSoon ? 'text-yellow-600' : ($certification->status === \App\Enums\CertificationStatus::Expired ? 'text-red-600' : 'text-gray-800') }}">
                            {{ $certification->expires_at->format('d/m/Y') }}
                            @if ($isExpiringSoon)
                                <span class="text-xs font-normal text-yellow-500">(Vence pronto)</span>
                            @endif
                        </p>
                    @else
                        <p class="text-sm text-gray-400">Sin vencimiento</p>
                    @endif
                </div>

                @if ($certification->certificate_number)
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Número de Certificado</p>
                        <p class="text-sm font-mono text-gray-800">{{ $certification->certificate_number }}</p>
                    </div>
                @endif

            </div>

            @if ($certification->notes)
                <div class="border-t border-gray-100 pt-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Notas</p>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $certification->notes }}</p>
                </div>
            @endif

            <div class="border-t border-gray-100 pt-4 flex items-center gap-4 text-xs text-gray-400">
                <span>Registrada {{ $certification->created_at->diffForHumans() }}</span>
                @if ($certification->updated_at->ne($certification->created_at))
                    <span>· Actualizada {{ $certification->updated_at->diffForHumans() }}</span>
                @endif
            </div>
        </div>

    </div>

</x-layouts.cmms>
