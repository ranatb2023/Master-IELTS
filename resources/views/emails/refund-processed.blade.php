<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 30px;
        }

        .info-box {
            background: #f9fafb;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .amount {
            font-size: 32px;
            font-weight: bold;
            color: #10b981;
            text-align: center;
            margin: 20px 0;
        }

        .timeline {
            background: #fef3c7;
            border: 1px solid #fbbf24;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }

        .timeline strong {
            color: #92400e;
        }

        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }

        table {
            width: 100%;
            margin: 15px 0;
        }

        td {
            padding: 8px 0;
        }

        .label {
            color: #6b7280;
            font-weight: 500;
        }

        .value {
            text-align: right;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Refund Processed</h1>
            <p style="margin: 10px 0 0 0;">Master IELTS</p>
        </div>

        <div class="content">
            <p>Hi {{ $user->name }},</p>

            <p>Your refund request has been processed successfully. We're sorry to see you go, but we understand that
                circumstances change.</p>

            <div class="amount">
                ${{ number_format($refundAmount, 2) }}
            </div>

            <div class="info-box">
                <h3 style="margin-top: 0;">Refund Details</h3>
                <table>
                    <tr>
                        <td class="label">Course:</td>
                        <td class="value">{{ $course->title }}</td>
                    </tr>
                    <tr>
                        <td class="label">Refund Amount:</td>
                        <td class="value">${{ number_format($refundAmount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Processed On:</td>
                        <td class="value">{{ $enrollment->refunded_at->format('F d, Y') }}</td>
                    </tr>
                    @if($refundReason)
                        <tr>
                            <td class="label">Reason:</td>
                            <td class="value">{{ $refundReason }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            <div class="timeline">
                <strong>⏱️ Refund Timeline:</strong><br>
                Your refund of <strong>${{ number_format($refundAmount, 2) }}</strong> will be credited back to your
                original payment method within <strong>5-10 business days</strong>. The exact timing depends on your
                bank or card issuer.
            </div>

            <h3>What This Means:</h3>
            <ul>
                <li>✅ Your refund has been initiated with our payment processor</li>
                <li>🔒 Your access to "{{ $course->title }}" has been revoked</li>
                <li>💳 Funds will appear in your account within 5-10 business days</li>
                <li>📧 You'll see the refund as "REFUND: MASTER IELTS" on your statement</li>
            </ul>

            <h3>Need Help?</h3>
            <p>If you have any questions about your refund or if you don't see it reflected in your account after 10
                business days, please don't hesitate to contact our support team.</p>

            <div style="text-align: center;">
                <a href="mailto:support@masterielts.com" class="button">Contact Support</a>
            </div>

            <p style="margin-top: 30px;">We hope to see you again in the future!</p>

            <p>Best regards,<br>
                <strong>Master IELTS Team</strong>
            </p>
        </div>

        <div class="footer">
            <p><strong>Master IELTS</strong></p>
            <p>© {{ date('Y') }} Master IELTS. All rights reserved.</p>
            <p style="margin-top: 10px;">
                <a href="mailto:support@masterielts.com"
                    style="color: #667eea; text-decoration: none;">support@masterielts.com</a>
            </p>
        </div>
    </div>
</body>

</html>