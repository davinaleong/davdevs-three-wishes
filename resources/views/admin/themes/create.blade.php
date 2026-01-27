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
                                <input type="hidden" name="colors_json" id="colors_json_input" value='{{ old('colors_json', '{"text": "#000000", "muted": "#EEEEEE", "accent": "#237D9F", "primary": "#002037", "secondary": "#F8BE5D", "background": "#FFFFFF"}') }}'>
                                @error('colors_json')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Define color keys and their hex values for the theme.</p>
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

<script>
    class ColorEditor {
        constructor() {
            this.colorPairsContainer = document.getElementById('color-pairs');
            this.addButton = document.getElementById('add-color-pair');
            this.hiddenInput = document.getElementById('colors_json_input');
            this.pairCount = 0;
            
            this.init();
        }

        init() {
            // Load existing colors
            this.loadColors();
            
            // Add event listener for add button
            this.addButton.addEventListener('click', () => this.addColorPair());
            
            // Update JSON on form submit
            document.querySelector('form').addEventListener('submit', (e) => {
                this.updateHiddenInput();
            });
        }

        loadColors() {
            // Clear existing pairs first
            this.colorPairsContainer.innerHTML = '';
            this.pairCount = 0;
            
            try {
                const colorsJson = this.hiddenInput.value;
                console.log('Raw JSON:', colorsJson);
                
                if (!colorsJson || colorsJson.trim() === '') {
                    this.addColorPair('text', '#000000');
                    return;
                }
                
                const colors = JSON.parse(colorsJson);
                console.log('Parsed colors:', colors);
                
                // Check if colors is actually an object, not a string
                if (typeof colors !== 'object' || colors === null) {
                    console.error('Colors is not an object:', colors);
                    this.addColorPair('text', '#000000');
                    return;
                }
                
                const colorEntries = Object.entries(colors);
                if (colorEntries.length === 0) {
                    this.addColorPair('text', '#000000');
                } else {
                    colorEntries.forEach(([key, value]) => {
                        this.addColorPair(key, value);
                    });
                }
            } catch (e) {
                console.error('Error parsing colors JSON:', e);
                console.error('Raw value was:', this.hiddenInput.value);
                this.addColorPair('text', '#000000');
            }
        }

        addColorPair(key = '', color = '#000000') {
            const pairId = `color-pair-${this.pairCount++}`;
            const pairHtml = `
                <div class="flex items-center space-x-3 bg-white p-3 rounded border" data-pair-id="${pairId}">
                    <input 
                        type="text" 
                        placeholder="Key (e.g., primary)" 
                        value="${key}" 
                        class="color-key flex-1 px-3 py-2 border rounded text-sm" 
                        onchange="colorEditor.updateHiddenInput()"
                    />
                    <div class="flex items-center space-x-2">
                        <input 
                            type="color" 
                            value="${color}" 
                            class="color-picker w-10 h-8 border rounded cursor-pointer"
                            onchange="colorEditor.updateColorValue(this)"
                        />
                        <input 
                            type="text" 
                            value="${color}" 
                            class="color-value w-20 px-2 py-1 border rounded text-sm font-mono"
                            pattern="#[0-9A-Fa-f]{6}"
                            onchange="colorEditor.updateColorPicker(this)"
                        />
                    </div>
                    <button 
                        type="button" 
                        class="text-red-600 hover:text-red-800 p-1"
                        onclick="colorEditor.removeColorPair('${pairId}')"
                    >
                        <x-icon name="trash" class="w-4 h-4" />
                    </button>
                </div>
            `;
            
            this.colorPairsContainer.insertAdjacentHTML('beforeend', pairHtml);
            this.updateHiddenInput();
        }

        removeColorPair(pairId) {
            const pair = document.querySelector(`[data-pair-id="${pairId}"]`);
            if (pair) {
                pair.remove();
                this.updateHiddenInput();
            }
        }

        updateColorValue(colorPicker) {
            const textInput = colorPicker.nextElementSibling;
            textInput.value = colorPicker.value;
            this.updateHiddenInput();
        }

        updateColorPicker(textInput) {
            const colorPicker = textInput.previousElementSibling;
            if (/^#[0-9A-Fa-f]{6}$/.test(textInput.value)) {
                colorPicker.value = textInput.value;
            }
            this.updateHiddenInput();
        }

        updateHiddenInput() {
            const pairs = this.colorPairsContainer.querySelectorAll('[data-pair-id]');
            const colors = {};
            
            pairs.forEach(pair => {
                const key = pair.querySelector('.color-key').value.trim();
                const value = pair.querySelector('.color-value').value.trim();
                
                if (key && value && /^#[0-9A-Fa-f]{6}$/.test(value)) {
                    colors[key] = value;
                }
            });
            
            this.hiddenInput.value = JSON.stringify(colors);
        }
    }

    // Initialize the color editor when the page loads
    document.addEventListener('DOMContentLoaded', () => {
        window.colorEditor = new ColorEditor();
    });
</script>