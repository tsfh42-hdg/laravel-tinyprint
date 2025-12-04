<?php
/**
 * Laravel TinyPrint – Configuration complète
 * Swiss Army Knife d’impression multi-protocole pour Laravel
 *
 * @author  Thomas HARDING
 * @license AGPL-3.0-or-later
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Paramètres globaux de connexion et robustesse
    |--------------------------------------------------------------------------
    */
    'timeout'          => env('TINYP_TIMEOUT', 12),           // secondes totales
    'connect_timeout'  => env('TINYP_CONNECT_TIMEOUT', 4),    // secondes connexion
    'retries'          => env('TINYP_RETRIES', 2),            // nombre de retries sur fallback
    'debug'            => env('TINYP_DEBUG', false),
    'log_channel'      => env('TINYP_LOG_CHANNEL', 'daily'),


    /*
    |--------------------------------------------------------------------------
    | Ordre de fallback des protocoles (le plus fiable en premier)
    |--------------------------------------------------------------------------
    */
    'protocol_fallback' => ['ipp', 'raw', 'lpr', 'usb'],


    /*
    |--------------------------------------------------------------------------
    | Imprimantes disponibles (nom logique → paramètres)
    |--------------------------------------------------------------------------
    */
    'printers' => [

        'default' => [
            'host'          => env('TINYP_DEFAULT_HOST', '127.0.0.1'),
            'port'          => env('TINYP_DEFAULT_PORT', 631),
            'username'      => env('TINYP_DEFAULT_USER'),
            'password'      => env('TINYP_DEFAULT_PASS'),
            'printer_name'  => env('TINYP_DEFAULT_PRINTER_NAME'),
            'protocol'      => 'ipp',
            'ssl'           => false,
            'path'          => '/printers/',
        ],

        'receipt' => [
            'host'       => env('TINYP_RECEIPT_HOST', '192.168.1.42'),
            'port'       => env('TINYP_RECEIPT_PORT', 9100),
            'protocol'   => 'raw',
            'encoding'   => 'UTF-8',
            'cut'        => true,
            'cashdrawer' => true,
            'beep'       => 0,
        ],

        'kitchen' => [
            'host'       => env('TINYP_KITCHEN_HOST', '192.168.1.88'),
            'port'       => 9100,
            'protocol'   => 'raw',
            'beep'       => 3,
            'cut'        => true,
        ],

        'label' => [
            'host'       => env('TINYP_LABEL_HOST', '10.0.50.10'),
            'port'       => 9100,
            'protocol'   => 'raw',
        ],

        'a4' => [
            'host'         => env('TINYP_A4_HOST', 'localhost'),
            'port'         => env('TINYP_A4_PORT', 631),
            'username'     => env('TINYP_A4_USER'),
            'password'password'   => env('TINYP_A4_PASS'),
            'printer_name' => env('TINYP_A4_PRINTER_NAME', 'PDF' ou 'HP_LaserJet'),
            'protocol'     => 'ipp',
            'ssl'          => env('TINYP_A4_SSL', true),
            'pdf'          => true,                 // force conversion Blade → PDF
        ],

    ],


    /*
    |--------------------------------------------------------------------------
    | DomPDF – génération PDF à la volée
    |--------------------------------------------------------------------------
    */
    'pdf' => [
        'enabled'       => true,
        'default_font'  => 'DejaVuSans',
        'font_dir'      => storage_path('fonts/'),
        'temp_dir'      => sys_get_temp_dir(),
        'options' => [
            'isRemoteEnabled'       => true,
            'isHtml5ParserEnabled'  => true,
            'isFontSubsettingEnabled' => true,
            'defaultPaperSize'      => 'a4',
            'dpi'                   => 203,
            'isPhpEnabled'          => false,
        ],
    ],
    
?>