<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Devis & Facturation')</title>
    @vite('resources/css/app.css')
</head>
@stack('scripts')
<body class="bg-gray-50 text-gray-900">

    <nav class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <a href="{{ url('/') }}" class="font-semibold text-lg">Devis & Facturation</a>
            <div class="flex gap-4 text-sm">
                <a href="{{ route('clients.index') }}" class="hover:text-blue-600">Clients</a>
                <a href="{{ route('documents.index') }}" class="hover:text-blue-600">Documents</a>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-6 py-8">

        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')

    </main>

</body>
</html>
