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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .info-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-row:last-child {
            border-bottom: none;
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
        <h1 style="margin: 0;">Subscription Cancelled</h1>
        <p style="margin: 10px 0 0 0;">We're sorry to see you go</p>
    </div>

    <div class="content">
        <p>Hi {{ $user->name }},</p>

        <p>We've received your request to cancel your subscription. While we're sad to see you go, we want you to know
            that you'll retain access to all your subscription benefits until your current billing period ends.</p>

        <div class="alert">
            <strong>⏰ Important:</strong> Your subscription will remain active until
            <strong>{{ $endsAt->format('F j, Y') }}</strong>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0;">What Happens Next?</h3>

            <div class="info-row">
                <span><strong>Current Access</strong></span>
                <span style="color: #28a745;">✓ Active</span>
            </div>

            <div class="info-row">
                <span><strong>Access Ends On</strong></span>
                <span>{{ $endsAt->format('M d, Y') }}</span>
            </div>

            <div class="info-row">
                <span><strong>Next Billing</strong></span>
                <span style="color: #6c757d;">Cancelled</span>
            </div>
        </div>

        <h3>Until {{ $endsAt->format('M d, Y') }}, you can:</h3>
        <ul>
            <li>✓ Access all your courses</li>
            <li>✓ Complete lessons and track progress</li>
            <li>✓ Download course materials</li>
            <li>✓ <strong>Change your mind and resume your subscription</strong></li>
        </ul>

        <h3>Changed your mind?</h3>
        <p>You can resume your subscription anytime before {{ $endsAt->format('M d, Y') }} with just one click. No need
            to go through checkout again!</p>

        <center>
            <a href="{{ route('student.subscriptions.manage') }}" class="button">
                Resume Subscription
            </a>
        </center>

        <p>If you cancelled because of an issue or have feedback for us, we'd love to hear from you. Just reply to this
            email and let us know how we can improve.</p>

        <p>Thank you for being part of our community!</p>

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