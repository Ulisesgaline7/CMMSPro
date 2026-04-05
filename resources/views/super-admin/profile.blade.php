<x-layouts.super-admin title="Mi Perfil" breadcrumb="Mi Perfil">

    <div class="p-6 max-w-2xl space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold" style="color:#0f172a; font-family:'Manrope',sans-serif;">Mi Perfil</h1>
            <p class="text-sm mt-1" style="color:#64748b;">Administra tu información personal y contraseña</p>
        </div>

        {{-- Avatar + info --}}
        <div class="bg-white rounded-xl border p-6 flex items-center gap-5" style="border-color:#e2e8f0;">
            @php
                $user = auth()->user();
                $initials = strtoupper(substr($user->name, 0, 1)) . strtoupper(substr(strstr($user->name, ' ') ?: ' ', 1, 1));
            @endphp
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white text-xl font-black shrink-0"
                 style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                {{ $initials }}
            </div>
            <div>
                <p class="text-lg font-black" style="color:#0f172a;">{{ $user->name }}</p>
                <p class="text-sm" style="color:#64748b;">{{ $user->email }}</p>
                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider"
                      style="background:#ede9fe; color:#6d28d9;">
                    <i data-lucide="shield-check" class="w-2.5 h-2.5"></i>
                    Super Admin
                </span>
            </div>
        </div>

        {{-- Información de perfil --}}
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#e2e8f0;">
            <div class="px-6 py-4 border-b" style="border-color:#f1f5f9;">
                <p class="text-sm font-bold" style="color:#0f172a;">Información de Perfil</p>
                <p class="text-xs mt-0.5" style="color:#94a3b8;">Actualiza tu nombre y correo electrónico</p>
            </div>
            <form method="POST" action="{{ route('super-admin.profile.update') }}" class="p-6 space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-bold mb-1.5" style="color:#475569;">Nombre completo</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3 py-2.5 text-sm border rounded-lg outline-none transition-all"
                           style="border-color:#e2e8f0;"
                           onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'"
                           onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    @error('name')
                        <p class="text-xs mt-1" style="color:#ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1.5" style="color:#475569;">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3 py-2.5 text-sm border rounded-lg outline-none transition-all"
                           style="border-color:#e2e8f0;"
                           onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'"
                           onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    @error('email')
                        <p class="text-xs mt-1" style="color:#ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-2">
                    @if(session('success') && !session()->has('password_success'))
                        <p class="text-xs font-semibold" style="color:#16a34a;">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5 inline mr-1"></i>
                            {{ session('success') }}
                        </p>
                    @else
                        <div></div>
                    @endif
                    <button type="submit"
                            class="px-5 py-2 text-sm font-bold rounded-lg text-white transition-colors"
                            style="background:#6366f1;">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>

        {{-- Cambiar contraseña --}}
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color:#e2e8f0;">
            <div class="px-6 py-4 border-b" style="border-color:#f1f5f9;">
                <p class="text-sm font-bold" style="color:#0f172a;">Cambiar Contraseña</p>
                <p class="text-xs mt-0.5" style="color:#94a3b8;">Usa una contraseña segura de al menos 8 caracteres</p>
            </div>
            <form method="POST" action="{{ route('super-admin.profile.password') }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold mb-1.5" style="color:#475569;">Contraseña actual</label>
                    <input type="password" name="current_password" required
                           class="w-full px-3 py-2.5 text-sm border rounded-lg outline-none transition-all"
                           style="border-color:#e2e8f0;"
                           onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'"
                           onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    @error('current_password')
                        <p class="text-xs mt-1" style="color:#ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1.5" style="color:#475569;">Nueva contraseña</label>
                    <input type="password" name="password" required
                           class="w-full px-3 py-2.5 text-sm border rounded-lg outline-none transition-all"
                           style="border-color:#e2e8f0;"
                           onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'"
                           onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    @error('password')
                        <p class="text-xs mt-1" style="color:#ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1.5" style="color:#475569;">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-3 py-2.5 text-sm border rounded-lg outline-none transition-all"
                           style="border-color:#e2e8f0;"
                           onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'"
                           onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                </div>

                <div class="flex items-center justify-between pt-2">
                    @if(session('success') && session()->has('password_success'))
                        <p class="text-xs font-semibold" style="color:#16a34a;">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5 inline mr-1"></i>
                            {{ session('success') }}
                        </p>
                    @else
                        <div></div>
                    @endif
                    <button type="submit"
                            class="px-5 py-2 text-sm font-bold rounded-lg text-white transition-colors"
                            style="background:#0f172a;">
                        Actualizar Contraseña
                    </button>
                </div>
            </form>
        </div>

        {{-- Sesión info --}}
        <div class="bg-white rounded-xl border p-5" style="border-color:#e2e8f0;">
            <p class="text-xs font-bold mb-3" style="color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em;">Información de Sesión</p>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs" style="color:#94a3b8;">Última actualización</p>
                    <p class="font-semibold mt-0.5" style="color:#0f172a;">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs" style="color:#94a3b8;">Cuenta creada</p>
                    <p class="font-semibold mt-0.5" style="color:#0f172a;">{{ $user->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

    </div>

</x-layouts.super-admin>
