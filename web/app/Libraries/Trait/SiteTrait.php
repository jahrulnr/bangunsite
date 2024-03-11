<?php

namespace App\Libraries\Trait;

use App\Libraries\Commander;
use App\Libraries\Facades\Disk;
use App\Libraries\Nginx;
use App\Models\Website;
use Illuminate\Support\Facades\Session;

trait SiteTrait
{
    public static $baseConfig = '/storage/webconfig/site.conf';

    public static $configPath = '/storage/webconfig/site.d';

    public static $activePath = '/storage/webconfig/active.d';

    public static $nginxPath = '/etc/nginx';

    public static $defaultPath = '/www/default';

    public static function getNginxConfig(): string
    {
        return file_get_contents(self::$nginxPath.'/nginx.conf');
    }

    public static function getDefaultConfig(): string
    {
        return file_get_contents(self::$nginxPath.'/http.d/default.conf');
    }

    public static function getBaseConfig(): string
    {
        return file_get_contents(base_path().self::$baseConfig);
    }

    public static function getActiveConfigPath(string $domain): string
    {
        $path = base_path().self::$activePath;
        is_dir($path) or mkdir($path, 0750, true);

        return $path.'/'.$domain.'.conf';
    }

    public static function getConfigPath(string $domain): string
    {
        $path = base_path().self::$configPath;
        is_dir($path) or mkdir($path, 0750, true);

        return $path.'/'.$domain.'.conf';
    }

    public static function getDefaultPath()
    {
        return env('WEB_PATH', self::$defaultPath);
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

        is_dir($attributes['path']) ?: mkdir($attributes['path'], 755, true);
        $indexFile = $attributes['path'].'/index.html';
        copy(static::$defaultPath.'/index.html', $indexFile);
        Commander::exec("chown -R nginx:nginx {$attributes['path']}");

        return Disk::createFile(
            self::getConfigPath($domain),
            str_replace(array_keys($replacement), array_values($replacement), self::getBaseConfig())
        );
    }

    public static function enableSite(string $domain, bool $enable = true): bool
    {
        $configPath = self::getConfigPath($domain);
        $enablePath = self::getActiveConfigPath($domain);
        if (is_file($configPath) && $enable) {
            $result = symlink($configPath, $enablePath);
        } elseif ($enable == false && (is_link($enablePath) || is_file($enablePath))) {
            $result = unlink($enablePath);
        } else {
            if (file_exists($enablePath)) {
                Session::flash('warning', 'Website already enabled');
            }

            return true;
        }

        return $result ?? false;
    }

    public static function removeSite(Website $data, $remove): void
    {
        if (($remove !== null || $remove !== false) && file_exists($data->path)) {
            Disk::rm($data->path, true);
        }

        static::enableSite($data->domain, false);
        Nginx::restart();

        $sitePath = self::getConfigPath($data->domain);
        if (file_exists($sitePath)) {
            unlink($sitePath);
        }
    }
}
