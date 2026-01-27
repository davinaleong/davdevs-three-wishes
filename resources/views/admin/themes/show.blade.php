<x-admin-layout>
    <x-slot name="title">Theme Details</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $theme->theme_title }}</h2>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.themes.edit', $theme) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Edit</a>
                            @unless($theme->is_active)
                                <form action="{{ route('admin.themes.activate', $theme) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Activate</button>
                                </form>
                            @endunless
                            <form action="{{ route('admin.themes.destroy', $theme) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this theme? This will permanently delete the theme and all associated data including wishes.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete</button>
                            </form>
                            <a href="{{ route('admin.themes.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back</a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Theme Information</h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="font-medium text-gray-900">Year</dt>
                                    <dd class="text-gray-700">{{ $theme->year }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-900">Title</dt>
                                    <dd class="text-gray-700">{{ $theme->theme_title }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-900">Tagline</dt>
                                    <dd class="text-gray-700">{{ $theme->theme_tagline }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-900">Verse Reference</dt>
                                    <dd class="text-gray-700">{{ $theme->theme_verse_reference }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-900">Status</dt>
                                    <dd>
                                        @if($theme->is_active)
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Active</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Inactive</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Theme Verse</h3>
                            <blockquote class="border-l-4 border-blue-500 pl-4 italic text-gray-700">
                                {{ $theme->theme_verse_text }}
                            </blockquote>
                        </div>
                    </div>

                    @if($theme->colors_json)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Color Scheme</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                            @foreach((is_string($theme->colors_json) ? json_decode($theme->colors_json, true) : $theme->colors_json) as $name => $color)
                                <div class="text-center">
                                    <div class="w-16 h-16 rounded-lg shadow-md mx-auto mb-2" style="background-color: {{ $color }}"></div>
                                    <div class="text-xs font-medium capitalize">{{ $name }}</div>
                                    <div class="text-xs text-gray-500">{{ $color }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>