<?php

namespace App\Libraries;

use App\Libraries\Facades\Disk;
use App\Libraries\Facades\Log;
use App\Libraries\Facades\Site;
use App\Models\Website;
use Exception;

class Nginx
{
    public static function test(string $domain = '')
    {
        $basepath = empty($domain)
            ? (new Site())->nginxPath.'/nginx.conf'
            : '/storage/webconfig/nginx.conf';

        if (! empty($domain)) {
            $path = '/tmp/nginx-'.time().'.conf';
            Disk::createFile(
                $path,
                str_replace(
                    'site.d/*.conf',
                    "site.d/{$domain}.conf",
                    file_get_contents($basepath)
                )
            );
        }

        $exec = Commander::shell('nginx -t -c '.($path ?? $basepath));
        if (isset($path) && $path != $basepath) {
            unlink($path);
        }

        $result = explode("\n", $exec);
        if (strpos($result[0], 'syntax is ok')) {
            return true;
        } else {
            return $result[0];
        }
    }

    public static function testNginxConf(?string $path = null)
    {
        if ($path == null) {
            $path = (new Site())->nginxPath.'/nginx-test.conf';
        }

        $exec = Commander::shell('nginx -t -c '.$path);
        $result = explode("\n", $exec);
        if (strpos($result[0], 'syntax is ok')) {
            return true;
        } else {
            return $result[0];
        }
    }

    public static function restart(): void
    {
        sleep(1);
        Commander::exec('supervisorctl restart nginx');
    }

    public static function moveRoot(Website $model, array $attributes): bool
    {
        $from = $model->path;
        $to = $attributes['path'];
        if (! file_exists(dirname($to))) {
            // mkdir(dirname($to), 755, true);
            $dirname = dirname($to);
            Commander::shell("mkdir -p {$dirname}");
            Commander::shell("chmod -R 755 {$dirname}");
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

        Site::enableSite($from, false);

        $oldConfigPath = Site::getConfigPath($from);
        $newConfigPath = Site::getConfigPath($to);
        $config = Site::getSiteConfig($from);
        $config = str_replace(
            'server_name '.$from,
            'server_name '.$to,
            $config
        );
        $logPathFrom = Log::getLogPath($from);
        $logPathTo = Log::getLogPath($to);
        $config = str_replace(
            'access_log '.$logPathFrom['access'],
            'access_log '.$logPathTo['access'],
            $config
        );
        $config = str_replace(
            'error_log '.$logPathFrom['error'],
            'error_log '.$logPathTo['error'],
            $config
        );
        $result = Disk::createFile($newConfigPath, $config);

        if (! $result) {
            return false;
        }

        try {
            rename($logPathFrom['access'], $logPathTo['access']);
            rename($logPathFrom['error'], $logPathTo['error']);
        } catch (Exception $e) {
            // ignore
        }
        Disk::rm($oldConfigPath);
        if (isset($attributes['active']) && $attributes['active'] == true) {
            Site::enableSite($to);
            self::restart();
        }

        return true;
    }

    public static function setCustomSSL(string $domain, string $certPath, string $keyPath)
    {
        /**
         * TODO: custom SSL if not set & redirect from 80 to 443
         * - ssl_certificate
         * - ssl_certificate_key
         */
        $getConfig = Site::getConfigPath($domain);
        $readConfig = file_get_contents($getConfig);
        dd($domain, $certPath, $keyPath, $getConfig, $readConfig);
    }
}
