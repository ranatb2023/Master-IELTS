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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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

        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #155724;
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
            background: #28a745;
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
        <h1 style="margin: 0;">🎉 Welcome Back!</h1>
        <p style="margin: 10px 0 0 0;">Your subscription has been resumed</p>
    </div>

    <div class="content">
        <p>Hi {{ $user->name }},</p>

        <p>Great news! Your subscription has been successfully resumed. We're thrilled to have you back!</p>

        <div class="success-box">
            <strong>✓ Subscription Active:</strong> Your full access has been restored immediately.
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0;">What's Restored?</h3>
            <ul>
                <li>✓ <strong>Full access to all subscription courses</strong></li>
                <li>✓ All your previous progress and enrollments</li>
                <li>✓ Access to new courses added to your plan</li>
                <li>✓ Automatic renewal at the end of your billing period</li>
            </ul>
        </div>

        <h3>Ready to Continue Learning?</h3>
        <p>Jump right back in and pick up where you left off. All your progress has been preserved!</p>

        <center>
            <a href="{{ route('student.enrollments.index') }}" class="button">
                Go to My Courses
            </a>
        </center>

        <div class="info-box">
            <h3 style="margin-top: 0;">Your Subscription Details</h3>
            <p><strong>Plan:</strong> {{ $subscription->subscriptionPlan->name ?? 'Subscription Plan' }}</p>
            <p><strong>Status:</strong> <span style="color: #28a745;">Active</span></p>
            <p><strong>Billing:</strong> Auto-renews at the end of your current period</p>

            <center style="margin-top: 20px;">
                <a href="{{ route('student.subscriptions.manage') }}"
                    style="color: #667eea; text-decoration: none; font-weight: bold;">
                    Manage Subscription →
                </a>
            </center>
        </div>

        <p>Thank you for continuing your learning journey with us. If you have any questions, feel free to reach out!
        </p>

        <p>Happy learning!<br>
            The {{ config('app.name') }} Team</p>
    </div>

    <div class="footer">
        <p>This email was sent to {{ $user->email }}<br>
            <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
        </p>
    </div>
</body>

</html>