<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Documentos de Pedido
                <span class="ml-2 text-sm font-normal text-gray-500">{{ $documentos->total() }} registros</span>
            </h2>
            <a href="{{ route('documentos.create') }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                + Nuevo Documento
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filtros --}}
            <form method="GET" action="{{ route('documentos.index') }}"
                  class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Número, tienda, proveedor..."
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
                    <select name="estado"
                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">Todos</option>
                        <option value="borrador"   {{ request('estado') === 'borrador'   ? 'selected' : '' }}>Borrador</option>
                        <option value="confirmado" {{ request('estado') === 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                        <option value="enviado"    {{ request('estado') === 'enviado'    ? 'selected' : '' }}>Enviado</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">Filtrar</button>
                    @if(request('search') || request('estado'))
                        <a href="{{ route('documentos.index') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">Limpiar</a>
                    @endif
                </div>
            </form>

            {{-- Tabla --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Número</th>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Proveedor</th>
                                <th class="px-4 py-3">Tienda</th>
                                <th class="px-4 py-3 text-center">Ítems</th>
                                <th class="px-4 py-3 text-center">Estado</th>
                                <th class="px-4 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documentos as $doc)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-mono font-bold text-indigo-600">
                                        <a href="{{ route('documentos.show', $doc) }}" class="hover:underline">
                                            {{ $doc->numero }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">{{ $doc->fecha->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium">{{ $doc->proveedor->nombre }}</p>
                                        <p class="text-xs text-gray-500 font-mono">{{ $doc->proveedor->codigo_sap }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p>{{ $doc->nombre_tienda }}</p>
                                        <p class="text-xs text-gray-500 font-mono">{{ $doc->codigo_tienda }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">
                                            {{ $doc->items_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $doc->getEstadoBadgeClass() }}">
                                            {{ ucfirst($doc->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-3 items-center">
                                            <a href="{{ route('documentos.show', $doc) }}"
                                               class="text-indigo-600 hover:underline text-xs">Ver</a>
                                            @if($doc->estado !== 'enviado')
                                                <form method="POST" action="{{ route('documentos.destroy', $doc) }}"
                                                      onsubmit="return confirm('¿Eliminar {{ $doc->numero }}?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline text-xs">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        No hay documentos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $documentos->links() }}</div>
            </div>

        </div>
    </div>
</x-app-layout>
