<?php

namespace App\Libraries\Facades;

/**
 * @method static string getPath(DataCollectorInterface $collector)
 * @method static string|null phpConf()
 * @method static string|null fpmConf()
 * @method static string|null poolConf()
 * @method static bool setPhpConf(string $config)
 * @method static bool setFpmConf(string $config)
 * @method static bool setPoolConf(string $config)
 *
 * @see \App\Libraries\FPM
 */
class FPM extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Libraries\FPM::class;
    }
}
