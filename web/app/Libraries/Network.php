<?php

namespace App\Libraries;

class Network
{
    public function interfaces(): array
    {
        $path = '/sys/class/net/';
        $interfaces = glob("{$path}*");
        foreach ($interfaces as $interface) {
            $ifaces[] = str_replace($path, '', $interface);
        }

        return $ifaces;
    }

    public function traffic(): array
    {
        $ifaces = $this->interfaces();
        $path = '/sys/class/net/';
        $rx_path = '/statistics/rx_bytes';
        $tx_path = '/statistics/tx_bytes';

        foreach ($ifaces as $iface) {
            if (file_exists($path.$iface.$rx_path)) {
                $rx[$iface] = trim(file_get_contents($path.$iface.$rx_path));
            }
            if (file_exists($path.$iface.$tx_path)) {
                $tx[$iface] = trim(file_get_contents($path.$iface.$tx_path));
            }
        }

        return [
            'in' => $rx,
            'out' => $tx,
        ];
    }
}
