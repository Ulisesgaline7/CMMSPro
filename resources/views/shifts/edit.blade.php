<x-layouts.cmms title="Editar Turno" headerTitle="Turnos de Técnicos">

    <div class="p-6 max-w-2xl mx-auto space-y-5">

        <div class="flex items-center gap-3">
            <a href="{{ route('shifts.show', $shift) }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Editar Turno</h2>
        </div>

        <form action="{{ route('shifts.update', $shift) }}" method="POST"
              x-data="{ type: '{{ old('type', $shift->type->value) }}' }"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
            @csrf
            @method('PATCH')

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            {{-- Técnico --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Técnico *</label>
                <select name="user_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                    @foreach ($technicians as $tech)
                        <option value="{{ $tech->id }}" @selected(old('user_id', $shift->user_id) == $tech->id)>
                            {{ $tech->name }}@if($tech->employee_code) ({{ $tech->employee_code }})@endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Nombre --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nombre del turno *</label>
                <input type="text" name="name" value="{{ old('name', $shift->name) }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
            </div>

            {{-- Tipo --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tipo *</label>
                <div class="grid grid-cols-4 gap-2">
                    @foreach ([
                        ['morning',   'Mañana',        'sunrise',  'border-amber-300 bg-amber-50 text-amber-700'],
                        ['afternoon', 'Tarde',         'sun',      'border-blue-300 bg-blue-50 text-blue-700'],
                        ['night',     'Noche',         'moon',     'border-indigo-300 bg-indigo-50 text-indigo-700'],
                        ['custom',    'Personalizado', 'clock',    'border-gray-300 bg-gray-50 text-gray-700'],
                    ] as [$val, $lbl, $icon, $activeClass])
                        <button type="button" @click="type = '{{ $val }}'"
                                :class="type === '{{ $val }}' ? '{{ $activeClass }} border-2 shadow-sm' : 'border border-gray-200 text-gray-400 hover:border-gray-300'"
                                class="flex flex-col items-center gap-1.5 px-3 py-3 rounded-lg text-xs font-semibold transition-all">
                            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                            {{ $lbl }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="type" :value="type">
            </div>

            {{-- Estado --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Estado *</label>
                <select name="status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                    @foreach (\App\Enums\ShiftStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(old('status', $shift->status->value) === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fecha --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Fecha *</label>
                <input type="date" name="date" value="{{ old('date', $shift->date->format('Y-m-d')) }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
            </div>

            {{-- Horario --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Hora inicio *</label>
                    <input type="time" name="start_time" value="{{ old('start_time', $shift->start_time) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Hora fin *</label>
                    <input type="time" name="end_time" value="{{ old('end_time', $shift->end_time) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>
            </div>

            {{-- Notas --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Notas</label>
                <textarea name="notes" rows="3"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20 resize-none">{{ old('notes', $shift->notes) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-[#002046] text-white py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors">
                    Guardar Cambios
                </button>
                <a href="{{ route('shifts.show', $shift) }}"
                   class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

</x-layouts.cmms>
