<x-layouts.cmms title="Programar Turno" headerTitle="Turnos de Técnicos">

    <div class="p-6 max-w-2xl mx-auto space-y-5">

        <div class="flex items-center gap-3">
            <a href="{{ route('shifts.index') }}" class="text-gray-400 hover:text-[#002046] transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Programar Turno</h2>
        </div>

        <form action="{{ route('shifts.store') }}" method="POST"
              x-data="{
                  type: 'morning',
                  setType(t) {
                      this.type = t;
                      const times = {morning: ['06:00','14:00'], afternoon: ['14:00','22:00'], night: ['22:00','06:00'], custom: ['08:00','16:00']};
                      this.$refs.start.value = times[t][0];
                      this.$refs.end.value = times[t][1];
                  }
              }"
              class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
            @csrf

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
                    <option value="">Seleccionar técnico...</option>
                    @foreach ($technicians as $tech)
                        <option value="{{ $tech->id }}" @selected(old('user_id') == $tech->id)>
                            {{ $tech->name }}@if($tech->employee_code) ({{ $tech->employee_code }})@endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Nombre --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nombre del turno *</label>
                <input type="text" name="name" value="{{ old('name', 'Turno Mañana') }}" required
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
                        <button type="button" @click="setType('{{ $val }}')"
                                :class="type === '{{ $val }}' ? '{{ $activeClass }} border-2 shadow-sm' : 'border border-gray-200 text-gray-400 hover:border-gray-300'"
                                class="flex flex-col items-center gap-1.5 px-3 py-3 rounded-lg text-xs font-semibold transition-all">
                            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                            {{ $lbl }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="type" :value="type">
            </div>

            {{-- Fecha --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Fecha *</label>
                <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
            </div>

            {{-- Horario --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Hora inicio *</label>
                    <input type="time" name="start_time" x-ref="start" value="{{ old('start_time', '06:00') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Hora fin *</label>
                    <input type="time" name="end_time" x-ref="end" value="{{ old('end_time', '14:00') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20">
                </div>
            </div>

            {{-- Notas --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Notas</label>
                <textarea name="notes" rows="3"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#002046]/20 resize-none">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-[#002046] text-white py-2.5 rounded-lg text-sm font-bold tracking-wide hover:bg-[#1b365d] transition-colors">
                    Programar Turno
                </button>
                <a href="{{ route('shifts.index') }}"
                   class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

</x-layouts.cmms>
