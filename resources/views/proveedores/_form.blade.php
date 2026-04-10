<div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6">

    {{-- Datos SAP e identificación --}}
    <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Identificación</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <x-input-label for="codigo_sap" value="Código SAP *" />
                <x-text-input id="codigo_sap" name="codigo_sap" type="text" class="mt-1 block w-full"
                    :value="old('codigo_sap', $proveedor->codigo_sap ?? '')" required placeholder="Ej: PROV-001" />
                <x-input-error :messages="$errors->get('codigo_sap')" class="mt-2" />
            </div>
            <div class="sm:col-span-2">
                <x-input-label for="nombre" value="Nombre / Razón Social *" />
                <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full"
                    :value="old('nombre', $proveedor->nombre ?? '')" required />
                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="nit" value="NIT / RUC" />
                <x-text-input id="nit" name="nit" type="text" class="mt-1 block w-full"
                    :value="old('nit', $proveedor->nit ?? '')" placeholder="Ej: 900123456-7" />
                <x-input-error :messages="$errors->get('nit')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                    :value="old('email', $proveedor->email ?? '')" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="telefono" value="Teléfono" />
                <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full"
                    :value="old('telefono', $proveedor->telefono ?? '')" />
            </div>
        </div>
    </div>

    <hr class="dark:border-gray-600">

    {{-- Ubicación --}}
    <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Ubicación</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-3">
                <x-input-label for="direccion" value="Dirección" />
                <x-text-input id="direccion" name="direccion" type="text" class="mt-1 block w-full"
                    :value="old('direccion', $proveedor->direccion ?? '')" />
            </div>
            <div>
                <x-input-label for="ciudad" value="Ciudad" />
                <x-text-input id="ciudad" name="ciudad" type="text" class="mt-1 block w-full"
                    :value="old('ciudad', $proveedor->ciudad ?? '')" />
            </div>
            <div>
                <x-input-label for="pais" value="País" />
                <x-text-input id="pais" name="pais" type="text" class="mt-1 block w-full"
                    :value="old('pais', $proveedor->pais ?? 'Colombia')" />
            </div>
        </div>
    </div>

    <hr class="dark:border-gray-600">

    {{-- Contacto --}}
    <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Contacto Principal</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <x-input-label for="contacto" value="Nombre" />
                <x-text-input id="contacto" name="contacto" type="text" class="mt-1 block w-full"
                    :value="old('contacto', $proveedor->contacto ?? '')" />
            </div>
            <div>
                <x-input-label for="cargo_contacto" value="Cargo" />
                <x-text-input id="cargo_contacto" name="cargo_contacto" type="text" class="mt-1 block w-full"
                    :value="old('cargo_contacto', $proveedor->cargo_contacto ?? '')" />
            </div>
            <div>
                <x-input-label for="telefono_contacto" value="Teléfono Directo" />
                <x-text-input id="telefono_contacto" name="telefono_contacto" type="text" class="mt-1 block w-full"
                    :value="old('telefono_contacto', $proveedor->telefono_contacto ?? '')" />
            </div>
        </div>
    </div>

    <hr class="dark:border-gray-600">

    {{-- Estado --}}
    <div class="flex items-center gap-3">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" id="is_active" name="is_active" value="1" class="rounded"
            {{ old('is_active', $proveedor->is_active ?? true) ? 'checked' : '' }}>
        <x-input-label for="is_active" value="Proveedor activo" />
    </div>

</div>
