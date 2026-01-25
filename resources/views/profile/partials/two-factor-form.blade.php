<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Two-Factor Authentication') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Add additional security to your account using two-factor authentication.') }}
        </p>
    </header>

    @if($user->hasTwoFactorEnabled())
        <!-- 2FA is enabled -->
        <div class="mt-6 space-y-6">
            <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900 rounded-lg border border-green-200 dark:border-green-700">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-900 dark:text-green-100">
                            {{ __('Two-Factor Authentication is enabled.') }}
                        </p>
                        <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                            {{ __('Enabled on') }} {{ $user->two_factor_enabled_at->format('M j, Y \\a\\t g:i A') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <x-secondary-button onclick="window.open('{{ route('two-factor.recovery-codes') }}', '_blank', 'width=600,height=800')">
                    {{ __('View Recovery Codes') }}
                </x-secondary-button>
                
                <form method="post" action="{{ route('two-factor.regenerate-codes') }}" class="inline">
                    @csrf
                    <x-secondary-button type="submit" onclick="return confirm('This will invalidate your existing recovery codes. Continue?')">
                        {{ __('Regenerate Recovery Codes') }}
                    </x-secondary-button>
                </form>
            </div>

            <!-- Disable 2FA Form -->
            <form method="post" action="{{ route('two-factor.disable') }}" class="mt-6">
                @csrf
                @method('delete')
                
                <div>
                    <x-input-label for="disable_password" :value="__('Password')" />
                    <x-text-input id="disable_password" name="password" type="password" class="mt-1 block w-full" placeholder="{{ __('Enter your password to disable 2FA') }}" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4 mt-4">
                    <x-danger-button onclick="return confirm('Are you sure you want to disable two-factor authentication? This will make your account less secure.')">
                        {{ __('Disable Two-Factor Authentication') }}
                    </x-danger-button>
                </div>
            </form>
        </div>
    @else
        <!-- 2FA is not enabled -->
        <div class="mt-6">
            <div class="flex items-center justify-between p-4 bg-yellow-50 dark:bg-yellow-900 rounded-lg border border-yellow-200 dark:border-yellow-700 mb-6">
                <div class="flex gap-2">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-yellow-900 dark:text-yellow-100">
                            {{ __('Two-Factor Authentication is not enabled.') }}
                        </p>
                        <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                            {{ __('Protect your account with an additional layer of security.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                        {{ __('How it works:') }}
                    </h3>
                    <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                        <li class="flex items-center">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-2 flex-shrink-0"></span>
                            {{ __('Download an authenticator app like Google Authenticator or Authy') }}
                        </li>
                        <li class="flex items-center">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-2 flex-shrink-0"></span>
                            {{ __('Scan the QR code to link your account') }}
                        </li>
                        <li class="flex items-center">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-2 flex-shrink-0"></span>
                            {{ __('Enter the 6-digit code when signing in') }}
                        </li>
                    </ul>
                </div>

                <form method="post" action="{{ route('two-factor.enable') }}" class="mt-4">
                    @csrf
                    <x-primary-button>
                        {{ __('Enable Two-Factor Authentication') }}
                    </x-primary-button>
                </form>
            </div>
        </div>
    @endif
</section>