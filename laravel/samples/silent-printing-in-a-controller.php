<?php
use LaravelTinyPrint\Facades\TinyP;

public function print(Invoice $invoice)
{
    try {
        TinyP::print(storage_path("app/invoices/{$invoice->id}.pdf"));
        return back()->with('success', 'Imprimé !');
    } catch (Exception $e) {
        return back()->with('error', 'Impression échouée : '.$e->getMessage());
    }
}
?>
