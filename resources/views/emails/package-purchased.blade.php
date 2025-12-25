<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }

        .package-details {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .amount {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white !important;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }

        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-radius: 0 0 8px 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        td {
            padding: 8px 0;
        }

        .label {
            color: #6b7280;
        }

        .value {
            font-weight: 600;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 style="margin: 0;">Thank You for Your Purchase!</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Your order has been confirmed</p>
    </div>

    <div class="content">
        <p>Hi {{ $user->name }},</p>

        <p>Thank you for purchasing <strong>{{ $package->name }}</strong>! Your payment has been successfully processed.
        </p>

        <div class="package-details">
            <h3 style="margin-top: 0;">Package Details</h3>
            <p><strong>{{ $package->name }}</strong></p>
            <p style="color: #6b7280; margin: 5px 0;">{{ $package->courses->count() }} courses included</p>

            <table>
                <tr>
                    <td class="label">Invoice Number:</td>
                    <td class="value">{{ $invoice->number }}</td>
                </tr>
                <tr>
                    <td class="label">Date:</td>
                    <td class="value">{{ $invoice->issued_at->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="value">${{ number_format($invoice->data['amount'] ?? 0, 2) }}</td>
                </tr>
                @if($invoice->tax > 0)
                    <tr>
                        <td class="label">Tax:</td>
                        <td class="value">${{ number_format($invoice->data['tax'] ?? 0, 2) }}</td>
                    </tr>
                @endif
                <tr style="border-top: 2px solid #e5e7eb;">
                    <td class="label"><strong>Total Paid:</strong></td>
                    <td class="value">
                        <div class="amount">${{ number_format($invoice->total, 2) }}</div>
                    </td>
                </tr>
            </table>

            <p style="margin: 10px 0 0 0;">
                <strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}
            </p>
        </div>

        <h3>Getting Started</h3>
        <p>You now have access to all courses in this package! Here's how to get started:</p>
        <ol>
            <li>Visit your <a href="{{ route('student.packages.index') }}">My Packages</a> page</li>
            <li>Click on "{{ $package->name }}" to view all included courses</li>
            <li>Start learning at your own pace</li>
        </ol>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('student.packages.index') }}" class="button">View My Packages</a>
        </div>

        <p style="font-size: 14px; color: #6b7280; margin-top: 30px;">
            <strong>Invoice attached:</strong> Your invoice has been attached to this email as a PDF. You can also view
            and download it anytime from your account.
        </p>
    </div>

    <div class="footer">
        <p>Need help? Contact us at <a href="mailto:support@masterielts.com">support@masterielts.com</a></p>
        <p style="margin-top: 15px;">© {{ date('Y') }} Master IELTS. All rights reserved.</p>
    </div>
</body>

</html>