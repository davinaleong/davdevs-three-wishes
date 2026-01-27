<x-admin-layout>
    <x-slot name="title">Edit Theme</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Edit Theme: {{ $theme->theme_title }}</h2>
                        <div class="space-x-2">
                            <a href="{{ route('admin.themes.show', $theme) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">View</a>
                            <a href="{{ route('admin.themes.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back</a>
                        </div>
                    </div>

                    <form action="{{ route('admin.themes.update', $theme) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                                <input type="number" name="year" id="year" value="{{ old('year', $theme->year) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @error('year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="theme_title" class="block text-sm font-medium text-gray-700">Theme Title</label>
                                <input type="text" name="theme_title" id="theme_title" value="{{ old('theme_title', $theme->theme_title) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @error('theme_title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="theme_tagline" class="block text-sm font-medium text-gray-700">Theme Tagline</label>
                                <input type="text" name="theme_tagline" id="theme_tagline" value="{{ old('theme_tagline', $theme->theme_tagline) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @error('theme_tagline')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="theme_verse_reference" class="block text-sm font-medium text-gray-700">Verse Reference</label>
                                <input type="text" name="theme_verse_reference" id="theme_verse_reference" value="{{ old('theme_verse_reference', $theme->theme_verse_reference) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @error('theme_verse_reference')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="is_active" class="flex items-center">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $theme->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                                    <span class="ml-2 text-sm text-gray-700">Set as active theme</span>
                                </label>
                            </div>

                            <div class="md:col-span-2">
                                <label for="theme_verse_text" class="block text-sm font-medium text-gray-700">Verse Text</label>
                                <textarea name="theme_verse_text" id="theme_verse_text" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('theme_verse_text', $theme->theme_verse_text) }}</textarea>
                                @error('theme_verse_text')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Theme Colors</label>
                                <div id="color-editor" class="border rounded-md p-4 bg-gray-50">
                                    <div id="color-pairs" class="space-y-3">
                                        <!-- Color pairs will be populated by JavaScript -->
                                    </div>
                                    <button type="button" id="add-color-pair" class="mt-3 bg-blue-500 text-white px-3 py-2 rounded text-sm hover:bg-blue-600 flex items-center">
                                        <x-icon name="plus" class="w-4 h-4 mr-1" />Add Color
                                    </button>
                                </div>
                                <input
                                    type="hidden"
                                    name="colors_json"
                                    id="colors_json_input"
                                    value='@json(old("colors_json", $theme->colors_json))'
                                >

                                @error('colors_json')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Define color keys and their hex values for the theme.</p>
                            </div>
                        </div>

                        <div class="mt-6 flex space-x-2">
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">Update Theme</button>
                            @if(!$theme->is_active)
                                <button type="button" onclick="activateTheme()" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">Activate Theme</button>
                            @endif
                        </div>
                    </form>

                    <!-- Delete Form -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <form action="{{ route('admin.themes.destroy', $theme) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this theme? This will permanently delete the theme and all associated data including wishes.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600">Delete Theme</button>
                            <span class="text-sm text-gray-600 ml-3">This action cannot be undone and will delete all associated wishes.</span>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$theme->is_active)
    <script>
        function activateTheme() {
            if (confirm('Are you sure you want to activate this theme? This will deactivate the current active theme.')) {
                fetch('{{ route('admin.themes.activate', $theme) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error activating theme');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error activating theme');
                });
            }
        }
    </script>
    @endif

    <script>
        class ColorEditor {
            constructor() {
                this.container = document.getElementById('color-pairs');
                this.addButton = document.getElementById('add-color-pair');
                this.hiddenInput = document.getElementById('colors_json_input');
                this.pairCount = 0;

                this.init();
            }

            init() {
                this.loadFromHiddenInput();
                this.addButton.addEventListener('click', () => this.addPair());
                document.querySelector('form').addEventListener('submit', () => {
                    this.syncToHiddenInput();
                });
            }

            // -------------------------
            // Data loading
            // -------------------------
            loadFromHiddenInput() {
                this.container.innerHTML = '';
                this.pairCount = 0;

                let data = {};

                try {
                    if (this.hiddenInput.value) {
                        data = JSON.parse(this.hiddenInput.value);
                    }
                } catch (e) {
                    console.warn('Invalid colors_json, resetting:', e);
                    data = {};
                }

                if (!data || typeof data !== 'object' || Array.isArray(data)) {
                    data = {};
                }

                const entries = Object.entries(data);

                if (entries.length === 0) {
                    this.addPair('text', '#000000');
                    return;
                }

                entries.forEach(([key, value]) => {
                    this.addPair(key, value);
                });
            }

            // -------------------------
            // UI actions
            // -------------------------
            addPair(key = '', value = '#000000') {
                const id = `pair-${this.pairCount++}`;

                const html = `
                    <div
                        class="flex items-center space-x-3 bg-white p-3 rounded border"
                        data-id="${id}"
                    >
                        <input
                            type="text"
                            class="color-key flex-1 px-3 py-2 border rounded text-sm"
                            placeholder="Key (e.g. primary)"
                            value="${key}"
                        />

                        <input
                            type="color"
                            class="color-picker w-10 h-8 border rounded"
                            value="${value}"
                        />

                        <input
                            type="text"
                            class="color-value w-24 px-2 py-1 border rounded text-sm font-mono"
                            value="${value}"
                        />

                        <button
                            type="button"
                            class="text-red-600 hover:text-red-800"
                            data-remove
                        >
                            <x-icon name="trash" class="w-4 h-4" />
                        </button>
                    </div>
                `;

                this.container.insertAdjacentHTML('beforeend', html);

                const row = this.container.lastElementChild;
                this.bindRowEvents(row);
                this.syncToHiddenInput();
            }

            removePair(row) {
                row.remove();
                this.syncToHiddenInput();
            }

            // -------------------------
            // Event wiring
            // -------------------------
            bindRowEvents(row) {
                const keyInput = row.querySelector('.color-key');
                const colorPicker = row.querySelector('.color-picker');
                const valueInput = row.querySelector('.color-value');
                const removeBtn = row.querySelector('[data-remove]');

                colorPicker.addEventListener('input', () => {
                    valueInput.value = colorPicker.value;
                    this.syncToHiddenInput();
                });

                valueInput.addEventListener('input', () => {
                    if (this.isValidHex(valueInput.value)) {
                        colorPicker.value = valueInput.value;
                    }
                    this.syncToHiddenInput();
                });

                keyInput.addEventListener('input', () => {
                    this.syncToHiddenInput();
                });

                removeBtn.addEventListener('click', () => {
                    this.removePair(row);
                });
            }

            // -------------------------
            // Sync logic
            // -------------------------
            syncToHiddenInput() {
                const rows = this.container.querySelectorAll('[data-id]');
                const output = {};

                rows.forEach(row => {
                    const key = row.querySelector('.color-key').value.trim();
                    const value = row.querySelector('.color-value').value.trim();

                    if (key && this.isValidHex(value)) {
                        output[key] = value;
                    }
                });

                this.hiddenInput.value = JSON.stringify(output);
            }

            isValidHex(value) {
                return /^#[0-9A-Fa-f]{6}$/.test(value);
            }
        }

        // Boot safely
        document.addEventListener('DOMContentLoaded', () => {
            window.colorEditor = new ColorEditor();
        });
        </script>

</x-admin-layout>