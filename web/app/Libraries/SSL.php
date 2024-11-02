<?php

namespace App\Libraries;

class SSL
{
    public static $confPath = '/storage/webconfig/site.d';

    public static $sslPath = '/storage/webconfig/live';

    private static $keyStart = 'ssl_certificate_key';

    private static $pubStart = 'ssl_certificate';

    public static function getCertPath(string $domain): object|false
    {
        $configPath = self::$confPath.DIRECTORY_SEPARATOR.$domain.'.conf';
        if (! is_file($configPath) && ! is_link($configPath)) {
            return false;
        }

        $config = trim(file_get_contents($configPath));
        preg_match('|'.self::$pubStart.'\s+([a-zA-Z0-9\.\/\-]+);|i', $config, $match1);
        preg_match('|'.self::$keyStart.'\s+([a-zA-Z0-9\.\/\-]+);|i', $config, $match2);

        return (object) [
            'public' => empty($match1) ? false : $match1[1],
            'private' => empty($match2) ? false : $match2[1],
        ];
    }

    public static function readPublic(string $domain): string
    {
        $cert = self::getCertPath($domain)?->public;

        return empty($cert) ? false
            : (file_exists($cert) ? file_get_contents($cert) : false);
    }

    public static function readPrivate(string $domain): string
    {
        $cert = self::getCertPath($domain)?->private;

        return empty($cert) ? false
            : (file_exists($cert) ? file_get_contents($cert) : false);
    }

    public static function checkSSL(string $domain)
    {
        $configPath = self::$confPath.DIRECTORY_SEPARATOR.$domain.'.conf';
        if (! is_file($configPath) && ! is_link($configPath)) {
            return false;
        }

        $config = trim(file_get_contents($configPath));
        preg_match('|'.self::$pubStart.'\s+([a-zA-Z0-9\.\/\-]+);|i', $config, $match1);
        preg_match('|'.self::$keyStart.'\s+([a-zA-Z0-9\.\/\-]+);|i', $config, $match2);

        $pubExists = isset($match1[1]) ? $match1[1] : false;
        $keyExists = isset($match2[1]) ? $match2[1] : false;

        if (
            ! ($pubExists && $keyExists)
            || (preg_match("|#(\s+)?{$match1[0]}|i", $config)
                || preg_match("|#(\s+)?{$match2[0]}|i", $config))
        ) {
            return false;
        }

        return true;
    }
}
