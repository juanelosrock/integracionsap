<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Crear Rol</h2>
            <a href="{{ route('roles.index') }}"
               class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Volver</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6">
                    <div>
                        <x-input-label for="name" value="Nombre del Rol" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full max-w-sm"
                            :value="old('name')" placeholder="ej: editor, moderador..." required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-3">Permisos</h3>
                        @foreach($permissions as $group => $groupPermissions)
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold uppercase text-gray-500 mb-2">{{ $group }}</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @foreach($groupPermissions as $permission)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="permissions[]"
                                                value="{{ $permission->name }}" class="rounded"
                                                {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>Crear Rol</x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
