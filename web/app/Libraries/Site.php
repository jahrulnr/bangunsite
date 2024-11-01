<?php

namespace App\Libraries;

use App\Libraries\Facades\Disk;
use App\Models\Website;

class Site
{
    public $baseConfig = '/storage/webconfig/site.conf';

    public $configPath = '/storage/webconfig/site.d';

    public $activePath = '/storage/webconfig/active.d';

    public $nginxPath = '/etc/nginx';

    public $defaultPath = '/www/default';

    public function getNginxConfig(): string
    {
        return file_get_contents($this->nginxPath.'/nginx.conf');
    }

    public function getDefaultConfig(): string
    {
        return file_get_contents($this->nginxPath.'/conf.d/default.conf');
    }

    public function getBaseConfig(): string
    {
        return file_get_contents(base_path().$this->baseConfig);
    }

    public function getActiveConfigPath(string $domain): string
    {
        $path = base_path().$this->activePath;
        is_dir($path) or mkdir($path, 0750, true);

        return $path.'/'.$domain.'.conf';
    }

    public function getConfigPath(string $domain): string
    {
        $path = base_path().$this->configPath;
        is_dir($path) or mkdir($path, 0750, true);

        return $path.'/'.$domain.'.conf';
    }

    public function getDefaultPath()
    {
        return env('WEB_PATH', $this->defaultPath);
    }

    public function getSite($domain): \Illuminate\Database\Eloquent\Builder
    {
        return Website::getSite($domain);
    }

    public function getSiteConfig(string $domain): string
    {
        $configPath = $this->getConfigPath($domain);

        if (! file_exists($configPath)) {
            $site = Website::getSite($domain)->first()->toArray();
            $this->createConfig($domain, $site);
        }

        return file_get_contents($configPath);
    }

    public function createConfig(string $domain, array $attributes): bool
    {
        $attributes['ssl_cert'] = '/app/storage/webconfig/ssl/live/default/cert.pem';
        $attributes['ssl_key'] = '/app/storage/webconfig/ssl/live/default/key.pem';
        $replacement = [
            '<ssl_cert>' => '',
            '<ssl_key>' => '',
        ];

        $this->copySSL($attributes);

        foreach ($attributes as $key => $value) {
            if (! is_string($value)) {
                continue;
            }
            $replacement["<{$key}>"] = $value;
        }

        is_dir($attributes['path']) ?: mkdir($attributes['path'], 755, true);
        $indexFile = $attributes['path'].'/index.html';
        copy($this->defaultPath.'/index.html', $indexFile);
        Commander::exec("chown -R nginx:nginx {$attributes['path']}");

        return Disk::createFile(
            $this->getConfigPath($domain),
            str_replace(array_keys($replacement), array_values($replacement), $this->getBaseConfig())
        );
    }

    private function copySSL(array $attributes)
    {
        $domainCertPath = "/app/storage/webconfig/ssl/live/{$attributes['domain']}";
        if (! is_dir($domainCertPath)) {
            mkdir($domainCertPath);
        }
        copy($attributes['ssl_cert'], "{$domainCertPath}/cert.pem");
        copy($attributes['ssl_key'], "{$domainCertPath}/key.pem");
    }

    public function enableSite(string $domain, bool $enable = true): bool
    {
        $configPath = $this->getConfigPath($domain);
        $enablePath = $this->getActiveConfigPath($domain);
        if (is_file($configPath) && $enable && ! is_file($enablePath)) {
            $result = symlink($configPath, $enablePath);
        } elseif ($enable == false && (is_link($enablePath) || is_file($enablePath))) {
            $result = unlink($enablePath);
        } else {
            return true;
        }

        return $result ?? false;
    }

    public function removeSite(Website $data, $remove): void
    {
        if (($remove !== null || $remove !== false) && file_exists($data->path)) {
            Disk::rm($data->path, true);
        }

        $this->enableSite($data->domain, false);
        Nginx::restart();

        $sitePath = $this->getConfigPath($data->domain);
        if (file_exists($sitePath)) {
            unlink($sitePath);
        }
    }
}
