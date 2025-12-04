<?php

class Order extends Model
{
    use \LaravelTinyPrint\Traits\HasPrinting;

    // Après paiement
    public function printAll()
    {
        $this->printKitchen();        // cuisine
        $this->printReceipt([         // ticket client
            'shop_name' => 'Resto Gourmet',
            'qr_content' => route('order.show', $this),
        ]);
    }
}

?>
