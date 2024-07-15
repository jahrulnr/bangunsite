<?php

namespace App\Libraries\Facades;

/**
 * @property-read static string $baseConfig
 * @property-read static string $configPath
 * @property-read static string $activePath
 * @property-read static string $nginxPath
 * @property-read static string $defaultPath
 *
 * @method static string getNginxConfig()
 * @method static \Illuminate\Database\Eloquent\Builder getSite($domain)
 * @method static string getConfigPath(string $domain)
 * @method static string getActiveConfigPath(string $domain)
 * @method static string getBaseConfig()
 * @method static string getSiteConfig(string $domain)
 * @method static bool createConfig(string $domain)
 * @method static bool enableSite(string $domain, bool $enable = true)
 * @method static void removeSite(Website $data, $remove)
 *
 * @see \App\Libraries\Site
 */
class Site extends \Illuminate\Support\Facades\Facade
{
    public function __get($name)
    {
        return (new \App\Libraries\Site)->$name;
    }

    protected static function getFacadeAccessor()
    {
        return \App\Libraries\Site::class;
    }
}
