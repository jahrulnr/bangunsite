<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Log;

class Disk
{
    public static function devices()
    {
        $stats = Commander::exec('iostat -k -d');
        unset($stats[count($stats) - 1], $stats[0], $stats[1]);
        $stats = [...$stats];

        return $stats;
    }

    public static function simpleStat()
    {
        $devices = static::devices();
        $label = trim(preg_replace("/\s+/", ' ', $devices[0]));
        $label = explode(' ', $label);
        $rps = str_replace('_read/s', '', $label[2]);
        $wps = str_replace('_wrtn/s', '', $label[3]);
        unset($devices[0]);

        $rps_value = 0;
        $wps_value = 0;
        foreach ($devices as $name => $value) {
            $value = preg_replace('/\s+/', ' ', $value);
            $value = explode(' ', $value);
            $rps_value += $value[2];
            $wps_value += $value[3];
        }

        return [
            'read' => $rps_value.' '.$rps,
            'write' => $wps_value.' '.$wps,
        ];
    }

    public static function createFile(string $filename, string $content): bool
    {
        $path = dirname($filename);
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

        return $result ?? false;
    }

    public static function rm(string $path, bool $recursive = false)
    {
        if ($recursive === false || $recursive === null) {
            if (is_dir($path)) {
                rmdir($path);
            } elseif (is_file($path) || is_link($path)) {
                unlink($path);
            }
        } else {
            if (is_dir($path)) {
                $files = scandir($path);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        if (is_dir($path.DIRECTORY_SEPARATOR.$file) && ! is_link($path.'/'.$file)) {
                            static::rm($path.DIRECTORY_SEPARATOR.$file);
                        } else {
                            unlink($path.DIRECTORY_SEPARATOR.$file);
                        }
                    }
                }
                static::rm($path);
            }
        }
    }
}
