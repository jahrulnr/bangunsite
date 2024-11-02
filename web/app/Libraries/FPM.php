<?php

namespace App\Libraries;

class FPM
{
    protected $app;

    private $path = '/etc/php/';

    private $ini = 'php.ini';

    private $fpm = 'php-fpm.conf';

    private $pool = 'php-fpm.d/www.conf';

    public function getPath(): string
    {
        return $this->path;
    }

    private function readConf($filename): ?string
    {
        $filepath = str_replace('..', '', $this->getPath().$filename);

        return file_exists($filepath) ? file_get_contents($filepath) : null;
    }

    public function phpConf(): ?string
    {
        return $this->readConf($this->ini);
    }

    public function fpmConf(): ?string
    {
        return $this->readConf($this->fpm);
    }

    public function poolConf(): ?string
    {
        return $this->readConf($this->pool);
    }

    public function setPhpConf(string $config): bool
    {
        return file_put_contents($this->getPath().$this->ini, $config);
    }

    public function setFpmConf(string $config): bool
    {
        return file_put_contents($this->getPath().$this->fpm, $config);
    }

    public function setPoolConf(string $config): bool
    {
        return file_put_contents($this->getPath().$this->pool, $config);
    }
}
