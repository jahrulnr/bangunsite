<?php

namespace App\Libraries;

use Error;
use ErrorException;
use Illuminate\Support\Facades\Log;

class Disk
{
    private $umask;

    public function __construct()
    {
        $this->umask = umask(0);
    }

    public function __destruct()
    {
        // im trying the best for bad permission :)
        umask($this->umask);
    }

    public function devices(): array
    {
        $stats = Commander::exec('iostat -k -d');
        unset($stats[count($stats) - 1], $stats[0], $stats[1]);
        $stats = [...$stats];

        return $stats;
    }

    public function simpleStat(): array
    {
        $devices = $this->devices();
        $label = trim(preg_replace("/\s+/", ' ', $devices[0]));
        $label = explode(' ', $label);
        $rps = str_replace('_read/s', '', $label[2]);
        $wps = str_replace('_wrtn/s', '', $label[3]);
        unset($devices[0]);

        $rps_value = 0;
        $wps_value = 0;
        foreach ($devices as $value) {
            $value = preg_replace('/\s+/', ' ', $value);
            $value = explode(' ', $value);
            $rps_value += isset($value[2]) ? $value[2] : 0;
            $wps_value += isset($value[3]) ? $value[3] : 0;
        }

        return [
            'read' => $rps_value.' '.$rps,
            'write' => $wps_value.' '.$wps,
        ];
    }

    public function createFile(string $filename, string $content = ''): bool
    {
        $path = dirname($filename);
        try {
            is_dir($path) ?: mkdir($path, 750, true);

            if (file_exists($filename)) {
                Log::info("[Disk] Rewrite $filename content");
                $result = file_put_contents($filename, $content);
            } else {
                Log::info("[Disk] Creating $filename");
                $file = fopen($filename, 'w');
                if ($file) {
                    $result = fwrite($file, $content);
                    fclose($file);
                } else {
                    Log::error("[Disk] Cant create $filename file");

                    return false;
                }
            }
        } catch (Error $e) {
            Log::error('[Disk] '.$e->getMessage());

            return false;
        }

        return $result ?? false;
    }

    public function cp($src, $dst): bool
    {
        $success = true;
        try {
            if (is_dir($src)) {
                $dir = opendir($src);
                @mkdir($dst);
                while (false !== ($file = readdir($dir))) {
                    if (($file != '.') && ($file != '..')) {
                        if (is_dir($src.'/'.$file)) {
                            $success = $this->cp($src.'/'.$file, $dst.'/'.$file);
                        } else {
                            $success = copy($src.'/'.$file, $dst.'/'.$file);
                        }
                    }
                }
                closedir($dir);
            } else {
                $endWithSlash = str_ends_with($dst, '/');
                if (is_dir($dst) || $endWithSlash) {
                    @mkdir($dst, '0755', true);
                    $success = copy($src, ($endWithSlash ? $dst : $dst.'/').basename($src));
                } else {
                    $success = copy($src, $dst);
                }
            }
        } catch (Error|ErrorException $e) {
            Log::alert('[Disk] '.$e->getMessage());
            $success = false;
        }

        return $success;
    }

    public function rm(string $path, bool $recursive = false): bool
    {
        $success = true;
        if ($recursive === false || $recursive === null) {
            if (is_dir($path)) {
                $success = rmdir($path);
            } elseif (is_file($path) || is_link($path)) {
                $success = unlink($path);
            }
        } else {
            if (is_dir($path)) {
                $files = scandir($path);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        if (is_dir($path.DIRECTORY_SEPARATOR.$file) && ! is_link($path.'/'.$file)) {
                            $success = $this->rm($path.DIRECTORY_SEPARATOR.$file, $recursive);
                        } else {
                            $success = unlink($path.DIRECTORY_SEPARATOR.$file);
                        }
                    }
                }
                $success = rmdir($path);
            } else {
                $success = unlink($path);
            }
        }

        return $success;
    }

    public function ls(string $path, $readable = true): array
    {
        $scan = scandir($path);
        $dir = [];
        $file = [];
        foreach ($scan as $list) {
            if ($list === '.') {
                continue;
            }
            $fullPath = $path.DIRECTORY_SEPARATOR.$list;
            if (is_dir($fullPath)) {
                $dir[] = [
                    'type' => 'directory',
                    'name' => $list,
                    'icon' => $this->getIcon($fullPath),
                    'permission' => $this->perm($fullPath),
                    'link' => ! is_link($fullPath) ?: readlink($fullPath),
                    'size' => false,
                ];

                continue;
            }

            $size = filesize($fullPath);
            $file[] = [
                'type' => 'file',
                'name' => $list,
                'icon' => $this->getIcon($fullPath),
                'permission' => $this->perm($fullPath),
                'link' => ! is_link($fullPath) ?: readlink($fullPath),
                'size' => $readable ? $this->bytesReadable($size) : $size,
            ];
        }

        return [...$dir, ...$file];
    }

    public function bytesReadable(int|float $bytes): string
    {
        $symbols = ['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'];
        try {
            $exp = floor(log($bytes) / log(1024));

            return @sprintf('%.1f'.$symbols[$exp], ($bytes / pow(1024, floor($exp))));
        } catch (Error $e) {
            return '0'.$symbols[0];
        }
    }

    public function toBytes(string $from): ?int
    {
        $units = ['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'];
        $number = substr($from, 0, -1);
        $suffix = strtoupper(substr($from, -1));

        //B or no suffix
        if (is_numeric(substr($suffix, 0, 1))) {
            return preg_replace('/[^\d]/', '', $from);
        }

        $exponent = array_flip($units)[$suffix] ?? null;
        if ($exponent === null) {
            return null;
        }

        return $number * (1024 ** $exponent);
    }

    public function validatePath(string $path): bool
    {
        return strpbrk($path, '\\?%*:|"<>\'') === false;
    }

    public function getIcon(string $path): string
    {
        if (is_dir($path)) {
            return setIcon('fas fa-folder fa-sm text-orange');
        }
        if (is_link($path)) {
            return setIcon('fas fa-link fa-sm text-primary');
        }
        if (is_file($path)) {
            return setIconByType($path);
        }

        return setIcon('far fa-question-circle fa-sm text-danger');
    }

    public function perm(string $path): string
    {
        return substr(sprintf('%o', fileperms($path)), -4);
    }

    public function curl(string $url, $ignoreError = false): object
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, env(
            'CURL_USER_AGENT',
            'Mozilla/5.0 (X11; Linux downloader; rv:122.0) Gecko/20100101 Firefox/122.0'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if (curl_errno($ch) > 0) {
            return $ignoreError ? $error : false;
        }

        return (object)
        [
            'result' => $result,
            'code' => $httpCode,
            'error' => $error,
        ];
    }
}
