<?php

namespace Unscode\Galleries\Facades;

use Illuminate\Support\Facades\Facade;

class Gallery extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Unscode\Galleries\Facades\Html';
    }
}