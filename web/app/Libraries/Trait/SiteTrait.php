<?php

namespace App\Libraries\Trait;

use App\Libraries\Disk;
use App\Models\Website;

trait SiteTrait
{
    public static $baseConfig = '/storage/webconfig/site.conf';

    public static $configPath = '/storage/webconfig/site.d';

    public static $activePath = '/storage/webconfig/active.d';

    public static function getBaseConfig(): string
    {
        return file_get_contents(base_path().self::$baseConfig);
    }

    public static function getActiveConfigPath(string $domain): string
    {
        return base_path().self::$activePath.'/'.$domain.'.conf';
    }

    public static function getConfigPath(string $domain): string
    {
        $path = base_path().self::$configPath;
        is_dir($path) or mkdir($path, 0750, true);

        return $path.'/'.$domain.'.conf';
    }

    public static function getSiteConfig(string $domain): string
    {
        $configPath = self::getConfigPath($domain);

        return file_get_contents($configPath);
    }

    public static function createConfig(string $domain, array $attributes): bool
    {
        $replacement = [];
        foreach ($attributes as $key => $value) {
            if (! is_string($value)) {
                continue;
            }
            $replacement["<{$key}>"] = $value;
        }

        return Disk::createFile(
            self::getConfigPath($domain),
            str_replace(array_keys($replacement), array_values($replacement), self::getBaseConfig())
        );
    }

    public static function enableSite(string $domain, bool $enable = true): bool
    {
        $site = Website::getSite($domain)->first();
        $configPath = self::getSiteConfig($domain);
        $enablePath = self::getActiveConfigPath($domain);
        if (is_file($configPath) && $enable) {
            $result = symlink($configPath, $enablePath);
            $site->active = true;
            $site->save();
        } elseif ($enable == false && (is_link($enablePath) || is_file($enablePath))) {
            $result = unlink($enablePath);
            $site->active = false;
            $site->save();
        }

        return $result ?? false;
    }
}
