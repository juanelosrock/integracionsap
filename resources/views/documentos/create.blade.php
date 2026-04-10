<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Nuevo Documento de Pedido
            </h2>
            <a href="{{ route('documentos.index') }}"
               class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">Volver</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8"
             x-data="documentoForm('{{ route('documentos.items-proveedor', ':id') }}')">

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-800 rounded text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @error('items')
                <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded text-sm">
                    {{ $message }}
                </div>
            @enderror

            <form method="POST" action="{{ route('documentos.store') }}" id="formDocumento">
                @csrf

                {{-- CABECERA --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Cabecera del Documento</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <x-input-label for="fecha" value="Fecha *" />
                            <x-text-input id="fecha" name="fecha" type="date" class="mt-1 block w-full"
                                :value="old('fecha', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('fecha')" class="mt-1" />
                        </div>

                        <div class="sm:col-span-1">
                            <x-input-label for="proveedor_id" value="Proveedor *" />
                            <select id="proveedor_id" name="proveedor_id" required
                                    @change="cargarItems($event.target.value)"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">— Seleccionar —</option>
                                @foreach($proveedores as $p)
                                    <option value="{{ $p->id }}" {{ old('proveedor_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nombre }} ({{ $p->codigo_sap }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('proveedor_id')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="codigo_tienda" value="Código Tienda *" />
                            <x-text-input id="codigo_tienda" name="codigo_tienda" type="text" class="mt-1 block w-full"
                                :value="old('codigo_tienda')" required placeholder="Ej: T001" />
                            <x-input-error :messages="$errors->get('codigo_tienda')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="nombre_tienda" value="Nombre Tienda *" />
                            <x-text-input id="nombre_tienda" name="nombre_tienda" type="text" class="mt-1 block w-full"
                                :value="old('nombre_tienda')" required placeholder="Ej: Tienda Centro" />
                            <x-input-error :messages="$errors->get('nombre_tienda')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="observaciones" value="Observaciones" />
                        <textarea id="observaciones" name="observaciones" rows="2"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm">{{ old('observaciones') }}</textarea>
                    </div>
                </div>

                {{-- CUERPO: Items --}}
                <div class="mt-6 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Productos del Pedido
                    </h3>

                    {{-- Estado de carga --}}
                    <div x-show="cargando" class="text-center py-8 text-gray-500 text-sm">
                        Cargando productos del proveedor...
                    </div>

                    <div x-show="!cargando && items.length === 0 && !proveedorSeleccionado"
                         class="text-center py-8 text-gray-400 text-sm border-2 border-dashed rounded-lg">
                        Selecciona un proveedor para ver sus productos asociados.
                    </div>

                    <div x-show="!cargando && items.length === 0 && proveedorSeleccionado"
                         class="text-center py-8 text-yellow-600 text-sm border-2 border-dashed border-yellow-300 rounded-lg">
                        Este proveedor no tiene productos asociados. Agrégalos desde la sección de Proveedores.
                    </div>

                    <div x-show="!cargando && items.length > 0">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm text-gray-500" x-text="`${items.length} productos disponibles · ${itemsConCantidad()} seleccionados`"></span>
                            <div class="flex gap-2">
                                <button type="button" @click="seleccionarTodos()"
                                        class="text-xs text-indigo-600 hover:underline">Marcar todos</button>
                                <span class="text-gray-300">|</span>
                                <button type="button" @click="limpiarCantidades()"
                                        class="text-xs text-gray-500 hover:underline">Limpiar</button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 text-gray-500">
                                    <tr>
                                        <th class="px-3 py-2 text-left w-8"></th>
                                        <th class="px-3 py-2 text-left">Código</th>
                                        <th class="px-3 py-2 text-left">Descripción</th>
                                        <th class="px-3 py-2 text-center w-24">Unidad</th>
                                        <th class="px-3 py-2 text-center w-32">Cantidad *</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, index) in items" :key="item.id">
                                        <tr class="border-b dark:border-gray-700"
                                            :class="item.cantidad > 0 ? 'bg-indigo-50 dark:bg-indigo-900/20' : ''">
                                            <td class="px-3 py-2 text-center">
                                                <input type="checkbox"
                                                       :checked="item.cantidad > 0"
                                                       @change="toggleItem(item, $event.target.checked)"
                                                       class="rounded">
                                            </td>
                                            <td class="px-3 py-2 font-mono text-xs text-gray-600 dark:text-gray-400"
                                                x-text="item.codarticulo"></td>
                                            <td class="px-3 py-2 font-medium text-gray-900 dark:text-gray-100"
                                                x-text="item.descripcion"></td>
                                            <td class="px-3 py-2 text-center text-gray-500"
                                                x-text="item.unidadmedida"></td>
                                            <td class="px-3 py-2">
                                                {{-- Campos hidden para envío --}}
                                                <input type="hidden" :name="`items[${index}][item_id]`" :value="item.id">
                                                <input type="number"
                                                       :name="`items[${index}][cantidad]`"
                                                       x-model.number="item.cantidad"
                                                       min="0" step="0.01" placeholder="0"
                                                       class="w-full text-center border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                       :class="item.cantidad > 0 ? 'border-indigo-400 bg-white' : ''">
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <p class="mt-2 text-xs text-gray-400">
                            Solo se guardarán los ítems con cantidad mayor a 0.
                        </p>
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-3">
                    <a href="{{ route('documentos.index') }}"
                       class="px-5 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white font-medium rounded hover:bg-indigo-700 text-sm">
                        Guardar Documento
                        <span x-show="itemsConCantidad() > 0"
                              x-text="`(${itemsConCantidad()} ítems)`"
                              class="ml-1 text-indigo-200 text-xs"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('formDocumento').addEventListener('submit', function () {
            // Deshabilitar inputs de items con cantidad = 0 para no enviarlos
            document.querySelectorAll('input[type="number"][name*="[cantidad]"]').forEach(function (input) {
                const val = parseFloat(input.value);
                if (!val || val <= 0) {
                    input.disabled = true;
                    const hidden = input.closest('td')?.querySelector('input[type="hidden"]');
                    if (hidden) hidden.disabled = true;
                }
            });
        });
    });

    function documentoForm(urlTemplate) {
        return {
            items: [],
            cargando: false,
            proveedorSeleccionado: false,

            cargarItems(proveedorId) {
                this.items = [];
                this.proveedorSeleccionado = false;

                if (!proveedorId) return;

                this.proveedorSeleccionado = true;
                this.cargando = true;

                const url = urlTemplate.replace(':id', proveedorId);

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    this.items = data.map(item => ({ ...item, cantidad: 0 }));
                    this.cargando = false;
                })
                .catch(() => { this.cargando = false; });
            },

            itemsConCantidad() {
                return this.items.filter(i => i.cantidad > 0).length;
            },

            toggleItem(item, checked) {
                item.cantidad = checked ? 1 : 0;
            },

            seleccionarTodos() {
                this.items.forEach(i => { if (i.cantidad === 0) i.cantidad = 1; });
            },

            limpiarCantidades() {
                this.items.forEach(i => { i.cantidad = 0; });
            }
        };
    }
    </script>
</x-app-layout>
