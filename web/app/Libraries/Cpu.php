<?php

namespace App\Libraries;

class Cpu
{
    public function count(): int
    {
        $cpus = Commander::exec('echo $(cat /proc/cpuinfo | grep processor | wc -l)');

        return $cpus[0];
    }
}
