<?php

namespace App\Libraries\Facades;

/**
 * @method static array devices()
 * @method static array simpleStat()
 * @method static bool createFile(string $filename, string $content)
 * @method static bool cp($src, $dst)
 * @method static bool rm(string $path, bool $recursive = false)
 * @method static array ls(string $path, $readable = true)
 * @method static string bytesReadable(int|float $bytes)
 * @method static ?int toBytes(string $from)
 * @method static bool validatePath(string $path)
 * @method static string getIcon($path)
 * @method static string perm($path)
 * @method static string curl($url, $ignoreError = false, &$httpCode = null, &$error = null)
 *
 * @see \App\Libraries\Disk
 */
class Disk extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Libraries\Disk::class;
    }
}
