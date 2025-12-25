<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 36px;
        }

        .invoice-info {
            background: #f9fafb;
            padding: 30px;
            display: table;
            width: 100%;
        }

        .invoice-info-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .invoice-info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        .section-title {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .content {
            padding: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th {
            background: #f9fafb;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #e5e7eb;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            margin-top: 30px;
            float: right;
            width: 300px;
        }

        .totals table {
            margin: 0;
        }

        .totals td {
            border: none;
            padding: 8px 12px;
        }

        .total-row {
            background: #f9fafb;
            font-weight: bold;
            font-size: 18px;
        }

        .paid-badge {
            background: #10b981;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            margin-top: 40px;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="header">
            <h1>INVOICE</h1>
            <p style="margin: 10px 0 0 0; font-size: 18px;">Master IELTS</p>
        </div>

        <div class="invoice-info">
            <div class="invoice-info-left">
                <div class="section-title">Bill To</div>
                <strong>{{ $user->name }}</strong><br>
                {{ $user->email }}<br>
                @if($user->phone)
                    {{ $user->phone }}<br>
                @endif
            </div>
            <div class="invoice-info-right">
                <div class="section-title">Invoice Details</div>
                <strong>{{ $invoice->number }}</strong><br>
                <strong>Date:</strong> {{ $invoice->issued_at->format('F d, Y') }}<br>
                <strong>Status:</strong> <span class="paid-badge">PAID</span>
            </div>
        </div>

        <div class="content">
            <h3>Package Purchase</h3>

            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-right">Quantity</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>{{ $package->name }}</strong><br>
                            <small style="color: #6b7280;">
                                Includes {{ $package->courses->count() }} courses
                                @if(!$package->is_lifetime)
                                    | {{ $package->duration_days }} days access
                                @else
                                    | Lifetime access
                                @endif
                            </small>
                        </td>
                        <td class="text-right">1</td>
                        <td class="text-right">${{ number_format(($invoice->data['amount'] ?? 0), 2) }}</td>
                        <td class="text-right">${{ number_format(($invoice->data['amount'] ?? 0), 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="totals">
                <table>
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-right">${{ number_format(($invoice->data['amount'] ?? 0), 2) }}</td>
                    </tr>
                    @if(($invoice->data['tax'] ?? 0) > 0)
                        <tr>
                            <td>Tax:</td>
                            <td class="text-right">${{ number_format(($invoice->data['tax'] ?? 0), 2) }}</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td>Total:</td>
                        <td class="text-right">${{ number_format(($invoice->data['total'] ?? 0), 2) }}</td>
                    </tr>
                    <tr>
                        <td>Amount Paid:</td>
                        <td class="text-right" style="color: #10b981;">
                            ${{ number_format(($invoice->data['total'] ?? 0), 2) }}</td>
                    </tr>
                </table>
            </div>

            <div class="clear"></div>

            <div style="margin-top: 40px; padding: 20px; background: #f9fafb; border-radius: 8px;">
                <strong>Payment Information</strong><br>
                <small style="color: #6b7280;">
                    Payment Method: {{ ucfirst($order->payment_method) }}<br>
                    Transaction ID: {{ $order->payment_id }}<br>
                    Payment Date:
                    {{ \Carbon\Carbon::parse($invoice->data['paid_at'] ?? $invoice->issued_at)->format('F d, Y g:i A') }}
                </small>
            </div>
        </div>

        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>If you have any questions about this invoice, please contact us at support@masterielts.com</p>
            <p style="margin-top: 15px;">© {{ date('Y') }} Master IELTS. All rights reserved.</p>
        </div>
    </div>
</body>

</html>