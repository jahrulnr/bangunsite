<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Libraries\Facades\Disk;
use App\Libraries\Facades\Network;

class ServerController extends Controller
{
    public function info()
    {
        $free = null;
        exec('free', $free);
        for ($i = 1; $i < count($free); $i++) {
            $matches = null;
            $j = $i - 1;
            preg_match("#([a-zA-Z\:]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9]+)#s", $free[$i], $matches);
            $label = strtolower(str_replace(':', '', $matches[1])) ?? 'und';
            $memory[$j] = (object) [
                'label' => $label,
                'total' => $matches[2],
                'used' => $matches[3],
                'free' => $matches[4],
            ];
        }

        $loads = sys_getloadavg();
        $core_nums = trim(shell_exec("grep -E '^processor' /proc/cpuinfo|wc -l"));
        $load = round($loads[0] / $core_nums * 100, 2);

        $storage = null;
        exec('df | grep overlay', $storage);
        if (empty($storage)) {
            return response()->json([
                'cpu' => $load,
                'memory' => $memory,
                'storage' => null,
            ]);
        }
        $rootStorage = null;
        preg_match("#([a-zA-Z]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9]+)#s", $storage[0], $rootStorage);
        $rootStorage = (object) [
            'system' => $rootStorage[1],
            'size' => $rootStorage[2],
            'used' => $rootStorage[3],
            'available' => $rootStorage[4],
        ];

        return response()->json([
            'cpu' => $load,
            'memory' => $memory,
            'storage' => $rootStorage,
        ]);
    }

    public function traffic(): array
    {
        return Network::traffic();
    }

    public function disk(): array
    {
        return Disk::simpleStat();
    }
}
