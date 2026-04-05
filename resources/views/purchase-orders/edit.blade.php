<x-layouts.cmms :title="'Editar ' . $purchaseOrder->code" :headerTitle="'Editar ' . $purchaseOrder->code">

<div class="p-8 max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('purchase-orders.show', $purchaseOrder) }}"
           class="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-[#002046] font-headline tracking-tight">Editar Orden de Compra</h2>
            <p class="text-sm text-gray-400 mt-0.5 font-mono">{{ $purchaseOrder->code }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder) }}" class="space-y-6"
          x-data="{
              priority: '{{ old('priority', $purchaseOrder->priority->value) }}',
              items: {{ json_encode(old('items') ? array_map(fn($i) => ['description' => $i['description'] ?? '', 'part_number' => $i['part_number'] ?? '', 'quantity' => $i['quantity'] ?? 1, 'unit' => $i['unit'] ?? 'pz', 'unit_price' => $i['unit_price'] ?? 0], old('items')) : $purchaseOrder->items->map(fn($i) => ['description' => $i->description, 'part_number' => $i->part_number ?? '', 'quantity' => (float) $i->quantity, 'unit' => $i->unit, 'unit_price' => (float) $i->unit_price])->values()->toArray()) }},
              get total() {
                  return this.items.reduce((sum, item) => sum + (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0), 0);
              },
              addItem() {
                  this.items.push({ description: '', part_number: '', quantity: 1, unit: 'pz', unit_price: 0 });
              },
              removeItem(index) {
                  if (this.items.length > 1) this.items.splice(index, 1);
              },
              itemTotal(item) {
                  return ((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)).toFixed(2);
              }
          }">
        @csrf
        @method('PATCH')

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ── Proveedor ─────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500">Información del Proveedor</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nombre del Proveedor <span class="text-red-500">*</span></label>
                    <input type="text" name="supplier_name" value="{{ old('supplier_name', $purchaseOrder->supplier_name) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] @error('supplier_name') border-red-400 @enderror">
                    @error('supplier_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Contacto del Proveedor</label>
                    <input type="text" name="supplier_contact" value="{{ old('supplier_contact', $purchaseOrder->supplier_contact) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
                </div>
            </div>
        </div>

        {{-- ── Detalles de la OC ──────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500">Detalles de la Orden</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Orden de Trabajo (opcional)</label>
                    <select name="work_order_id"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white">
                        <option value="">Sin OT vinculada</option>
                        @foreach ($workOrders as $wo)
                            <option value="{{ $wo->id }}" {{ old('work_order_id', $purchaseOrder->work_order_id) == $wo->id ? 'selected' : '' }}>
                                {{ $wo->code }} – {{ $wo->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha Entrega Est.</label>
                        <input type="date" name="expected_delivery"
                               value="{{ old('expected_delivery', optional($purchaseOrder->expected_delivery)->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Moneda</label>
                        <select name="currency"
                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white">
                            <option value="MXN" {{ old('currency', $purchaseOrder->currency) === 'MXN' ? 'selected' : '' }}>MXN</option>
                            <option value="USD" {{ old('currency', $purchaseOrder->currency) === 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ old('currency', $purchaseOrder->currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Estado</label>
                    <select name="status"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] bg-white">
                        @foreach (\App\Enums\PurchaseOrderStatus::cases() as $s)
                            <option value="{{ $s->value }}" {{ old('status', $purchaseOrder->status->value) === $s->value ? 'selected' : '' }}>
                                {{ $s->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha de Recepción</label>
                    <input type="date" name="received_at"
                           value="{{ old('received_at', optional($purchaseOrder->received_at)->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046]">
                </div>
            </div>

            {{-- Priority buttons --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-2">Prioridad <span class="text-red-500">*</span></label>
                <div class="flex flex-wrap gap-2">
                    @foreach ([['value' => 'low', 'label' => 'Baja', 'active' => 'bg-gray-100 text-gray-700 border-gray-300', 'inactive' => 'border-gray-200 text-gray-400'], ['value' => 'medium', 'label' => 'Media', 'active' => 'bg-blue-50 text-blue-700 border-blue-300', 'inactive' => 'border-gray-200 text-gray-400'], ['value' => 'high', 'label' => 'Alta', 'active' => 'bg-orange-50 text-orange-700 border-orange-300', 'inactive' => 'border-gray-200 text-gray-400'], ['value' => 'urgent', 'label' => 'Urgente', 'active' => 'bg-red-50 text-red-700 border-red-300', 'inactive' => 'border-gray-200 text-gray-400']] as $p)
                        <button type="button"
                                @click="priority = '{{ $p['value'] }}'"
                                :class="priority === '{{ $p['value'] }}' ? '{{ $p['active'] }} font-bold' : '{{ $p['inactive'] }} hover:border-gray-300'"
                                class="px-4 py-1.5 text-sm rounded-lg border transition-all">
                            {{ $p['label'] }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="priority" :value="priority">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Notas</label>
                <textarea name="notes" rows="3"
                          class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#002046]/20 focus:border-[#002046] resize-none">{{ old('notes', $purchaseOrder->notes) }}</textarea>
            </div>
        </div>

        {{-- ── Artículos ─────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500">Artículos <span class="text-red-500">*</span></h3>
                <button type="button" @click="addItem()"
                        class="flex items-center gap-1.5 text-xs font-semibold text-[#002046] hover:text-[#1b365d] transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Agregar artículo
                </button>
            </div>

            @error('items')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-2 pr-3 text-xs font-bold uppercase tracking-wider text-gray-400 w-48">Descripción</th>
                            <th class="text-left py-2 px-3 text-xs font-bold uppercase tracking-wider text-gray-400 w-32">No. Parte</th>
                            <th class="text-left py-2 px-3 text-xs font-bold uppercase tracking-wider text-gray-400 w-20">Cant.</th>
                            <th class="text-left py-2 px-3 text-xs font-bold uppercase tracking-wider text-gray-400 w-20">Unidad</th>
                            <th class="text-left py-2 px-3 text-xs font-bold uppercase tracking-wider text-gray-400 w-28">Precio Unit.</th>
                            <th class="text-right py-2 pl-3 text-xs font-bold uppercase tracking-wider text-gray-400 w-28">Total</th>
                            <th class="w-8"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="py-2 pr-3">
                                    <input type="text" x-bind:name="'items[' + index + '][description]'" x-model="item.description"
                                           class="w-full px-2 py-1.5 text-sm border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-[#002046]/20 focus:border-[#002046]"
                                           placeholder="Descripción">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" x-bind:name="'items[' + index + '][part_number]'" x-model="item.part_number"
                                           class="w-full px-2 py-1.5 text-sm border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-[#002046]/20 focus:border-[#002046]"
                                           placeholder="PN-000">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="number" x-bind:name="'items[' + index + '][quantity]'" x-model="item.quantity"
                                           min="0.01" step="0.01"
                                           class="w-full px-2 py-1.5 text-sm border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-[#002046]/20 focus:border-[#002046]">
                                </td>
                                <td class="py-2 px-3">
                                    <select x-bind:name="'items[' + index + '][unit]'" x-model="item.unit"
                                            class="w-full px-2 py-1.5 text-sm border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-[#002046]/20 focus:border-[#002046] bg-white">
                                        <option value="pz">pz</option>
                                        <option value="kg">kg</option>
                                        <option value="lt">lt</option>
                                        <option value="m">m</option>
                                        <option value="caja">caja</option>
                                        <option value="par">par</option>
                                        <option value="rollo">rollo</option>
                                        <option value="juego">juego</option>
                                    </select>
                                </td>
                                <td class="py-2 px-3">
                                    <input type="number" x-bind:name="'items[' + index + '][unit_price]'" x-model="item.unit_price"
                                           min="0" step="0.01"
                                           class="w-full px-2 py-1.5 text-sm border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-[#002046]/20 focus:border-[#002046]">
                                </td>
                                <td class="py-2 pl-3 text-right">
                                    <span class="text-sm font-medium text-gray-700" x-text="'$' + itemTotal(item)"></span>
                                </td>
                                <td class="py-2 pl-2">
                                    <button type="button" @click="removeItem(index)"
                                            class="p-1 rounded text-gray-300 hover:text-red-500 transition-colors"
                                            x-show="items.length > 1">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-200">
                            <td colspan="5" class="pt-3 pr-3 text-right text-sm font-bold text-gray-600">Total:</td>
                            <td class="pt-3 pl-3 text-right text-sm font-extrabold text-[#002046]" x-text="'$' + total.toFixed(2)"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- ── Actions ───────────────────────────────── --}}
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}"
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
