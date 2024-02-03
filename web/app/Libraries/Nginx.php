<?php

namespace App\Libraries;

class Nginx
{
    public static function test(string $domain = '')
    {
        $basepath = base_path().'/storage/webconfig/nginx.conf';
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
}
