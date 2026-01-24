<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Wish #{{ $wish->position }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h1 class="text-2xl font-bold">Wish #{{ $wish->position }}</h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $wish->theme->name }} ({{ $wish->theme->year }})
                            </p>
                        </div>
                        
                        @can('update', $wish)
                            <div class="flex gap-2">
                                <a href="{{ route('wishes.edit', $wish) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-600 focus:bg-indigo-600 active:bg-indigo-700 transition ease-in-out duration-150">
                                    Edit Wish
                                </a>
                                <a href="{{ route('wishes.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 transition ease-in-out duration-150">
                                    Back to Wishes
                                </a>
                            </div>
                        @endcan
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                        <h2 class="text-lg font-semibold mb-3">Your Wish:</h2>
                        <p class="text-gray-900 dark:text-gray-100 leading-relaxed">
                            {{ $wish->content }}
                        </p>
                    </div>

                    @if($wish->theme && $wish->theme->verse_text)
                        <div class="mt-8 bg-indigo-50 dark:bg-indigo-900/20 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-3">Theme Verse:</h3>
                            <blockquote class="italic text-gray-700 dark:text-gray-300 mb-2">
                                "{{ $wish->theme->verse_text }}"
                            </blockquote>
                            <cite class="text-sm text-gray-500 dark:text-gray-400">
                                — {{ $wish->theme->verse_reference }}
                            </cite>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>