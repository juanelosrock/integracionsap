<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Items SAP
            <span class="ml-2 text-sm font-normal text-gray-500">{{ $items->total() }} registros</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Filtros --}}
            <form method="GET" action="{{ route('items.index') }}"
                  class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Código, descripción, ref. proveedor..."
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm">
                </div>
                <div class="min-w-[180px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sector SAP</label>
                    <select name="sector"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">Todos los sectores</option>
                        @foreach($sectores as $sector)
                            <option value="{{ $sector }}" {{ request('sector') === $sector ? 'selected' : '' }}>
                                {{ $sector }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                        Filtrar
                    </button>
                    @if(request('search') || request('sector'))
                    <a href="{{ route('items.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">
                        Limpiar
                    </a>
                    @endif
                </div>
            </form>

            {{-- Tabla --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Cód. Artículo</th>
                                <th class="px-4 py-3">Descripción</th>
                                <th class="px-4 py-3">Unidad</th>
                                <th class="px-4 py-3">Ref. Proveedor</th>
                                <th class="px-4 py-3">Sector SAP</th>
                                <th class="px-4 py-3">Ref. SAP</th>
                                <th class="px-4 py-3 text-right">Último Costo</th>
                                <th class="px-4 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-mono font-medium">{{ trim($item->codarticulo) }}</td>
                                    <td class="px-4 py-3">{{ trim($item->descripcion) }}</td>
                                    <td class="px-4 py-3">{{ trim($item->unidadmedida) }}</td>
                                    <td class="px-4 py-3">{{ trim($item->refproveedor) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded">
                                            {{ trim($item->sector_sap) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ trim($item->ref_sap) }}</td>
                                    <td class="px-4 py-3 text-right font-mono text-xs">
                                        {{ $item->ultimocoste !== null ? number_format($item->ultimocoste, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('items.show', $item->ID) }}"
                                           class="text-indigo-600 hover:underline text-xs">Ver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                        No se encontraron items.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $items->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
