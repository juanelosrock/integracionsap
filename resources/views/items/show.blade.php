<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Item: {{ trim($item->codarticulo) }}
            </h2>
            <a href="{{ route('items.index') }}"
               class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6">
                    {{ trim($item->descripcion) }}
                </h3>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">ID</dt>
                        <dd class="text-gray-900 dark:text-gray-100 font-mono">{{ $item->ID }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Código de Artículo</dt>
                        <dd class="text-gray-900 dark:text-gray-100 font-mono">{{ trim($item->codarticulo) }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Unidad de Medida</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ trim($item->unidadmedida) }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Referencia Proveedor</dt>
                        <dd class="text-gray-900 dark:text-gray-100 font-mono">{{ trim($item->refproveedor) }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Sector SAP</dt>
                        <dd>
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                {{ trim($item->sector_sap) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Referencia SAP</dt>
                        <dd class="text-gray-900 dark:text-gray-100 font-mono">{{ trim($item->ref_sap) }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Último Costo</dt>
                        <dd class="text-gray-900 dark:text-gray-100 font-mono">
                            {{ $item->ultimocoste !== null ? number_format($item->ultimocoste, 2) : '—' }}
                        </dd>
                    </div>
                </dl>

            </div>
        </div>
    </div>
</x-app-layout>
