<?php

namespace App\Libraries;

use App\Libraries\Trait\SiteTrait;

/**
 * @method static string getConfigPath(string $domain)
 * @method static string getActiveConfigPath(string $domain)
 * @method static string getBaseConfig()
 * @method static string getSiteConfig(string $domain)
 * @method static bool createConfig(string $domain)
 * @method static bool enableSite(string $domain, bool $enable = true)
 */
class Site
{
    use SiteTrait;
}
