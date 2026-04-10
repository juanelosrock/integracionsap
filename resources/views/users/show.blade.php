<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Detalle de Usuario
            </h2>
            <div class="flex gap-2">
                @can('users.edit')
                <a href="{{ route('users.edit', $user) }}"
                   class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Editar</a>
                @endcan
                <a href="{{ route('users.index') }}"
                   class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Información principal --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Información Personal</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">Nombre</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Email</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Teléfono</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $user->profile?->phone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Fecha de nacimiento</dt>
                        <dd class="text-gray-900 dark:text-gray-100">
                            {{ $user->profile?->birthdate?->format('d/m/Y') ?? '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Ciudad</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $user->profile?->city ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">País</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $user->profile?->country ?? '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="font-medium text-gray-500">Dirección</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $user->profile?->address ?? '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="font-medium text-gray-500">Biografía</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $user->profile?->bio ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Estado</dt>
                        <dd>
                            @if($user->profile?->is_active ?? true)
                                <span class="text-green-600 font-semibold">Activo</span>
                            @else
                                <span class="text-red-600 font-semibold">Inactivo</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Registrado</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Roles y Permisos --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Roles Asignados</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse($user->roles as $role)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ $role->name }}
                        </span>
                    @empty
                        <p class="text-gray-500 text-sm">Sin roles asignados.</p>
                    @endforelse
                </div>

                <h3 class="text-lg font-semibold mt-6 mb-4 text-gray-900 dark:text-gray-100">Permisos Directos</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse($user->permissions as $permission)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            {{ $permission->name }}
                        </span>
                    @empty
                        <p class="text-gray-500 text-sm">Sin permisos directos.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
