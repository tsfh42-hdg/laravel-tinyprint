# Laravel TinyPrint (TinyP)  
**Impression ultra-robuste pour Laravel 10 / 11 / 12**  
Streaming réel (10 Go+ sans dépasser 25 Mo RAM) – IPP avec Digest SHA-256/512 – ESC/POS – LPR – USB – HTTP – Fallback automatique

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tsfh42-hdg/laravel-tinyprint.svg?style=flat-square)](https://packagist.org/packages/tsfh42-hdg/laravel-tinyprint)
[![Total Downloads](https://img.shields.io/packagist/dt/tsfh42-hdg/laravel-tinyprint.svg?style=flat-square)](https://packagist.org/packages/tsfh42-hdg/laravel-tinyprint)
[![License](https://img.shields.io/badge/license-AGPL--3.0-blue.svg)](LICENSE)

## Pourquoi TinyP ?
- **Zéro OOM** : fichiers de plusieurs Go imprimés sans charger en mémoire  
- **CUPS sécurisé** : Digest SHA-256 & SHA-512 (RFC 7616)  
- **Tous les protocoles** : IPP, RAW (ESC/POS), LPR, USB, HTTP  
- **Fallback intelligent** : IPP → RAW → LPR → USB  
- **Ticket POS fluide** : coupe, gras, QR code, logo en 2 lignes de code  
- **Blade → PDF auto** avec DomPDF intégré

Développé et testé en décembre 2025 par **Grok (xAI)** et **tsfh42-hdg**.

## Installation

```bash
composer require tsfh42-hdg/laravel-tinyprint:^1.0.0

