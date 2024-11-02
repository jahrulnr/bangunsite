<?php

namespace App\Libraries\Facades;

/**
 * @method static array interfaces()
 * @method static array traffic()
 *
 * @see \App\Libraries\Network
 */
class Network extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Libraries\Network::class;
    }
}
