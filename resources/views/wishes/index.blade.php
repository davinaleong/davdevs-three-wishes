<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl leading-tight" style="color: #1447e6;">
                    My Wishes - {{ $activeTheme->theme_title }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $cutoffDescription }}
                </p>
            </div>
            @if($canEdit && $wishes->count() < 10)
                <a href="{{ route('wishes.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150" style="background-color: #2b7fff; hover:background-color: #1447e6; focus:background-color: #1447e6; active:background-color: #1447e6;">
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
                            @php
                                $totalWishes = $wishes->flatten()->count();
                            @endphp
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                You have <strong>{{ $totalWishes }}</strong> wishes total.
                                @if($totalWishes < 3)
                                    <span class="text-amber-600 dark:text-amber-400">(Minimum 3 required for {{ $activeTheme->year }})</span>
                                @endif
                            </p>
                            @if($canEdit && $wishes->get($activeTheme->year, collect())->count() < 10)
                                <a href="{{ route('wishes.create') }}" class="inline-flex items-center mt-3 px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 transition ease-in-out duration-150">
                                    Add New Wish
                                </a>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            @for($i = 1; $i <= 10; $i++)
                                @php
                                    $activeThemeWishes = $wishes->get($activeTheme->year, collect());
                                @endphp
                                @if($activeThemeWishes->where('position', $i)->first())
                                    <div class="w-4 h-4 bg-blue-500 rounded" title="Wish {{ $i }}"></div>
                                @else
                                    <div class="w-4 h-4 bg-gray-300 dark:bg-gray-600 rounded" title="Empty slot {{ $i }}"></div>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wishes List -->
            @if($wishes->flatten()->count() > 0)
                @foreach($wishes as $year => $yearWishes)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            {{ $year }} Wishes
                        </h3>
                        <div class="grid gap-6">
                            @foreach($yearWishes as $wish)
                                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-white font-semibold text-sm" style="background-color: var(--color-primary, #6366f1);">
                                                        {{ $wish->position }}
                                                    </span>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                                        Wish #{{ $wish->position }}
                                                    </span>
                                                </div>
                                                <p class="text-gray-900 dark:text-gray-100 text-lg">
                                                    {{ $wish->content }}
                                                </p>
                                            </div>
                                            
                                            @if($canEdit && $wish->theme_id === $activeTheme->id)
                                                <div class="flex gap-2 space-x-2 ml-4">
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
                    </div>
                @endforeach
            @else
                <!-- Empty State -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="p-12 text-center">
                        <div class="text-6xl mb-4">✨</div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                            No wishes yet
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Start creating your wishes for {{ $activeTheme->year }}. You need at least 3 wishes.
                        </p>
                        @if($canEdit)
                            <a href="{{ route('wishes.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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