<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Proveedores
                <span class="ml-2 text-sm font-normal text-gray-500">{{ $proveedores->total() }} registros</span>
            </h2>
            <a href="{{ route('proveedores.create') }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                + Nuevo Proveedor
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

            {{-- Búsqueda --}}
            <form method="GET" action="{{ route('proveedores.index') }}"
                  class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 flex gap-3 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nombre, código SAP, NIT, ciudad..."
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                        Buscar
                    </button>
                    @if(request('search'))
                        <a href="{{ route('proveedores.index') }}"
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
                                <th class="px-4 py-3">Código SAP</th>
                                <th class="px-4 py-3">Nombre</th>
                                <th class="px-4 py-3">NIT</th>
                                <th class="px-4 py-3">Ciudad</th>
                                <th class="px-4 py-3">Contacto</th>
                                <th class="px-4 py-3 text-center">Productos</th>
                                <th class="px-4 py-3 text-center">Estado</th>
                                <th class="px-4 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proveedores as $proveedor)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-mono font-medium text-xs">{{ $proveedor->codigo_sap }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $proveedor->nombre }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $proveedor->nit ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ $proveedor->ciudad ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        @if($proveedor->contacto)
                                            <span>{{ $proveedor->contacto }}</span>
                                            @if($proveedor->cargo_contacto)
                                                <span class="text-xs text-gray-500 block">{{ $proveedor->cargo_contacto }}</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold
                                            {{ $proveedor->proveedor_items_count > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $proveedor->proveedor_items_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($proveedor->is_active)
                                            <span class="text-xs text-green-600 font-semibold">Activo</span>
                                        @else
                                            <span class="text-xs text-red-500 font-semibold">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-3">
                                            <a href="{{ route('proveedores.show', $proveedor) }}"
                                               class="text-indigo-600 hover:underline text-xs">Ver</a>
                                            <a href="{{ route('proveedores.edit', $proveedor) }}"
                                               class="text-yellow-600 hover:underline text-xs">Editar</a>
                                            <form method="POST" action="{{ route('proveedores.destroy', $proveedor) }}"
                                                  onsubmit="return confirm('¿Eliminar proveedor {{ $proveedor->nombre }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline text-xs">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                        No se encontraron proveedores.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $proveedores->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
