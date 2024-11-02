<?php

namespace App\Libraries\Facades;

/**
 * @method static object info()
 *
 * @see \App\Libraries\Memory
 */
class Memory extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Libraries\Memory::class;
    }
}
