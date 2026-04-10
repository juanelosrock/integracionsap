<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Roles y Permisos
            </h2>
            @can('roles.create')
            <a href="{{ route('roles.create') }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Nuevo Rol
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($roles as $role)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $role->name }}</h3>
                            <span class="text-sm text-gray-500">{{ $role->users_count }} usuario(s)</span>
                        </div>

                        <div class="flex flex-wrap gap-1 mb-4 min-h-[40px]">
                            @foreach($role->permissions->take(6) as $permission)
                                <span class="px-2 py-0.5 text-xs bg-purple-100 text-purple-800 rounded">
                                    {{ $permission->name }}
                                </span>
                            @endforeach
                            @if($role->permissions->count() > 6)
                                <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">
                                    +{{ $role->permissions->count() - 6 }} más
                                </span>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            @can('roles.edit')
                            <a href="{{ route('roles.edit', $role) }}"
                               class="text-sm text-yellow-600 hover:underline">Editar</a>
                            @endcan
                            @can('roles.delete')
                            @if(!in_array($role->name, ['admin', 'super-admin']))
                            <form method="POST" action="{{ route('roles.destroy', $role) }}"
                                  onsubmit="return confirm('¿Eliminar el rol {{ $role->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:underline">Eliminar</button>
                            </form>
                            @endif
                            @endcan
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 col-span-3">No hay roles creados.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
