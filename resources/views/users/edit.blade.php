<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Editar Usuario: {{ $user->name }}
            </h2>
            <a href="{{ route('users.show', $user) }}"
               class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Volver</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf @method('PUT')

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6">

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Datos de cuenta</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $user->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email', $user->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    <hr class="dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Perfil</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="phone" value="Teléfono" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                                :value="old('phone', $user->profile?->phone)" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="birthdate" value="Fecha de nacimiento" />
                            <x-text-input id="birthdate" name="birthdate" type="date" class="mt-1 block w-full"
                                :value="old('birthdate', $user->profile?->birthdate?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('birthdate')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="city" value="Ciudad" />
                            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full"
                                :value="old('city', $user->profile?->city)" />
                        </div>
                        <div>
                            <x-input-label for="country" value="País" />
                            <x-text-input id="country" name="country" type="text" class="mt-1 block w-full"
                                :value="old('country', $user->profile?->country)" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="address" value="Dirección" />
                            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full"
                                :value="old('address', $user->profile?->address)" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="bio" value="Biografía" />
                            <textarea id="bio" name="bio" rows="3"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-300">{{ old('bio', $user->profile?->bio) }}</textarea>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                class="rounded" {{ old('is_active', $user->profile?->is_active ?? true) ? 'checked' : '' }}>
                            <x-input-label for="is_active" value="Usuario activo" />
                        </div>
                    </div>

                    <hr class="dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Roles</h3>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($roles as $role)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                    class="rounded"
                                    {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>Guardar cambios</x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
