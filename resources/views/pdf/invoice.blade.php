<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Factura {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #ffffff;
        }

        .page {
            padding: 36px 40px;
            min-height: 100vh;
            position: relative;
        }

        .watermark {
            position: fixed;
            top: 300px;
            left: 50%;
            margin-left: -150px; /* half of width */
            width: 300px;
            opacity: 0.12;
            z-index: -10;
            text-align: center;
        }

        .watermark img {
            width: 100%;
            object-fit: contain;
        }

        /* 🟦 HEADER 🟦 */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 28px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 24px;
        }

        .company-name {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .company-info p {
            color: #64748b;
            font-size: 10px;
            margin-bottom: 3px;
            line-height: 1.6;
        }

        /* 🟦 INVOICE META 🟦 */
        .invoice-meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 36px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .invoice-title {
            font-size: 16px;
            color: #0f172a;
            font-weight: 800;
            letter-spacing: -0.3px;
            margin-bottom: 6px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .invoice-title span {
            color: #2563eb;
            font-size: 14px;
        }

        .invoice-badge {
            background: white;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            display: inline-block;
            min-width: 120px;
        }

        .invoice-badge .label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .invoice-badge .value {
            font-size: 11px;
            color: #0f172a;
            font-weight: 600;
        }

        /* 🟦 TWO COLUMN GRID 🟦 */
        .info-grid {
            display: flex;
            gap: 24px;
            margin-bottom: 36px;
        }

        .info-box {
            flex: 1;
        }

        .box-title {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 6px;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }

        .field {
            margin-bottom: 6px;
            display: flex;
            align-items: flex-start;
        }

        .field-label {
            font-weight: 600;
            color: #64748b;
            width: 75px;
            flex-shrink: 0;
        }

        .field-value {
            color: #334155;
            font-weight: 500;
        }

        /* 🟦 TABLE 🟦 */
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }

        .service-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .service-table th {
            background: #1e3a8a;
            color: white;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            text-align: left;
            font-weight: 600;
            border-radius: 6px 6px 0 0;
        }

        .service-table td {
            padding: 16px 14px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
            font-size: 12px;
            font-weight: 500;
            background: #f8fafc;
        }

        .service-table th:last-child,
        .service-table td:last-child {
            text-align: right;
        }

        /* 🟦 NOTES 🟦 */
        .notes-box {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-left: 3px solid #f59e0b;
            padding: 12px 16px;
            border-radius: 0 6px 6px 0;
        }

        .notes-label {
            font-size: 9px;
            font-weight: 700;
            color: #d97706;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .notes-box p {
            color: #92400e;
            font-size: 10px;
            line-height: 1.5;
        }

        /* 🟦 TOTALS 🟦 */
        .total-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 24px;
            margin-bottom: 36px;
        }

        .total-box {
            width: 250px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 14px;
            font-size: 12px;
        }

        .total-row.subtotal {
            color: #64748b;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }

        .total-row.grand-total {
            background: #1e3a8a;
            color: white;
            border-radius: 6px;
            padding: 12px 14px;
            font-size: 14px;
            font-weight: 700;
        }

        /* 🟦 SIGNATURE 🟦 */
        .signature-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 8px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .signature-box {
            text-align: center;
            min-width: 180px;
        }

        .signature-line {
            border-top: 2px solid #1e3a8a;
            margin-bottom: 6px;
        }

        .signature-label {
            font-size: 9px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* 🟦 FOOTER 🟦 */
        .invoice-footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
        }

        .invoice-footer p {
            font-size: 9px;
            color: #94a3b8;
            line-height: 1.6;
        }

        .invoice-footer .thank-you {
            font-size: 12px;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 4px;
        }

        /* 🟦 STATUS BADGE 🟦 */
        .status-sent { color: #059669; font-weight: 700; }
        .status-pending { color: #d97706; font-weight: 700; }
        .status-error { color: #dc2626; font-weight: 700; }
    </style>
</head>
<body>

@if($logoBase64)
<div class="watermark">
    <img src="{{ $logoBase64 }}" alt="Marca de agua" />
</div>
@endif

<div class="page">

    {{-- HEADER --}}
    <div class="header">
        <div class="company-info">
            <div class="company-name">{{ $settings?->company_name ?? 'Mi Empresa' }}</div>
            @if($settings?->company_phone)
                <p>📞 {{ $settings->company_phone }}</p>
            @endif
            @if($settings?->company_email)
                <p>📧 {{ $settings->company_email }}</p>
            @endif
            @if($settings?->company_address)
                <p>📍 {{ $settings->company_address }}</p>
            @endif
        </div>
    </div>

    {{-- INVOICE META --}}
    <div class="invoice-meta">
        <div>
            <div class="invoice-title">
                FACTURA DE SERVICIO
                <span>N° {{ $invoice->invoice_number }}</span>
            </div>
        </div>
        <div style="text-align: right;">
            <div class="invoice-badge" style="margin-bottom: 8px;">
                <div class="label">Fecha</div>
                <div class="value">{{ $invoice->invoice_date->format('d/m/Y') }}</div>
            </div>
            <div class="invoice-badge">
                <div class="label">Estado</div>
                <div class="value status-{{ $invoice->status }}">{{ $invoice->status_label }}</div>
            </div>
        </div>
    </div>

    {{-- CLIENT + SERVICE INFO --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="box-title">Información del Cliente</div>
            <div class="field">
                <span class="field-label">Nombre: </span>
                <span class="field-value">{{ $client->name }}</span>
            </div>
            @if($client->whatsapp)
            <div class="field">
                <span class="field-label">WhatsApp: </span>
                <span class="field-value">{{ $client->whatsapp }}</span>
            </div>
            @endif
            @if($client->email)
            <div class="field">
                <span class="field-label">Email: </span>
                <span class="field-value">{{ $client->email }}</span>
            </div>
            @endif
            @if($client->address)
            <div class="field">
                <span class="field-label">Dirección: </span>
                <span class="field-value">{{ $client->address }}</span>
            </div>
            @endif
        </div>

        <div class="info-box">
            <div class="box-title">Datos de la Factura</div>
            <div class="field">
                <span class="field-label">N° Factura: </span>
                <span class="field-value">{{ $invoice->invoice_number }}</span>
            </div>
            <div class="field">
                <span class="field-label">Fecha: </span>
                <span class="field-value">{{ $invoice->invoice_date->format('d \d\e F \d\e Y') }}</span>
            </div>
            <div class="field">
                <span class="field-label">Moneda: </span>
                <span class="field-value">{{ $settings?->currency ?? 'COP' }}</span>
            </div>
            <div class="field">
                <span class="field-label">Estado: </span>
                <span class="field-value status-{{ $invoice->status }}">{{ $invoice->status_label }}</span>
            </div>
        </div>
    </div>

    {{-- SERVICE TABLE --}}
    <div class="section-title">Descripción del Servicio</div>
    <table class="service-table">
        <thead>
            <tr>
                <th style="width: 70%;">Servicio / Descripción</th>
                <th style="width: 30%;">Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $invoice->service }}</td>
                <td>
                    {{ $settings?->currency ?? 'COP' }}
                    {{ number_format((float)$invoice->value, 2, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- NOTES --}}
    @if($invoice->notes)
    <div class="notes-box" style="margin-top: 10px;">
        <div class="notes-label">📋 Observaciones</div>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    {{-- TOTAL --}}
    <div class="total-section">
        <div class="total-box">
            <div class="total-row subtotal">
                <span>Subtotal</span>
                <span>
                    {{ $settings?->currency ?? 'COP' }}
                    {{ number_format((float)$invoice->value, 2, ',', '.') }}
                </span>
            </div>
            <div class="total-row grand-total" style="{{ (!$invoice->is_paid && !is_null($invoice->pending_amount)) ? 'border-bottom-left-radius: 0; border-bottom-right-radius: 0;' : '' }}">
                <span>TOTAL</span>
                <span>
                    {{ $settings?->currency ?? 'COP' }} 
                    {{ number_format((float)$invoice->value, 2, ',', '.') }}
                </span>
            </div>
            @if(!$invoice->is_paid && !is_null($invoice->pending_amount))
            <div class="total-row" style="background: #fffbeb; color: #d97706; font-weight: 700; padding: 12px 14px; font-size: 13px;">
                <span>SALDO PENDIENTE</span>
                <span>
                    {{ $settings?->currency ?? 'COP' }} 
                    {{ number_format((float)$invoice->pending_amount, 2, ',', '.') }}
                </span>
            </div>
            @endif
        </div>
    </div>

    {{-- SIGNATURE --}}
    <div class="signature-section" style="justify-content: flex-end;">
        <div style="text-align: right; font-size: 9px; color: #94a3b8;">
            <div>Documento generado el</div>
            <div style="font-weight: 600; color: #64748b;">{{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="invoice-footer">
        <div class="thank-you">¡Gracias por su confianza!</div>
        <p>Este documento es una factura de servicios técnicos.</p>
        @if($settings?->company_phone)
        <p>Para cualquier consulta contáctenos: {{ $settings->company_phone }}</p>
        @endif
    </div>

</div>
</body>
</html>
