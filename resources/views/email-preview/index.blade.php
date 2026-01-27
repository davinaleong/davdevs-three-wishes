<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template Previews</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
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
        h1 {
            color: #2b7fff;
            text-align: center;
            margin-bottom: 40px;
        }
        .email-list {
            display: grid;
            gap: 20px;
            margin-top: 30px;
        }
        .email-card {
            border: 1px solid #e1e5e9;
            border-radius: 8px;
            padding: 20px;
            background: #f8f9fa;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .email-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .email-card h3 {
            margin: 0 0 10px 0;
            color: #2b7fff;
        }
        .email-card p {
            margin: 0 0 15px 0;
            color: #666;
        }
        .preview-btn {
            display: inline-block;
            background: #2b7fff;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }
        .preview-btn:hover {
            background: #1447e6;
        }
        .note {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
            color: #1565c0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Email Template Previews</h1>
        
        <div class="note">
            <strong>💡 Note:</strong> These previews use test data and current theme settings. No actual emails are sent when viewing these previews.
        </div>

        <div class="email-list">
            @foreach($emails as $key => $name)
            <div class="email-card">
                <h3>{{ $name }}</h3>
                <p>Preview how this email will look with current theme colors and test data.</p>
                <a href="{{ route('email-preview.show', $key) }}" class="preview-btn" target="_blank">
                    👀 Preview Template
                </a>
            </div>
            @endforeach
        </div>

        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e1e5e9; text-align: center; color: #666; font-size: 14px;">
            <p>These previews help you test email designs without sending actual emails.</p>
        </div>
    </div>
</body>
</html>