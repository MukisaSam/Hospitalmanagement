<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .page { padding: 40px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .hospital-name { font-size: 20px; font-weight: bold; color: #0d6efd; }
        .invoice-title { font-size: 24px; font-weight: bold; text-align: right; }
        .invoice-meta { text-align: right; color: #666; }
        .section { margin-bottom: 24px; }
        .section-title { font-size: 11px; text-transform: uppercase; color: #666; margin-bottom: 8px; font-weight: bold; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .row { display: flex; gap: 24px; margin-bottom: 24px; }
        .col { flex: 1; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background-color: #f8f9fa; text-align: left; padding: 8px 10px; font-size: 11px; text-transform: uppercase; color: #666; border-bottom: 2px solid #e5e7eb; }
        td { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; }
        .text-right { text-align: right; }
        .totals-table { width: 280px; margin-left: auto; }
        .totals-table td { padding: 4px 8px; }
        .total-row td { font-weight: bold; border-top: 2px solid #333; font-size: 13px; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 10px; text-transform: uppercase; font-weight: bold; }
        .badge-paid { background-color: #d1fae5; color: #065f46; }
        .badge-unpaid { background-color: #fee2e2; color: #991b1b; }
        .badge-partial { background-color: #fef3c7; color: #92400e; }
        .footer { text-align: center; color: #999; font-size: 10px; margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 16px; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div>
            <div class="hospital-name">Hospital Management System</div>
            <div style="color:#666; margin-top:4px;">Professional Healthcare Services</div>
        </div>
        <div>
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-meta">
                <div><strong>{{ $invoice->invoice_number }}</strong></div>
                <div>Date: {{ $invoice->created_at?->format('d M Y') }}</div>
                <div>Due: {{ $invoice->due_date?->format('d M Y') ?? 'N/A' }}</div>
                <div style="margin-top:6px;">
                    <span class="status-badge badge-{{ $invoice->status->value }}">
                        {{ ucfirst($invoice->status->value) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="section-title">Bill To</div>
            <div><strong>{{ $invoice->patient->full_name ?? '—' }}</strong></div>
            <div>MRN: {{ $invoice->patient->mrn ?? '' }}</div>
            <div>{{ $invoice->patient->phone_number ?? '' }}</div>
            <div>{{ $invoice->patient->email ?? '' }}</div>
        </div>
        <div class="col">
            <div class="section-title">Doctor</div>
            <div><strong>Dr. {{ $invoice->doctor->full_name ?? '—' }}</strong></div>
            <div>{{ $invoice->doctor->specialization ?? '' }}</div>
            @if($invoice->appointment)
            <div style="margin-top:8px;">
                <div class="section-title">Appointment</div>
                <div>{{ $invoice->appointment->appointment_date?->format('d M Y') }} at {{ substr($invoice->appointment->appointment_time, 0, 5) }}</div>
            </div>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Tax ({{ $invoice->tax_rate }}%)</td>
            <td class="text-right">{{ number_format($invoice->tax_amount, 2) }}</td>
        </tr>
        @if($invoice->discount_amount > 0)
        <tr>
            <td>Discount</td>
            <td class="text-right">-{{ number_format($invoice->discount_amount, 2) }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td>TOTAL</td>
            <td class="text-right">{{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Amount Paid</td>
            <td class="text-right">{{ number_format($invoice->amount_paid, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Balance Due</strong></td>
            <td class="text-right"><strong>{{ number_format(max(0, $invoice->total_amount - $invoice->amount_paid), 2) }}</strong></td>
        </tr>
    </table>

    @if($invoice->payments->isNotEmpty())
    <div class="section" style="margin-top:24px;">
        <div class="section-title">Payment History</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $payment)
                <tr>
                    <td>{{ $payment->payment_date?->format('d M Y') }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $payment->payment_method?->value ?? '')) }}</td>
                    <td>{{ $payment->reference_number ?? '—' }}</td>
                    <td class="text-right">{{ number_format($payment->amount_paid, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Thank you for choosing our healthcare services. For inquiries, please contact administration.
        <br>Generated on {{ now()->format('d M Y H:i') }}
    </div>
</div>
</body>
</html>
