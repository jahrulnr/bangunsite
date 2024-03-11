<?php

namespace App\Http\Controllers;

use App\Jobs\RunCommand;
use App\Libraries\Commander;
use App\Libraries\Facades\Disk;
use App\Libraries\Nginx;
use App\Libraries\Site;
use App\Libraries\SSL;
use App\Models\Website;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebsiteManagerController extends Controller
{
    public function index()
    {
        $website = Website::all();
        $nginxConf = Site::getNginxConfig();
        $defConf = Site::getDefaultConfig();

        return view('Website.index', compact('website', 'nginxConf', 'defConf'));
    }

    public function check(Request $r, $id = null): string
    {
        $validate = Validator::make([
            'domain' => 'string|required',
            'path' => 'string|required',
        ], [
            'domain.required' => 'Domain not valid',
            'path.required' => 'Path not valid',
        ]);

        if ($validate->fails()) {
            return $validate->errors()->first();
        }

        if (! filter_var($r->domain, FILTER_VALIDATE_DOMAIN)) {
            return 'Domain not valid';
        }

        $path = $r->get('path');
        if (! Disk::validatePath($path)) {
            return 'Path have illegal character!';
        }

        $site = Website::where('path', $path);
        if (! $site->exists() || ($id != null && ! $site->where('id', '!=', $id)->exists())) {
            try {
                if (is_file($path)) {
                    return 'Path is file!';
                }

                return 'success';
            } catch (\Exception $e) {
                Log::error($e);

                return 'Create path failed! See logs for details.';
            }
        }

        return 'Path used by other website! Please use another path.';
    }

    public function store(Request $r)
    {
        $validate = $this->check($r);
        if ($validate !== 'success') {
            return back()->with('error', $validate);
        }

        $input = $r->only((new Website)->getFillable());
        if ($r->name == null) {
            $input['name'] = $r->domain;
        }
        $website = Website::create($input);

        if (! $website) {
            return back()->with('error', 'Fail to save website data');
        }

        return back()->with('success', $r->domain.' created');
    }

    public function edit($domain)
    {
        $site = Website::getSite($domain);
        if (! $site->exists()) {
            return back()->with('error', "Site doesn't exists");
        }

        $site = $site->first();
        $config = Site::getSiteConfig($site->domain);
        $publicCert = SSL::readPublic($site->domain);
        $privateCert = SSL::readPrivate($site->domain);
        $isEnabled = SSL::checkSSL($site->domain);

        return view('Website.edit', compact('site', 'config', 'publicCert', 'privateCert', 'isEnabled'));
    }

    public function update($id, Request $r)
    {
        $site = Website::find($id);
        if (! $site) {
            return back()->with('error', "Site doesn't exists");
        }

        $validate = $this->check($r, $id);
        if ($validate !== 'success') {
            return back()->with('error', $validate);
        }

        $input = $r->only((new Website)->getFillable());
        if ($r->name == null) {
            $input['name'] = $r->domain;
        }

        $site = $site->first();
        if ($site->update($input)) {
            return redirect(route('website.edit', $r->domain))->with('success', 'Update successfully');
        }

        return redirect(route('website.edit', $r->domain))->with('error', 'Update failed');
    }

    public function updateConfig($id, Request $r)
    {
        if ($id != 'default') {
            $site = Website::find($id);
            if (! $site->exists()) {
                return back()->with('error', "Site doesn't exists");
            }

            $oriConfig = Site::getSiteConfig($site->domain);
            $newConfig = str_replace("\r", '', $r->config);
            $pathConfig = Site::getConfigPath($site->domain);

            Disk::createFile($pathConfig, $newConfig);
            $test = Nginx::test($site->domain);
        } else {
            $pathConfig = Site::$nginxPath.'/http.d/default.conf';
            if (! is_writable($pathConfig)) {
                return back()
                    ->with('error', 'Update failed! Default configuration is not writeable');
            }

            $oriConfig = file_get_contents($pathConfig);
            $newConfig = str_replace("\r", '', $r->config);

            Disk::createFile($pathConfig, $newConfig);
            $test = Nginx::test();
        }

        if ($test === true) {
            Nginx::restart();

            return back()->with('success', 'Update successfully');
        }

        Disk::createFile($pathConfig, $oriConfig);

        return back()
            ->with('error', "Update failed! {$test}")
            ->with('config', $newConfig);
    }

    public function enableSite(string $domain)
    {
        $site = Website::getSite($domain);
        if (! $site->exists()) {
            return response()->json([
                'msg' => "Site doesn't exists",
            ], 400);
        }

        $site = $site->first();
        $site->active = ! $site->active;
        Site::enableSite($domain, $site->active);
        $site->save();

        return response()->json([
            'msg' => 'Site '.($site->active ? 'actived' : 'disabled').' successfully',
        ]);
    }

    public function installSSL($id, Request $r)
    {
        $site = Website::find($id);
        if (! $site) {
            return back()->with('error', "Site doesn't exists");
        }

        $scriptName = "/tmp/{$site->domain}.sh";
        $outputName = "/tmp/{$site->domain}.txt";
        $cmd = "certbot --non-interactive --agree-tos --register-unsafely-without-email --nginx -d {$site->domain}";
        // $cmd = 'ping 1.1.1.1 -c 100';

        if ($r->start == 'true') {
            ! file_exists($outputName) ?: unlink($outputName);
            Disk::createFile($scriptName, <<<BASH
                #!/bin/bash
                echo "-- Task Start --"
                echo "$cmd"
                $cmd
                echo "-- Task Done --"
                rm $scriptName
            BASH);
            Commander::exec("chmod +x $scriptName");
            dispatch((new RunCommand("{$scriptName} > {$outputName}", true)));
        }

        if (file_exists($outputName)) {
            $read = '';
            try {
                $read = file_get_contents($outputName);
            } catch (Error $e) {
                Log::emergency($e->getMessage());
                $read = $e->getMessage().PHP_EOL.'-- Task Done --';
            }
        } else {
            return 'Waiting task run on queue';
        }

        return $read;
    }

    public function updateSSL($id, Request $r)
    {
        $site = Website::find($id);
        if (! $site) {
            return back()->with('error', "Site doesn't exists");
        }

        $privateCert = str_replace("\r", '', $r->private);
        $publicCert = str_replace("\r", '', $r->public);
        $certPath = SSL::getCertPath($site->domain);
        $realPath = dirname(SSL::$sslPath)."/archive/{$site->domain}";
        $livePath = SSL::$sslPath;
        if ($certPath == false) {
            $publicPath = $realPath.'/fullchain.pem';
            $privatePath = $realPath.'/privkey.pem';
            $publicLink = $livePath.'/fullchain.pem';
            $privateLink = $livePath.'/privkey.pem';
            $put = @Disk::createFile($publicPath, $publicCert)
                && @Disk::createFile($privatePath, $privateCert);
            @mkdir($livePath, 0755, true);
            @symlink('../../live/'.$site->domain.'/fullchain.pem', $publicLink);
            @symlink('../../live/'.$site->domain.'/privkey.pem', $privateLink);
        } else {
            try {
                $put = file_put_contents($certPath->private, $privateCert)
                    && file_put_contents($certPath->public, $publicCert);
            } catch (Error $e) {
                $put = false;
            }
        }

        return back()->with(
            $put ? 'success' : 'error',
            $put ? 'Update SSL successfully' : 'Error when writting SSL'
        );
    }

    public function updateNginx(Request $r)
    {
        if (! $r->content) {
            return back()->with('error', 'Configuration not valid!');
        }

        $nginxConf = Site::$nginxPath.'/nginx.conf';
        if (! is_writable($nginxConf)) {
            return back()
                ->with('error', 'Update failed! Nginx configuration is not writeable');
        }

        $fileTest = Site::$nginxPath.'/nginx-test.conf';
        Disk::createFile($fileTest, $r->content);
        $test = Nginx::testNginxConf();
        unlink($fileTest);
        if ($test === true) {
            Disk::createFile($nginxConf, $r->content);
            Nginx::restart();

            return back()->with('success', 'Nginx configuration updated successfully.');
        }

        return back()->with('error', 'Update error! '.$test);
    }

    public function destroy(string $domain, Request $r)
    {
        $site = Website::getSite($domain);
        if (! $site->exists()) {
            return response()->json([
                'msg' => "Site doesn't exists",
            ], 400);
        }

        Site::removeSite($site->first(), $r->clean);
        $site->delete();

        return response()->json([
            'msg' => 'Site deleted successfully',
        ]);
    }
}
