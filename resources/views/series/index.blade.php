<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Series SAP
            <span class="ml-2 text-sm font-normal text-gray-500">{{ $series->total() }} registros</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Filtro --}}
            <form method="GET" action="{{ route('series.index') }}"
                  class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[220px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Serie, descripción, empresa, ciudad..."
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                        Filtrar
                    </button>
                    @if(request('search'))
                    <a href="{{ route('series.index') }}"
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
                                <th class="px-4 py-3">Serie</th>
                                <th class="px-4 py-3">Descripción</th>
                                <th class="px-4 py-3">Empresa SAP</th>
                                <th class="px-4 py-3">Centro SAP</th>
                                <th class="px-4 py-3">Ciudad</th>
                                <th class="px-4 py-3">Punto SAP</th>
                                <th class="px-4 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($series as $serie)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-mono font-bold">{{ $serie->serie }}</td>
                                    <td class="px-4 py-3">{{ $serie->descripcion ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        @if($serie->empresa_sap)
                                            <span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded">
                                                {{ $serie->empresa_sap }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $serie->centro_sap ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ $serie->nomciudad_sap ?? '—' }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $serie->punto_sap ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('series.show', $serie->serie) }}"
                                           class="text-indigo-600 hover:underline text-xs">Ver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        No se encontraron series.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $series->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
