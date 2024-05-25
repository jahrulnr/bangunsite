<?php

namespace App\Libraries\Facades;

/**
 * @method static array getLogPath(string $domain)
 * @method static array|false accessLogList()
 * @method static array|false errorLogList()
 * @method static string|false|null readLog(string $filename, int $n=100)
 *
 * @see \App\Libraries\Log
 */
class Log extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Libraries\Log::class;
    }
}
