<?php

namespace Mhbarry\Resourcefy\Facades;

use Illuminate\Support\Facades\Facade;

class Resourcefy extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'resourcefy';
    }
}
