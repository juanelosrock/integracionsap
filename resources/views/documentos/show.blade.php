<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $documento->numero }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Creado por {{ $documento->creador?->name ?? 'Sistema' }}
                    el {{ $documento->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="flex gap-2 items-center">
                {{-- Cambiar estado --}}
                @if($documento->estado !== 'enviado')
                    <form method="POST" action="{{ route('documentos.estado', $documento) }}"
                          class="flex gap-2 items-center">
                        @csrf @method('PATCH')
                        <select name="estado"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-200 rounded text-sm">
                            <option value="borrador"   {{ $documento->estado === 'borrador'   ? 'selected' : '' }}>Borrador</option>
                            <option value="confirmado" {{ $documento->estado === 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                            <option value="enviado"    {{ $documento->estado === 'enviado'    ? 'selected' : '' }}>Enviado</option>
                        </select>
                        <button type="submit"
                                class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700">
                            Actualizar
                        </button>
                    </form>
                @endif
                <a href="{{ route('documentos.index') }}"
                   class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-800 rounded text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- CABECERA --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Datos del Documento</h3>
                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $documento->getEstadoBadgeClass() }}">
                        {{ ucfirst($documento->estado) }}
                    </span>
                </div>

                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">Número</dt>
                        <dd class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $documento->numero }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Fecha</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $documento->fecha->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Código Tienda</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $documento->codigo_tienda }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Nombre Tienda</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $documento->nombre_tienda }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="font-medium text-gray-500">Proveedor</dt>
                        <dd>
                            <a href="{{ route('proveedores.show', $documento->proveedor) }}"
                               class="font-medium text-indigo-600 hover:underline">
                                {{ $documento->proveedor->nombre }}
                            </a>
                            <span class="ml-2 text-xs text-gray-500 font-mono">{{ $documento->proveedor->codigo_sap }}</span>
                        </dd>
                    </div>
                    @if($documento->observaciones)
                        <div class="col-span-4">
                            <dt class="font-medium text-gray-500">Observaciones</dt>
                            <dd class="text-gray-700 dark:text-gray-300">{{ $documento->observaciones }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- CUERPO: Items --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                        Productos del Pedido
                        <span class="ml-1 text-sm font-normal text-gray-500">({{ $documento->items->count() }} ítems)</span>
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 text-gray-500">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Código</th>
                                <th class="px-4 py-3 text-left">Descripción</th>
                                <th class="px-4 py-3 text-center">Unidad</th>
                                <th class="px-4 py-3 text-right">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documento->items as $i => $line)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">
                                        {{ $line->codarticulo }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                        {{ $line->descripcion }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-500">{{ $line->unidadmedida }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-gray-100">
                                        {{ number_format($line->cantidad, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right text-sm font-semibold text-gray-600 dark:text-gray-300">
                                    Total ítems:
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $documento->items->count() }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
