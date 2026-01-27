<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email -  Dav/Devs Three Wishes {{ $year }}</title>
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
            color: {{ $yearTheme->getColors('accent') ?? '#e74c3c' }};
            margin-bottom: 10px;
            font-size: 28px;
        }
        .header p {
            color: #7f8c8d;
            font-size: 16px;
            margin: 10px 0;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 30px;
            color: {{ $yearTheme && $yearTheme->getColors('primary') ? $yearTheme->getColors('primary') : '#2c3e50' }};
        }
        .verification-section {
            background: linear-gradient(135deg, {{ $yearTheme && $yearTheme->getColors('accent') ? $yearTheme->getColors('accent') : '#3498db' }}, {{ $yearTheme && $yearTheme->getColors('primary') ? $yearTheme->getColors('primary') : '#2980b9' }});
            color: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            margin: 40px 0;
        }
        .verify-button {
            display: inline-block;
            background: #6366f1;
            color: white !important;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            text-transform: uppercase;
            margin-top: 20px;
            transition: transform 0.2s;
            letter-spacing: 0.5px;
        }
        .verify-button:hover {
            transform: translateY(-2px);
            background: #5856eb;
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
        .footer {
            text-align: center;
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }
        .year-highlight {
            color: {{ $yearTheme->getColors('secondary') ?? '#e74c3c' }};
            font-weight: bold;
        }
        .manual-link {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 20px;
            line-height: 1.4;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✨ Please Verify Your Email for <span class="year-highlight">Dav/Devs Three Wishes</span> ✨</h1>
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
            Hello, {{ $user->name }}! 🙏
        </div>

        <p>Thank you for joining our Dav/Devs Three Wishes community! We're excited to have you on this journey of faith and spiritual intention-setting for {{ $year }}.</p>

        <div class="info-box">
            <div class="info-title">📧 Email Verification Required</div>
            <div class="info-content">To protect your account and ensure you receive our annual reflection emails, please verify your email address by clicking the button below.</div>
        </div>

        <div class="verification-section">
            <h3>✨ Verify Your Email Address ✨</h3>
            <p>Click the button below to confirm your email and start your Dav/Devs Three Wishes journey!</p>
            <a href="{{ $verificationUrl }}" class="verify-button">Verify Email Address</a>
        </div>

        <p><strong>What happens next?</strong></p>
        <ul>
            <li>✅ Your email will be verified and your account activated</li>
            <li>🎯 You'll receive a welcome email with instructions</li>
            <li>📝 You can set your three spiritual intentions for {{ $year }}</li>
            <li>💌 Each December 31st, we'll send you a beautiful reflection email</li>
        </ul>

        <p><em>"Trust in the Lord with all your heart and lean not on your own understanding; in all your ways submit to him, and he will make your paths straight." - Proverbs 3:5-6</em></p>

        <div class="manual-link">
            <p><strong>Having trouble clicking the button?</strong><br>
            Copy and paste the URL below into your web browser:</p>
            <p>{{ $verificationUrl }}</p>
        </div>

        <div class="footer">
            <p>Blessings and grace,<br>
            <strong>The Dav/Devs Three Wishes Ministry Team</strong></p>
            
            <p style="margin-top: 15px; font-style: italic;">
                "For I know the plans I have for you," declares the Lord, "plans to prosper you and not to harm you, to give you hope and a future." - Jeremiah 29:11
            </p>
            
            <p style="margin-top: 20px; font-size: 12px;">
                You received this email because you created a Dav/Devs Three Wishes account.<br>
                If you didn't create an account, no further action is required.<br>
                <a href="{{ config('app.url') }}/privacy" style="color: #2b7fff; text-decoration: underline;">Privacy Policy</a> | 
                <a href="{{ config('app.url') }}/terms" style="color: #2b7fff; text-decoration: underline;">Terms & Conditions</a>
            </p>
        </div>
    </div>
</body>
</html>