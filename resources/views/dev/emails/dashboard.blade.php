<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Email Testing Dashboard') }}
        </h2>
        <p class="text-sm text-red-600 mt-1">
            ⚠️ Development Only - These routes are disabled in production
        </p>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">Manual Email Triggers</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Click the buttons below to manually send emails to your account ({{ auth()->user()->email }})
                </p>

                <div class="grid gap-4 md:grid-cols-3">
                    <!-- Email Verification -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <h4 class="font-medium mb-2">Email Verification</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Send a verification email to test the design and functionality.
                        </p>
                        <a href="{{ route('dev.emails.verification') }}" 
                            class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 transition mb-2">
                            Send Verification Email
                        </a>
                        <a href="{{ route('dev.emails.preview', ['type' => 'verification']) }}" 
                            class="inline-flex items-center px-3 py-1 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition">
                            Preview
                        </a>
                    </div>

                    <!-- Welcome Email -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <h4 class="font-medium mb-2">Welcome Email</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Send a welcome email to test post-verification messaging.
                        </p>
                        <a href="{{ route('dev.emails.welcome') }}" 
                            class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 transition mb-2">
                            Send Welcome Email
                        </a>
                        <a href="{{ route('dev.emails.preview', ['type' => 'welcome']) }}" 
                            class="inline-flex items-center px-3 py-1 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition">
                            Preview
                        </a>
                    </div>

                    <!-- Password Reset -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <h4 class="font-medium mb-2">Password Reset</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Send a password reset email to test the design and functionality.
                        </p>
                        <a href="{{ route('dev.emails.password-reset') }}" 
                            class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 transition">
                            Send Password Reset
                        </a>
                    </div>

                    <!-- Year-End Wishes -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <h4 class="font-medium mb-2">Year-End Wishes</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Send a year-end wishes reminder email (Dec 31 email).
                        </p>
                        <a href="{{ route('dev.emails.year-end-wishes') }}" 
                            class="inline-flex items-center px-4 py-2 bg-purple-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-600 transition mb-2">
                            Send Wishes Email
                        </a>
                        <a href="{{ route('dev.emails.preview', ['type' => 'year-end']) }}" 
                            class="inline-flex items-center px-3 py-1 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition">
                            Preview
                        </a>
                    </div>
                </div>

                <div class="mt-8 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <h4 class="font-medium text-yellow-800 dark:text-yellow-200 mb-2">📧 Email Configuration</h4>
                    <div class="text-sm text-yellow-700 dark:text-yellow-300">
                        <p><strong>Mail Driver:</strong> {{ config('mail.default') }}</p>
                        <p><strong>From Address:</strong> {{ config('mail.from.address') }}</p>
                        @if(config('mail.default') === 'smtp' && config('mail.mailers.smtp.host') === 'sandbox.smtp.mailtrap.io')
                            <p class="mt-2">✅ Using Mailtrap - Check your Mailtrap inbox for test emails</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>