<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activeTheme?->theme_title ?? 'My Wishes' }} - {{ $activeTheme?->year ?? date('Y') }} Wish Card</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <style>
        /* Theme CSS Variables */
        {!! $themeCssVariables ?? '' !!}

        /* ================================
        RESET
        ================================ */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
        }

        /* ================================
        BASE / PAGE
        ================================ */
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--color-background, #fff);
            color: var(--color-text, #000);
            line-height: 1.4;
            padding: 1.2cm;
            font-size: 12pt;
        }

        /* ================================
        TYPOGRAPHY
        ================================ */
        .theme-year {
            font-size: 36pt;
            font-weight: 700;
            text-align: center;
            color: var(--color-primary, #000);
        }

        .theme-title {
            font-size: 20pt;
            font-weight: 700;
            text-align: center;
            color: var(--color-primary, #000);
        }

        .theme-verse {
            font-size: 11pt;
            font-style: italic;
            text-align: center;
            margin-top: 0.4em;
        }

        /* ================================
        DIVIDER
        ================================ */
        .divider {
            height: 1px;
            background: var(--color-secondary, #000);
            width: 100%;
            margin: 1.2em 0;
        }

        /* ================================
        WISHES
        ================================ */
        .wishes-title {
            font-size: 16pt;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.8em;
            color: var(--color-primary, #000);
        }

        .wish-item {
            display: flex;
            gap: 0.75em;
            max-width: 100%;
            margin-bottom: 0.8em;
            page-break-inside: avoid;
        }

        .wish-number {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            border: 1px solid var(--color-accent, #000);
            background: var(--color-accent, transparent);
            color: var(--color-background, #fff);
            display: grid;
            place-items: center;
            font-weight: 600;
            flex-shrink: 0;
            aspect-ratio: 1 / 1;
        }

        .wish-content {
            font-size: 11.5pt;
        }

        /* ================================
        FOOTER
        ================================ */
        .card-footer {
            margin-top: 1.5em;
            font-size: 9.5pt;
            text-align: center;
        }

        /* ================================
        FLOW UTILITY
        ================================ */
        .flow > * + * {
            margin-top: 0.6em;
        }

        /* ================================
        PRINT SAFETY
        ================================ */
        @page {
            margin: 1.2cm;
        }

    </style>
</head>
<body class="card">
    <header class="card-header | flow">
        <h1 class="theme-year">{{ $activeTheme->year ?? date('Y') }}</h1>
        <p class="theme-title">{{ $activeTheme?->theme_title ?? 'My Best Year Yet' }}</p>
        <p class="theme-verse">
            "{{ $activeTheme->theme_verse_text }}"<br>
            <strong>— {{ $activeTheme->theme_verse_reference }}</strong>
        </p>
    </header>
    <div class="divider"></div>
    <main class="flow">
        @if($wishes->count() > 0)
            <div class="wishes-section | flow">
                <div class="wishes-title">My Wishes</div>
                @foreach($wishes as $wish)
                    <div class="wish-item">
                        <span class="wish-number">{{ $wish->position }}</span>
                        <span class="wish-content">{{ $wish->content }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="wishes-section | flow">
                <div class="wishes-title">My Wishes</div>
                <div class="wish-item">
                    <span class="wish-content" style="text-align: center; width: 100%; font-style: italic; opacity: 0.7;">
                        No wishes created yet for {{ $activeTheme->year }}
                    </span>
                </div>
            </div>
        @endif
    </main>
    <footer class="card-footer | flow">
        <p class="footer-text">Created with Dav/Devs Three Wishes &copy; {{ date('Y') }}</p>
        <p class="footer-copyright">Created with love by Davina Leong</p>
    </footer>
</body>
</html>