<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Nuevo Proveedor</h2>
            <a href="{{ route('proveedores.index') }}"
               class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">Volver</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('proveedores.store') }}">
                @csrf
                @include('proveedores._form')
                <div class="mt-4 flex justify-end">
                    <x-primary-button>Crear Proveedor</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
