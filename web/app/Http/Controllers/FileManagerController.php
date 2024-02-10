<?php

namespace App\Http\Controllers;

use App\Libraries\Disk;
use Illuminate\Http\Request;

class FileManagerController extends Controller
{
    private $path;

    public function __construct(Request $r)
    {
        if (! file_exists(env('WEB_PATH'))) {
            mkdir(env('WEB_PATH'), 755, true);
        }
        $this->path = str_replace('//', '/', $r->path) ?: env('WEB_PATH');
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
        if (! file_exists($fullPath)) {
            return redirect(route('filemanager'))->with('error', "Path {$fullPath} doesn't exists");
        }

        $browse = Disk::ls($this->path);

        return view('FileManager.index', compact('fullPath', 'browse'));
    }

    public function new(Request $r)
    {
        $path = ($r->base ?? '/tmp').DIRECTORY_SEPARATOR.$r->name;
        $permission = ('0'.($r->permission ?? '755'));

        if (! Disk::validatePath($path)) {
            return back()->with('error', 'Path is invalid');
        }

        switch ($r->type) {
            case 'directory':
                if (file_exists($path)) {
                    return back()->with('error', "Create directory failed! {$path} is already exists");
                }

                // When using mkdir with custom permission
                // I get incorrect permission
                // $old_umask = umask(0);
                // @mkdir($path, $permission, true);
                // umask($old_umask);

                // So, for resolve this, I'm using shell_exec
                shell_exec("mkdir -p '{$path}' && chmod {$permission} '{$path}' && chown nginx: '{$path}'");

                return back()->with('success', 'Directory created successfully');
                break;

            case 'file':
                if (is_dir($path)) {
                    return back()->with('error', "Create file failed! {$path} is directory");
                }

                Disk::createFile($path, $r->content);
                shell_exec("chmod {$permission} '{$path}' && chown nginx: '{$path}'");

                return back()->with('success', 'File created successfully');
                break;

            case 'remote':
                if (file_exists($path)) {
                    $path = $path = ($r->base ?? '/tmp').DIRECTORY_SEPARATOR.'import-'.$r->name;
                }

                $curl = Disk::curl($r->url, $r->ignore ?? false, $httpCode, $error);
                if ($r->ignore == null && $httpCode != 200) {
                    return back()->with('error', "Import file failed! {$error}");
                }

                Disk::createFile($path, $curl);
                shell_exec("chmod {$permission} '{$path}' && chown nginx: '{$path}'");

                return back()->with('success', 'File imported successfully');
                break;

            case 'upload':
                if (file_exists($path)) {
                    $path = $path = ($r->base ?? '/tmp').DIRECTORY_SEPARATOR.'upload-'.$r->name;
                }

                $file = $r->file('file');
                dd($file);

                // shell_exec("chmod {$permission} '{$path}' && chown nginx: '{$path}'");
                return back()->with('success', 'File imported successfully');
                break;
        }

        return $r->all();
    }
}
