<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('My Wishes') }} - {{ $activeTheme->theme_title }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $cutoffDescription }}
                </p>
            </div>
            @if($canEdit && $wishes->count() < 10)
                <a href="{{ route('wishes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New Wish
                </a>
            @endif
        </div>
    </x-slot>

    <!-- Theme CSS Variables -->
    <style>
        {!! $themeCssVariables ?? '' !!}
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Edit Window Status -->
            @if(!$canEdit)
                <div class="bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 text-yellow-700 dark:text-yellow-300 px-4 py-3 rounded mb-6">
                    <strong>Editing Closed:</strong> The editing window for {{ $activeTheme->year }} has closed. Your wishes are now read-only.
                </div>
            @endif

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Wishes Count Info -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                You have <strong>{{ $wishes->count() }}</strong> of 10 possible wishes.
                                @if($wishes->count() < 3)
                                    <span class="text-amber-600 dark:text-amber-400">(Minimum 3 required)</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            @for($i = 1; $i <= 10; $i++)
                                @if($wishes->where('position', $i)->first())
                                    <div class="w-4 h-4 rounded" style="background-color: var(--color-primary, #6366f1);" title="Wish {{ $i }}"></div>
                                @else
                                    <div class="w-4 h-4 bg-gray-300 dark:bg-gray-600 rounded" title="Empty slot {{ $i }}"></div>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wishes List -->
            @if($wishes->count() > 0)
                <div class="grid gap-6">
                    @foreach($wishes as $wish)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-white font-semibold text-sm mr-3" style="background-color: var(--color-primary, #6366f1);">
                                                {{ $wish->position }}
                                            </span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                Wish #{{ $wish->position }}
                                            </span>
                                        </div>
                                        <p class="text-gray-900 dark:text-gray-100 text-lg">
                                            {{ $wish->content }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            Created: {{ $wish->created_at->format('M j, Y g:i A') }}
                                            @if($wish->updated_at != $wish->created_at)
                                                • Updated: {{ $wish->updated_at->format('M j, Y g:i A') }}
                                            @endif
                                        </p>
                                    </div>
                                    
                                    @if($canEdit)
                                        <div class="flex space-x-2 ml-4">
                                            <a href="{{ route('wishes.edit', $wish) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('wishes.destroy', $wish) }}" class="inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this wish?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <div class="text-6xl mb-4">✨</div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                            No wishes yet
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Start creating your wishes for {{ $activeTheme->year }}. You need at least 3 wishes.
                        </p>
                        @if($canEdit)
                            <a href="{{ route('wishes.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white" style="background-color: var(--color-primary, #6366f1);">
                                Create Your First Wish
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Theme Info -->
            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-4">
                        {{ $activeTheme->theme_title }} Theme
                    </h3>
                    <blockquote class="italic text-gray-700 dark:text-gray-300 mb-2">
                        "{{ $activeTheme->theme_verse_text }}"
                    </blockquote>
                    <cite class="text-sm text-gray-500 dark:text-gray-400">
                        — {{ $activeTheme->theme_verse_reference }}
                    </cite>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>