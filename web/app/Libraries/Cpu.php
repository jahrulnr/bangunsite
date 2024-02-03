<?php

namespace App\Libraries;

class Cpu
{
    public static function count()
    {

        $cpus = Commander::exec('echo $(cat /proc/cpuinfo | grep processor | wc -l)');

        return $cpus[0];
    }
}
