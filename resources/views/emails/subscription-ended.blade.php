<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            padding: 30px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }

        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }

        .alert {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #721c24;
        }

        .info-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .button {
            display: inline-block;
            background: #667eea;
            color: white !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-size: 14px;
        }

        ul {
            padding-left: 20px;
        }

        li {
            margin: 8px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 style="margin: 0;">Subscription Ended</h1>
        <p style="margin: 10px 0 0 0;">Your access has expired</p>
    </div>

    <div class="content">
        <p>Hi {{ $user->name }},</p>

        <p>Your subscription has ended and your access to premium courses has been suspended. We hope you enjoyed
            learning with us!</p>

        <div class="alert">
            <strong>⚠ Access Suspended:</strong> Your subscription-based course enrollments have been suspended.
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0;">What Happened?</h3>
            <p>Your subscription ended and wasn't renewed. This means:</p>
            <ul>
                <li>❌ Access to subscription courses has been suspended</li>
                <li>❌ You won't be charged anymore</li>
                <li>✓ Your learning progress has been preserved</li>
                <li>✓ You can resubscribe anytime to regain access</li>
            </ul>
        </div>

        <h3>Want to Continue Learning?</h3>
        <p>Resubscribe now to regain instant access to all your courses and pick up right where you left off. Your
            progress has been saved!</p>

        <center>
            <a href="{{ route('student.subscriptions.index') }}" class="button">
                View Subscription Plans
            </a>
        </center>

        <div class="info-box">
            <h3 style="margin-top: 0;">Other Ways to Access Courses</h3>
            <p>You can also:</p>
            <ul>
                <li>Purchase individual courses</li>
                <li>Buy course packages</li>
                <li>Enroll in free courses</li>
            </ul>
            <center>
                <a href="{{ route('student.courses.index') }}"
                    style="color: #667eea; text-decoration: none; font-weight: bold;">
                    Browse All Courses →
                </a>
            </center>
        </div>

        <p>Thank you for being part of our learning community. We hope to see you again soon!</p>

        <p>Best regards,<br>
            The {{ config('app.name') }} Team</p>
    </div>

    <div class="footer">
        <p>This email was sent to {{ $user->email }}<br>
            <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
        </p>
    </div>
</body>

</html>