<?php

namespace App\Http\Controllers;

use App\Libraries\Disk;
use App\Libraries\Nginx;
use App\Libraries\Site;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebsiteManagerController extends Controller
{
    public function index()
    {
        $website = Website::all();

        return view('Website.index', compact('website'));
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

        return view('Website.edit', compact('site', 'config'));
    }

    public function update($id, Request $r)
    {
        $site = Website::find($id);
        if (! $site->exists()) {
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
        if ($site->update($r->only($site->getFillable()))) {
            return redirect(route('website.edit', $r->domain))->with('success', 'Update successfully');
        }

        return redirect(route('website.edit', $r->domain))->with('error', 'Update failed');
    }

    public function updateConfig($id, Request $r)
    {
        $site = Website::find($id);
        if (! $site->exists()) {
            return back()->with('error', "Site doesn't exists");
        }

        $oriConfig = Site::getSiteConfig($site->domain);
        $newConfig = str_replace("\r", '', $r->config);
        $pathConfig = Site::getConfigPath($site->domain);

        Disk::createFile($pathConfig, $newConfig);
        $test = Nginx::test($site->domain);

        if ($test === true) {
            Nginx::restart();

            return back()->with('success', 'Update successfully');
        }

        Disk::createFile($pathConfig, $oriConfig);

        return back()
            ->with('error', "Update failed! {$test}")
            ->with('config', $newConfig);
    }

    public function updateSSL($id, Request $r)
    {
        $site = Website::find($id);
        if (! $site->exists()) {
            return back()->with('error', "Site doesn't exists");
        }

        return $r->all();
    }

    public function destroy($id, Request $r)
    {
        $website = Website::find($id);
        if ($website->doesntExists()) {
            return back()->with('error', "Website doesn't exists!");
        }

        $website::removeSite($website, $r->clean);
        $website->delete();

        return back()->with('success', 'Website deleted successfully');
    }
}
