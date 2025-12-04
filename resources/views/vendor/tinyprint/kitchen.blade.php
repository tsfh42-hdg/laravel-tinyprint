<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', monospace; font-size: 16pt; padding:15px; }
        .center { text-align:center; }
        .huge   { font-size: 2.2em; font-weight:bold; }
        .bold   { font-weight:bold; }
        hr { border-top: 3px double #000; margin:20px 0; }
    </style>
</head>
<body>
    <div class="center huge">COMMANDE #{{ $order->id }}</div>
    <div class="center bold">{{ $order->created_at->format('H:i') }} – {{ $table ?? 'À emporter' }}</div>
    <hr>

    @foreach($order->items as $item)
        <div class="bold">{{ $item->quantity }} × {{ $item->name }}</div>
        @if($item->comment)
            <div style="margin-left:30px; color:#d00;">→ {{ $item->comment }}</div>
        @endif
    @endforeach

    <hr>
    <div class="center huge">À PRÉPARER</div>
    <br><br><br>
</body>
</html>