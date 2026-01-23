<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Three Wishes - {{ $year }}</title>
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
            color: {{ $yearTheme && $yearTheme->getColors('primary') ? $yearTheme->getColors('primary') : '#2c3e50' }};
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
            color: {{ $yearTheme && $yearTheme->getColors('primary') ? $yearTheme->getColors('primary') : '#2c3e50' }};
        }
        .welcome-section {
            margin: 40px 0;
        }
        .section-title {
            font-size: 20px;
            color: {{ $yearTheme && $yearTheme->getColors('primary') ? $yearTheme->getColors('primary') : '#2c3e50' }};
            margin-bottom: 20px;
            border-bottom: 2px solid {{ $yearTheme && $yearTheme->getColors('accent') ? $yearTheme->getColors('accent') : '#3498db' }};
            padding-bottom: 10px;
        }
        .info-box {
            background: {{ $yearTheme && $yearTheme->getColors('light') ? $yearTheme->getColors('light') : '#f8f9fa' }};
            border-left: 4px solid {{ $yearTheme && $yearTheme->getColors('accent') ? $yearTheme->getColors('accent') : '#3498db' }};
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 0 6px 6px 0;
        }
        .info-title {
            font-weight: bold;
            color: {{ $yearTheme && $yearTheme->getColors('primary') ? $yearTheme->getColors('primary') : '#2c3e50' }};
            margin-bottom: 8px;
            font-size: 16px;
        }
        .info-content {
            color: #555;
            line-height: 1.5;
        }
        .cta-section {
            background: linear-gradient(135deg, {{ $yearTheme && $yearTheme->getColors('accent') ? $yearTheme->getColors('accent') : '#3498db' }}, {{ $yearTheme && $yearTheme->getColors('primary') ? $yearTheme->getColors('primary') : '#2980b9' }});
            color: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            margin: 40px 0;
        }
        .cta-button {
            display: inline-block;
            background: #6366f1;
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
            color: {{ $yearTheme && $yearTheme->getColors('accent') ? $yearTheme->getColors('accent') : '#e74c3c' }};
            font-weight: bold;
        }
        .email-info-section {
            background: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }
        .email-info-title {
            color: #0c5460;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✨ Welcome to <span class="year-highlight">Three Wishes</span> ✨</h1>
            <p>"And we know that in all things God works for the good of those who love him, who have been called according to his purpose." - Romans 8:28</p>
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
            Welcome, {{ $user->name }}! 🙏
        </div>

        <p>Thank you for joining our Three Wishes community! Your email has been successfully verified, and we're excited to journey with you as you seek God's will and set your spiritual intentions for this year.</p>

        <div class="welcome-section">
            <div class="section-title">What is Three Wishes?</div>
            
            <div class="info-box">
                <div class="info-title">🎯 Your Spiritual Goals</div>
                <div class="info-content">Three Wishes is a sacred space where you can prayerfully set three meaningful intentions for the year, trusting God to work in His perfect timing.</div>
            </div>

            <div class="info-box">
                <div class="info-title">📅 Annual Reflection</div>
                <div class="info-content">Each December 31st, we'll send you a beautiful reminder of your year's wishes, encouraging spiritual reflection and preparation for the new year ahead.</div>
            </div>

            <div class="info-box">
                <div class="info-title">💌 Community of Faith</div>
                <div class="info-content">Join believers worldwide in the practice of bringing their hopes and dreams before the Lord, seeking His guidance in all things.</div>
            </div>
        </div>

        <div class="email-info-section">
            <div class="email-info-title">📧 Important Email Information</div>
            <p><strong>Please add noreply@gracesoft.dev to your contacts</strong> to ensure you receive all important communications from us:</p>
            <ul>
                <li><strong>Annual Wish Emails:</strong> Your beautiful yearly reflection emails on December 31st</li>
                <li><strong>Account Notifications:</strong> Password resets, email verification, and other account-related communications</li>
                <li><strong>Spiritual Encouragement:</strong> Occasional messages of faith and inspiration</li>
            </ul>
            <p><em>We respect your inbox and will only send meaningful, purposeful communications that support your spiritual journey.</em></p>
        </div>

        <div class="cta-section">
            <h3>✨ Ready to Set Your {{ $year }} Three Wishes? ✨</h3>
            <p>Now that you're verified, you can begin the beautiful practice of bringing your hopes and dreams before the Lord. What three things will you trust Him with this year?</p>
            <a href="{{ config('app.url') }}/dashboard" class="cta-button">Set My Three Wishes</a>
        </div>

        <p>Remember, this isn't about demanding outcomes, but about aligning your heart with God's will and trusting His perfect plan for your life. Each wish is an opportunity to draw closer to Him and experience His faithfulness.</p>

        <p>We're praying for you and believing that {{ $year }} will be a year of God's abundant blessings in your life! 🙏</p>

        <p><em>"Trust in the Lord with all your heart and lean not on your own understanding; in all your ways submit to him, and he will make your paths straight." - Proverbs 3:5-6</em></p>

        <div class="footer">
            <p>Blessings and grace,<br>
            <strong>The Three Wishes Ministry Team</strong></p>
            
            <p style="margin-top: 15px; font-style: italic;">
                "Now to him who is able to do immeasurably more than all we ask or imagine, according to his power that is at work within us..." - Ephesians 3:20
            </p>
            
            <p style="margin-top: 20px; font-size: 12px;">
                You received this email because you just verified your Three Wishes account.<br>
                To manage your email preferences, visit your account settings.
            </p>
        </div>
    </div>
</body>
</html>