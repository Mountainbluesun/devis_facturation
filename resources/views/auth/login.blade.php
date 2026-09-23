<x-guest-layout>

    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-800 mb-2">📄 Invoicing & Quotes</h1>
        <p class="text-sm text-gray-600">
            Quote and invoice management app built by <strong>Jeremy Lebrun</strong> — designed for small
            businesses and freelancers: quote creation, conversion to invoice with legal sequential numbering,
            payment tracking, PDF export.
        </p>
        <a href="https://github.com/Mountainbluesun/devis_facturation" target="_blank"
           class="inline-block mt-2 text-sm text-blue-600 hover:underline">
            View source on GitHub →
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
        <h2 class="text-sm font-semibold text-gray-700 mb-3">⚙️ Tech Stack & DevOps Workflow</h2>
        <ul class="text-xs text-gray-500 space-y-2">
            <li><strong>Laravel & Blade:</strong> Laravel 13, Eloquent ORM, business logic in dedicated services (legal numbering, quote-to-invoice conversion)</li>
            <li><strong>VPS Deployment:</strong> Infomaniak VPS (Ubuntu 24.04), Nginx, PHP-FPM, SQLite</li>
            <li><strong>Security & SSL:</strong> HTTPS (Let's Encrypt), Laravel Breeze authentication, CSRF protection</li>
            <li><strong>Workflow & DevOps:</strong> Git (GitHub → VPS), Composer, npm/Vite, versioned migrations</li>
            <li><strong>Tools & IDE:</strong> PyCharm with PHP plugin, PHPUnit tests</li>
            <li><strong>Key Features:</strong> Gapless legal numbering, quote-to-invoice conversion, partial payments with automatic "paid" status, PDF generation (DomPDF)</li>
        </ul>
    </div>

</x-guest-layout>
