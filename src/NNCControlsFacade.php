<?php

namespace NNC\Controls;

use Illuminate\Support\Facades\Facade;

class NNCControlsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'nnc';
    }
}