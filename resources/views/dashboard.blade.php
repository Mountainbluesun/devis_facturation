<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                    <a href="{{ route('clients.index') }}" class="text-blue-600 hover:underline text-sm">Voir les clients</a>
                    <a href="{{ route('documents.index') }}" class="text-blue-600 hover:underline text-sm">Voir les documents</a>
                </div>
            </div>

            </div>

        </div>
    </div>
</x-app-layout>
