<x-admin-layout>
    <x-slot name="title">Themes</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Themes</h2>
                        <a href="{{ route('admin.themes.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create New Theme</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tagline</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($themes as $theme)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $theme->year }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $theme->theme_title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $theme->theme_tagline }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($theme->is_active)
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Active</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.themes.show', $theme) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                                <a href="{{ route('admin.themes.edit', $theme) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                                @unless($theme->is_active)
                                                    <form action="{{ route('admin.themes.activate', $theme) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900">Activate</button>
                                                    </form>
                                                @endunless
                                                <form action="{{ route('admin.themes.destroy', $theme) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this theme? This will permanently delete the theme and all associated data including wishes.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No themes found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>