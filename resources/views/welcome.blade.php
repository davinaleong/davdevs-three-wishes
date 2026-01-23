<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $activeTheme->theme_title ?? 'Three Wishes' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Theme CSS Variables -->
    <style>
        {!! $themeCssVariables ?? '' !!}
        body {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-white to-gray-50 flex items-center justify-center px-4">
    <div class="max-w-4xl mx-auto text-center">
        <!-- Theme Header -->
        <div class="mb-8">
            @if($activeTheme?->logo_path)
                <img src="{{ asset($activeTheme->logo_path) }}" alt="Logo" class="mx-auto mb-6 h-16">
            @endif
            
            <h1 class="text-4xl md:text-6xl font-bold mb-4" style="color: var(--color-primary, #6366f1);">
                {{ $activeTheme?->theme_title ?? 'Three Wishes ' . date('Y') }}
            </h1>
            
            @if($activeTheme?->theme_tagline)
                <p class="text-xl md:text-2xl text-gray-600 mb-6">
                    {{ $activeTheme->year ?? date('Y') }}
                </p>
            @endif
        </div>

        <!-- Bible Verse -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border-l-4" style="border-color: var(--color-accent, #06b6d4);">
            <blockquote class="text-lg md:text-xl text-gray-700 italic mb-4">
                "{{ $activeTheme?->theme_verse_text ?? 'For I know the thoughts that I think toward you, says the Lord, thoughts of peace and not of evil, to give you a future and a hope.' }}"
            </blockquote>
            <cite class="text-sm font-medium" style="color: var(--color-primary, #6366f1);">
                — {{ $activeTheme?->theme_verse_reference ?? 'Jeremiah 29:11 (NKJV)' }}
            </cite>
        </div>

        <!-- Auth Buttons -->
        @if (Route::has('login'))
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('wishes.index') }}" 
                       class="inline-flex items-center justify-center gap-2 px-8 py-3 rounded-lg font-semibold text-white transition duration-200 shadow-md hover:shadow-lg"
                       style="background-color: var(--color-primary, #6366f1);">
                        View Your Wishes
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center justify-center gap-2 px-8 py-3 rounded-lg border-2 font-semibold transition duration-200"
                       style="border-color: var(--color-primary, #6366f1); color: var(--color-primary, #6366f1);">
                        Log In
                    </a>
                    
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center justify-center gap-2 px-8 py-3 rounded-lg font-semibold text-white transition duration-200 shadow-md hover:shadow-lg"
                           style="background-color: var(--color-primary, #6366f1);">
                            Get Started
                        </a>
                    @endif
                @endauth
            </div>
        @endif

        <!-- Year Info -->
        <div class="mt-12 text-gray-500 text-sm">
            <p>{{ $activeTheme?->year ?? date('Y') }} Theme of the Year</p>
            <p class="mt-2">Made with love by Davina Leong</p>
        </div>
    </div>
</body>
</html>