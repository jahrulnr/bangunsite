<?php

namespace App\Libraries;

class Memory
{
    public function info(): object
    {
        $info = Commander::exec('free -h');
        $stats = null;
        preg_match("#([a-zA-Z\:]+)\s+([0-9a-zA-Z\.]+)\s+([0-9a-zA-Z\.]+)\s+([0-9a-zA-Z\.]+)#s", $info[1], $stats);
        $label = strtolower(str_replace(':', '', $stats[1])) ?? 'und';
        $memory = (object) [
            'label' => $label,
            'total' => $stats[2],
            'used' => $stats[3],
            'free' => $stats[4],
        ];

        return $memory;
    }
}
