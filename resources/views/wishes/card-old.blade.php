<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activeTheme->theme_title }} - {{ $activeTheme->year }} Wish Card</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', 'Helvetica', 'Arial', sans-serif;
            background: white;
            color: {{ $activeTheme->getColors('text') ?? '#000000' }};
        }

        /* Print styles */
        @media print {
            body {
                margin: 0;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-before: always;
            }
        }

        /* Layout Options */
        @media screen {
            .card-container {
                max-width: 1200px;
                margin: 20px auto;
                padding: 20px;
            }
        }

        /* Portrait A4 (210mm x 297mm) */
        .layout-portrait-a4 .wish-card {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: linear-gradient(135deg, {{ $activeTheme->getColors('accent') ?? '#23D09F' }} 0%, {{ $activeTheme->getColors('primary') ?? '#002037' }} 100%);
            padding: 30mm;
            position: relative;
            overflow: hidden;
        }

        /* Portrait Letter (8.5" x 11") */
        .layout-portrait-letter .wish-card {
            width: 8.5in;
            min-height: 11in;
            margin: 0 auto;
            background: linear-gradient(135deg, {{ $activeTheme->getColors('accent') ?? '#23D09F' }} 0%, {{ $activeTheme->getColors('primary') ?? '#002037' }} 100%);
            padding: 1in;
            position: relative;
            overflow: hidden;
        }

        /* Landscape A4 (297mm x 210mm) */
        .layout-landscape-a4 .wish-card {
            width: 297mm;
            min-height: 210mm;
            margin: 0 auto;
            background: linear-gradient(135deg, {{ $activeTheme->getColors('accent') ?? '#23D09F' }} 0%, {{ $activeTheme->getColors('primary') ?? '#002037' }} 100%);
            padding: 20mm;
            position: relative;
            overflow: hidden;
        }

        /* Landscape Letter (11" x 8.5") */
        .layout-landscape-letter .wish-card {
            width: 11in;
            min-height: 8.5in;
            margin: 0 auto;
            background: linear-gradient(135deg, {{ $activeTheme->getColors('accent') ?? '#23D09F' }} 0%, {{ $activeTheme->getColors('primary') ?? '#002037' }} 100%);
            padding: 0.75in;
            position: relative;
            overflow: hidden;
        }

        /* Card 5x7 inches */
        .layout-card-5x7 .wish-card {
            width: 7in;
            min-height: 5in;
            margin: 0 auto;
            background: linear-gradient(135deg, {{ $activeTheme->getColors('accent') ?? '#23D09F' }} 0%, {{ $activeTheme->getColors('primary') ?? '#002037' }} 100%);
            padding: 0.5in;
            position: relative;
            overflow: hidden;
        }

        /* Card content styles */
        .card-header {
            text-align: center;
            margin-bottom: 30px;
            color: {{ $activeTheme->getColors('text') ?? '#FFFFFF' }};
        }

        .year {
            font-size: 4em;
            font-weight: bold;
            margin-bottom: 10px;
            color: {{ $activeTheme->getColors('secondary') ?? '#F8BE5D' }};
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .theme-title {
            font-size: 2.2em;
            font-weight: bold;
            margin-bottom: 15px;
            font-style: italic;
        }

        .theme-tagline {
            font-size: 1.2em;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .theme-verse {
            font-size: 1em;
            font-style: italic;
            opacity: 0.8;
            border-top: 2px solid {{ $activeTheme->getColors('secondary') ?? '#F8BE5D' }};
            padding-top: 15px;
            margin-top: 15px;
        }

        .wishes-section {
            margin: 40px 0;
        }

        .wishes-title {
            font-size: 1.8em;
            font-weight: bold;
            text-align: center;
            margin-bottom: 25px;
            color: {{ $activeTheme->getColors('secondary') ?? '#F8BE5D' }};
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .wish-item {
            display: flex;
            gap: 0.2em;
        }

        .wish-number {
            display: grid;
            place-items: center;
            background: {{ $activeTheme->getColors('secondary') ?? '#F8BE5D' }};
            color: {{ $activeTheme->getColors('primary') ?? '#002037' }};
            width: 30px;
            height: 30px;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
            margin-right: 15px;
            font-size: 14px;
        }

        .wish-content {
            display: inline-block;
            vertical-align: top;
            width: calc(100% - 45px);
            color: {{ $activeTheme->getColors('text') ?? '#FFFFFF' }};
            font-size: 1.1em;
            line-height: 1.4;
        }

        /* Control panel */
        .control-panel {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            width: 30ch;
        }

        .control-panel h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .control-panel select,
        .control-panel button {
            width: 100%;
            padding: 8px 12px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .control-panel button {
            background: {{ $activeTheme->getColors('primary') ?? '#002037' }};
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .control-panel button:hover {
            background: {{ $activeTheme->getColors('accent') ?? '#23D09F' }};
        }

        /* Decorative elements */
        .card-decoration {
            position: absolute;
            opacity: 0.1;
            pointer-events: none;
        }

        .decoration-top {
            top: -50px;
            right: -50px;
            font-size: 150px;
            transform: rotate(45deg);
        }

        .decoration-bottom {
            bottom: -50px;
            left: -50px;
            font-size: 120px;
            transform: rotate(-45deg);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .control-panel {
                position: relative;
                width: 100%;
                margin-bottom: 20px;
            }
            
            .wish-card {
                transform: scale(0.7);
                transform-origin: top center;
            }
        }
    </style>
</head>
<body class="layout-{{ $layout }}">
    <!-- Control Panel (hidden when printing) -->
    <div class="control-panel no-print">
        <h3>Card Options</h3>
        
        <label for="layout-select">Layout:</label>
        <select id="layout-select" onchange="changeLayout(this.value)">
            <option value="portrait-a4" {{ $layout == 'portrait-a4' ? 'selected' : '' }}>Portrait A4 (210×297mm)</option>
            <option value="portrait-letter" {{ $layout == 'portrait-letter' ? 'selected' : '' }}>Portrait Letter (8.5×11")</option>
            <option value="landscape-a4" {{ $layout == 'landscape-a4' ? 'selected' : '' }}>Landscape A4 (297×210mm)</option>
            <option value="landscape-letter" {{ $layout == 'landscape-letter' ? 'selected' : '' }}>Landscape Letter (11×8.5")</option>
            <option value="card-5x7" {{ $layout == 'card-5x7' ? 'selected' : '' }}>Card 5×7"</option>
        </select>
        
        <button onclick="window.print()">Print Card</button>
        <button onclick="downloadPDF()">Download PDF</button>
        <button onclick="window.close()">Close</button>
    </div>

    <!-- Wish Card -->
    <div class="card-container">
        <div class="wish-card">
            <!-- Decorative elements -->
            <div class="card-decoration decoration-top">✨</div>
            <div class="card-decoration decoration-bottom">⭐</div>
            
            <!-- Header -->
            <div class="card-header">
                <div class="year">{{ $activeTheme->year }}</div>
                <div class="theme-title">{{ $activeTheme->theme_title }}</div>
                <div class="theme-verse">
                    "{{ $activeTheme->theme_verse_text }}"<br>
                    <strong>— {{ $activeTheme->theme_verse_reference }}</strong>
                </div>
            </div>

            <!-- Wishes Section -->
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
                <div class="wishes-section">
                    <div class="wishes-title">My Wishes</div>
                    @foreach($tempWishes as $wish)
                        <div class="wish-item">
                            <span class="wish-number">{{ $wish->position }}</span>
                            <span class="wish-content">{{ $wish->content }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="wishes-section">
                    <div class="wishes-title">My Wishes</div>
                    <div class="wish-item">
                        <span class="wish-content" style="text-align: center; width: 100%; font-style: italic; opacity: 0.7;">
                            No wishes created yet for {{ $activeTheme->year }}
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function changeLayout(layout) {
            const url = new URL(window.location);
            url.searchParams.set('layout', layout);
            window.location.href = url.toString();
        }

        function downloadPDF() {
            // Hide control panel for cleaner PDF
            const controlPanel = document.querySelector('.control-panel');
            controlPanel.style.display = 'none';
            
            // Print to PDF (browser will show print dialog with PDF option)
            window.print();
            
            // Show control panel again
            setTimeout(() => {
                controlPanel.style.display = 'block';
            }, 1000);
        }

        // Auto-hide control panel on print
        window.addEventListener('beforeprint', function() {
            document.querySelector('.control-panel').style.display = 'none';
        });

        window.addEventListener('afterprint', function() {
            document.querySelector('.control-panel').style.display = 'block';
        });
    </script>
</body>
</html>