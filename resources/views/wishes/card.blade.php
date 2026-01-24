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
        :root {
            --primary-color: {{ $activeTheme?->getColors('primary') ?? '#002037' }};
            --secondary-color: {{ $activeTheme?->getColors('secondary') ?? '#F8BE5D' }};
            --accent-color: {{ $activeTheme?->getColors('accent') ?? '#23D09F' }};
            --background-color: {{ $activeTheme?->getColors('background') ?? '#FFFFFF' }};
            --text-color: {{ $activeTheme?->getColors('text') ?? '#000000' }};
            --muted-color: {{ $activeTheme?->getColors('muted') ?? '#EEEEEE' }};
        }

        /* http://meyerweb.com/eric/tools/css/reset/ 
        v2.0 | 20110126
        License: none (public domain)
        */

        html, body, div, span, applet, object, iframe,
        h1, h2, h3, h4, h5, h6, p, blockquote, pre,
        a, abbr, acronym, address, big, cite, code,
        del, dfn, em, img, ins, kbd, q, s, samp,
        small, strike, strong, sub, sup, tt, var,
        b, u, i, center,
        dl, dt, dd, ol, ul, li,
        fieldset, form, label, legend,
        table, caption, tbody, tfoot, thead, tr, th, td,
        article, aside, canvas, details, embed, 
        figure, figcaption, footer, header, hgroup, 
        menu, nav, output, ruby, section, summary,
        time, mark, audio, video {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
        }
        /* HTML5 display-role reset for older browsers */
        article, aside, details, figcaption, figure, 
        footer, header, hgroup, menu, nav, section {
            display: block;
        }
        body {
            line-height: 1;
        }
        ol, ul {
            list-style: none;
        }
        blockquote, q {
            quotes: none;
        }
        blockquote:before, blockquote:after,
        q:before, q:after {
            content: '';
            content: none;
        }
        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--background-color);
            background: linear-gradient(to bottom right, var(--accent-color), var(--primary-color));
            background-repeat: no-repeat;
            color: var(--text-color);
            margin: 0;
            padding: 2em;
            min-height: 100vh;
            line-height: 1.5em;
        }

        .theme-year {
            color: var(--secondary-color);
            font-size: 4em;
            font-weight: 700;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            line-height: normal;
        }

        .theme-title {
            font-size: 2em;
            font-weight: 700;
            text-align: center;
            color: var(--text-color);
            line-height: normal;
        }

        .theme-verse {
            font-size: 1.2em;
            font-style: italic;
            text-align: center;
        }

        .divider {
            height: 1px;
            background-color: var(--secondary-color);
            width: 80ch;
            margin-inline: auto;
            margin-block: 2em;
        }

        .wishes-title {
            font-size: 1.5em;
            font-weight: 700;
            text-align: center;
            color: var(--secondary-color);
            margin-block-end: 1em;
        }

        .wish-item {
            display: flex;
            gap: 1em;
            max-width: 60ch;
            margin-inline: auto;
        }

        .wish-number {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            color: var(--primary-color);
            background-color: var(--secondary-color);
            display: grid;
            place-items: center;
            flex-shrink: 0;
            aspect-ratio: 1 / 1;
        }

        .card-footer {
            font-size: 0.9em;
            text-align: center;
            opacity: 80%;
        }

        .flow > * + * {
            margin-bottom: 1em;
        }

        @media print {

            /* Page margins (printer-level) */
            @page {
                margin: 1.2cm;
            }

            /* Reset body for print */
            body {
                background: #fff !important;
                background-color: #fff !important;
                color: #000 !important;
                padding: 0 !important;
                margin: 0 !important;
                min-height: auto;
                line-height: 1.4;
            }

            /* Kill all color, shadows, effects */
            * {
                color: #000 !important;
                background: transparent !important;
                box-shadow: none !important;
                text-shadow: none !important;
            }

            /* Headings */
            .theme-year {
                font-size: 3em;              /* slightly smaller for paper */
                color: #000 !important;
            }

            .theme-title {
                font-size: 1.6em;
                color: #000 !important;
            }

            .theme-verse {
                font-size: 1.1em;
            }

            /* Divider becomes a thin black rule */
            .divider {
                background-color: #000 !important;
                width: 100%;
                margin-block: 1.2em;
            }

            /* Wishes */
            .wishes-title {
                color: #000 !important;
                margin-bottom: 0.6em;
            }

            .wish-item {
                max-width: 100%;
                gap: 0.75em;
                page-break-inside: avoid; /* prevent awkward splits */
            }

            /* Number circles → outlined, printer-friendly */
            .wish-number {
                background: transparent !important;
                border: 1px solid #000;
                color: #000 !important;
            }

            /* Footer */
            .card-footer {
                font-size: 0.8em;
                opacity: 1;
                margin-top: 1.5em;
            }

            /* Reduce vertical rhythm for print */
            .flow > * + * {
                margin-bottom: 0.6em;
            }
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
        @php
            // Temporary long wishes for styling adjustment
            $tempWishes = collect([
                (object)['position' => 1, 'content' => 'I wish to travel around the world and experience different cultures, meet new people, learn new languages, and create lasting memories that will inspire me for the rest of my life while building meaningful connections.'],
                (object)['position' => 2, 'content' => 'I wish to start my own business that not only provides financial freedom but also makes a positive impact on the community by creating jobs, supporting local initiatives, and contributing to sustainable development practices.'],
                (object)['position' => 3, 'content' => 'I wish to write and publish a novel that touches people\'s hearts, inspires them to pursue their dreams, and becomes a bestseller that gets adapted into a movie that reaches millions of people worldwide.'],
                (object)['position' => 4, 'content' => 'I wish to build a strong, loving family with my partner, raise happy and healthy children, create a warm home environment, and maintain deep relationships that last a lifetime through all of life\'s challenges.'],
                (object)['position' => 5, 'content' => 'I wish to achieve financial independence through smart investments, careful budgeting, and strategic planning so that I can retire early and spend more time pursuing my passions and helping others achieve their goals.'],
                (object)['position' => 6, 'content' => 'I wish to learn multiple new skills including playing the piano, speaking three foreign languages fluently, mastering photography, and developing expertise in cooking various international cuisines.'],
                (object)['position' => 7, 'content' => 'I wish to make a significant contribution to solving climate change by developing innovative solutions, supporting renewable energy initiatives, and inspiring others to adopt more sustainable lifestyles.'],
                (object)['position' => 8, 'content' => 'I wish to maintain excellent physical and mental health throughout my life by exercising regularly, eating nutritious foods, practicing mindfulness, and building resilience to handle stress effectively.'],
                (object)['position' => 9, 'content' => 'I wish to pursue higher education and earn advanced degrees in fields that fascinate me, conduct meaningful research that advances human knowledge, and mentor the next generation of students and professionals.'],
                (object)['position' => 10, 'content' => 'I wish to volunteer regularly for causes I care about, donate generously to charitable organizations, and use my skills and resources to help disadvantaged communities and make the world a better place for everyone.']
            ]);
        @endphp

        @if($tempWishes->count() > 0 || $wishes->count() > 0)
            <div class="wishes-section | flow">
                <div class="wishes-title">My Wishes</div>
                @foreach($tempWishes as $wish)
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