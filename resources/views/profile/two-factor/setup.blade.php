<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Setup Two-Factor Authentication') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="max-w-xl mx-auto">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium mb-2">{{ __('Scan QR Code') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Scan this QR code with your authenticator app, then enter the 6-digit code to complete setup.') }}
                        </p>
                    </div>

                    <!-- QR Code -->
                    <div class="bg-white p-6 rounded-lg shadow-inner mb-6 text-center">
                        <div class="inline-block">
                            <img src="{{ $qrCode }}" alt="QR Code" class="mx-auto" />
                        </div>
                    </div>

                    <!-- Manual Secret -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6">
                        <h4 class="text-sm font-medium mb-2">{{ __('Manual Entry') }}</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                            {{ __('If you cannot scan the QR code, enter this code manually:') }}
                        </p>
                        <div class="bg-white dark:bg-gray-800 p-2 rounded border font-mono text-sm text-center select-all">
                            {{ $secret }}
                        </div>
                    </div>

                    <!-- Verification Form -->
                    <form method="POST" action="{{ route('two-factor.confirm') }}">
                        @csrf
                        
                        <div class="mb-6">
                            <x-input-label for="code" :value="__('Verification Code')" />
                            <x-text-input 
                                id="code" 
                                name="code" 
                                type="text" 
                                class="mt-1 block w-full text-center text-lg font-mono tracking-widest" 
                                placeholder="000000"
                                maxlength="6" 
                                pattern="[0-9]{6}"
                                required 
                                autofocus 
                                autocomplete="one-time-code" 
                            />
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('Enter the 6-digit code from your authenticator app') }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                {{ __('← Back to Profile') }}
                            </a>
                            
                            <x-primary-button>
                                {{ __('Verify & Enable') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-format the verification code input
        document.getElementById('code').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
        
        // Auto-submit when 6 digits are entered
        document.getElementById('code').addEventListener('input', function(e) {
            if (this.value.length === 6) {
                this.closest('form').submit();
            }
        });
    </script>
</x-app-layout>