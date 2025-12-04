{{-- Ticket ESC/POS classique – très rapide, zéro CSS lourd --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', monospace; font-size: 11pt; margin:0; padding:8px; width: 100%; }
        .center { text-align:center; }
        .right  { text-align:right; }
        .bold   { font-weight:bold; }
        .double { font-size: 1.6em; line-height: 1.4; }
        .cut    { page-break-after: always; }
        table { width:100%; border-collapse: collapse; }
        hr    { border:none; border-top:1px dashed #000; margin:10px 0; }
    </style>
</head>
<body>
    <div class="center bold double">{{ $shop_name ?? 'MA BOUTIQUE' }}</div>
    <div class="center">{{ $shop_address ?? '123 Rue Exemple – 75001 Paris' }}</div>
    <div class="center">Tél : {{ $shop_phone ?? '01.23.45.67.89' }}</div>
    <hr>

    <div>Ticket #{{ $order->id }} – {{ $order->created_at->format('d/m/Y H:i') }}</div>
    @if($order->customer)
        <div>Client : {{ $order->customer->name }}</div>
    @endif
    <div>Caissier : {{ $cashier ?? auth()->user()?->name ?? 'Inconnu' }}</div>

    <hr>
    <table>
        @foreach($order->items as $item)
            <tr>
                <td>{{ $item->quantity }} × {{ $item->name }}</td>
                <td class="right bold">{{ number_format($item->price_ttc * $item->quantity, 2) }} €</td>
            </tr>
            @if($item->comment)
                <tr><td colspan="2" style="padding-left:20px;font-style:italic;">{{ $item->comment }}</td></tr>
            @endif
        @endforeach
    </table>

    <hr>
    <table>
        <tr><td>Sous-total</td><td class="right">{{ number_format($order->subtotal, 2) }} €</td></tr>
        @if($order->discount > 0)
            <tr><td>Remise</td><td class="right">-{{ number_format($order->discount, 2) }} €</td></tr>
        @endif
        <tr class="bold double"><td>TOTAL</td><td class="right">{{ number_format($order->total_ttc, 2) }} €</td></tr>
        <tr><td>Payé {{ $payment_method ?? 'Espèces' }}</td><td class="right bold">{{ number_format($order->paid_amount, 2) }} €</td></tr>
        @if($order->change > 0)
            <tr class="bold"><td>Rendu</td><td class="right">{{ number_format($order->change, 2) }} €</td></tr>
        @endif
    </table>

    <hr>
    <div class="center">Merci de votre visite !</div>
    <div class="center">www.maboutique.com</div>
    <br><br>
    <div class="center>
        {{-- QR code optionnel (ex: lien vers facture PDF ou programme fidélité) --}}
        @if(isset($qr_content))
            {!! QrCode::size(180)->generate($qr_content) !!}
        @endif
    </div>

    <div class="cut"></div>
</body>
</html>