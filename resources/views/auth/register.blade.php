<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4" x-data="passwordToggle()">
            <x-input-label for="password" :value="__('Password')" />
            
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-10"
                                ::type="type"
                                name="password"
                                required autocomplete="new-password" />
                                
                <button type="button" @click="toggle(); renderIcon()" :title="title"
                        class="password-toggle-btn absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 hover:opacity-70">
                    <span class="lucide-icon w-5 h-5"></span>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4" x-data="passwordToggle()">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            
            <div class="relative">
                <x-text-input id="password_confirmation" class="block mt-1 w-full pr-10"
                                ::type="type"
                                name="password_confirmation" required autocomplete="new-password" />
                                
                <button type="button" @click="toggle(); renderIcon()" :title="title"
                        class="password-toggle-btn absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 hover:opacity-70">
                    <span class="lucide-icon w-5 h-5"></span>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2" style="color: #2b7fff; focus:ring-color: #2b7fff;" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
