<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your {{ $year }} Dav/Devs Three Wishes</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            color: #2b7fff;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .header p {
            color: #7f8c8d;
            font-size: 16px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .wishes-section {
            margin: 40px 0;
        }
        .wishes-title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .wish-item {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 0 6px 6px 0;
        }
        .wish-theme {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 16px;
        }
        .wish-content {
            color: #555;
            font-style: italic;
            line-height: 1.5;
        }
        .cta-section {
            background: linear-gradient(135deg, #2b7fff, #1447e6);
            color: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            margin: 40px 0;
        }
        .cta-button {
            display: inline-block;
            background: #2b7fff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            margin-top: 15px;
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        .footer {
            text-align: center;
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }
        .year-highlight {
            color: #e74c3c;
            font-weight: bold;
        }
        .reflection-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }
        .reflection-title {
            color: #b8860b;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✨ Your <span class="year-highlight">{{ $year }}</span> Dav/Devs Three Wishes ✨</h1>
            <p>"For I know the plans I have for you," declares the Lord, "plans to prosper you and not to harm you, to give you hope and a future." - Jeremiah 29:11</p>
            @if($yearTheme)
                <p><strong>{{ $year }} Theme:</strong> {{ $yearTheme->theme_title }}</p>
                @if($yearTheme->theme_tagline)
                    <p><em>{{ $yearTheme->theme_tagline }}</em></p>
                @endif
                @if($yearTheme->theme_verse_reference && $yearTheme->theme_verse_text)
                    <p style="margin-top: 15px;"><strong>{{ $yearTheme->theme_verse_reference }}</strong><br>
                    <em>"{{ $yearTheme->theme_verse_text }}"</em></p>
                @endif
            @endif
        </div>

        <div class="greeting">
            Dear {{ $user->name }}, blessed child of God,
        </div>

        <p>As we stand at the threshold of a new year, let us pause to give thanks for God's faithfulness throughout {{ $year }}. It's a sacred time to reflect on the hopes and dreams you entrusted to the Lord, and to see how He has been working in your life.</p>

        @if($wishes->count() > 0)
            <div class="wishes-section">
                <div class="wishes-title">The Hopes You Brought Before God in {{ $year }}</div>
                
                @foreach($wishes as $wish)
                    <div class="wish-item">
                        <div class="wish-theme">{{ $wish->theme->name ?? 'General' }} (Position {{ $wish->position }})</div>
                        <div class="wish-content">"{{ $wish->content }}"</div>
                    </div>
                @endforeach
            </div>

            <div class="reflection-section">
                <div class="reflection-title">� Time for Spiritual Reflection</div>
                <p>As you look back on {{ $year }}, consider these questions in prayer:</p>
                <ul>
                    <li>How has God shown His faithfulness in answering your prayers?</li>
                    <li>What challenges did the Lord help you overcome with His strength?</li>
                    <li>How has God grown your faith and character this year?</li>
                    <li>What new ways has the Holy Spirit been leading you?</li>
                    <li>How can you give glory to God for His goodness in your life?</li>
                </ul>
                <p><em>"Give thanks to the Lord, for he is good; his love endures forever." - Psalm 107:1</em></p>
            </div>
        @endif

        <div class="cta-section">
            <h3>✨ Stepping into {{ $year + 1 }} with Faith ✨</h3>
            <p>As you enter this new year, what hopes will you bring before the Lord? What dreams is He placing on your heart? Trust in His perfect timing and goodness as you set your intentions for {{ $year + 1 }}.</p>
            <a href="{{ config('app.url') }}" class="cta-button">Prayerfully Set My New Dav/Devs Three Wishes</a>
        </div>

        <p>The practice of Dav/Devs Three Wishes reminds us that while we may not know what the future holds, we know Who holds the future. In every season, we can bring our hopes and dreams before our loving Father, trusting in His perfect will.</p>

        <p>May {{ $year + 1 }} be a year where you experience God's abundant blessings and walk closely with Him! 🙏</p>

        <p><em>"Commit to the Lord whatever you do, and he will establish your plans." - Proverbs 16:3</em></p>

        <div class="footer">
            <p>Praying God's richest blessings over your life,<br>
            <strong>The Dav/Devs Three Wishes Ministry Team</strong></p>
            
            <p style="margin-top: 15px; font-style: italic;">
                "May the God of hope fill you with all joy and peace as you trust in him, so that you may overflow with hope by the power of the Holy Spirit." - Romans 15:13
            </p>
            
            <p style="margin-top: 20px; font-size: 12px;">
                This email was sent because you have an account with Dav/Devs Three Wishes.<br>
                If you no longer wish to receive these annual reminders, you can update your preferences in your account settings.<br>
                <a href="{{ config('app.url') }}/privacy" style="color: #2b7fff; text-decoration: underline;">Privacy Policy</a> | 
                <a href="{{ config('app.url') }}/terms" style="color: #2b7fff; text-decoration: underline;">Terms & Conditions</a>
            </p>
        </div>
    </div>
</body>
</html>