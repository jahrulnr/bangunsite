<?php

namespace App\Http\Controllers;

use Error;
use Exception;
use Illuminate\Http\Request;

class MountManager extends Controller
{
    private $FSTAB = '/etc/fstab';

    private $list = [];

    public function __construct()
    {
        $raw = file_get_contents($this->FSTAB);
        $line = explode("\n", $raw);
        $this->setValues($line);
    }

    public function setValues(array $line)
    {
        foreach ($line as $row) {
            if (str_starts_with($row, '#')) {
                continue;
            }
            $row = trim($row);
            if (empty($row)) {
                continue;
            }
            $row = preg_replace("/\t+/", ' ', $row);
            $columns = explode(' ', $row);
            $check = shell_exec("mountpoint {$columns[1]}") ?? 'is not';
            $columns[99] = ! (
                str_contains($check, 'failed')
                || str_contains($check, 'No such')
                || str_contains($check, 'is not')
            ) ? 'Enabled' : 'Disabled';
            $this->list[] = $columns;
            // if path mounted, file_exists or is_dir is broken
            // so, we use shell for fixed it
            shell_exec("mkdir -p {$columns[1]}");
        }
    }

    public function index()
    {
        return view('MountManager.index', [
            'fstabs' => $this->list,
        ]);
    }

    public function enable(Request $r)
    {
        $log = md5($r->device.'|-|'.$r->dir).'.log';
        foreach ($this->list as $index => $list) {
            if ($r->device == $list[0] && $r->dir == $list[1]) {
                $success = true;
                shell_exec("nohup mount {$r->dir} > /tmp/{$log} 2>&1 &");
            }
        }

        sleep(3);
        $check = '';
        try {
            $check = str_replace("\n", '', file_get_contents('/tmp/'.$log));
            $success = ! (
                str_contains($check, 'failed')
                || str_contains($check, 'No such')
                || str_contains($check, 'is no')
            );
            unlink('/tmp/'.$log);
        } catch (Error|Exception $e) {
            $success = false;
        }

        return $success
        ? back()->with('success', "Mount {$r->device} => {$r->dir} successfully")
        : back()->with('error', ! empty($check) ? $check : "Mount {$r->device} => {$r->dir} failed");
    }

    public function add(Request $r)
    {
        $this->list[] = [
            $r->device,
            $r->dir,
            $r->type,
            $r->option,
            $r->dump,
            $r->fsck,
        ];

        return $this->save()
        ? back()->with('success', "Mount {$r->device} => {$r->dir} removed successfully")
        : back()->with('error', "Mount {$r->device} => {$r->dir} not found");
    }

    public function update(Request $r)
    {
        $success = false;
        foreach ($this->list as $index => $list) {
            if ($r->mount_device == $list[0] && $r->mount_dir == $list[1]) {
                $this->list[$index] = [
                    $r->device,
                    $r->dir,
                    $r->type,
                    $r->option,
                    $r->dump,
                    $r->fsck,
                ];
                shell_exec("umount {$r->dir}");
                $success = $this->save();
            }
        }

        return $success
        ? back()->with('success', "Mount {$r->device} => {$r->dir} updated successfully")
        : back()->with('error', "Mount {$r->device} => {$r->dir} not found");
    }

    public function destroy(Request $r)
    {
        $success = false;
        foreach ($this->list as $index => $list) {
            if ($r->device == $list[0] && $r->dir == $list[1]) {
                unset($this->list[$index]);
                shell_exec("umount {$r->dir}");
                $success = $this->save();
            }
        }

        return $success
        ? back()->with('success', "Mount {$r->device} => {$r->dir} removed successfully")
        : back()->with('error', "Mount {$r->device} => {$r->dir} not found");
    }

    private function save()
    {
        $line = [];
        foreach ($this->list as $list) {
            foreach ($list as $index => $value) {
                $list[$index] = preg_replace("#(\s|\t)#", '', $value);
            }

            $line[] = "{$list[0]}\t{$list[1]}\t{$list[2]}\t{$list[3]} {$list[4]} {$list[5]}";
        }

        $raw = implode("\n", $line);
        $success = file_put_contents($this->FSTAB, $raw);

        return $success;
    }
}
