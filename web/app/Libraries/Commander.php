<?php

namespace App\Libraries;

class Commander
{
    public static function exec(string $command)
    {
        exec($command, $output);

        return $output;
    }

    public static function shell(string $command)
    {
        return shell_exec($command.' 2>&1');
    }
}
