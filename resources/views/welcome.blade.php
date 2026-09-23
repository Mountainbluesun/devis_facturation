<x-guest-layout>

    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-800 mb-2">📄 Invoicing & Quotes</h1>
        <p class="text-sm text-gray-600">
            Quote and invoice management application built by <strong>Jeremy Lebrun</strong> —
            a tool designed for small businesses and freelancers: quote creation, conversion to invoice
            with legal sequential numbering, payment tracking, PDF export.
        </p>
        <a href="https://github.com/Mountainbluesun/devis_facturation" target="_blank"
           class="inline-block mt-2 text-sm text-blue-600 hover:underline">
            View the code on GitHub →
        </a>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-8 pt-6 border-t border-gray-200">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">⚙️ Tech Stack</h2>
        <ul class="text-xs text-gray-500 space-y-1">
            <li><strong>Backend:</strong> Laravel 13, Eloquent ORM, business logic in dedicated services (legal numbering, quote-to-invoice conversion)</li>
            <li><strong>Deployment:</strong> Infomaniak VPS (Ubuntu 24.04), Nginx, PHP-FPM, SQLite</li>
            <li><strong>Security:</strong> HTTPS (Let's Encrypt), Laravel Breeze authentication, CSRF protection</li>
            <li><strong>Workflow:</strong> Git, Composer, npm/Vite, versioned migrations</li>
        </ul>
    </div>

</x-guest-layout>
