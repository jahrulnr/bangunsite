<?php

namespace App\Http\Controllers;

use App\Libraries\Facades\Disk;
use Illuminate\Http\Request;

class FileManagerController extends Controller
{
    private $path;

    public function __construct(Request $r)
    {
        if (! file_exists(env('WEB_PATH'))) {
            mkdir(env('WEB_PATH'), 755, true);
        }

        $this->path = str_replace('//', '/', $r->path ?? '') ?: env('WEB_PATH');
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

    public function show(Request $r)
    {
        $path = $this->path.DIRECTORY_SEPARATOR.$r->name;
        if (! $r->name || ! is_file($path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'File doesnt exists',
            ], 400);
        }

        $content = file_get_contents($path);

        return response()->json([
            'status' => 'success',
            'content' => $content,
        ]);
    }

    public function new(Request $r)
    {
        $name = $r->name ?? basename($r->path);
        $path = ($r->path ?? '/tmp').DIRECTORY_SEPARATOR.$name;
        $permission = ('0'.($r->permission ?? '755'));

        if (! Disk::validatePath($path)) {
            return back()->with('error', 'Path is invalid');
        }

        $needChown = str_starts_with($path, env('WEB_PATH'))
            ? "&& chown nginx:nginx '{$path}'"
            : '';

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
                shell_exec("mkdir -p '{$path}' && chmod {$permission} '{$path}' {$needChown}");

                return back()->with('success', 'Directory created successfully');
                break;

            case 'file':
                if (is_dir($path)) {
                    return back()->with('error', "Create file failed! {$path} is directory");
                }

                Disk::createFile($path, $r->content);
                shell_exec("chmod {$permission} '{$path}' {$needChown}");

                return back()->with('success', 'File created successfully');
                break;

            case 'remote':
                $name = $r->name ?: (basename(parse_url($r->url)['path']) ?? 'new-file');
                if (file_exists($path)) {
                    $path = $path = ($r->path ?? '/tmp').DIRECTORY_SEPARATOR.'import-'.$name;
                }

                $curl = Disk::curl($r->url, $r->ignore ?? false);
                if ($r->ignore == null && $curl->code != 200) {
                    return back()->with('error', "Import file failed! {$curl->error}");
                }

                Disk::createFile($path, $curl->result);
                shell_exec("chmod {$permission} '{$path}' {$needChown}");

                return back()->with('success', 'File imported successfully');
                break;

            case 'upload':
                $file = $r->file('file');
                $maxsize = Disk::toBytes(trim(strtoupper(ini_get('upload_max_filesize'))));

                if ($file->getMaxFilesize() > $maxsize) {
                    return $r->ajax()
                        ? response()->json([
                            'status' => 'error',
                            'message' => 'Upload file failed! File too large',
                        ])
                        : back()->with(
                            'error',
                            'Upload file failed! File too large'
                        );
                }

                $name = $r->name ?: $file->getClientOriginalName();
                if (file_exists($path)) {
                    $path = $path = ($r->path ?? '/tmp').DIRECTORY_SEPARATOR.'upload-'.$name;
                }

                $success = $file->getError() === UPLOAD_ERR_OK;

                $file->move(dirname($path), basename($path));
                shell_exec("chmod {$permission} '{$path}' {$needChown}");

                if ($r->ajax()) {
                    return response()->json([
                        'status' => $success
                            ? 'success'
                            : 'error',
                        'message' => $success
                            ? 'File uploaded successfully'
                            : 'Upload file failed! '.$file->getErrorMessage(),
                    ]);
                }

                return back()->with(
                    $success
                        ? 'success'
                        : 'error',
                    $success
                        ? 'File imported successfully'
                        : 'Upload file failed! '.$file->getErrorMessage()
                );
                break;
        }

        return $r->all();
    }

    public function action(Request $r)
    {
        $path = $r->path.DIRECTORY_SEPARATOR.$r->name;
        switch ($r->type) {
            case 'chmod':
                if (! $r->permission || ($r->permission < 600 || $r->permission > 777)) {
                    return back()->with('error', 'Permission not valid');
                }

                shell_exec("chmod {$r->permission} {$path}");

                return back()->with('success', "chmod {$path} to {$r->permission} successfully");
                break;
            case 'copy':
                $toPath = $r->to;
                if (! $toPath) {
                    $toPath = '/tmp';
                }
                if (is_file($toPath)) {
                    return back()->with('error', 'Cannot copy to '.$toPath.', caused this is file!');
                }
                if ($path == $toPath || $path.'/' == $toPath) {
                    return back()->with('error', 'Cannot copy to same path!');
                }
                if (! file_exists($toPath)) {
                    $needChown = str_starts_with($path, env('WEB_PATH'))
                    ? "&& chown nginx:nginx '{$path}'"
                    : '';
                    shell_exec("mkdir -p '{$toPath}' && chmod 755 '{$toPath}' {$needChown}");
                }

                Disk::cp($path, $toPath);

                // dd(Disk::cp($path, $toPath));
                return back()->with('success', "{$path} copied to {$toPath}");
                break;
            default:
                return $r->all();
        }
    }

    public function delete(Request $r)
    {
        $path = $r->path.DIRECTORY_SEPARATOR.$r->name;

        if (file_exists($path)) {
            Disk::rm($path, true);
        }

        return back()->with('success', $path.' deleted successfully');
    }
}
