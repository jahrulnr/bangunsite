<?php

namespace App\Http\Controllers;

use App\Libraries\Disk;
use Illuminate\Http\Request;

class FileManagerController extends Controller
{
    private $path;

    public function __construct(Request $r)
    {
        $this->path = str_replace('//', '/', $r->path) ?: '/app';
        if (basename($this->path) == '..') {
            $i = 0;
            $splitPath = explode('/', $this->path);
            $newPath = [];
            foreach ($splitPath as $path) {
                if (empty($path)) {
                    continue;
                }
                if ($path == '..') {
                    unset($newPath[--$i]);

                    continue;
                }
                $newPath[$i++] = $path;
            }
            $this->path = '/'.implode('/', $newPath);
        }
    }

    public function index()
    {
        $fullPath = $this->path;
        $browse = Disk::ls($this->path);

        return view('FileManager.index', compact('fullPath', 'browse'));
    }
}
