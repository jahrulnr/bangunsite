<?php

namespace App\Libraries;

use App\Models\Website;

class Nginx
{
    public static function test(string $domain = '')
    {
        $basepath = empty($domain)
            ? Site::$nginxPath.'/nginx.conf'
            : base_path().'/storage/webconfig/nginx.conf';

        if (! empty($domain)) {
            $path = '/tmp/nginx-'.time().'.conf';
            Disk::createFile($path,
                str_replace(
                    'site.d/*.conf',
                    "site.d/{$domain}.conf",
                    file_get_contents($basepath))
            );
        }

        $exec = Commander::shell('nginx -t -c '.($path ?? $basepath));
        if (isset($path) && $path != $basepath) {
            unlink($path);
        }

        $result = explode("\n", $exec);
        if (strpos($result[1], 'success')) {
            return true;
        } else {
            return $result[0];
        }
    }

    public static function testNginxConf(?string $path = null)
    {
        if ($path == null) {
            $path = Site::$nginxPath.'/nginx-test.conf';
        }

        $exec = Commander::shell('nginx -t -c '.$path);
        $result = explode("\n", $exec);
        if (strpos($result[1], 'success')) {
            return true;
        } else {
            return $result[0];
        }
    }

    public static function restart(): void
    {
        Commander::exec('nginx -s reload');
    }

    public static function moveRoot(Website $model, array $attributes): bool
    {
        $from = $model->path;
        $to = $attributes['path'];
        if (! file_exists(dirname($to))) {
            mkdir(dirname($to), 755, true);
        }

        Disk::cp($from, $to);
        Disk::rm($from, true);
        $configPath = Site::getConfigPath($model->domain);
        $config = Site::getSiteConfig($model->domain);
        $config = str_replace($from, $to, $config);
        $result = file_put_contents($configPath, $config);
        self::restart();

        return $result;
    }

    public static function moveDomain(Website $model, array $attributes): bool
    {
        $from = $model->domain;
        $to = $attributes['domain'];

        $model::enableSite($from, false);

        $oldConfigPath = Site::getConfigPath($from);
        $newConfigPath = Site::getConfigPath($to);
        $config = Site::getSiteConfig($from);
        $config = str_replace(
            'server_name '.$from,
            'server_name '.$to,
            $config);
        $result = Disk::createFile($newConfigPath, $config);

        if (! $result) {
            return false;
        }

        Disk::rm($oldConfigPath);
        if (isset($attributes['active']) && $attributes['active'] == true) {
            $model::enableSite($to);
            self::restart();
        }

        return true;
    }
}
