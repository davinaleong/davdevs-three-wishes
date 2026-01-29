<x-guest-layout>
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
        <div class="mt-4" x-data="passwordToggle()">
            <x-input-label for="password" :value="__('Password')" />
            
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-10"
                                ::type="type"
                                name="password"
                                required autocomplete="current-password" />
                                
                <button type="button" @click="toggle(); renderIcon()" :title="title"
                        class="password-toggle-btn absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 hover:opacity-70">
                    <span class="lucide-icon w-5 h-5"></span>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 shadow-sm focus:ring-offset-gray-800" style="color: #2b7fff; focus:ring-color: #2b7fff;" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <div>
                @if (Route::has('register'))
                    <a class="underline text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2" style="color: #2b7fff; focus:ring-color: #2b7fff;" href="{{ route('register') }}">
                        {{ __('Sign up') }}
                    </a>
                @endif
            </div>
            <div class="flex items-center">
                @if (Route::has('password.request'))
                    <a class="underline text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2" style="color: #2b7fff; focus:ring-color: #2b7fff;" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-primary-button class="ms-3">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</x-guest-layout>
