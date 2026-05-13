<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Bandeja de Entrada Gmail
            </h2>
            @if($token)
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        Conectado como <span class="font-medium text-gray-700 dark:text-gray-300">{{ $token->email }}</span>
                    </span>
                    <form method="POST" action="{{ route('gmail.disconnect') }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('¿Desconectar cuenta de Gmail?')"
                                class="px-3 py-1.5 bg-red-100 text-red-700 text-sm rounded hover:bg-red-200">
                            Desconectar
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Alertas --}}
            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error') || $error)
                <div class="p-4 bg-red-100 border border-red-400 text-red-800 rounded">
                    {{ session('error') ?? $error }}
                </div>
            @endif

            {{-- Sin cuenta conectada --}}
            @if(! $token)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-10 text-center">
                    <svg class="mx-auto mb-4 w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">
                        Conecta tu cuenta de Gmail
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Autoriza el acceso para ver los correos entrantes desde esta interfaz.
                    </p>
                    <a href="{{ route('gmail.redirect') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4.236l-8 4.882-8-4.882V6h16v2.236z"/>
                        </svg>
                        Conectar con Google
                    </a>
                </div>

            {{-- Lista de correos --}}
            @elseif($emails->isEmpty())
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-10 text-center">
                    <p class="text-gray-500">No hay correos en la bandeja de entrada.</p>
                </div>

            @else
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <span class="text-sm text-gray-500">{{ $emails->count() }} correos recientes</span>
                        <a href="{{ route('gmail.index') }}"
                           class="text-sm text-indigo-600 hover:text-indigo-800">Actualizar</a>
                    </div>

                    <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($emails as $email)
                            <li class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition
                                       {{ $email['unread'] ? 'bg-indigo-50 dark:bg-indigo-900/20' : '' }}">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-{{ $email['unread'] ? 'semibold' : 'medium' }} text-gray-900 dark:text-gray-100 truncate">
                                            {{ $email['subject'] ?: '(Sin asunto)' }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate mt-0.5">
                                            {{ $email['from'] }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1 line-clamp-2">
                                            {{ $email['snippet'] }}
                                        </p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <p class="text-xs text-gray-400 whitespace-nowrap">{{ $email['date'] }}</p>
                                        @if($email['unread'])
                                            <span class="inline-block mt-1 w-2 h-2 rounded-full bg-indigo-500"></span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
