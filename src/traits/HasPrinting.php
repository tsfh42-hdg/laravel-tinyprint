<?php

namespace LaravelTinyPrint\Traits;

use TinyP;
use Illuminate\Support\Facades\Storage;

trait HasPrinting
{
    /**
     * Imprime directement un ticket thermique (receipt)
     */
    public function printReceipt(array $extra = [], ?string $printer = null)
    {
        return TinyP::printer($printer ?? 'receipt')
                   ->view('tinyprint::receipt', ['order' => $this] + $extra)
                   ->cut()
                   ->cashDrawer()   // si activé dans config
                   ->send();
    ();
    }

    /**
     * Imprime en cuisine (gros caractères + beep)
     */
    public function printKitchen(array $extra = [])
    {
        return TinyP::printer('kitchen')
                   ->view('tinyprint::kitchen', ['order' => $this] + $extra)
                   ->beep(3)
                   ->send();
    }

    /**
     * Imprime une étiquette ZPL
     */
    public function printLabel(array $extra = [], ?string $printer = null)
    {
        return TinyP::printer($printer ?? 'label')
                   ->rawView('tinyprint::label-zpl', ['product' => $this] + $extra)
                   ->send();
    }

    /**
     * Imprime une facture A4 via CUPS/IPP
     */
    public function printInvoiceA4(array $extra = [])
    {
        return TinyP::printer('a4')
                   ->pdf('tinyprint::invoice-a4', ['invoice' => $this] + $extra)
                   ->send();
    }
}
?>