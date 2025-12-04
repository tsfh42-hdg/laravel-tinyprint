<?php

use TinyP;

// Exemple fluide
TinyP::to('192.168.1.100')           // ou TinyP::printer('receipt')
     ->pdf('tickets.order', $data)   // Blade → PDF → impression
     ->cut()
     ->send();

// Ou raw ESC/POS
TinyP::raw('10.0.0.50:9100')
     ->text("Bonjour Thomas !\n")
     ->bold()
     ->qr('https://github.com/tsfh42-hdg/laravel-tinyprint')
     ->cut()
     ->send();
     
?>