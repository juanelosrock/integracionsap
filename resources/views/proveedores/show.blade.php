<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $proveedor->nombre }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Código SAP: <span class="font-mono">{{ $proveedor->codigo_sap }}</span></p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('proveedores.edit', $proveedor) }}"
                   class="px-4 py-2 bg-yellow-500 text-white text-sm rounded hover:bg-yellow-600">Editar</a>
                <a href="{{ route('proveedores.index') }}"
                   class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Info del proveedor --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Información General</h3>
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">NIT / RUC</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $proveedor->nit ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Email</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $proveedor->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Teléfono</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $proveedor->telefono ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Estado</dt>
                        <dd>
                            @if($proveedor->is_active)
                                <span class="text-green-600 font-semibold">Activo</span>
                            @else
                                <span class="text-red-500 font-semibold">Inactivo</span>
                            @endif
                        </dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="font-medium text-gray-500">Dirección</dt>
                        <dd class="text-gray-900 dark:text-gray-100">
                            {{ collect([$proveedor->direccion, $proveedor->ciudad, $proveedor->pais])->filter()->join(', ') ?: '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Contacto</dt>
                        <dd class="text-gray-900 dark:text-gray-100">
                            {{ $proveedor->contacto ?? '—' }}
                            @if($proveedor->cargo_contacto)
                                <span class="text-xs text-gray-500 block">{{ $proveedor->cargo_contacto }}</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Tel. Contacto</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $proveedor->telefono_contacto ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- ===== SECCIÓN DE PRODUCTOS ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Productos asociados --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            Productos Asociados
                            <span class="ml-1 text-sm font-normal text-gray-500">({{ $items->count() }})</span>
                        </h3>
                    </div>

                    @if($items->isEmpty())
                        <p class="text-sm text-gray-500 py-4 text-center">
                            Sin productos asociados. Usa el buscador para agregar.
                        </p>
                    @else
                        <div class="overflow-y-auto max-h-[460px] space-y-1 pr-1">
                            @foreach($items as $item)
                                <div class="flex justify-between items-center px-3 py-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700 group">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ trim($item->descripcion) }}
                                        </p>
                                        <p class="text-xs text-gray-500 font-mono">
                                            {{ trim($item->codarticulo) }}
                                            @if(trim($item->refproveedor))
                                                · Ref: {{ trim($item->refproveedor) }}
                                            @endif
                                        </p>
                                    </div>
                                    <form method="POST"
                                          action="{{ route('proveedores.items.remove', [$proveedor, $item->ID]) }}"
                                          onsubmit="return confirm('¿Quitar este producto?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="ml-2 text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition text-xs shrink-0">
                                            ✕ Quitar
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Buscador para agregar productos --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Agregar Productos
                    </h3>

                    {{-- Formulario de búsqueda --}}
                    <form method="GET" action="{{ route('proveedores.show', $proveedor) }}"
                          class="flex gap-2 mb-4" id="formBusqueda">
                        <input type="text" name="item_search" id="item_search"
                               value="{{ $searchQuery }}"
                               placeholder="Nombre, código o ref. proveedor..."
                               class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm">
                        <button type="submit"
                                class="px-3 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                            Buscar
                        </button>
                        @if($searchQuery)
                            <a href="{{ route('proveedores.show', $proveedor) }}"
                               class="px-3 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">✕</a>
                        @endif
                    </form>

                    {{-- Resultados de búsqueda --}}
                    @if($searchQuery !== null)
                        @if($searchResults->isEmpty())
                            <p class="text-sm text-gray-500 text-center py-4">
                                No se encontraron productos con "{{ $searchQuery }}".
                            </p>
                        @else
                            <form method="POST" action="{{ route('proveedores.items.add', $proveedor) }}">
                                @csrf
                                <input type="hidden" name="item_search_back" value="{{ $searchQuery }}">

                                {{-- Seleccionar todos --}}
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs text-gray-500">{{ $searchResults->count() }} resultado(s)</span>
                                    <label class="flex items-center gap-1 text-xs text-gray-600 cursor-pointer">
                                        <input type="checkbox" id="selectAll" class="rounded">
                                        Seleccionar todos
                                    </label>
                                </div>

                                <div class="overflow-y-auto max-h-[340px] space-y-1 pr-1 mb-3">
                                    @foreach($searchResults as $item)
                                        @php $yaAsociado = in_array($item->ID, $itemIds); @endphp
                                        <label class="flex items-start gap-2 px-3 py-2 rounded cursor-pointer
                                            {{ $yaAsociado ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-50 dark:hover:bg-gray-700' }}">
                                            <input type="checkbox" name="item_ids[]" value="{{ $item->ID }}"
                                                   class="item-checkbox mt-0.5 rounded"
                                                   {{ $yaAsociado ? 'disabled checked' : '' }}>
                                            <div class="min-w-0">
                                                <p class="text-sm text-gray-900 dark:text-gray-100 truncate">
                                                    {{ trim($item->descripcion) }}
                                                    @if($yaAsociado)
                                                        <span class="ml-1 text-xs text-green-600">(ya asociado)</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-gray-500 font-mono">
                                                    {{ trim($item->codarticulo) }}
                                                    @if(trim($item->refproveedor))
                                                        · Ref: {{ trim($item->refproveedor) }}
                                                    @endif
                                                    · {{ trim($item->sector_sap) }}
                                                </p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <button type="submit"
                                        class="w-full py-2 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700">
                                    Agregar seleccionados
                                </button>
                            </form>

                            <script>
                                document.getElementById('selectAll').addEventListener('change', function() {
                                    document.querySelectorAll('.item-checkbox:not(:disabled)')
                                        .forEach(cb => cb.checked = this.checked);
                                });
                            </script>
                        @endif
                    @else
                        <p class="text-sm text-gray-400 text-center py-8">
                            Ingresa un término de búsqueda para encontrar productos.
                        </p>
                    @endif
                </div>

            </div>
            {{-- ===== FIN SECCIÓN PRODUCTOS ===== --}}

        </div>
    </div>
</x-app-layout>
