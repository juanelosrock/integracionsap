<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Serie: {{ $serie->serie }}
            </h2>
            <a href="{{ route('series.index') }}"
               class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Info principal --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ $serie->descripcion ?? 'Sin descripción' }}
                </h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">Serie</dt>
                        <dd class="font-mono font-bold text-gray-900 dark:text-gray-100">{{ $serie->serie }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Punto SAP</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $serie->punto_sap ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Empresa SAP</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $serie->empresa_sap ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Centro SAP</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $serie->centro_sap ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">CEBE SAP</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $serie->cebe_sap ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">CECO SAP</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $serie->ceco_sap ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Zona SAP</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $serie->zona_sap ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Storage Loc. SAP</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $serie->storageloc_sap ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Ubicación --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Ubicación</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">Ciudad</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $serie->nomciudad_sap ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Cód. Ciudad SAP</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $serie->codciudad_sap ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Código Postal SAP</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $serie->codpostal_sap ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Región SAP</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $serie->region_sap ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Cuentas contables --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Cuentas Contables</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">Cuenta Venta SAP</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $serie->cuentaventa_sap ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Cuenta Caja SAP</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $serie->cuentacaja_sap ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">GL Account SAP</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $serie->glaccount_sap ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">GL Account NC SAP</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $serie->glaccountnc_sap ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

        </div>
    </div>
</x-app-layout>
