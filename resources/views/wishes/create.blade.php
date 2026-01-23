<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Wish') }} - Position {{ $nextPosition }}
        </h2>
    </x-slot>

    <!-- Theme CSS Variables -->
    <style>
        {!! $themeCssVariables ?? '' !!}
    </style>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('wishes.store') }}">
                        @csrf
                        
                        <input type="hidden" name="position" value="{{ $nextPosition }}">
                        
                        <!-- Wish Content -->
                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Your Wish #{{ $nextPosition }}
                            </label>
                            <textarea id="content" 
                                    name="content" 
                                    rows="4" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" 
                                    placeholder="Enter your wish here..."
                                    maxlength="1000"
                                    required>{{ old('content') }}</textarea>
                            
                            @error('content')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Maximum 1000 characters
                            </p>
                        </div>

                        <!-- Character Counter -->
                        <div class="mb-6">
                            <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                                <span>Characters used:</span>
                                <span id="charCount">0/1000</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                <div id="charBar" class="h-2 rounded-full transition-all duration-300" style="background-color: var(--color-primary, #6366f1); width: 0%"></div>
                            </div>
                        </div>

                        <!-- Theme Reminder -->
                        <div class="mb-6 p-4 border-l-4 bg-gray-50 dark:bg-gray-700" style="border-color: var(--color-accent, #06b6d4);">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">
                                {{ $activeTheme->theme_title }} Theme
                            </h4>
                            <blockquote class="italic text-gray-700 dark:text-gray-300 text-sm mb-2">
                                "{{ $activeTheme->theme_verse_text }}"
                            </blockquote>
                            <cite class="text-xs text-gray-500 dark:text-gray-400">
                                — {{ $activeTheme->theme_verse_reference }}
                            </cite>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-2 space-x-4">
                            <a href="{{ route('wishes.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                                Cancel
                            </a>
                            <button type="submit" class="px-6 py-2 bg-indigo-500 text-white font-semibold rounded-md shadow-sm hover:bg-indigo-600 focus:bg-indigo-600 active:bg-indigo-700 transition-colors duration-150">
                                Create Wish
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('content');
            const charCount = document.getElementById('charCount');
            const charBar = document.getElementById('charBar');
            const maxLength = 1000;

            function updateCounter() {
                const currentLength = textarea.value.length;
                const percentage = (currentLength / maxLength) * 100;
                
                charCount.textContent = `${currentLength}/${maxLength}`;
                charBar.style.width = `${percentage}%`;
                
                // Change color based on usage
                if (percentage > 90) {
                    charBar.style.backgroundColor = '#ef4444'; // red
                } else if (percentage > 75) {
                    charBar.style.backgroundColor = '#f59e0b'; // amber
                } else {
                    charBar.style.backgroundColor = 'var(--color-primary, #6366f1)'; // primary
                }
            }

            textarea.addEventListener('input', updateCounter);
            updateCounter(); // Initial call
        });
    </script>
</x-app-layout>