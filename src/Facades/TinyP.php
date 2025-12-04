<?php
// src/Facades/TinyP.php

namespace LaravelTinyPrint\Facades;

use Illuminate\Support\Facades\Facade;

class TinyP extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \LaravelTinyPrint\TinyPrint::class;
    }
}
?>
