<?php

namespace App\Http\Controllers;

use App\Libraries\Commander;
use App\Libraries\Facades\Disk;
use App\Libraries\Facades\FPM;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController
{
    public function index()
    {
        $user = Auth::user();
        $phpConfig = FPM::phpConf();
        $fpmConfig = FPM::fpmConf();
        $poolConfig = FPM::poolConf();

        return view('Setting.index', compact('user', 'phpConfig', 'fpmConfig', 'poolConfig'));
    }

    public function updateProfile(Request $request)
    {
        $user = User::find($request->id);
        if (! $user) {
            return back()->with('error', "Account doesnt exist's");
        }

        $input = $request->only(['name', 'email']);
        $password = $request->password;
        $passLen = strlen($password);
        if ($passLen > 0 && $passLen < 6) {
            return back()->with('warning', 'Password must more than 5 character');
        }
        if ($passLen > 5) {
            $input['password'] = bcrypt($password);
        }

        $user->update($input);

        return back()->with('success', 'Account updated successfully');
    }

    public function updatePHP(Request $request)
    {
        $phpConf = str_replace("\r", '', $request->{'php-config'});
        $tmp = '/tmp/'.time().'.ini';
        Disk::createFile($tmp, $phpConf);
        $test = Commander::shell('php -nc '.$tmp.' -v');
        unlink($tmp);

        if (str_contains($test, 'error')) {
            return back()->with('error', explode("\n", $test)[0]);
        }

        if (is_writable(FPM::getPath('php.ini'))) {
            FPM::setPhpConf($phpConf);

            return back()->with('success', 'php.ini updated successfully');
        }

        return back()->with('error', 'php.ini update failed. Please check php.ini permission!');
    }

    public function updateFPM(Request $request)
    {
        $fpmConf = str_replace("\r", '', $request->{'fpm-config'});
        $tmp = '/tmp/'.time().'.conf';
        Disk::createFile($tmp, $fpmConf);
        $test = Commander::shell('php-fpm -ny '.$tmp.' -t');
        unlink($tmp);

        if (str_contains($test, 'ERROR')) {
            $err = explode("\n", $test)[0];

            return back()->with('error', preg_replace("/^\[.*\] ERROR/", 'ERROR', $err));
        }

        if (is_writable(FPM::getPath('php-fpm.conf'))) {
            FPM::setFpmConf($fpmConf);

            return back()->with('success', 'php-fpm.conf updated successfully');
        }

        return back()->with('error', 'php-fpm.conf update failed. Please check php-fpm.conf permission!');
    }

    public function updatePool(Request $request)
    {
        $poolFpm = str_replace("\r", '', $request->{'pool-config'});
        $tmp = '/tmp/'.time().'.conf';
        Disk::createFile($tmp, $poolFpm);
        $test = Commander::shell('php-fpm -ny '.$tmp.' -t');
        unlink($tmp);

        if (str_contains($test, 'ERROR')) {
            $err = explode("\n", $test)[0];

            return back()->with('error', preg_replace("/^\[.*\] ERROR/", 'ERROR', $err));
        }

        if (is_writable(FPM::getPath('php-fpm.d/www.conf'))) {
            FPM::setPoolConf($poolFpm);

            return back()->with('success', 'www.conf updated successfully');
        }

        return back()->with('error', 'www.conf update failed. Please check www.conf permission!');
    }
}
