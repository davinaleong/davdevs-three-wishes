<x-admin-layout>
    <x-slot name="title">Create Theme</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Create New Theme</h2>
                        <a href="{{ route('admin.themes.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back</a>
                    </div>

                    <form action="{{ route('admin.themes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                                <input type="number" name="year" id="year" value="{{ old('year') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @error('year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="theme_title" class="block text-sm font-medium text-gray-700">Theme Title</label>
                                <input type="text" name="theme_title" id="theme_title" value="{{ old('theme_title') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @error('theme_title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="theme_tagline" class="block text-sm font-medium text-gray-700">Theme Tagline</label>
                                <input type="text" name="theme_tagline" id="theme_tagline" value="{{ old('theme_tagline') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @error('theme_tagline')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="theme_verse_reference" class="block text-sm font-medium text-gray-700">Verse Reference</label>
                                <input type="text" name="theme_verse_reference" id="theme_verse_reference" value="{{ old('theme_verse_reference') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @error('theme_verse_reference')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="is_active" class="flex items-center">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                                    <span class="ml-2 text-sm text-gray-700">Set as active theme</span>
                                </label>
                            </div>

                            <div class="md:col-span-2">
                                <label for="theme_verse_text" class="block text-sm font-medium text-gray-700">Verse Text</label>
                                <textarea name="theme_verse_text" id="theme_verse_text" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('theme_verse_text') }}</textarea>
                                @error('theme_verse_text')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">Create Theme</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>