<?php

namespace App\Libraries\Facades;

/**
 * @method static int count()
 *
 * @see \App\Libraries\Cpu
 */
class Cpu extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Libraries\Cpu::class;
    }
}
