<?php

namespace App\Libraries;

class Log
{
    private $logPath = '/app/storage/logs/';

    public function getLogPath(string $domain): array
    {
        return [
            'access' => "{$this->logPath}access-{$domain}.log",
            'error' => "{$this->logPath}error-{$domain}.log",
        ];
    }

    public function accessLogList(): array|false|null
    {
        return glob($this->logPath.'access*.log');
    }

    public function errorLogList(): array|false
    {
        return glob($this->logPath.'error*.log');
    }

    public function readLog(string $filename, int $n = 100): string|false|null
    {
        $filename = preg_replace("/[\.]+/", '.', $filename);
        $fullPath = $this->logPath.$filename;
        if (! file_exists($this->logPath.$filename)) {
            return false;
        }

        $tail = Commander::shell("tail -n $n $fullPath");

        return $tail;
    }
}
