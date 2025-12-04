<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $invoice->number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size:10pt; }
        .header, .footer { text-align:center; margin-bottom:30px; }
        table { width:100%; border-collapse:collapse; }
        th, td { border:1px solid #ccc; padding:8px; text-align:left; }
        .text-right { text-align:right; }
        .total { font-size:1.4em; font-weight:bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>FACTURE {{ $invoice->number }}</h1>
        Date : {{ $invoice->date->format('d/m/Y') }}
    </div>

    <!-- Adresses client / société ici -->

    <table>
        <thead>
            <tr><th>Désignation</th><th>Qté</th><th>PU HT</th><th>Total HT</th></tr>
        </thead>
        <tbody>
            @foreach($invoice->lines as $line)
                <tr>
                    <td>{{ $line->description }}</td>
                    <td>{{ $line->quantity }}</td>
                    <td class="text-right">{{ number_format($line->unit_price_ht, 2) }} €</td>
                    <td class="text-right">{{ number_format($line->total_ht, 2) }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top:30px;float:right;">
        <tr><td>Total HT</td><td class="text-right">{{ number_format($invoice->total_ht, 2) }} €</td></tr>
        <tr><td>TVA</td><td class="text-right">{{ number_format($invoice->total_vat, 2) }} €</td></tr>
        <tr class="total"><td>TOTAL TTC</td><td class="text-right">{{ number_format($invoice->total_ttc, 2) }} €</td></tr>
    </table>
</body>
</html>