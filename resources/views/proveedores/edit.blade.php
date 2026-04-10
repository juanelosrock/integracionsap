<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Editar: {{ $proveedor->nombre }}
            </h2>
            <a href="{{ route('proveedores.show', $proveedor) }}"
               class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">Volver</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('proveedores.update', $proveedor) }}">
                @csrf @method('PUT')
                @include('proveedores._form')
                <div class="mt-4 flex justify-end">
                    <x-primary-button>Guardar Cambios</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
