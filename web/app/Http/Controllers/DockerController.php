<?php

namespace App\Http\Controllers;

use App\Libraries\Commander;

class DockerController extends Controller
{
    private $pattern = "/[^a-zA-Z0-9\-]/";

    public function index()
    {
        $list = Commander::shell('docker ps -a');
        $list = preg_replace("/\s\s+/", '[ttt]', $list);
        $list = explode("\n", $list);
        $data = [];
        $maxCol = $i = 0;
        foreach ($list as $line) {
            if (empty($line)) {
                continue;
            }
            $data[$i] = explode('[ttt]', $line);
            $maxCol = max($maxCol, count($data[$i++]));
        }
        $head = $data[0];
        unset($data[0]);

        return view('Docker.index', compact('head', 'data', 'maxCol'));
    }

    public function restart($id)
    {
        $id = preg_replace($this->pattern, '', $id);
        $container = Commander::shell("docker restart $id &");

        return $container;
    }

    public function logs($id)
    {
        $id = preg_replace($this->pattern, '', $id);
        $container = Commander::shell("docker logs $id -n 200");

        return $container;
    }

    public function stop($id)
    {
        $id = preg_replace($this->pattern, '', $id);
        $container = Commander::shell("docker stop $id");

        return $container;
    }
}
