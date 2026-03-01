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

    public function accessTraffic($domain = null): array|false
    {
        $files = [];
        if ($domain) {
            $paths = $this->getLogPath($domain);
            $files = [basename($paths['access']) => $paths['access']];
        } else {
            $list = $this->accessLogList();
            if (! $list) {
                return false;
            }
            foreach ($list as $file) {
                $files[basename($file)] = $file;
            }
        }

        $result = [
            'sites' => [],
            'total' => ['requests' => 0, 'bytes' => 0],
        ];

        foreach ($files as $basename => $path) {
            if (! file_exists($path)) {
                continue;
            }

            $fp = fopen($path, 'r');
            if (! $fp) {
                continue;
            }

            $req = 0;
            $bytes = 0;

            while (! feof($fp)) {
                $line = fgets($fp);
                if (! $line) {
                    continue;
                }
                $req++;

                // Try to extract body bytes from typical nginx combined log:
                // ... "request" STATUS BODY_BYTES "ref" "agent"
                if (preg_match('/"[^\"]*"\s+\d{3}\s+(\d+|-)\s+"/', $line, $m)) {
                    $b = $m[1] === '-' ? 0 : (int) $m[1];
                    $bytes += $b;
                } else {
                    // fallback: find last numeric token
                    if (preg_match('/\s(\d+)\s*$/', trim($line), $m2)) {
                        $bytes += (int) $m2[1];
                    }
                }
            }

            fclose($fp);

            // derive domain from filename: access-domain.log
            $domainName = preg_replace(['/^access-/', '/\.log$/'], '', $basename);
            $result['sites'][$domainName] = [
                'requests' => $req,
                'bytes' => $bytes,
            ];

            $result['total']['requests'] += $req;
            $result['total']['bytes'] += $bytes;
        }

        return $result;
    }
}
