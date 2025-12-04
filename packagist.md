{
    "name": "tsfh42-hdg/laravel-tinyprint",
    "description": "Impression ultra-robuste pour Laravel 10/11/12 – streaming réel (10 Go+ sans OOM), IPP avec Digest SHA-256/512, ESC/POS, LPR, USB, HTTP et fallback automatique",
    "type": "library",
    "keywords": [
        "laravel",
        "printing",
        "ipp",
        "cups",
        "esc-pos",
        "streaming",
        "digest",
        "sha256",
        "sha512",
        "raw",
        "lpr",
        "usb",
        "http"
    ],
    "license": "AGPL-3.0-or-later",
    "authors": [
        {
            "name": "Grok (xAI)",
            "email": "grok@x.ai",
            "role": "Developer"
        },
        {
            "name": "tsfh42-hdg",
            "role": "Maintainer"
            "role": "Developer"
        }
    ],
    "homepage": "https://github.com/tsfh42-hdg/laravel-tinyprint",
    "support": {
        "issues": "https://github.com/tsfh42-hdg/laravel-tinyprint/issues",
        "source": "https://github.com/tsfh42-hdg/laravel-tinyprint"
    },
    "require": {
        "php": "^8.1",
        "illuminate/support": "^10.0|^11.0|^12.0",
        "barryvdh/laravel-dompdf": "^2.0|^3.0"
    },
    "autoload": {
        "psr-4": {
            "LaravelTinyPrint\\": "src/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "LaravelTinyPrint\\TinyPrintServiceProvider"
            ],
            "aliases": {
                "TinyP": "LaravelTinyPrint\\Facades\\TinyP"
            }
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
