<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Email Preview') }} - {{ ucfirst($type) }}
        </h2>
        <div class="mt-2 flex items-center space-x-4">
            <a href="{{ route('dev.emails.dashboard') }}" 
               class="text-sm text-indigo-600 hover:text-indigo-500">
                ← Back to Dashboard
            </a>
            @if($theme)
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Theme: {{ $theme->theme_title }} ({{ $theme->year }})
                </span>
            @endif
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Preview Controls -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Email Preview Controls</h3>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('dev.emails.preview', ['type' => 'verification']) }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm {{ $type === 'verification' ? 'bg-blue-800' : 'hover:bg-blue-700' }}">
                        Verification Email
                    </a>
                    <a href="{{ route('dev.emails.preview', ['type' => 'welcome']) }}"
                       class="px-4 py-2 bg-green-600 text-white rounded-md text-sm {{ $type === 'welcome' ? 'bg-green-800' : 'hover:bg-green-700' }}">
                        Welcome Email
                    </a>
                    <a href="{{ route('dev.emails.preview', ['type' => 'year-end']) }}"
                       class="px-4 py-2 bg-purple-600 text-white rounded-md text-sm {{ $type === 'year-end' ? 'bg-purple-800' : 'hover:bg-purple-700' }}">
                        Year-End Wishes
                    </a>
                </div>
            </div>
        </div>

        <!-- Email Content Preview -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Email Content Preview</h3>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        ⚠️ Development Preview Only
                    </div>
                </div>
                
                <!-- Email Preview Container -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between text-sm">
                            <div>
                                <span class="font-medium">Preview Mode:</span>
                                <span class="text-gray-600 dark:text-gray-400">{{ ucfirst($type) }} Email</span>
                            </div>
                            <div class="text-gray-500">
                                Generated: {{ now()->format('Y-m-d H:i:s') }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Email Content -->
                    <div class="p-4 bg-white">
                        <iframe srcdoc="{{ htmlspecialchars($content) }}" 
                                class="w-full h-96 border-0 rounded"
                                style="min-height: 600px;">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <h4 class="font-medium text-yellow-800 dark:text-yellow-200 mb-2">
                📧 Email Preview Information
            </h4>
            <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                <li>• This preview shows how the email will appear to recipients</li>
                <li>• Email styling may vary across different email clients</li>
                <li>• Links in preview mode may not function as expected</li>
                @if($type === 'year-end' && !auth()->user()->wishes()->exists())
                    <li>• Using sample wishes data for preview (you have no wishes yet)</li>
                @endif
            </ul>
        </div>
    </div>
</x-app-layout>