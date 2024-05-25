<?php

namespace App\Libraries\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string getPath()
 * @method static array getTables()
 * @method static array getCols(string $name)
 * @method static \Illuminate\Support\Collection getRows(string $table, $limit = 100)
 *
 * @see \App\Libraries\SQLite
 */
class SQLite extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Libraries\SQLite::class;
    }
}
