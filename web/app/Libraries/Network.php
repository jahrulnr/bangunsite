<?php

namespace App\Libraries;

class Network
{
    public static function interfaces(): array
    {
        $path = '/sys/class/net/';
        $interfaces = glob("{$path}*");
        foreach ($interfaces as $interface) {
            $ifaces[] = str_replace($path, '', $interface);
        }

        return $ifaces;
    }

    public static function traffic(): array
    {
        $ifaces = self::interfaces();
        $path = '/sys/class/net/';
        $rx_path = '/statistics/rx_bytes';
        $tx_path = '/statistics/tx_bytes';

        foreach ($ifaces as $iface) {
            $rx[$iface] = trim(file_get_contents($path.$iface.$rx_path));
            $tx[$iface] = trim(file_get_contents($path.$iface.$tx_path));
        }

        return [
            'in' => $rx,
            'out' => $tx,
        ];
    }
}
