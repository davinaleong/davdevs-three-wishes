<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
    </div>

    <form method="POST" action="{{ route('two-factor.verify') }}" x-data="{ recovery: false }">
        @csrf

        <div x-show="! recovery">
            <x-input-label for="code" :value="__('Code')" />
            <x-text-input id="code" class="block mt-1 w-full text-center text-lg font-mono tracking-widest" 
                          type="text" 
                          name="code" 
                          placeholder="000000"
                          maxlength="6" 
                          pattern="[0-9]{6}"
                          autocomplete="one-time-code" 
                          autofocus />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div x-show="recovery">
            <x-input-label for="recovery_code" :value="__('Recovery Code')" />
            <x-text-input id="recovery_code" class="block mt-1 w-full font-mono" 
                          type="text" 
                          name="code" 
                          placeholder="Enter recovery code"
                          autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
            <input type="hidden" name="recovery" x-bind:value="recovery">
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="button" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline cursor-pointer"
                    x-show="! recovery"
                    x-on:click="
                        recovery = true;
                        $nextTick(() => { $refs.recovery_code.focus() })
                    ">
                {{ __('Use a recovery code') }}
            </button>

            <button type="button" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline cursor-pointer"
                    x-show="recovery"
                    x-on:click="
                        recovery = false;
                        $nextTick(() => { $refs.code.focus() })
                    ">
                {{ __('Use an authentication code') }}
            </button>

            <x-primary-button class="ml-4">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        // Auto-format the verification code input
        document.getElementById('code').addEventListener('input', function(e) {
            if (!document.querySelector('[name="recovery"]').value) {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
            }
        });
    </script>
</x-guest-layout>